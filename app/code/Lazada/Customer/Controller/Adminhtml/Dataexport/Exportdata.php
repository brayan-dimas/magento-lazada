<?php
namespace Lazada\Customer\Controller\Adminhtml\Dataexport;

class Exportdata extends \Magento\Framework\App\Action\Action
{
	protected $fileFactory;
	protected $csvProcessor;
    protected $directoryList;
    protected $_customerSession;
    protected $_customerFactory;

	public function __construct(
        \Magento\Framework\App\Action\Context $context,
    	\Magento\Customer\Model\Session $customerSession,
    	\Magento\Framework\App\Response\Http\FileFactory $fileFactory,
    	\Magento\Framework\File\Csv $csvProcessor,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Customer\Model\ResourceModel\Grid\Collection $customer
	)
	{
    	$this->fileFactory = $fileFactory;
    	$this->csvProcessor = $csvProcessor;
        $this->directoryList = $directoryList;
        $this->_customerFactory = $customer;
        $this->_customerSession = $customerSession;
    	parent::__construct($context, $customerSession);
	}

	public function execute()
	{
    	$fileName = 'csv_filename.csv';
    	$filePath = $this->directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR)
        	. "/" . $fileName;

    	$customer = $this->_customerSession->getCustomer();
    	$personalData = $this->getPresonalData($customer);

    	$this->csvProcessor
    	    // ->setDelimiter(';')
        	// ->setEnclosure('"')
        	->saveData(
            	$filePath,
            	$personalData
        	);

    	return $this->fileFactory->create(
        	$fileName,
        	[
            	'type' => "filename",
            	'value' => $fileName,
            	'rm' => true,
        	],
            \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,
        	'application/octet-stream'
    	);
	}

	protected function getPresonalData()
	{
    	// $result = [];
        $customerData = $this->_customerFactory->getData();                

        $columnHeader[] = [
        	'Entity ID',
        	'Name',
        	'Email',
        	'Group ID',
 	        'Created At',
        	'Website ID'
        ];

        $find = array(";");
        $replace = array("DIMAS");
        foreach ($columnHeader as $header) {
            $result[] = str_replace($find,$replace,$header);              
        }

        // echo '<pre>';
        //     print_r($result);
        // echo '</pre>';
        // die;   
        
        foreach ($customerData as $results) {
            $result[] = [
                $results['entity_id'],
                $results['name'],
                $results['email'],
                $results['group_id'],
                $results['created_at'],
                $results['website_id'],

            ];
        }

    	return $result;
    }
    
    // public function getColumnHeader() {
    //     $headers = ['entity_id','name','email','group_id', 'created_at', 'website_id'];
    //     return $headers;
    // }
}