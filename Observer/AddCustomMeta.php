<?php
namespace ClickPay\PayPage\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\View\Page\Config as PageConfig;

class AddCustomMeta implements ObserverInterface
{
    protected $pageConfig;

    public function __construct(PageConfig $pageConfig)
    {
        $this->pageConfig = $pageConfig;
    }

    public function execute(Observer $observer)
    {
        // Directly manipulating meta tags for CSP might not be effective in Magento due to its CSP framework.
        // This is an example of adding a generic meta tag.
        $this->pageConfig->addRemotePageAsset(
            "http-equiv://Content-Security-Policy",
            'meta',
            ['attributes' => [
                'http-equiv' => 'Content-Security-Policy',
                'content' => "default-src 'self' data: gap: https://secure.clickpay.com.sa 'unsafe-eval'; style-src 'self' 'unsafe-inline'; script-src 'self' https://secure.clickpay.com.sa/ 'unsafe-inline' 'unsafe-eval'; media-src *"
            ]]
        );
    }
}