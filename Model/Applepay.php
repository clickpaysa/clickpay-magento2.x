<?php

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace ClickPay\PayPage\Model;



/**
 * Pay In Store payment method model
 */
class Applepay extends \Magento\Payment\Model\Method\Adapter
{

    /**
     * Payment code
     *
     * @var string
     */
    protected $_code = 'applepay';

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_isOffline = false;

    public function isInitializeNeeded()
    {
        $preorder = (bool) $this->getConfigData('payment_preorder');
        if ($preorder) {
            return false;
        }

        return parent::isInitializeNeeded();
    }
}