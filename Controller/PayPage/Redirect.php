<?php
namespace ClickPay\PayPage\Controller\Custom;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class Redirect extends Action
{
    protected $resultFactory;

    public function __construct(Context $context, ResultFactory $resultFactory)
    {
        $this->resultFactory = $resultFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        if ($this->getRequest()->isPost()) {
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath('checkout/onepage/success');
            return $resultRedirect;
        }
    }
}