<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace ClickPay\PayPage\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use ClickPay\PayPage\Gateway\Http\ClickPayCore;
use ClickPay\PayPage\Gateway\Http\ClickPayHelper;
use ClickPay\PayPage\Model\Adminhtml\Source\CurrencySelect;
use Magento\Store\Model\StoreManagerInterface;


class PaymentMethodAvailable implements ObserverInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * YourClass constructor.
     *
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * payment_method_is_active event handler.
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(Observer $observer)
    {
        $paymentMethod = $observer->getEvent()->getMethodInstance();
        $code = $paymentMethod->getCode();

        new ClickPayCore();
        $isClickPay = ClickPayHelper::isClickPayPayment($code);

        if ($isClickPay) {
            $checkResult = $observer->getEvent()->getResult();

            if ($checkResult->getData('is_available')) {
                $use_order_currency = CurrencySelect::IsOrderCurrency($paymentMethod);
                $currency = $this->getCurrency($use_order_currency);
                // Check if the payment method is Apple Pay
                if ($code === 'applepay') {
                    $userAgent = $_SERVER['HTTP_USER_AGENT'];
                    // Check if the user agent indicates an Apple device
                    if ((strpos($userAgent, 'iPhone') !== false || strpos($userAgent, 'iPad') !== false || (strpos($userAgent, 'Macintosh') !== false && strpos($userAgent, 'Intel Mac OS X') !== false)) && strpos($userAgent, 'Safari') !== false && strpos($userAgent, 'Chrome') === false) {
                        // User is on an Apple device, allow the Apple Pay payment method
                        $isAllowed = ClickPayHelper::paymentAllowed($code, $currency);
                        $checkResult->setData('is_available', $isAllowed);
                    } else {
                        // User is not on an Apple device, disallow the Apple Pay payment method
                        $checkResult = $observer->getEvent()->getResult();
                        $checkResult->setData('is_available', false);
                    }
                } else {
                    $isAllowed = ClickPayHelper::paymentAllowed($code, $currency);
                    $checkResult->setData('is_available', $isAllowed);
                }
            }
        }
    }

    private function getCurrency($use_order_currency)
    {
        if ($use_order_currency) {
            $currencyCode = $this->storeManager->getStore()->getCurrentCurrency()->getCode();
        } else {
            $currencyCode = $this->storeManager->getStore()->getBaseCurrency()->getCode();
        }

        return $currencyCode;
    }
}
