<?php

// declare(strict_types=1);

namespace ClickPay\PayPage\Controller\PayPage;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use ClickPay\PayPage\Gateway\Http\ClickPayCore;
use ClickPay\PayPage\Gateway\Http\ClickPayHelper;
use ClickPay\PayPage\Gateway\Http\ClickPayHelpers;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\Framework\DB\Transaction;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use ClickPay\PayPage\Model\Adminhtml\Source\CurrencySelect;
use Magento\Sales\Model\Order;
use Magento\Checkout\Model\Session;
use ClickPay\PayPage\Gateway\Http\ClickPayEnum;
use ClickPay\PayPage\Model\Adminhtml\Source\EmailConfig;
use Magento\Vault\Api\Data\PaymentTokenFactoryInterface;
use Magento\Sales\Model\Order\Invoice;

/**
 * Class Index
 */
class Response extends Action
{
     use ClickPayHelpers;

    // protected $resultRedirect;
    private $ClickPay;

    protected $quoteRepository;

    /**
     * @var InvoiceService
     */
    private $invoiceService;

    /**
     * @var Transaction
     */
    private $dbTransaction;

    protected $configHelper;

    /**
     * @var InvoiceSender
     */
    private $invoiceSender;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    private $orderFactory;

    protected $order;

    protected $checkoutSession;

    private $_row_details = \Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS;

    private $_paymentTokenFactory;

    /**
     * @var Magento\Sales\Model\Order\Email\Sender\OrderSender
     */
    private $_orderSender;

    /**
     * @var EncryptorInterface
     */
    private $encryptor;


    /**
     * @var \Psr\Log\LoggerInterface
     */
    // protected $_logger;

    /**
     * @param Context $context
     * @param PageFactory $pageFactory
     */
    public function __construct(
        Context $context,
        InvoiceSender $invoiceSender,
        InvoiceService $invoiceService,
        Transaction $dbTransaction,
        Session $checkoutSession,
        Order $order,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \ClickPay\PayPage\Helper\Config $configHelper,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        PaymentTokenFactoryInterface $paymentTokenFactory,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender
    ) {
        parent::__construct($context);

        $this->quoteRepository = $quoteRepository;
        $this->invoiceService     = $invoiceService;
        $this->dbTransaction      = $dbTransaction;
        $this->configHelper  = $configHelper;
        $this->invoiceSender = $invoiceSender;
        $this->orderFactory = $orderFactory;
        $this->order = $order;
        $this->checkoutSession = $checkoutSession;
        $this->_paymentTokenFactory = $paymentTokenFactory;
        $this->encryptor = $encryptor;
        $this->_orderSender = $orderSender;
        $this->ClickPay = new \ClickPay\PayPage\Gateway\Http\Client\Api;
        new ClickPayCore();
    }

    /**
     * @return ResponseInterface|ResultInterface|Page
     */
    public function execute()
    {
        if (!$this->getRequest()->isPost()) {
            ClickPayHelper::log("ClickPay: no post back data received in callback", 3);
            return;
        }

        $_p_tran_ref = 'tranRef';
        $_p_cart_id = 'cartId';
        $transactionId = $this->getRequest()->getParam($_p_tran_ref, null);
        $pOrderId = $this->getRequest()->getParam($_p_cart_id, null);

        if (!$pOrderId || !$transactionId) {
            ClickPayHelper::log("ClickPay: OrderId/TransactionId data did not receive in callback", 3);
            return;
        }

        ClickPayHelper::log("Return triggered, Order [{$pOrderId}], Transaction [{$transactionId}]", 1);

        $order = $this->order->loadByIncrementId($pOrderId);

        if (!$this->isValidOrder($order)) {
            ClickPayHelper::log("ClickPay: Order is missing, Order [{$pOrderId}]", 3);
            return;
        }

        $payment = $order->getPayment();
        $paymentMethod = $payment->getMethodInstance();
        $ptApi = $this->ClickPay->pt($paymentMethod);

        $verify_response = $ptApi->verify_payment($transactionId);
        if (!$verify_response) {
            return;
        }

        $cart_refill = (bool) $paymentMethod->getConfigData('order_failed_reorder');
        return $this->pt_handle_return($order, $verify_response, $cart_refill , $payment);

    }

    private function pt_handle_return($order, $verify_response, $cart_refill , $payment)
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $paymentMethod = $payment->getMethodInstance();

        $paymentSuccess =
            $paymentMethod->getConfigData('order_success_status') ?? Order::STATE_PROCESSING;
        $paymentFailed =
            $paymentMethod->getConfigData('order_failed_status') ?? Order::STATE_CANCELED;

        $sendInvoice = (bool) $paymentMethod->getConfigData('automatic_invoice');
        $sendInvoiceEmail = (bool) $paymentMethod->getConfigData('email_customer');
        $emailConfig = $paymentMethod->getConfigData('email_config');
        // $cart_refill = (bool) $paymentMethod->getConfigData('order_failed_reorder');
        $use_order_currency = CurrencySelect::UseOrderCurrency($payment);

        //

        $success = $verify_response->success;
        $is_on_hold = $verify_response->is_on_hold;
        $is_pending = $verify_response->is_pending;
        $res_msg = $verify_response->message;
        $orderId = @$verify_response->reference_no;
        $transaction_ref = @$verify_response->transaction_id;
        $pt_prev_tran_ref = @$verify_response->previous_tran_ref;
        $transaction_type = @$verify_response->tran_type;
        $response_code = @$verify_response->response_code;

        //

        $_fail = !($success || $is_on_hold || $is_pending);

