<?php

namespace ClickPay\PayPage\Controller\PayPage;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use ClickPay\PayPage\Gateway\Http\ClickPayCore;
use ClickPay\PayPage\Gateway\Http\ClickPayHelper;
use ClickPay\PayPage\Gateway\Http\ClickPayHelpers;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Filesystem\DirectoryList;

class ApplePay extends Action
{

    use ClickPayHelpers;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    protected $storeManager;

    protected $scopeConfig;

    protected $directoryList;

    /**
     * Constructor
     *
     * @param Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        DirectoryList $directoryList
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->directoryList = $directoryList;
        $this->ClickPay = new \ClickPay\PayPage\Gateway\Http\Client\Api;
        new ClickPayCore();
    }

    /**
     * Execute action based on request and return result
     *
     * Note: Request will be redirected if validation fails or forwarded if validation is successful
     *
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        ClickpayHelper::log("ApplePay - Token Start", 1);

        $validationUrl = $this->getRequest()->getParam('vurl');
        ClickpayHelper::log("ApplePay - Token VURL [$validationUrl]", 1);

        if (!$validationUrl) {
            return $this->resultJsonFactory->create()->setData(['error' => 'No vURL']);
        }

        $baseUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
        $baseUrl = parse_url($baseUrl, PHP_URL_HOST);
        $merchantId = $this->scopeConfig->getValue('payment/applepay/merchant_id');

        $applePayData = [
            'merchantIdentifier' => $merchantId,
            'displayName' => "PT Integrations Team",
            'initiative' => "web",
            'initiativeContext' => $baseUrl,
           
        ];

        $result = $this->sendRequest($validationUrl, $applePayData);

        // return $this->resultJsonFactory->create()->setData(['result' => $result]);

        echo  $result;
    }

    /**
     * Send request to Apple Pay server for merchant validation
     *
     * @param string $requestUrl
     * @param array $values
     * @return string
     */
    protected function sendRequest($requestUrl, $values)
    {
        $sslKeyPath = $this->scopeConfig->getValue('payment/applepay/merchant_key');
        $sslCertPath = $this->scopeConfig->getValue('payment/applepay/merchant_certificate');

        $magentoRoot = $this->directoryList->getRoot();
        $sslKeyPath = $magentoRoot . '/pub/media/applepay/' . $sslKeyPath;
        $sslCertPath = $magentoRoot . '/pub/media/applepay/' . $sslCertPath;

        $headers = [
            'Content-Type: application/json',
        ];

        $postParams = json_encode($values);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $requestUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postParams);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        // Specify the path to your SSL certificates

        curl_setopt($ch, CURLOPT_SSLKEY, $sslKeyPath);
        curl_setopt($ch, CURLOPT_SSLCERT, $sslCertPath);

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            $result = ['error' => curl_error($ch)];
        }

        curl_close($ch);

        return $result;
    }
}
