<?php

namespace ClickPay\PayPage\Controller\PayPage;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use ClickPay\PayPage\Gateway\Http\Client\Api;
use ClickPay\PayPage\Gateway\Http\ClickPayCore;
use ClickPay\PayPage\Gateway\Http\ClickpayHelper;
use ClickPay\PayPage\Gateway\Http\ClickPayHelpers;
use Magento\Vault\Model\Ui\VaultConfigProvider;
use stdClass;

class AppleRedirect extends Action
{

    use ClickPayHelpers;

    /**
     * @var PageFactory
     */
    private $pageFactory;
    private $jsonResultFactory;
    protected $orderRepository;
    protected $_orderFactory;
    protected $quoteRepository;

    protected $checkoutSession;
    protected $_customerSession;


    private $clickpay;

    /**
     * @var \Magento\Quote\Model\QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;

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
        PageFactory $pageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory
        // \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct($context);

        $this->_orderFactory = $orderFactory;
        $this->pageFactory = $pageFactory;
        $this->jsonResultFactory = $jsonResultFactory;
        $this->orderRepository = $orderRepository;
        $this->quoteRepository = $quoteRepository;
        // $this->_logger = $logger;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;

        $this->checkoutSession = $checkoutSession;
        $this->_customerSession = $customerSession;

        $this->ClickPay = new \ClickPay\PayPage\Gateway\Http\Client\Api;
        new ClickPayCore();
    }

    /**
     * @return ResponseInterface|ResultInterface|Page
     */
    public function execute()
    {
        ClickpayHelper::log("ApplePay - Redirect", 3);
        $result = $this->jsonResultFactory->create();

        $quoteId = $this->getRequest()->getPostValue('quote', null);
        $isTokenise = (bool) $this->getRequest()->getPostValue('vault', null);
        $methodCode = $this->getRequest()->getPostValue('method', null);
        $token = $this->getRequest()->getPostValue('token', null);

        ClickpayHelper::log("ApplePay - Redirect quoteId - [$quoteId]", 3);
        ClickpayHelper::log("ApplePay - Redirect methodCode - [$methodCode]", 3);

        if (!$quoteId) {
            ClickpayHelper::log("Clickpay: Quote ID is missing!", 3);
            $result->setData([
                'result' => 'Quote ID is missing!'
            ]);
            return $result;
        }

        try {
            $isLoggedIn = $this->_customerSession->isLoggedIn();
            if (!$isLoggedIn) {
                $quoteIdMask = $this->quoteIdMaskFactory->create()->load($quoteId, 'masked_id');
                $quote = $this->quoteRepository->getActive($quoteIdMask->getQuoteId());
            } else {
                $quote = $this->quoteRepository->getActive($quoteId);
            }
        } catch (\Throwable $th) {
            $quote = null;
        }

        if (!$quote) {
            ClickpayHelper::log("Clickpay: Quote is missing!, Quote [{$quoteId}]", 3);
            $result->setData([
                'result' => 'Quote is missing!'
            ]);
            return $result;
        }

        $customeremail = $this->checkoutSession->getQuote()->getCustomerEmail();
        ClickPayHelper::log("QuoteData : ". json_encode($quote->getData()), 1);

        $paypage = $this->prepare($quote, $isTokenise, $methodCode, $isLoggedIn, $token, $customeremail);
        ClickPayHelper::log("ApplePay - Redirect applePayData ". json_encode($paypage), 1);

        if ($paypage->success) {
            // Create paypage success
            $tran_ref = $paypage->transaction_id;
            ClickpayHelper::log("Create paypage success!, Quote [{$quoteId}] [{$tran_ref}]", 1);

            // Remove sensetive information
            $res = new stdClass();
            $res->success = true;
            $res->payment_url = $paypage->return . "?tranRef=" . $tran_ref . "&cartId=" . $paypage->reference_no;
            $res->tran_ref = $tran_ref;

            $quote
                ->getPayment()
                ->setAdditionalInformation(
                    'pt_registered_transaction',
                    $tran_ref
                )
                ->save();
        } else {
            ClickpayHelper::log("Create paypage failed!, Order [{$quoteId}] - " . json_encode($paypage), 3);

            $res = $paypage;
        }

        $result->setData($res);

        return $result;
    }


    function prepare($quote, $isTokenise, $methodCode, $isLoggedIn, $token, $customeremail)
    {
        $paymentMethod = $this->_confirmPaymentMethod($quote, $methodCode);

        if (!$paymentMethod) {
            $res = new stdClass();
            $res->result = "Quote [" . $quote->getId() . "] payment method is missing!";
            $res->success = false;

            return $res;
        }

        $ptApi = $this->ClickPay->pt($paymentMethod);

        // $isTokenise = $payment->getAdditionalInformation(VaultConfigProvider::IS_ACTIVE_CODE);
        // $a = $payment->getAdditionalInformation('pt_registered_transaction');
        $values = $this->ClickPay->prepare_apple_order($quote, $paymentMethod, $isTokenise, true, $isLoggedIn, $token, $customeremail);

        $res = $ptApi->create_pay_apple($values);

        return $res;
    }


    private function _confirmPaymentMethod($quote, $method_code)
    {
        $paymentMethod = null;

        try {
            $payment = $quote->getPayment();
            $paymentMethod = $payment->getMethodInstance();
        } catch (\Throwable $th) {
            ClickpayHelper::log("Quote [{$quote->getId()}] payment method is missing!, ({$th->getMessage()})", 2);
            try {
                if (ClickpayHelper::isClickPayPayment($method_code)) {
                    $payment->setMethod($method_code);
                    $paymentMethod = $payment->getMethodInstance();
                }
            } catch (\Throwable $th) {
                ClickpayHelper::log("Quote [{$quote->getId()}] not able to set payment method!, ({$th->getMessage()})", 2);
            }
        }

        return $paymentMethod;
    }
}
