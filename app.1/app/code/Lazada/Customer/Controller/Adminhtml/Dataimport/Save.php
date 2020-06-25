<?php

namespace Lazada\Customer\Controller\Adminhtml\Dataimport;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Image\AdapterFactory;
use Magento\Store\Model\ScopeInterface; 


class Save extends \Magento\Backend\App\Action
{

    protected $fileSystem;

    protected $uploaderFactory;

    protected $request;

    protected $adapterFactory;


    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Filesystem $fileSystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        AdapterFactory $adapterFactory

    ) {
        parent::__construct($context);
        $this->fileSystem = $fileSystem;
        $this->request = $request;
        $this->scopeConfig = $scopeConfig;
        $this->adapterFactory = $adapterFactory;
        $this->uploaderFactory = $uploaderFactory;
    }

    public function execute()
    { 

         if ( (isset($_FILES['importdata']['name'])) && ($_FILES['importdata']['name'] != '') ) 
         {
            try 
           {    
                $uploaderFactory = $this->uploaderFactory->create(['fileId' => 'importdata']);
                $uploaderFactory->setAllowedExtensions(['csv', 'xls']);
                $uploaderFactory->setAllowRenameFiles(true);
                $uploaderFactory->setFilesDispersion(true);

                $mediaDirectory = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA);
                $destinationPath = $mediaDirectory->getAbsolutePath('lazada_customer_IMPORTDATA');

                $result = $uploaderFactory->save($destinationPath);

                if (!$result) 
                   {
                     throw new LocalizedException
                     (
                        __('File cannot be saved to path: $1', $destinationPath)
                     );

                   }
                else
                    {   
                        $imagePath = 'lazada_customer_IMPORTDATA'.$result['file'];

                        $mediaDirectory = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA);

                        $destinationfilePath = $mediaDirectory->getAbsolutePath($imagePath);

                        /* file read operation */

                        $f_object = fopen($destinationfilePath, "r");

                        $column = fgetcsv($f_object);

                        // echo '<pre>';
                        //     print_r($column);
                        // echo '</pre>';
                        // die;
                        // column name must be same as the Sample file name 

                        if($f_object)
                        {
                            if( ($column[0] == 'entity_id') && ($column[1] == 'name') && ($column[2] == 'email') && ($column[3] == 'group_id') && ($column[4] == 'created_id') && ($column[5] == 'website_id') && ($column[6] == 'website_id') )
                            {   

                                $count = 0;

                                while (($column = fgetcsv($f_object)) !== FALSE) 
                                {                               
                                    // echo '<pre>';                                            
                                    //     print_r($f_object);
                                    // echo '</pre>';                                            
                                    $rowData = $this->_objectManager->create("\Magento\Store\Model\StoreManagerInterface")->setCurrentStore(0);

                                    if($column[0] != 'entity_id')// unique Name like Primary key
                                    {   
                                        $count++;

                                    /// here this are all the Getter Setter Method which are call to set value 
                                    // the auto increment column name not used to set value 

                                        $rowData->setCol_name_1($column[1]);

                                        $rowData->setCol_name_2($column[2]);

                                        $rowData->setCol_name_3($column[3]);

                                        $rowData->setCol_name_4($column[4]);

                                        $rowData->setCol_name_5($column[5]);

                                        $rowData->setCol_name_5($column[6]);

                                        $rowData->save();   

                                    }

                                } 
                                // die;
                            $this->messageManager->addSuccess(__('A total of %1 record(s) have been Added.', $count));
                            $this->_redirect('customer/dataimport/importdata');
                            }
                            else
                            {
                                $this->messageManager->addError(__("invalid Formated File"));
                                $this->_redirect('customer/dataimport/importdata');
                            }

                        } 
                        else
                        {
                            $this->messageManager->addError(__("File hase been empty"));
                            $this->_redirect('customer/dataimport/importdata');
                        }

                    }                   

           } 
           catch (\Exception $e) 
          {   
               $this->messageManager->addError(__($e->getMessage()));
               $this->_redirect('customer/dataimport/importdata');
          }

         }
         else
         {
            $this->messageManager->addError(__("Please try again."));
            $this->_redirect('customer/dataimport/importdata');
         }
    }
}