<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Lazada\Customer\Controller\Adminhtml\Dataimport;

use Magento\Customer\Controller\RegistryConstants;

class Result extends \Magento\Backend\App\Action
{
    /**
     * Initialize current group and set it in the registry.
     *
     * @return int
     */
   

    /**
     * Edit or create customer group.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected $customerGroupFactory;
    protected $resourceConnection;
    protected $userSession;
    protected $messageManager;
    protected $addcustomerImport;

    
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Unilab\Grid\Model\customerGroupFactory $customerGroupFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Backend\Model\Auth\Session $userSession,
		\Magento\Framework\Message\ManagerInterface $messageManager,
		\Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Magento\Framework\App\Filesystem\DirectoryList $directorylist,
        \Lazada\Customer\Model\Adminhtml\Addproducts $addcustomerImport
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->resourceConnection = $resourceConnection;
        $this->userSession = $userSession;
		$this->messageManager = $messageManager;
		$this->_coreSession = $coreSession;
        $this->_directorylist = $directorylist;
        $this->addcustomerImport = $addcustomerImport;
    }

    public function execute()
    {   
        // $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        // $customerFactory = $objectManager->get('\Magento\Customer\Model\CustomerFactory')->create();

        // $customerId = 8;

        // $customer = $customerFactory->load($customerId);

        // //fetch whole customer information
        // echo "<pre>";
        // print_r($customer->getData());

        // //fetch specific information
        // // echo $customer->getEmail();
        // // echo $customer->getFirstname();
        // // echo $customer->getLastname();
        // die;

        if(!defined('DS')){
            define('DS',DIRECTORY_SEPARATOR);
        }
        $filecsv = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'customer';
        $csv = file_get_contents($filecsv);
        $filehead = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'customerhead';
        $head = file_get_contents($filehead);
        $filecount = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'customercount';        

        $dataArr 			= array();		
		$dataArr['csv'] 	= json_decode($csv);
        $dataArr['head'] 	= json_decode($head);       

        $SaveData 	= $this->addcustomerImport->addData($dataArr)->processData();
        $records  	= $this->_coreSession->getRecords();
        $status  	= $this->_coreSession->getStatussave();          

		$this->_coreSession->setsavecount($records['Savecount']);
		file_put_contents($filecount,$records['Savecount']);
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Lazada_Customer::customer');
        $resultPage->getConfig()->getTitle()->prepend(__('Import Customer Result'));
        $resultPage->addBreadcrumb(__('Customer'), __('Import Customer Result'));

        // echo '<pre>';
        //     print_r($records['Savecount']);
        // echo '</pre>';
        // die;

        return $resultPage;
    }
}
