<?php
namespace ClickPay\PayPage\Model\Config\Source;

class CustomDropdown implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => '0', 'label' => __('Redirect')],
            ['value' => '1', 'label' => __('Iframe')],
            ['value' => '2', 'label' => __('Managed form')]
        ];
    }
}

