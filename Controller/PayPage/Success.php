<?php

namespace ClickPay\PayPage\Controller\PayPage;

use Magento\Checkout\Controller\Onepage\Success as MagentoSuccess;

class Success extends MagentoSuccess
{
    public function execute()
    {
        $session = $this->getOnepage()->getCheckout();
        if (!$this->_objectManager->get(\Magento\Checkout\Model\Session\SuccessValidator::class)->isValid()) {

            $entityId = $this->getRequest()->getParam('entity_id');


            if ($entityId) {
                // Set session values using the entity_id parameter
                $incrementId = $this->getRequest()->getParam('increment_id');
                $quoteId = $this->getRequest()->getParam('quote_id');

                $session->setLastSuccessQuoteId($quoteId);
                $session->setLastQuoteId($quoteId);
                $session->setLastOrderId($entityId);
                $session->setLastRealOrderId($incrementId);
                $session->setCartWasUpdated(false);

                // Redirect to the success page or any other page
                // return $this->resultRedirectFactory->create()->setPath('checkout/onepage/success');
            } else {
                // Redirect to the cart page if entity_id parameter is not available
                return $this->resultRedirectFactory->create()->setPath('checkout/cart');
            }
        }
        $session->clearQuote();
        //@todo: Refactor it to match CQRS
        $resultPage = $this->resultPageFactory->create();
        $this->_eventManager->dispatch(
            'checkout_onepage_controller_success_action',
            [
                'order_ids' => [$session->getLastOrderId()],
                'order' => $session->getLastRealOrder()
            ]
        );
        return $resultPage;
    }
}
