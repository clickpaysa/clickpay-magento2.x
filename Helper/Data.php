<?php
namespace ClickPay\PayPage\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    const XML_PATH_IFRAME_MODE = 'payment/all/iframe_mode';

    public function getIframeMode($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_IFRAME_MODE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
