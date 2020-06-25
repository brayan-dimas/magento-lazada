<?php

namespace Lazada\Customer\Model\Adminhtml;

use Magento\Framework\App\Filesystem\DirectoryList;

class Addproducts extends \Magento\Framework\Model\AbstractModel
{

    protected $resourceConnection;
    protected $userSession;
    protected $messageManager;
    
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Unilab\Grid\Model\customerGroupFactory $customerGroupFactory
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Backend\Model\Auth\Session $userSession,
		\Magento\Framework\Message\ManagerInterface $messageManager,
		\Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Magento\Framework\App\Filesystem\DirectoryList $directorylist,
        \Magento\Catalog\Model\Product\Gallery\ReadHandler $galleryReadHandle,
        \Magento\Catalog\Model\Product\Gallery\Processor $imageProcessor,
        \Magento\Framework\Filesystem $filesystem
    ) {
        $this->_objectManager = $objectmanager;
        $this->resourceConnection = $resourceConnection;
        $this->userSession = $userSession;
		$this->messageManager = $messageManager;
		$this->_coreSession = $coreSession;
        $this->_directorylist = $directorylist;
        $this->_galleryReadHandle = $galleryReadHandle;
        $this->_filesystem = $filesystem;
        $this->imageProcessor = $imageProcessor;
    }

    public function processData()
    {
        ini_set("memory_limit",-1);
		ini_set('max_execution_time', '0');
		
		$csv  = $this->getData('csv');
		$head = $this->getData('head');

		foreach ($head as $key => $value):
			if(strtolower($value) == 'entity_id'):		  
				$value = 'entity_id';			
			endif;
			
			$key 			= strtolower(str_replace(' ', '_', $key));		  
			$head[$key] 	= strtolower(str_replace(' ', '_', $value));
			
			if(!empty($head[$key])):
				$fieldName[] = $head[$key];
			endif;
			
		endforeach;
		
		$csvResult 		 = array_map("array_combine", array_fill(0, count($csv), $head), $csv);

		$fieldName 		 = implode(",", $fieldName);

		$saveTempProduct = $this->_saveTemp($csvResult, $fieldName);		
			
    }
    protected function _saveTemp($csvResult, $fieldName)
	{
				
        $dataSave = null;
		$entity_id 				= null;
		// $name 		= null;
		$email 		= null;
		$group_id 				= null;
		$created_id 		= null;
		$website_id 		= null;
		$store_id			= null;
		$firstname			= null;
		$lastname			= null;
		
		$getData 		    = array();
		$resData		    = array();
        $lastIncrement	= null;
        $count 			    = 0;
		$countSave		    = 0;
		$countBreak 	    = 10;
        
        $coreSession = $this->_objectManager->get('\Magento\Framework\Session\SessionManagerInterface');
		$coreSession->unsRecords();
		$records		= count($csvResult);                
        
		$filecount = $this->_directorylist->getPath('var'). DS. 'cache'. DS .'mage--csv' . DS. 'customercount';
        $SaveCount = file_get_contents($filecount);
		
		// echo '<pre>';
        //     print_r($SaveCount);
        // echo '</pre>';
        // die;

		if(empty($SaveCount)):
			$SaveCount = 0;
        endif;

        $counter = 0;                

		foreach($csvResult as $_key=>$_value):					
			
			$counter++;
			$fieldValue = null;
			$customerData = null;

			// Available Data
			$customerData['entity_id'] = null;
            $customerData['firstname'] = null;
            $customerData['lastname'] = null;
            $customerData['email'] = null;
            $customerData['group_id'] = null;
            $customerData['created_id'] = null;
            $customerData['website_id'] = null;          
			$customerData['store_id'] = null;

			if($count >= $SaveCount):				
				foreach($_value as $key=>$value):					
					if ($key == 'entity_id'):						
						$customerData['entity_id'] = $value;
						$entity_id = $value;
					elseif ($key == 'email'):
						$customerData['email'] = $value;

					elseif ($key == 'group_id'):
						$customerData['group_id'] = $value;
						
					elseif ($key == 'created_id'):
                        $customerData['created_id'] = $value;
                        
                    elseif ($key == 'website_id'):
						$customerData['website_id'] = $value;
						
					elseif ($key == 'store_id'):
						$customerData['store_id'] = $value;

					elseif ($key == 'firstname'):
						$customerData['firstname'] = $this->cleanString($value);
					
					elseif ($key == 'lastname'):
						$customerData['lastname'] = $this->cleanString($value);

					endif;

					$fieldValue[] = "'".$value."'";
				
					// echo 'Key = ' . $key . ' Value = ' . $value . '<br>';					
				endforeach;			
				// echo '<pre>';
				// 	print_r($fieldValue);
				// echo '</pre>';
				// die;
               
				if(!empty($fieldValue)):
					$dataSave = true;
					$countSave++;
                    $currentnumber = $count + 1;
                    
					if ($count >= $SaveCount):
								
						if($this->_isskuChecker($entity_id) == false):							                            
							// echo 'False';
							// print_r(json_encode($customerData));
							// die;

							$response = $this->createProduct($customerData);

							// print_r($response);
							// exit();
							if($response){
								$resData[] = $currentnumber. '. '. $customerData['entity_id'] .' : '.$customerData['firstname'].' '. $customerData['lastname']. ' - <span style="color:green;">Success!</span>';
								$coreSession->setStatussave(1);
							}else{
								$resData[] = $currentnumber. '. '. $customerData['entity_id'] .' : '.$customerData['firstname'].' '. $customerData['lastname']. ' - <span style="color:red;">Failed!</span>';
								$coreSession->setStatussave(0);
							}
							
						else:
							$resData[] = $currentnumber. '. '. $customerData['entity_id'] .' : '.$customerData['firstname'].' '. $customerData['lastname']. ' - <span style="color:red;">Exist!</span>';
							$coreSession->setStatussave(0);
						endif;
					endif;
					
					$coreSession->setRecordsave($resData);
				endif;
				
			endif;
			
			$count++;					
			$remainingRec  				= array();
			$remainingRec['Allrecords']	= $records;
			$remainingRec['Savecount']	= $count;		
			$coreSession->setRecords($remainingRec);
			
			// echo '<pre>';
			// 	print_r($count);
			// echo '<pre>';			

			if($dataSave == true && $countSave == $countBreak):
				// echo 'Yes';
				$countSave = 0;
				break;
			endif;
			
		endforeach;	
		// die;
		
		return $this;
	}

    protected function _isskuChecker($entity_id)
	{
		// echo $entity_id;
		// die;
		$id = $this->_objectManager->create('\Magento\Customer\Model\Customer')->getId($entity_id);
		if ($id){
			$response = true;
			// echo 'Yes';
			// die;
		}
		else{
			$response = false;
			// echo 'No' . 'ID: ' . $id;
			// die;
		}	
		
		return $response;
    }
    protected function createProduct($customerData)
	{
        
			
		try {			

			$this->_objectManager->create("\Magento\Store\Model\StoreManagerInterface")->setCurrentStore(0);
			
			$customer = $this->_objectManager->create('\Magento\Customer\Model\Customer');										

			$customer
				->setEntityId($customerData['entity_id'])
				->setFirstname($customerData['firstname'])								
				->setLastname($customerData['lastname'])
				->setEmail($customerData['email'])				
				->setGroupId($customerData['group_id'])				
				->setCreatedId($customerData['created_id'])				
				->setWebsiteId($customerData['website_id'])													
				->setStoreId($customerData['store_id']);								

				// print_r($customerData['entity_id']);					
				// exit();				

				$customer->save();
				$response = true;
			
			}
			catch(\Exception $e){
				$this->messageManager->addError($e->getMessage());
				$response = false;
			}	
			
			return $response;
	}
    /**
     * @return bool
     */
    function cleanString($str) {
		$utf8='';
        $str = (string)$str;
           if( is_null($utf8) ) {
               if( !function_exists('mb_detect_encoding') ) {
                   $utf8 = (strtolower($str)=='utf-8');
               } else {
                   $length = strlen($str);
                   $utf8 = true;
                   for ($i=0; $i < $length; $i++) {
                       $c = ord($str[$i]);
                       if ($c < 0x80) $n = 0; # 0bbbbbbb
                       elseif (($c & 0xE0) == 0xC0) $n=1; # 110bbbbb
                       elseif (($c & 0xF0) == 0xE0) $n=2; # 1110bbbb
                       elseif (($c & 0xF8) == 0xF0) $n=3; # 11110bbb
                       elseif (($c & 0xFC) == 0xF8) $n=4; # 111110bb
                       elseif (($c & 0xFE) == 0xFC) $n=5; # 1111110b
                       else return false; # Does not match any model
                       for ($j=0; $j<$n; $j++) { # n bytes matching 10bbbbbb follow ?
                           if ((++$i == $length)
                               || ((ord($str[$i]) & 0xC0) != 0x80)) {
                               $utf8 = false;
                               break;
                           }

                       }
                   }
               }

           }

           if(!$utf8)
               
               $str = utf8_encode($str);

           $transliteration = array(
           'Ĳ' => 'I', 'Ö' => 'O','Œ' => 'O','Ü' => 'U','ä' => 'a','æ' => 'a',
           'ĳ' => 'i','ö' => 'o','œ' => 'o','ü' => 'u','ß' => 's','ſ' => 's',
           'À' => 'A','Á' => 'A','Â' => 'A','Ã' => 'A','Ä' => 'A','Å' => 'A',
           'Æ' => 'A','Ā' => 'A','Ą' => 'A','Ă' => 'A','Ç' => 'C','Ć' => 'C',
           'Č' => 'C','Ĉ' => 'C','Ċ' => 'C','Ď' => 'D','Đ' => 'D','È' => 'E',
           'É' => 'E','Ê' => 'E','Ë' => 'E','Ē' => 'E','Ę' => 'E','Ě' => 'E',
           'Ĕ' => 'E','Ė' => 'E','Ĝ' => 'G','Ğ' => 'G','Ġ' => 'G','Ģ' => 'G',
           'Ĥ' => 'H','Ħ' => 'H','Ì' => 'I','Í' => 'I','Î' => 'I','Ï' => 'I',
           'Ī' => 'I','Ĩ' => 'I','Ĭ' => 'I','Į' => 'I','İ' => 'I','Ĵ' => 'J',
           'Ķ' => 'K','Ľ' => 'K','Ĺ' => 'K','Ļ' => 'K','Ŀ' => 'K','Ł' => 'L',
           'Ñ' => 'N','Ń' => 'N','Ň' => 'N','Ņ' => 'N','Ŋ' => 'N','Ò' => 'O',
           'Ó' => 'O','Ô' => 'O','Õ' => 'O','Ø' => 'O','Ō' => 'O','Ő' => 'O',
           'Ŏ' => 'O','Ŕ' => 'R','Ř' => 'R','Ŗ' => 'R','Ś' => 'S','Ş' => 'S',
           'Ŝ' => 'S','Ș' => 'S','Š' => 'S','Ť' => 'T','Ţ' => 'T','Ŧ' => 'T',
           'Ț' => 'T','Ù' => 'U','Ú' => 'U','Û' => 'U','Ū' => 'U','Ů' => 'U',
           'Ű' => 'U','Ŭ' => 'U','Ũ' => 'U','Ų' => 'U','Ŵ' => 'W','Ŷ' => 'Y',
           'Ÿ' => 'Y','Ý' => 'Y','Ź' => 'Z','Ż' => 'Z','Ž' => 'Z','à' => 'a',
           'á' => 'a','â' => 'a','ã' => 'a','ā' => 'a','ą' => 'a','ă' => 'a',
           'å' => 'a','ç' => 'c','ć' => 'c','č' => 'c','ĉ' => 'c','ċ' => 'c',
           'ď' => 'd','đ' => 'd','è' => 'e','é' => 'e','ê' => 'e','ë' => 'e',
           'ē' => 'e','ę' => 'e','ě' => 'e','ĕ' => 'e','ė' => 'e','ƒ' => 'f',
           'ĝ' => 'g','ğ' => 'g','ġ' => 'g','ģ' => 'g','ĥ' => 'h','ħ' => 'h',
           'ì' => 'i','í' => 'i','î' => 'i','ï' => 'i','ī' => 'i','ĩ' => 'i',
           'ĭ' => 'i','į' => 'i','ı' => 'i','ĵ' => 'j','ķ' => 'k','ĸ' => 'k',
           'ł' => 'l','ľ' => 'l','ĺ' => 'l','ļ' => 'l','ŀ' => 'l','ñ' => 'n',
           'ń' => 'n','ň' => 'n','ņ' => 'n','ŉ' => 'n','ŋ' => 'n','ò' => 'o',
           'ó' => 'o','ô' => 'o','õ' => 'o','ø' => 'o','ō' => 'o','ő' => 'o',
           'ŏ' => 'o','ŕ' => 'r','ř' => 'r','ŗ' => 'r','ś' => 's','š' => 's',
           'ť' => 't','ù' => 'u','ú' => 'u','û' => 'u','ū' => 'u','ů' => 'u',
           'ű' => 'u','ŭ' => 'u','ũ' => 'u','ų' => 'u','ŵ' => 'w','ÿ' => 'y',
           'ý' => 'y','ŷ' => 'y','ż' => 'z','ź' => 'z','ž' => 'z','Α' => 'A',
           'Ά' => 'A','Ἀ' => 'A','Ἁ' => 'A','Ἂ' => 'A','Ἃ' => 'A','Ἄ' => 'A',
           'Ἅ' => 'A','Ἆ' => 'A','Ἇ' => 'A','ᾈ' => 'A','ᾉ' => 'A','ᾊ' => 'A',
           'ᾋ' => 'A','ᾌ' => 'A','ᾍ' => 'A','ᾎ' => 'A','ᾏ' => 'A','Ᾰ' => 'A',
           'Ᾱ' => 'A','Ὰ' => 'A','ᾼ' => 'A','Β' => 'B','Γ' => 'G','Δ' => 'D',
           'Ε' => 'E','Έ' => 'E','Ἐ' => 'E','Ἑ' => 'E','Ἒ' => 'E','Ἓ' => 'E',
           'Ἔ' => 'E','Ἕ' => 'E','Ὲ' => 'E','Ζ' => 'Z','Η' => 'I','Ή' => 'I',
           'Ἠ' => 'I','Ἡ' => 'I','Ἢ' => 'I','Ἣ' => 'I','Ἤ' => 'I','Ἥ' => 'I',
           'Ἦ' => 'I','Ἧ' => 'I','ᾘ' => 'I','ᾙ' => 'I','ᾚ' => 'I','ᾛ' => 'I',
           'ᾜ' => 'I','ᾝ' => 'I','ᾞ' => 'I','ᾟ' => 'I','Ὴ' => 'I','ῌ' => 'I',
           'Θ' => 'T','Ι' => 'I','Ί' => 'I','Ϊ' => 'I','Ἰ' => 'I','Ἱ' => 'I',
           'Ἲ' => 'I','Ἳ' => 'I','Ἴ' => 'I','Ἵ' => 'I','Ἶ' => 'I','Ἷ' => 'I',
           'Ῐ' => 'I','Ῑ' => 'I','Ὶ' => 'I','Κ' => 'K','Λ' => 'L','Μ' => 'M',
           'Ν' => 'N','Ξ' => 'K','Ο' => 'O','Ό' => 'O','Ὀ' => 'O','Ὁ' => 'O',
           'Ὂ' => 'O','Ὃ' => 'O','Ὄ' => 'O','Ὅ' => 'O','Ὸ' => 'O','Π' => 'P',
           'Ρ' => 'R','Ῥ' => 'R','Σ' => 'S','Τ' => 'T','Υ' => 'Y','Ύ' => 'Y',
           'Ϋ' => 'Y','Ὑ' => 'Y','Ὓ' => 'Y','Ὕ' => 'Y','Ὗ' => 'Y','Ῠ' => 'Y',
           'Ῡ' => 'Y','Ὺ' => 'Y','Φ' => 'F','Χ' => 'X','Ψ' => 'P','Ω' => 'O',
           'Ώ' => 'O','Ὠ' => 'O','Ὡ' => 'O','Ὢ' => 'O','Ὣ' => 'O','Ὤ' => 'O',
           'Ὥ' => 'O','Ὦ' => 'O','Ὧ' => 'O','ᾨ' => 'O','ᾩ' => 'O','ᾪ' => 'O',
           'ᾫ' => 'O','ᾬ' => 'O','ᾭ' => 'O','ᾮ' => 'O','ᾯ' => 'O','Ὼ' => 'O',
           'ῼ' => 'O','α' => 'a','ά' => 'a','ἀ' => 'a','ἁ' => 'a','ἂ' => 'a',
           'ἃ' => 'a','ἄ' => 'a','ἅ' => 'a','ἆ' => 'a','ἇ' => 'a','ᾀ' => 'a',
           'ᾁ' => 'a','ᾂ' => 'a','ᾃ' => 'a','ᾄ' => 'a','ᾅ' => 'a','ᾆ' => 'a',
           'ᾇ' => 'a','ὰ' => 'a','ᾰ' => 'a','ᾱ' => 'a','ᾲ' => 'a','ᾳ' => 'a',
           'ᾴ' => 'a','ᾶ' => 'a','ᾷ' => 'a','β' => 'b','γ' => 'g','δ' => 'd',
           'ε' => 'e','έ' => 'e','ἐ' => 'e','ἑ' => 'e','ἒ' => 'e','ἓ' => 'e',
           'ἔ' => 'e','ἕ' => 'e','ὲ' => 'e','ζ' => 'z','η' => 'i','ή' => 'i',
           'ἠ' => 'i','ἡ' => 'i','ἢ' => 'i','ἣ' => 'i','ἤ' => 'i','ἥ' => 'i',
           'ἦ' => 'i','ἧ' => 'i','ᾐ' => 'i','ᾑ' => 'i','ᾒ' => 'i','ᾓ' => 'i',
           'ᾔ' => 'i','ᾕ' => 'i','ᾖ' => 'i','ᾗ' => 'i','ὴ' => 'i','ῂ' => 'i',
           'ῃ' => 'i','ῄ' => 'i','ῆ' => 'i','ῇ' => 'i','θ' => 't','ι' => 'i',
           'ί' => 'i','ϊ' => 'i','ΐ' => 'i','ἰ' => 'i','ἱ' => 'i','ἲ' => 'i',
           'ἳ' => 'i','ἴ' => 'i','ἵ' => 'i','ἶ' => 'i','ἷ' => 'i','ὶ' => 'i',
           'ῐ' => 'i','ῑ' => 'i','ῒ' => 'i','ῖ' => 'i','ῗ' => 'i','κ' => 'k',
           'λ' => 'l','μ' => 'm','ν' => 'n','ξ' => 'k','ο' => 'o','ό' => 'o',
           'ὀ' => 'o','ὁ' => 'o','ὂ' => 'o','ὃ' => 'o','ὄ' => 'o','ὅ' => 'o',
           'ὸ' => 'o','π' => 'p','ρ' => 'r','ῤ' => 'r','ῥ' => 'r','σ' => 's',
           'ς' => 's','τ' => 't','υ' => 'y','ύ' => 'y','ϋ' => 'y','ΰ' => 'y',
           'ὐ' => 'y','ὑ' => 'y','ὒ' => 'y','ὓ' => 'y','ὔ' => 'y','ὕ' => 'y',
           'ὖ' => 'y','ὗ' => 'y','ὺ' => 'y','ῠ' => 'y','ῡ' => 'y','ῢ' => 'y',
           'ῦ' => 'y','ῧ' => 'y','φ' => 'f','χ' => 'x','ψ' => 'p','ω' => 'o',
           'ώ' => 'o','ὠ' => 'o','ὡ' => 'o','ὢ' => 'o','ὣ' => 'o','ὤ' => 'o',
           'ὥ' => 'o','ὦ' => 'o','ὧ' => 'o','ᾠ' => 'o','ᾡ' => 'o','ᾢ' => 'o',
           'ᾣ' => 'o','ᾤ' => 'o','ᾥ' => 'o','ᾦ' => 'o','ᾧ' => 'o','ὼ' => 'o',
           'ῲ' => 'o','ῳ' => 'o','ῴ' => 'o','ῶ' => 'o','ῷ' => 'o','А' => 'A',
           'Б' => 'B','В' => 'V','Г' => 'G','Д' => 'D','Е' => 'E','Ё' => 'E',
           'Ж' => 'Z','З' => 'Z','И' => 'I','Й' => 'I','К' => 'K','Л' => 'L',
           'М' => 'M','Н' => 'N','О' => 'O','П' => 'P','Р' => 'R','С' => 'S',
           'Т' => 'T','У' => 'U','Ф' => 'F','Х' => 'K','Ц' => 'T','Ч' => 'C',
           'Ш' => 'S','Щ' => 'S','Ы' => 'Y','Э' => 'E','Ю' => 'Y','Я' => 'Y',
           'а' => 'A','б' => 'B','в' => 'V','г' => 'G','д' => 'D','е' => 'E',
           'ё' => 'E','ж' => 'Z','з' => 'Z','и' => 'I','й' => 'I','к' => 'K',
           'л' => 'L','м' => 'M','н' => 'N','о' => 'O','п' => 'P','р' => 'R',
           'с' => 'S','т' => 'T','у' => 'U','ф' => 'F','х' => 'K','ц' => 'T',
           'ч' => 'C','ш' => 'S','щ' => 'S','ы' => 'Y','э' => 'E','ю' => 'Y',
           'я' => 'Y','ð' => 'd','Ð' => 'D','þ' => 't','Þ' => 'T','ა' => 'a',
           'ბ' => 'b','გ' => 'g','დ' => 'd','ე' => 'e','ვ' => 'v','ზ' => 'z',
           'თ' => 't','ი' => 'i','კ' => 'k','ლ' => 'l','მ' => 'm','ნ' => 'n',
           'ო' => 'o','პ' => 'p','ჟ' => 'z','რ' => 'r','ს' => 's','ტ' => 't',
           'უ' => 'u','ფ' => 'p','ქ' => 'k','ღ' => 'g','ყ' => 'q','შ' => 's',
           'ჩ' => 'c','ც' => 't','ძ' => 'd','წ' => 't','ჭ' => 'c','ხ' => 'k',
           'ჯ' => 'j','ჰ' => 'h'
           );
           $str = str_replace( array_keys( $transliteration ),
                               array_values( $transliteration ),
                               $str);
           return $str;
        
   }
    public function _isAllowed()
    {
        return true;
    }

}