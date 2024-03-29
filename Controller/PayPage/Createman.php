<?php

// declare(strict_types=1);

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
use Magento\Vault\Model\Ui\VaultConfigProvider;
use stdClass;

/**
 * Class Index
 */
class Createman extends Action
{
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

        $this->clickpay = new \ClickPay\PayPage\Gateway\Http\Client\Api;
        new ClickPayCore();
    }

    /**
     * @return ResponseInterface|ResultInterface|Page
     */
    public function execute()
    {
        $result = $this->jsonResultFactory->create();

        $quoteId = $this->getRequest()->getPostValue('quote', null);
        $isTokenise = (bool) $this->getRequest()->getPostValue('vault', null);
        $methodCode = $this->getRequest()->getPostValue('method', null);
        $token = $this->getRequest()->getPostValue('token', null);

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

        $paypage = $this->prepare($quote, $isTokenise, $methodCode, $isLoggedIn, $token);

        if ($paypage->success) {
            // Create paypage success
            $tran_ref = $paypage->tran_ref;
            ClickpayHelper::log("Create paypage success!, Quote [{$quoteId}] [{$tran_ref}]", 1);

            // Remove sensetive information
            $res = new stdClass();
            $res->success = true;
            if($paypage->is_completed){
                $res->payment_url = $paypage->return . "?tranRef=" . $paypage->tran_ref . "&cartId=" . $paypage->cart_id;
            }else{
                $res->payment_url = $paypage->payment_url . "?tranRef=" . $paypage->tran_ref . "&cartId=" . $paypage->cart_id;
            }
            
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


    function prepare($quote, $isTokenise, $methodCode, $isLoggedIn, $token)
    {
        $paymentMethod = $this->_confirmPaymentMethod($quote, $methodCode);

        if (!$paymentMethod) {
            $res = new stdClass();
            $res->result = "Quote [" . $quote->getId() . "] payment method is missing!";
            $res->success = false;

            return $res;
        }

        $ptApi = $this->clickpay->pt($paymentMethod);

        // $isTokenise = $payment->getAdditionalInformation(VaultConfigProvider::IS_ACTIVE_CODE);
        // $a = $payment->getAdditionalInformation('pt_registered_transaction');
        $values = $this->clickpay->prepare_manage_order($quote, $paymentMethod, $isTokenise, true, $isLoggedIn, $token);

        $res = $ptApi->create_pay_manage($values);

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