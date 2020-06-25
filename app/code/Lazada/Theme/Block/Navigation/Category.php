<?php
namespace Lazada\Theme\Block\Navigation;

class Category extends \Magento\Framework\View\Element\Template 
{
    const CATEGORY_ROOT_LEVEL = 2;
    protected $storeManager;
    protected $customerSession;
    protected $categoryCollection;
    protected $registry;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollection,
        \Magento\Framework\Registry $registry
    ) {
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->categoryCollection = $categoryCollection;
        $this->registry = $registry;
        parent::__construct($context);
    }
    
    public function getCategories() 
    {
        $store_id =  $this->storeManager->getStore()->getId();

        // echo '<pre>';
        //     print_r($store_id);
        // echo '</pre>';
        // die;
        $rootCategoryId = $this->storeManager->getStore($store_id)->getRootCategoryId();
        $categories = $this->categoryCollection->create()
        ->addAttributeToSelect('*')
        ->addIsActiveFilter()
        ->addAttributeToFilter('level', self::CATEGORY_ROOT_LEVEL)
        ->addAttributeToFilter('path', array('like' => "1/{$rootCategoryId}/%"))
        ->load();

        return $categories;
    }

    public function getCurrentCategory()
    {
        if($category = $this->registry->registry('current_category')){
			return $category;
		} 
		return false;

    }

    public function canExpandMenu($category) 
    {
        if($current_category = $this->getCurrentCategory()){ 
			if($current_category->getId() == $category->getId() ||
			   in_array($current_category->getId(),$category->getAllChildren(true))){
				return true; 
			}
		}
		return false;
    }

    public function customerIsLoggedIn() 
    {
        // echo '<pre>';
        //     print_r($this->customerSession->getData());
        // echo '<pre>';
        // die;
        return $this->customerSession->isLoggedIn();
    }


    // public function categoryHide()
    // {
    //         $data = $this->arrayData();
    //         $save = array();
    //         $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of Object Manager
    //         $categoryFactory = $objectManager->get('\Magento\Catalog\Model\CategoryFactory');// Instance of Category Model
    //         foreach($data as $test)
    //         {
    //             $category = $categoryFactory->create()->load($test);
    //             addToSave($category, $save);
    //         }
    //         return array_merge($data,$save);   
    // }

    // public function addToSave($category, &$save)
    // {
    //     $childrenCategories = $category->getChildrenCategories();
    //     foreach ($childrenCategories as $cat){
    //         $save[] = $cat->getId();

    //         echo '<pre>';
    //             print_r($childrenCategories);
    //         echo '<pre>';
            
    //         addToSave($cat, $save);
    //     }
    //     die;
    // } 
}