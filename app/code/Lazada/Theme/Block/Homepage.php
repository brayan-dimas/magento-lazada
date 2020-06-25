<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Lazada\Theme\Block;

use Magento\Framework\UrlInterface;

class Homepage extends \Magento\Framework\View\Element\Template
{
    const CATEGORY_LEVEL = 2;
    protected $categoryCollection;
    protected $filter;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollection,
        array $data = [],
        \Magento\Framework\Filter\FilterManager $filter
    )
    {
        $this->categoryCollection = $categoryCollection;
        $this->filter = $filter;

        parent::__construct($context, $data);
    }

    public function getCategoryLists()
	{      
        $storeId = $this->_storeManager->getStore()->getId();
        $rootCategoryId = $this->_storeManager->getStore($storeId)->getRootCategoryId();

		if(!$this->getData('category.list')){
            $categories = $this->categoryCollection->create()
                                ->addAttributeToSelect('*')
								->addIsActiveFilter()
								->addAttributeToFilter('level',self::CATEGORY_LEVEL)
								->addAttributeToFilter('path', array('like' => "1/{$rootCategoryId}/%"));
								
			$this->setData('category.list',$categories);
        }
        
		return $this->getData('category.list');
    } 
    
    public function getMediaPath() {
        return $this->_urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]);
    }

    public function getFilter(){
        return $this->filter;
    }
    
}