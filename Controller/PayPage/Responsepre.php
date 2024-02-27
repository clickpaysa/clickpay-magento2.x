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
class Responsepre extends Action
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
        ClickpayHelper::log("Return pre triggered", 1);

        $_p_tran_ref = 'tranRef';
        $_p_cart_id = 'cartId';
        $transactionId = $this->getRequest()->getParam($_p_tran_ref, null);
        $pOrderId = $this->getRequest()->getParam($_p_cart_id, null);
        ClickpayHelper::log("Return pre [$pOrderId] [$transactionId]", 1);

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $quoteFactory = $objectManager->create('Magento\Quote\Model\QuoteFactory');
        $customerFactory = $objectManager->create('Magento\Customer\Model\CustomerFactory');
        $quoteRepository = $objectManager->create('Magento\Quote\Api\CartRepositoryInterface');
        $cartManagementInterface = $objectManager->create('Magento\Quote\Api\CartManagementInterface');
        $order = $objectManager->create('Magento\Sales\Model\Order');

        $quoteTosubmit = $quoteFactory->create()->load($pOrderId);
        ClickpayHelper::log("Return pre Quote Active  [$pOrderId]", 1);

        if ($quoteTosubmit->getIsActive()) {
            try {
                if (is_null($quoteTosubmit->getCustomerId())) {
                    $customerEmail = $quoteTosubmit->getBillingAddress()->getEmail();
                    $websiteId = $quoteTosubmit->getStore()->getWebsiteId();
                    $customer = $customerFactory->create();
                    $customer->setWebsiteId($websiteId);
                    $customer->loadByEmail($customerEmail);
                    if ($customer->getEntityId()) {
                        $quoteTosubmit->setCustomerId($customer->getEntityId());
                        $quoteTosubmit->setCustomerFirstname($customer->getFirstname());
                        $quoteTosubmit->setCustomerLastname($customer->getLastname());
                        $quoteTosubmit->setCustomerEmail($customerEmail);
                        $quoteTosubmit->setCustomerIsGuest(false);
                    } else {
                        $quoteTosubmit->setCustomerFirstname($quoteTosubmit->getBillingAddress()->getFirstname());
                        $quoteTosubmit->setCustomerLastname($quoteTosubmit->getBillingAddress()->getLastname());
                        $quoteTosubmit->setCustomerEmail($customerEmail);
                        $quoteTosubmit->setCustomerIsGuest(true);
                    }
                }


                // Save Quote
                $quoteTosubmit->setInventoryProcessed(false);
                $quoteTosubmit->save();
                $quoteTosubmit->collectTotals()->save();
                ClickpayHelper::log("Return pre Quote Saved  [$pOrderId]", 1);

                $finalquote = $quoteRepository->get($quoteTosubmit->getId());
                $orderId = $cartManagementInterface->placeOrder($finalquote->getId());
                $order = $order->load($orderId);

                if ($order->getId()) {
                    ClickpayHelper::log("Return pre Order Created  [$pOrderId]", 1);
                    $payment = $order->getPayment();
                    $paymentMethod = $payment->getMethodInstance();
                    $ptApi = $this->ClickPay->pt($paymentMethod);

                    $verify_response = $ptApi->verify_payment($transactionId);
                    if (!$verify_response) {
                        ClickpayHelper::log("Return pre Order Verify No Response", 1);
                        $this->messageManager->addErrorMessage('The payment failed - Verify No Response');
                        return $this->_redirect('checkout/cart');
                    }

                    $cart_refill = (bool) $paymentMethod->getConfigData('order_failed_reorder');

                    ClickpayHelper::log("Return pre Order invoice start", 1);
                    $resultRedirect = $this->resultRedirectFactory->create();

                    $paymentMethod = $payment->getMethodInstance();

                    $paymentSuccess =
                        $paymentMethod->getConfigData('order_success_status') ?? Order::STATE_PROCESSING;
                    $paymentFailed =
                        $paymentMethod->getConfigData('order_failed_status') ?? Order::STATE_CANCELED;
                    $sendInvoice = (bool) $paymentMethod->getConfigData('automatic_invoice');
                    $sendInvoiceEmail = (bool) $paymentMethod->getConfigData('email_customer');
                    $emailConfig = $paymentMethod->getConfigData('email_config');

                    $use_order_currency = CurrencySelect::UseOrderCurrency($payment);

                    $success = $verify_response->success;
                    $is_on_hold = $verify_response->is_on_hold;
                    $is_pending = $verify_response->is_pending;
                    $res_msg = $verify_response->message;
                    $orderId = @$verify_response->reference_no;
                    $transaction_ref = @$verify_response->transaction_id;
                    $pt_prev_tran_ref = @$verify_response->previous_tran_ref;
                    $transaction_type = @$verify_response->tran_type;
                    $response_code = @$verify_response->response_code;
                    ClickpayHelper::log("Return pre Order invoice start 1", 1);

                    $_fail = !($success || $is_on_hold || $is_pending);

                    if ($_fail) {
                        ClickPayHelper::log("ClickPay Response: Payment verify failed, Order {$orderId}, Message [$res_msg]", 2);

                        $payment->cancel();
                        $order->addStatusHistoryComment(__('Payment failed: [%1].', $res_msg));

                        if ($paymentFailed != Order::STATE_CANCELED) {
                            $this->setNewStatus($order, $paymentFailed);
                        } else {
                            $order->cancel();
                        }
                        $order->save();

                        $resultRedirect->setPath('checkout/cart');
                        return $resultRedirect;
                    } else {

                        ClickpayHelper::log("Return pre Order invoice start 2", 1);

                        // Success or OnHold  or Pending
                        $tranAmount = $verify_response->cart_amount;
                        $tranCurrency = $verify_response->cart_currency;

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

                        ClickpayHelper::log("Return pre Order invoice start 3", 1);

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

                            $redirectUrl = $this->_url->getUrl('checkout/onepage/success', [
                                'entity_id' =>  $order->getEntityId(),
                                'increment_id' => $order->getIncrementId(),
                                'quote_id' => $order->getQuoteId()
                            ]);
                            $resultRedirect->setUrl($redirectUrl);
                            return $resultRedirect;
                        } else {

                            ClickpayHelper::log("Return pre Order invoice start 4", 1);

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

                            ClickpayHelper::log("Return pre Order invoice start 5", 1);

                            $payment->accept();

                            $canSendEmail = EmailConfig::canSendEMail(EmailConfig::EMAIL_PLACE_AFTER_PAYMENT, $emailConfig);
                            if ($canSendEmail) {
                                $order->setCanSendNewEmailFlag(true);
                                $this->_orderSender->send($order);
                            }

                            ClickpayHelper::log("Return pre Order invoice start 6", 1);

                            if ($sendInvoice) {
                                $this->invoiceSend($order, $payment);
                            }
                            ClickpayHelper::log("Return pre Order invoice start 7", 1);

                            if ($success) {
                                ClickpayHelper::log("Return pre Order invoice start 8", 1);

                                $this->pt_manage_tokenize($this->_paymentTokenFactory, $this->encryptor, $payment, $verify_response);
                                $order->save();
                                $this->messageManager->addSuccessMessage('The payment has been completed successfully - ' . $res_msg);

                                ClickpayHelper::log("Return pre Order invoice start 11", 1);
                                // $resultRedirect->setPath($redirect_page);
                                // return $resultRedirect;
                                // Redirect to the success page
                                // Redirect to order success page with parameters

                                if ($sendInvoice && $sendInvoiceEmail) {
                                    $invoice = $order->getInvoiceCollection()->getFirstItem();
                                    $this->invoiceSender->send($invoice);
                                }
                                
                                $redirectUrl = $this->_url->getUrl('checkout/onepage/success', [
                                    'entity_id' =>  $order->getEntityId(),
                                    'increment_id' => $order->getIncrementId(),
                                    'quote_id' => $order->getQuoteId()
                                ]);
                                $resultRedirect->setUrl($redirectUrl);
                                return $resultRedirect;
                            } elseif ($is_on_hold) {
                                ClickpayHelper::log("Return pre Order invoice start 9", 1);
                                $order->hold();

                                ClickPayHelper::log("Order {$orderId}, On-Hold, transaction {$transaction_ref}", 1);
                                $order->addCommentToStatusHistory("Transaction {$transaction_ref} is On-Hold, Go to ClickPay dashboard to Approve/Decline it");

                                $order->save();
                                $this->messageManager->addWarningMessage('The payment is pending - ' . $res_msg);
                                ClickpayHelper::log("Return pre Order invoice start 11", 1);

                                $redirectUrl = $this->_url->getUrl('checkout/onepage/success', [
                                    'entity_id' =>  $order->getEntityId(),
                                    'increment_id' => $order->getIncrementId(),
                                    'quote_id' => $order->getQuoteId()
                                ]);
                                $resultRedirect->setUrl($redirectUrl);
                                return $resultRedirect;
                            } else {
                                ClickpayHelper::log("Return pre Order invoice start 10", 1);
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

                                ClickpayHelper::log("Return pre Order invoice start 11 -  [$redirect_page]", 1);

                                $resultRedirect->setPath($redirect_page);
                                return $resultRedirect;
                            }
                        }
                    }
                } else {
                    ClickpayHelper::log("Return pre Order Failed  [$pOrderId]", 1);
                    $redirect_page = 'checkout/cart';
                    ClickpayHelper::log("Return pre Order invoice start 11 -  [$redirect_page]", 1);
                    $resultRedirect->setPath($redirect_page);
                    return $resultRedirect;
                }
            } catch (LocalizedException $e) {
                echo "LocalizedException: " . $e->getMessage() . "\n";
                $mes = $e->getMessage();
                $redirect_page = 'checkout/cart';
                ClickpayHelper::log("Return pre Order invoice start 11 -  [$mes]", 1);
                $resultRedirect->setPath($redirect_page);
                return $resultRedirect;
            }
        }
    }
}
