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
use Magento\Sales\Model\Order;
use Magento\Checkout\Model\Session;

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
        \Magento\Sales\Model\OrderFactory $orderFactory

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

        $verify_response = $ptApi->read_response(false);
        if (!$verify_response) {
            return;
        }

        $cart_refill = (bool) $paymentMethod->getConfigData('order_failed_reorder');
        return $this->pt_handle_return($order, $verify_response, $cart_refill);

    }

    private function pt_handle_return($order, $verify_response, $cart_refill)
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        $success = $verify_response->success;
        $is_on_hold = $verify_response->is_on_hold;
        $res_msg = $verify_response->message;
        $orderId = @$verify_response->reference_no;


        if ($this->getActiveAll() == true && $success == true && $this->getAutomaticInvoiceAll() == true) {
            $invoice = $this->createMagentoInvoice($order);
            $invoiceId = $invoice->getId();
            $payment = $order->getPayment();
            $payment->setTransactionId($invoiceId);
            if($this->getEmailCustomerAll() == true){
                try {
                    $this->sendInvoiceEmail($invoice);
                } catch (\Exception $e) {
                    echo "Error sending invoice email: " . $e->getMessage();
                }
            }
        }

        if ($this->getActiveCreditCard() == true && $success == true && $this->getAutomaticInvoiceCreditCard() == true) {
            $invoice = $this->createMagentoInvoice($order);
            $invoiceId = $invoice->getId();
            $payment = $order->getPayment();
            $payment->setTransactionId($invoiceId);
            if($this->getEmailCustomerCreditCard() == true){
                try {
                    $this->sendInvoiceEmail($invoice);
                } catch (\Exception $e) {
                    echo "Error sending invoice email: " . $e->getMessage();
                }
            }
        }

        if ($this->getActiveApplePay() == true && $success == true && $this->getAutomaticInvoiceApplePay() == true) {
            $invoice = $this->createMagentoInvoice($order);
            $invoiceId = $invoice->getId();
            $payment = $order->getPayment();
            $payment->setTransactionId($invoiceId);
            if($this->getEmailCustomerApplePay() == true){
                try {
                    $this->sendInvoiceEmail($invoice);
                } catch (\Exception $e) {
                    echo "Error sending invoice email: " . $e->getMessage();
                }
            }
        }

        if ($this->getActiveMada() == true && $success == true && $this->getAutomaticInvoiceMada() == true) {
            $invoice = $this->createMagentoInvoice($order);
            $invoiceId = $invoice->getId();
            $payment = $order->getPayment();
            $payment->setTransactionId($invoiceId);
            if($this->getEmailCustomerMada() == true){
                try {
                    $this->sendInvoiceEmail($invoice);
                } catch (\Exception $e) {
                    echo "Error sending invoice email: " . $e->getMessage();
                }
            }
        }

        if ($this->getActiveAmexCard() == true && $success == true && $this->getAutomaticInvoiceAmex() == true) {
            $invoice = $this->createMagentoInvoice($order);
            $invoiceId = $invoice->getId();
            $payment = $order->getPayment();
            $payment->setTransactionId($invoiceId);
            if($this->getEmailCustomerAmex() == true){
                try {
                    $this->sendInvoiceEmail($invoice);
                } catch (\Exception $e) {
                    echo "Error sending invoice email: " . $e->getMessage();
                }
            }
        }
        
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

    public function getActiveAll()
    {
        return $this->configHelper->isActiveAll();
    }

    public function getActiveCreditCard()
    {
        return $this->configHelper->isActiveCreditCard();
    }

    public function getActiveApplePay()
    {
        return $this->configHelper->isActiveApplePay();
    }

    public function getActiveMada()
    {
        return $this->configHelper->isActiveMada();
    }

    public function getActiveAmexCard()
    {
        return $this->configHelper->isActiveAmexCard();
    }

    public function getAutomaticInvoiceAll()
    {
        return $this->configHelper->isAutomaticInvoiceAll();
    }

    public function getEmailCustomerAll()
    {
        return $this->configHelper->isEmailCustomerAll();
    }

    public function getAutomaticInvoiceCreditCard()
    {
        return $this->configHelper->isAutomaticInvoiceCreditCard();
    }
    
    public function getEmailCustomerCreditCard()
    {
        return $this->configHelper->isEmailCustomerCreditCard();
    }

    public function getAutomaticInvoiceApplePay()
    {
        return $this->configHelper->isAutomaticInvoiceApplePay();
    }

    public function getEmailCustomerApplePay()
    {
        return $this->configHelper->isEmailCustomerApplePay();
    }

    public function getAutomaticInvoiceMada()
    {
        return $this->configHelper->isAutomaticInvoiceMada();
    }

    public function getEmailCustomerMada()
    {
        return $this->configHelper->isEmailCustomerMada();
    }

    public function getAutomaticInvoiceAmex()
    {
        return $this->configHelper->isAutomaticInvoiceAmex();
    }

    public function getEmailCustomerAmex()
    {
        return $this->configHelper->isEmailCustomerAmex();
    }


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