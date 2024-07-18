<?php

namespace ClickPay\PayPage\Gateway\Http\Client;

use ClickPay\PayPage\Gateway\Http\ClickPayApi;
use ClickPay\PayPage\Gateway\Http\ClickPayCore;
use ClickPay\PayPage\Gateway\Http\ClickPayEnum;
use ClickPay\PayPage\Gateway\Http\ClickPayRequestHolder;
use ClickPay\PayPage\Gateway\Http\ClickPayManagedFormHolder;
use ClickPay\PayPage\Gateway\Http\ClickPayApplePayHolder;
use ClickPay\PayPage\Model\Adminhtml\Source\CurrencySelect;

class Api
{
    public function pt($paymentMethod)
    {
        // $paymentType = $paymentMethod->getCode();

        // PT
        $merchant_id = $paymentMethod->getConfigData('profile_id');
        $merchant_key = $paymentMethod->getConfigData('server_key');
        $endpoint = $paymentMethod->getConfigData('endpoint');

        new ClickPayCore();
        $pt = ClickPayApi::getInstance($endpoint, $merchant_id, $merchant_key);

        return $pt;
    }

    /**
     * Extract required parameters from the Order, to Pass to create_page API
     * -Client information
     * -Shipping address
     * -Products
     * @return Array of values to pass to create_paypage API
     */
    public function prepare_order($order, $paymentMethod, $isTokenise, $preApprove, $isLoggedIn)
    {
        /** 1. Read required Params */

        $paymentType = $paymentMethod->getCode(); //'creditcard';

        $hide_shipping = (bool) $paymentMethod->getConfigData('hide_shipping');
        $framed_mode = (bool) $paymentMethod->getConfigData('iframe_mode');
        $payment_action = $paymentMethod->getConfigData('payment_action');
        $exclude_shipping = (bool) $paymentMethod->getConfigData('exclude_shipping');

        $cart_refill = (bool) $paymentMethod->getConfigData('order_failed_reorder');

        $use_order_currency = CurrencySelect::IsOrderCurrency($paymentMethod);

        $allow_associated_methods = (bool) $paymentMethod->getConfigData('allow_associated_methods');


        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $localeResolver = $objectManager->get('\Magento\Framework\Locale\ResolverInterface');
        $productMetadata = $objectManager->get('Magento\Framework\App\ProductMetadataInterface');
        $versionMagento = $productMetadata->getVersion();

        if ($use_order_currency) {
            if ($preApprove) {
                $currency = $order->getQuoteCurrencyCode();
                $shippingAmount = $order->getShippingAddress()->getShippingAmount();
            } else {
                $currency = $order->getOrderCurrencyCode();
                $shippingAmount = $order->getShippingAddress()->getShippingAmount();
            }
            $amount = $order->getGrandTotal();
        } else {
            $currency = $order->getBaseCurrencyCode();
            $amount = $order->getBaseGrandTotal();

            if ($preApprove) {
                $shippingAmount = $order->getShippingAddress()->getBaseShippingAmount();
            } else {
                $shippingAmount = $order->getBaseShippingAmount();
            }
        }

        if ($exclude_shipping) {
            $amount -= $shippingAmount;
        }

        $baseurl = $storeManager->getStore()->getBaseUrl();
        if ($preApprove) {
            $orderId = 'Q' . $order->getId();

            $returnUrl = $baseurl . "clickpay/paypage/responsepre";
            $callbackUrl = $baseurl . "clickpay/paypage/responsepre"; // Disable IPN
        } else {
            $orderId = $order->getIncrementId();

            $returnUrl = $baseurl . "clickpay/paypage/response" . ($isLoggedIn ? "" : ($cart_refill ? "?g=1" : ""));
            $callbackUrl = $baseurl . "clickpay/paypage/responsepree";
        }

        $lang_code = $localeResolver->getLocale();
        $lang = ($lang_code == 'ar' || substr($lang_code, 0, 3) == 'ar_') ? 'ar' : 'en';

        // Compute Prices

        $amount = number_format((float) $amount, 3, '.', '');
        // $amount = $order->getPayment()->formatAmount($amount, true);

        // $discountAmount = abs($order->getDiscountAmount());
        // $shippingAmount = $order->getShippingAmount();
        // $taxAmount = $order->getTaxAmount();

        // $amount += $discountAmount;
        // $otherCharges = $shippingAmount + $taxAmount;


        /** 1.2. Read BillingAddress info */

        $billingAddress = $order->getBillingAddress();
        // $firstName = $billingAddress->getFirstname();
        // $lastName = $billingAddress->getlastname();

        // $email = $billingAddress->getEmail();
        // $city = $billingAddress->getCity();

        $postcode = trim($billingAddress->getPostcode());

        // $region = $billingAddress->getRegionCode();
        $country_iso2 = $billingAddress->getCountryId();

        // $telephone = $billingAddress->getTelephone();
        $streets = $billingAddress->getStreet();
        $billing_address = implode(', ', $streets);

        $hasShipping = false;
        $shippingAddress = $order->getShippingAddress();
        if ($shippingAddress) {
            $hasShipping = true;
            // $s_firstName = $shippingAddress->getFirstname();
            // $s_lastName = $shippingAddress->getlastname();
            // $s_city = $shippingAddress->getCity();

            $s_postcode = trim($shippingAddress->getPostcode());

            // $s_region = $shippingAddress->getRegionCode();
            $s_country_iso2 = $shippingAddress->getCountryId();

            $s_streets = $shippingAddress->getStreet();
            $shipping_address = implode(', ', $s_streets);
        }

        /** 1.3. Read Products */

        // $items = $order->getAllItems();
        // $items = $order->getItems();
        $items = $order->getAllVisibleItems();

        $items_arr = array_map(function ($p) {
            $q = (int)$p->getQtyOrdered();
            return "{$p->getName()} ({$q})";
        }, $items);

        $cart_desc = implode(', ', $items_arr);


        // System Parameters
        // $systemVersion = "Magento {$versionMagento}";


        $tran_type = ClickPayEnum::TRAN_TYPE_SALE;
        switch ($payment_action) {
            case 'authorize':
                $tran_type = ClickPayEnum::TRAN_TYPE_AUTH;
                break;

            case 'authorize_capture':
                $tran_type = ClickPayEnum::TRAN_TYPE_SALE;
                break;
        }

        /** 2. Fill post array */

        $pt_holder = new ClickPayRequestHolder();
        $pt_holder
            ->set01PaymentCode($paymentType, $allow_associated_methods, $currency)
            ->set02Transaction($tran_type, ClickPayEnum::TRAN_CLASS_ECOM)
            ->set03Cart($orderId, $currency, $amount, $cart_desc)
            ->set04CustomerDetails(
                $billingAddress->getName(),
                $billingAddress->getEmail(),
                $billingAddress->getTelephone(),
                $billing_address,
                $billingAddress->getCity(),
                $billingAddress->getRegionCode(),
                $country_iso2,
                $postcode,
                null
            );

        if ($hasShipping) {
            $pt_holder->set05ShippingDetails(
                false,
                $shippingAddress->getName(),
                $shippingAddress->getEmail(),
                $shippingAddress->getTelephone(),
                $shipping_address,
                $shippingAddress->getCity(),
                $shippingAddress->getRegionCode(),
                $s_country_iso2,
                $s_postcode,
                null
            );
        } else if (!$hide_shipping) {
            $pt_holder->set05ShippingDetails(true);
        }

        $pt_holder
            ->set06HideShipping($hide_shipping)
            ->set07URLs($returnUrl, $callbackUrl)
            ->set08Lang($lang)
            ->set09Framed($framed_mode || $preApprove, $preApprove ? 'iframe' : 'top')
            ->set10Tokenise($isTokenise)
            ->set99PluginInfo('Magento', $versionMagento, ClickPay_PAYPAGE_VERSION);


        if ($exclude_shipping) {
            $pt_holder->set50UserDefined('exclude_shipping=1');
        }
        $post_arr = $pt_holder->pt_build();

        //

        return $post_arr;
    }