        if ($_fail) {
            ClickPayHelper::log("ClickPay Response: Payment verify failed, Order {$orderId}, Message [$res_msg]", 2);

            // $payment->deny();
            $payment->cancel();

            $order->addStatusHistoryComment(__('Payment failed: [%1].', $res_msg));

            if ($paymentFailed != Order::STATE_CANCELED) {
                $this->setNewStatus($order, $paymentFailed);
            } else {
                $order->cancel();
            }
            $order->save();

            return;
        }

        // Success or OnHold  or Pending

        $tranAmount = @$verify_response->cart_amount;
        $tranCurrency = @$verify_response->cart_currency;

        $_tran_details = [
            'tran_amount'   => $tranAmount,
            'tran_currency' => $tranCurrency,
            'tran_type'     => $transaction_type,
            'response_code' => $response_code
        ];
        if ($pt_prev_tran_ref) {
            $_tran_details['previous_tran'] = $pt_prev_tran_ref;
        }

        $payment
            ->setTransactionId($transaction_ref)
            ->setTransactionAdditionalinfo($this->_row_details, $_tran_details);


        $paymentAmount = $this->getAmount($payment, $tranCurrency, $tranAmount, $use_order_currency);

        if ($is_pending) {
            $payment
                ->setIsTransactionPending(true)
                ->setIsTransactionClosed(false);

            //

            ClickpayHelper::log("Order {$orderId}, On-Hold (Pending), transaction {$transaction_ref}", 1);

            $order->hold();

            // Add Comment to Store Admin
            $order->addStatusHistoryComment("Transaction {$transaction_ref} is Pending, (Reference number: {$response_code}).");

            // Add comment to the Customer
            $order->addCommentToStatusHistory("Payment Reference number: {$response_code}", false, true);

            $order->save();

            return;
        }

        $payment->setAmountAuthorized($payment->getAmountOrdered());


        if (ClickPayEnum::TranIsSale($transaction_type)) {

            if ($pt_prev_tran_ref) {
                $payment->setParentTransactionId($pt_prev_tran_ref);
            }

            // $payment->capture();
            $payment->registerCaptureNotification($paymentAmount, true);
        } else {
            $payment
                ->setIsTransactionClosed(false)
                ->registerAuthorizationNotification($paymentAmount);
        }

        $payment->accept();

        $canSendEmail = EmailConfig::canSendEMail(EmailConfig::EMAIL_PLACE_AFTER_PAYMENT, $emailConfig);
        if ($canSendEmail) {
            $order->setCanSendNewEmailFlag(true);
            $this->_orderSender->send($order);
        }

        if ($sendInvoice) {
            $this->invoiceSend($order, $payment);
        }

        if ($success) {

            if ($paymentSuccess != Order::STATE_PROCESSING) {
                $this->setNewStatus($order, $paymentSuccess);
            }

            //

            $this->pt_manage_tokenize($this->_paymentTokenFactory, $this->encryptor, $payment, $verify_response);

            if ($sendInvoice && $sendInvoiceEmail) {
                $invoice = $order->getInvoiceCollection()->getFirstItem();
                $this->invoiceSender->send($invoice);
            }

            //
        } elseif ($is_on_hold) {
            $order->hold();

            ClickPayHelper::log("Order {$orderId}, On-Hold, transaction {$transaction_ref}", 1);
            $order->addCommentToStatusHistory("Transaction {$transaction_ref} is On-Hold, Go to ClickPay dashboard to Approve/Decline it");
        }

        //

        $order->save();

        ClickPayHelper::log("Order {$orderId}, Message [$res_msg]", 1);

        if ($success) {
            $this->messageManager->addSuccessMessage('The payment has been completed successfully - ' . $res_msg);
            $redirect_page = 'checkout/onepage/success';
        } else if ($is_on_hold) {
            $this->messageManager->addWarningMessage('The payment is pending - ' . $res_msg);
            $redirect_page = 'checkout/onepage/success';
        } else {

            $this->messageManager->addErrorMessage('The payment failed - ' . $res_msg);
            $redirect_page = 'checkout/onepage/failure';

            if ($cart_refill) {
                try {
                    $quoteId = $order->getQuoteId();
                    $quote = $this->quoteRepository->get($quoteId);
                    $quote->setIsActive(true)->removePayment()->save();
                    $this->checkoutSession->replaceQuote($quote);
                    $redirect_page = 'checkout/cart';
                } catch (\Throwable $th) {
                    ClickPayHelper::log("ClickPay: load Quote by ID failed!, Order [{$orderId}], QuoteId = [{$quoteId}]", 3);
                }
            }
        }

        $resultRedirect->setPath($redirect_page);
        return $resultRedirect;
    }

    private function createMagentoInvoice($order)
    {
        $invoice = $this->invoiceService->prepareInvoice($order);
        $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_OFFLINE);
        $invoice->register();
        $transaction = $this->dbTransaction->addObject($invoice)->addObject($invoice->getOrder());
        $transaction->save();
        $this->updateOrderStatus($order, Order::STATE_PROCESSING);
        return $invoice;
    }


    // public function getAutomaticInvoice()
    // {
    //     return $this->configHelper->isAutomaticInvoice();
    // }

    // public function getEmailCustomer()
    // {
    //     return $this->configHelper->isEmailCustomer();
    // }

    private function sendInvoiceEmail($invoice)
    {
        $order = $invoice->getOrder();
        $storeId = $order->getStoreId();
        $this->invoiceSender->send($invoice);
        $order->addStatusHistoryComment(
            __('Notified customer about invoice #%1.', $invoice->getIncrementId())
        )
        ->setState('processing')
        ->setStatus('processing')
        ->setIsCustomerNotified(true)
        ->save();
    }

    private function updateOrderStatus($order, $status)
    {
        $orderModel = $this->orderFactory->create()->load($order->getId());
        $orderModel->setState($status)->setStatus(Order::STATE_PROCESSING)->save();
    }

    
}
