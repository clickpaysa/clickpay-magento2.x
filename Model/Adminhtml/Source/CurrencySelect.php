<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace ClickPay\PayPage\Model\Adminhtml\Source;
use Magento\Directory\Model\CurrencyFactory;
use Magento\Sales\Api\Data\OrderPaymentInterface;


/**
 * Class EmailCOnfig
 */
class CurrencySelect implements \Magento\Framework\Option\ArrayInterface
{
    // Email send Options

    const CURRENCY_BASE  = 'base_currency';
    const CURRENCY_ORDER = 'order_currency';

    protected $currencyFactory;

    public function __construct(
        CurrencyFactory $currencyFactory
    ) {
        $this->currencyFactory = $currencyFactory;
    }


    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => $this::CURRENCY_BASE,
                'label' => 'Base Currency (recommended)'
            ],
            [
                'value' => $this::CURRENCY_ORDER,
                'label' => 'Order Currency'
            ]
        ];
    }

    //

    /**
     * @param $payment
     * @return bool
     */
    static function UseOrderCurrency($payment)
    {
        $paymentMethod = $payment->getMethodInstance();
        $order = $payment->getOrder();

        if ($order->getOrderCurrencyCode() == $order->getBaseCurrencyCode()) {
            return false;
        }

        return CurrencySelect::IsOrderCurrency($paymentMethod);
    }

    /**
     * @param $paymentMethod
     * @return bool
     */
    static function IsOrderCurrency($paymentMethod)
    {
        $currency_used = $paymentMethod->getConfigData('currency_select');
        return $currency_used == CurrencySelect::CURRENCY_ORDER;
    }


    static function convertOrderToBase($payment, $tranAmount)
    {
        $order = $payment->getOrder();
        $baseAmount = $order->getBaseGrandTotal();

        $rate = $this->currencyFactory->create()
            ->load($order->getOrderCurrencyCode())
            ->getAnyRate($order->getBaseCurrencyCode());

        $amount = $tranAmount * $rate;

        // $amount = $payment->formatAmount($amount, true);
        $amount = number_format((float) $amount, 3, '.', '');

        if (abs($baseAmount - $amount) < 0.1) {
            $amount = $baseAmount;
        }

        return $amount;
    }
}