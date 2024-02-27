<?php
namespace ClickPay\PayPage\Observer;

use Magento\Framework\Event\ObserverInterface;

class OrderSuccess implements ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        // Access order data and perform actions as needed

        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/custom.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info('Your text message');
        $logger->info(json_encode($order->getData())); //to log the array


    }
}