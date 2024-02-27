<?php

    namespace ClickPay\PayPage\Controller\Index;

    use Magento\Framework\App\Action\Action;
    use Magento\Framework\App\Action\Context;
    use Magento\Framework\Controller\Result\JsonFactory;
    use ClickPay\PayPage\Helper\Data;

    class GetIframeMode extends Action
    {
        protected $resultJsonFactory;
        protected $helper;

        public function __construct(
            Context $context,
            JsonFactory $resultJsonFactory,
            Data $helper
        ) {
            $this->resultJsonFactory = $resultJsonFactory;
            $this->helper = $helper;
            parent::__construct($context);
        }

        public function execute()
        {
            $result = $this->resultJsonFactory->create();
            $iframeMode = $this->helper->getIframeMode();
            return $result->setData(['iframe_mode' => $iframeMode]);
        }
    }