    /**
     * Extract required parameters from the Order, to Pass to create_page API
     * -Client information
     * -Shipping address
     * -Products
     * @return Array of values to pass to create_paypage API
     */
    public function prepare_manage_order($order, $paymentMethod, $isTokenise, $preApprove, $isLoggedIn, $token)
    {
        /** 1. Read required Params */

        $paymentType = $paymentMethod->getCode(); //'creditcard';

        $hide_shipping = (bool) $paymentMethod->getConfigData('hide_shipping');
        $framed_mode = (bool) $paymentMethod->getConfigData('iframe_mode');
        $payment_action = $paymentMethod->getConfigData('payment_action');
        $exclude_shipping = (bool) $paymentMethod->getConfigData('exclude_shipping');

        $cart_refill = (bool) $paymentMethod->getConfigData('order_failed_reorder');

        $use_order_currency = CurrencySelect::IsOrderCurrency($paymentMethod);

        $allow_associated_methods = (bool) $paymentMethod->getConfigData('allow_associated_methods');


        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $localeResolver = $objectManager->get('\Magento\Framework\Locale\ResolverInterface');
        $productMetadata = $objectManager->get('Magento\Framework\App\ProductMetadataInterface');
        $cookieManager = $objectManager->get('Magento\Framework\Stdlib\CookieManagerInterface');
        $versionMagento = $productMetadata->getVersion();

        $sequenceManager = $objectManager->get('\Magento\SalesSequence\Model\Manager');
        $quoteRepository = $objectManager->get('\Magento\Quote\Api\CartRepositoryInterface');
        $orderCollection = $objectManager->get('\Magento\Sales\Model\ResourceModel\Order\Collection');

        $reservedOrderId = $this->getReservedOrderId($sequenceManager, $orderCollection, $order->getStoreId());
        $order->setReservedOrderId($reservedOrderId);
        $quoteRepository->save($order);

        if ($use_order_currency) {
            if ($preApprove) {
                $currency = $order->getQuoteCurrencyCode();
                $shippingAmount = $order->getShippingAddress()->getShippingAmount();
            } else {
                $currency = $order->getOrderCurrencyCode();
                $shippingAmount = $order->getShippingAddress()->getShippingAmount();
            }
            $amount = $order->getGrandTotal();
        } else {
            $currency = $order->getBaseCurrencyCode();
            $amount = $order->getBaseGrandTotal();

            if ($preApprove) {
                $shippingAmount = $order->getShippingAddress()->getBaseShippingAmount();
            } else {
                $shippingAmount = $order->getBaseShippingAmount();
            }
        }

        if ($exclude_shipping) {
            $amount -= $shippingAmount;
        }

        $baseurl = $storeManager->getStore()->getBaseUrl();
        $orderId = $order->getId();
        $returnUrl = $baseurl . "clickpay/paypage/responsepre";
        $callbackUrl = $baseurl . "clickpay/paypage/responsepree"; // Disable IPN

        $lang_code = $localeResolver->getLocale();
        $lang = ($lang_code == 'ar' || substr($lang_code, 0, 3) == 'ar_') ? 'ar' : 'en';

        // Compute Prices

        $amount = number_format((float) $amount, 3, '.', '');
        // $amount = $order->getPayment()->formatAmount($amount, true);

        // $discountAmount = abs($order->getDiscountAmount());
        // $shippingAmount = $order->getShippingAmount();
        // $taxAmount = $order->getTaxAmount();

        // $amount += $discountAmount;
        // $otherCharges = $shippingAmount + $taxAmount;


        /** 1.2. Read BillingAddress info */

        $billingAddress = $order->getBillingAddress();
        // $firstName = $billingAddress->getFirstname();
        // $lastName = $billingAddress->getlastname();

        // $email = $billingAddress->getEmail();
        // $city = $billingAddress->getCity();

        $postcode = trim($billingAddress->getPostcode());

        // $region = $billingAddress->getRegionCode();
        $country_iso2 = $billingAddress->getCountryId();

        // $telephone = $billingAddress->getTelephone();
        $streets = $billingAddress->getStreet();
        $billing_address = implode(', ', $streets);

        $hasShipping = false;
        $shippingAddress = $order->getShippingAddress();
        if ($shippingAddress) {
            $hasShipping = true;
            // $s_firstName = $shippingAddress->getFirstname();
            // $s_lastName = $shippingAddress->getlastname();
            // $s_city = $shippingAddress->getCity();

            $s_postcode = trim($shippingAddress->getPostcode());

            // $s_region = $shippingAddress->getRegionCode();
            $s_country_iso2 = $shippingAddress->getCountryId();

            $s_streets = $shippingAddress->getStreet();
            $shipping_address = implode(', ', $s_streets);
        }

        /** 1.3. Read Products */

        // $items = $order->getAllItems();
        // $items = $order->getItems();
        $items = $order->getAllVisibleItems();

        $items_arr = array_map(function ($p) {
            $q = (int)$p->getQtyOrdered();
            return "{$p->getName()} ({$q})";
        }, $items);

        $cart_desc = implode(', ', $items_arr);


        // System Parameters
        // $systemVersion = "Magento {$versionMagento}";


        $tran_type = ClickPayEnum::TRAN_TYPE_SALE;
        switch ($payment_action) {
            case 'authorize':
                $tran_type = ClickPayEnum::TRAN_TYPE_AUTH;
                break;

            case 'authorize_capture':
                $tran_type = ClickPayEnum::TRAN_TYPE_SALE;
                break;
        }

        $customeremail = $cookieManager->getCookie('customer_em');
        $email = ($billingAddress->getEmail() !== null) ? $billingAddress->getEmail() : $customeremail;
        $order->getBillingAddress()->setEmail($email);
        $order->setCustomerEmail($email);
        $quoteRepository = $objectManager->create('Magento\Quote\Api\CartRepositoryInterface');
        $quoteRepository->save($order);



        /** 2. Fill post array */

        $pt_holder = new ClickPayManagedFormHolder();
        $pt_holder
            ->set30PaymentToken($token)
            ->set02Transaction($tran_type, ClickPayEnum::TRAN_CLASS_ECOM)
            ->set03Cart($reservedOrderId, $currency, $amount, $cart_desc)
            ->set04CustomerDetails(
                $billingAddress->getName(),
                $email,
                $billingAddress->getTelephone(),
                $billing_address,
                $billingAddress->getCity(),
                $billingAddress->getRegionCode(),
                $country_iso2,
                $postcode,
                null
            );

        $pt_holder
            ->set07URLs($returnUrl, $callbackUrl)
            ->set08Lang($lang)
            ->set99PluginInfo('Magento', $versionMagento, ClickPay_PAYPAGE_VERSION);


        if ($exclude_shipping) {
            $pt_holder->set50UserDefined('exclude_shipping=1');
        }
        $post_arr = $pt_holder->pt_build();

        //

        return $post_arr;
    }

