<?php

namespace ClickPay\PayPage\Helper;

class Config extends \Magento\Framework\App\Helper\AbstractHelper
{

    public const KEY_AUTOMATIC_INVOICE_ALL        = 'payment/all/automatic_invoice';
    public const KEY_AUTOMATIC_INVOICE_CREDITCARD = 'payment/creditcard/automatic_invoice';
    public const KEY_AUTOMATIC_INVOICE_APPLEPAY   = 'payment/applepay/automatic_invoice';
    public const KEY_AUTOMATIC_INVOICE_MADA       = 'payment/mada/automatic_invoice';
    public const KEY_AUTOMATIC_INVOICE_AMEX       = 'payment/amex/automatic_invoice';

    public const KEY_EMAIL_CUSTOMER_ALL           = 'payment/all/email_customer';
    public const KEY_EMAIL_CUSTOMER_CREDITCARD    = 'payment/creditcard/email_customer';
    public const KEY_EMAIL_CUSTOMER_APPLEPAY      = 'payment/applepay/email_customer';
    public const KEY_EMAIL_CUSTOMER_MADA          = 'payment/mada/email_customer';
    public const KEY_EMAIL_CUSTOMER_AMEX          = 'payment/amex/email_customer';

    public const KEY_ACTIVE_ALL           = 'payment/all/active';
    public const KEY_ACTIVE_CREDITCARD    = 'payment/creditcard/active';
    public const KEY_ACTIVE_APPLEPAY      = 'payment/applepay/active';
    public const KEY_ACTIVE_MADA          = 'payment/mada/active';
    public const KEY_ACTIVE_AMEX          = 'payment/amex/active';


    public function isActiveAll(
        $scopeType = \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
        $scopeCode = null
    ): bool {
        return (bool) $this->scopeConfig
            ->getValue(self::KEY_ACTIVE_ALL, $scopeType, $scopeCode);
    }

    public function isActiveCreditCard(
        $scopeType = \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
        $scopeCode = null
    ): bool {
        return (bool) $this->scopeConfig
            ->getValue(self::KEY_ACTIVE_CREDITCARD, $scopeType, $scopeCode);
    }

    public function isActiveApplePay(
        $scopeType = \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
        $scopeCode = null
    ): bool {
        return (bool) $this->scopeConfig
            ->getValue(self::KEY_ACTIVE_APPLEPAY, $scopeType, $scopeCode);
    }
    
    public function isActiveMada(
        $scopeType = \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
        $scopeCode = null
    ): bool {
        return (bool) $this->scopeConfig
            ->getValue(self::KEY_ACTIVE_MADA, $scopeType, $scopeCode);
    }

    public function isActiveAmexCard(
        $scopeType = \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
        $scopeCode = null
    ): bool {
        return (bool) $this->scopeConfig
            ->getValue(self::KEY_ACTIVE_AMEX, $scopeType, $scopeCode);
    }


    public function isEmailCustomerAll(
        $scopeType = \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
        $scopeCode = null
    ): bool {
        return (bool) $this->scopeConfig
            ->getValue(self::KEY_EMAIL_CUSTOMER_ALL, $scopeType, $scopeCode);
    }

    public function isEmailCustomerCreditCard(
        $scopeType = \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
        $scopeCode = null
    ): bool {
        return (bool) $this->scopeConfig
            ->getValue(self::KEY_EMAIL_CUSTOMER_CREDITCARD, $scopeType, $scopeCode);
    }

    public function isEmailCustomerApplePay(
        $scopeType = \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
        $scopeCode = null
    ): bool {
        return (bool) $this->scopeConfig
            ->getValue(self::KEY_EMAIL_CUSTOMER_APPLEPAY, $scopeType, $scopeCode);
    }

    public function isEmailCustomerMada(
        $scopeType = \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
        $scopeCode = null
    ): bool {
        return (bool) $this->scopeConfig
            ->getValue(self::KEY_EMAIL_CUSTOMER_MADA, $scopeType, $scopeCode);
    }

    public function isEmailCustomerAmex(
        $scopeType = \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
        $scopeCode = null
    ): bool {
        return (bool) $this->scopeConfig
            ->getValue(self::KEY_EMAIL_CUSTOMER_AMEX, $scopeType, $scopeCode);
    }

    public function isAutomaticInvoiceAll(
        $scopeType = \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
        $scopeCode = null
    ): bool {
        return (bool) $this->scopeConfig
            ->getValue(self::KEY_AUTOMATIC_INVOICE_ALL, $scopeType, $scopeCode);
    }

    public function isAutomaticInvoiceCreditCard(
        $scopeType = \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
        $scopeCode = null
    ): bool {
        return (bool) $this->scopeConfig
            ->getValue(self::KEY_AUTOMATIC_INVOICE_CREDITCARD, $scopeType, $scopeCode);
    }

    public function isAutomaticInvoiceApplePay(
        $scopeType = \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
        $scopeCode = null
    ): bool {
        return (bool) $this->scopeConfig
            ->getValue(self::KEY_AUTOMATIC_INVOICE_APPLEPAY, $scopeType, $scopeCode);
    }

    public function isAutomaticInvoiceMada(
        $scopeType = \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
        $scopeCode = null
    ): bool {
        return (bool) $this->scopeConfig
            ->getValue(self::KEY_AUTOMATIC_INVOICE_MADA, $scopeType, $scopeCode);
    }

    public function isAutomaticInvoiceAmex(
        $scopeType = \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
        $scopeCode = null
    ): bool {
        return (bool) $this->scopeConfig
            ->getValue(self::KEY_AUTOMATIC_INVOICE_AMEX, $scopeType, $scopeCode);
    }
}
