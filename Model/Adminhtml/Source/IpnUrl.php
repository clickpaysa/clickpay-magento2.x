<?php

namespace ClickPay\PayPage\Model\Adminhtml\Source;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Store\Model\StoreManagerInterface;

class IpnUrl extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * IpnUrl constructor.
     *
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->storeManager = $storeManager;
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $baseurl = $this->storeManager->getStore()->getBaseUrl();
        $ipnUrl = $baseurl . "ClickPay/paypage/ipn";

        return $ipnUrl;
    }
}
