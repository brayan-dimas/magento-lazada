<?php

namespace Lazada\Customer\Controller\Adminhtml\Dataimport;

class SubmitImport extends \Magento\Backend\App\Action
{
    /**
     * @var \Unilab\Grid\Model\GridFactory
     */
    protected $customerGroupFactory;
    protected $resourceConnection;
    protected $userSession;
    protected $messageManager;

    
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Unilab\Grid\Model\customerGroupFactory $customerGroupFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Backend\Model\Auth\Session $userSession,
		\Magento\Framework\Message\ManagerInterface $messageManager,
		\Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Magento\Framework\App\Filesystem\DirectoryList $directorylist
    ) {
        parent::__construct($context);
        $this->resourceConnection = $resourceConnection;
        $this->userSession = $userSession;
		$this->messageManager = $messageManager;
		$this->_coreSession = $coreSession;
        $this->_directorylist = $directorylist;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
		if(!defined('DS')){
            define('DS',DIRECTORY_SEPARATOR);
        }
		$fullpath 	= $_FILES['csv_file']['tmp_name'];
		$filename 	= $_FILES['csv_file']['name'];
		$size 		= $_FILES['csv_file']['size'];
		$ext 		= pathinfo($filename, PATHINFO_EXTENSION);
		

		$arrayValue = array();
		
		
		$fp = fopen($fullpath, 'r');

        $csv = array();
        while ($row = fgetcsv($fp)) {
            $csv[] = $row;
        }
        // echo "<pre>";
        //     print_r($csv);
        // echo "</pre>";
        // exit();
        $head   = array_shift($csv);
        fclose($fp);

        //For Validation check missing fields
        // foreach ($head as $key => $value):
        //     if(strtolower($value) == 'entity_id'):		  
        //         $value = 'entity_id';			
        //     endif;
            
        //     $key 			= strtolower(str_replace(' ', '_', $key));		  
        //     $head[$key] 	= strtolower(str_replace(' ', '_', $value));
            
        //     if(!empty($head[$key])):
        //         $fieldName[] = $head[$key];
        //     endif;   

        //     echo "<pre>";
        //         print_r($fieldName);
        //     echo "</pre>";
        //     exit();
        // endforeach;
        $fieldCheckArray = [];
        
			$missingFields=[];
			foreach($fieldCheckArray as $reqF){
				if (!in_array($reqF, $fieldName))
				{
					$missingFields[]= $reqF;
				}
            }
        // echo "<pre>";
        //     print_r($fieldCheckArray);
        // echo "</pre>";
        // exit();
        if(strtolower($ext) != 'csv'){
            $this->messageManager->addError($filename.' is not a CSV file');
            return $this->_redirect('customer/dataimport/importdata');
        }
    
        if($missingFields){
            $this->messageManager->addError('Missing Fields ['.implode(",",$missingFields).']');
            return $this->_redirect('customer/dataimport/importdata');
        }

		if(file_exists($this->_directorylist->getPath('var'). DS. 'cache'. DS . 'mage--csv') === FALSE) {
            mkdir($this->_directorylist->getPath('var'). DS. 'cache'. DS . 'mage--csv');
        }
		$filecsv = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'customer';
		$filehead = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'customerhead';
        $filecount = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'customercount';

		file_put_contents($filecsv, $this->safe_json_encode($csv));
		file_put_contents($filehead, json_encode($head));
		file_put_contents($filecount, json_encode(0));

		try{
			return $this->_redirect('customer/dataimport/result');
		}catch(\Exception $e){
			$this->messageManager->addError($e->getMessage());
		}
    }
    function safe_json_encode($value, $options = 0, $depth = 512, $utfErrorFlag = false) {
        $encoded = json_encode($value, $options, $depth);
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                return $encoded;
            case JSON_ERROR_DEPTH:
                return 'Maximum stack depth exceeded'; // or trigger_error() or throw new Exception()
            case JSON_ERROR_STATE_MISMATCH:
                return 'Underflow or the modes mismatch'; // or trigger_error() or throw new Exception()
            case JSON_ERROR_CTRL_CHAR:
                return 'Unexpected control character found';
            case JSON_ERROR_SYNTAX:
                return 'Syntax error, malformed JSON'; // or trigger_error() or throw new Exception()
            case JSON_ERROR_UTF8:
                $clean = $this->utf8ize($value);
                if ($utfErrorFlag) {
                    return 'UTF8 encoding error'; // or trigger_error() or throw new Exception()
                }
                return $this->safe_json_encode($clean, $options, $depth, true);
            default:
                return 'Unknown error'; // or trigger_error() or throw new Exception()
    
        }
    }
    
    function utf8ize($mixed) {
        if (is_array($mixed)) {
            foreach ($mixed as $key => $value) {
                $mixed[$key] = $this->utf8ize($value);
            }
        } else if (is_string ($mixed)) {
            return utf8_encode($mixed);
        }
        return $mixed;
    }

}