    public function prepare_apple_order($order, $paymentMethod, $isTokenise, $preApprove, $isLoggedIn, $token, $customeremail)
    {
        /** 1. Read required Params */

        $paymentType = $paymentMethod->getCode(); //'creditcard';

        $hide_shipping = (bool) $paymentMethod->getConfigData('hide_shipping');
        $framed_mode = (bool) $paymentMethod->getConfigData('iframe_mode');
        $payment_action = $paymentMethod->getConfigData('payment_action');
        $exclude_shipping = (bool) $paymentMethod->getConfigData('exclude_shipping');

        $cart_refill = (bool) $paymentMethod->getConfigData('order_failed_reorder');

        $use_order_currency = CurrencySelect::IsOrderCurrency($paymentMethod);

        $allow_associated_methods = (bool) $paymentMethod->getConfigData('allow_associated_methods');


        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $cookieManager = $objectManager->get('Magento\Framework\Stdlib\CookieManagerInterface');
        $localeResolver = $objectManager->get('\Magento\Framework\Locale\ResolverInterface');
        $productMetadata = $objectManager->get('Magento\Framework\App\ProductMetadataInterface');
        $versionMagento = $productMetadata->getVersion();

        $sequenceManager = $objectManager->get('\Magento\SalesSequence\Model\Manager');
        $quoteRepository = $objectManager->get('\Magento\Quote\Api\CartRepositoryInterface');
        $orderCollection = $objectManager->get('\Magento\Sales\Model\ResourceModel\Order\Collection');

        $reservedOrderId = $this->getReservedOrderId($sequenceManager, $orderCollection, $order->getStoreId());
        $order->setReservedOrderId($reservedOrderId);
        $quoteRepository->save($order);

        if ($use_order_currency) {
            if ($preApprove) {
                $currency = $order->getQuoteCurrencyCode();
                $shippingAmount = $order->getShippingAddress()->getShippingAmount();
            } else {
                $currency = $order->getOrderCurrencyCode();
                $shippingAmount = $order->getShippingAddress()->getShippingAmount();
            }
            $amount = $order->getGrandTotal();
        } else {
            $currency = $order->getBaseCurrencyCode();
            $amount = $order->getBaseGrandTotal();

            if ($preApprove) {
                $shippingAmount = $order->getShippingAddress()->getBaseShippingAmount();
            } else {
                $shippingAmount = $order->getBaseShippingAmount();
            }
        }

        if ($exclude_shipping) {
            $amount -= $shippingAmount;
        }

        $baseurl = $storeManager->getStore()->getBaseUrl();
        $orderId = $order->getId();
        $returnUrl = $baseurl . "clickpay/paypage/responsepre";
        $callbackUrl = $baseurl . "clickpay/paypage/responsepree"; // Disable IPN

        $lang_code = $localeResolver->getLocale();
        $lang = ($lang_code == 'ar' || substr($lang_code, 0, 3) == 'ar_') ? 'ar' : 'en';

        // Compute Prices

        $amount = number_format((float) $amount, 3, '.', '');
        // $amount = $order->getPayment()->formatAmount($amount, true);

        // $discountAmount = abs($order->getDiscountAmount());
        // $shippingAmount = $order->getShippingAmount();
        // $taxAmount = $order->getTaxAmount();

        // $amount += $discountAmount;
        // $otherCharges = $shippingAmount + $taxAmount;


        /** 1.2. Read BillingAddress info */

        $billingAddress = $order->getBillingAddress();
        // $firstName = $billingAddress->getFirstname();
        // $lastName = $billingAddress->getlastname();

        // $email = $billingAddress->getEmail();
        // $city = $billingAddress->getCity();

        $postcode = trim($billingAddress->getPostcode());

        // $region = $billingAddress->getRegionCode();
        $country_iso2 = $billingAddress->getCountryId();

        // $telephone = $billingAddress->getTelephone();
        $streets = $billingAddress->getStreet();
        $billing_address = implode(', ', $streets);

        $hasShipping = false;
        $shippingAddress = $order->getShippingAddress();
        if ($shippingAddress) {
            $hasShipping = true;
            // $s_firstName = $shippingAddress->getFirstname();
            // $s_lastName = $shippingAddress->getlastname();
            // $s_city = $shippingAddress->getCity();

            $s_postcode = trim($shippingAddress->getPostcode());

            // $s_region = $shippingAddress->getRegionCode();
            $s_country_iso2 = $shippingAddress->getCountryId();

            $s_streets = $shippingAddress->getStreet();
            $shipping_address = implode(', ', $s_streets);
        }

        /** 1.3. Read Products */

        // $items = $order->getAllItems();
        // $items = $order->getItems();
        $items = $order->getAllVisibleItems();

        $items_arr = array_map(function ($p) {
            $q = (int)$p->getQtyOrdered();
            return "{$p->getName()} ({$q})";
        }, $items);

        $cart_desc = implode(', ', $items_arr);


        // System Parameters
        // $systemVersion = "Magento {$versionMagento}";


        $tran_type = ClickPayEnum::TRAN_TYPE_SALE;
        switch ($payment_action) {
            case 'authorize':
                $tran_type = ClickPayEnum::TRAN_TYPE_AUTH;
                break;

            case 'authorize_capture':
                $tran_type = ClickPayEnum::TRAN_TYPE_SALE;
                break;
        }

        $customeremail = $cookieManager->getCookie('customer_em');
        $email = ($billingAddress->getEmail() !== null) ? $billingAddress->getEmail() : $customeremail;
        $order->getBillingAddress()->setEmail($email);
        $order->setCustomerEmail($email);
        $quoteRepository = $objectManager->create('Magento\Quote\Api\CartRepositoryInterface');
        $quoteRepository->save($order);


        /** 2. Fill post array */

        $pt_holder = new ClickPayApplePayHolder();
        $pt_holder
            ->set01PaymentCode('applepay')
            ->set02Transaction($tran_type, ClickPayEnum::TRAN_CLASS_ECOM)
            ->set03Cart($reservedOrderId, $currency, $amount, $cart_desc)
            ->set04CustomerDetails(
                $billingAddress->getName(),
                $email,
                $billingAddress->getTelephone(),
                $billing_address,
                $billingAddress->getCity(),
                $billingAddress->getRegionCode(),
                $country_iso2,
                null,
                null
            );

        $pt_holder
            ->set07URLs($returnUrl, $callbackUrl)
            ->set08Lang($lang)
            ->set50ApplePay($token)
            ->set99PluginInfo('Magento', $versionMagento, ClickPay_PAYPAGE_VERSION);


        if ($exclude_shipping) {
            $pt_holder->set50UserDefined('exclude_shipping=1');
        }
        $post_arr = $pt_holder->pt_build();

        //

        return $post_arr;
    }

    private function getReservedOrderId($sequenceManager, $orderCollection, $storeId)
    {
        do {
            $reservedOrderId = $sequenceManager->getSequence('order', $storeId)->getNextValue();
        } while ($this->isIncrementIdUsed($orderCollection, $reservedOrderId));

        return $reservedOrderId;
    }

    private function isIncrementIdUsed($orderCollection, $incrementId)
    {
        $existingOrder = $orderCollection->addFieldToFilter('increment_id', $incrementId)->getFirstItem();
        return $existingOrder && $existingOrder->getId();
    }

    /**
     * check if the Order is paid and complete
     * sometimes and for some reason, create_paypage been called twice, after the User paid for the Order
     * @return true if the Order has been paid before, false otherwise
     */
    public static function hadPaid($order)
    {
        $lastTransId = $order->getPayment()->getLastTransId();
        $amountPaid = $order->getPayment()->getAmountPaid();
        $info = $order->getPayment()->getAdditionalInformation();

        $payment_amount = 0;
        if ($info && isset($info['payment_amount'])) {
            $payment_amount = $info['payment_amount'];
        }

        if ($lastTransId && floor($amountPaid) == floor($payment_amount)) {
            return true;
        }
        return false;
    }
}
