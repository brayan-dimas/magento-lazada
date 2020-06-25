<?php

namespace Lazada\Customer\Controller\Adminhtml\Dataimport;

use Magento\Framework\Controller\ResultFactory;

class Importdata extends \Magento\Backend\App\Action
{
    private $coreRegistry;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry
    ) {                                 
       parent::__construct($context);
       $this->coreRegistry = $coreRegistry;
    }

    public function execute()
    {
        $rowData = $this->_objectManager->create('\Magento\Customer\Model\ResourceModel\Grid\Collection');
        $this->coreRegistry->register('row_data', $rowData);
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend(__('Import Customer'));
        return $resultPage;
    }

    // used for acl.xml
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('lazada_customer::customer_dataimport_importdata');
    }
}