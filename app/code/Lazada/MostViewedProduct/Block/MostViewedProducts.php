<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Productslider
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Lazada\MostViewedProduct\Block;

class MostViewedProducts extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Reports\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productsFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Reports\Model\ResourceModel\Product\CollectionFactory $productsFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Reports\Model\ResourceModel\Product\CollectionFactory $productsFactory,
        array $data = []
    ) {
        $this->_productsFactory = $productsFactory;
        parent::__construct($context, $data);
    }

    /**
     * Getting most viewed products
     */
    public function getCollection()
    {

        $currentStoreId = $this->_storeManager->getStore()->getId();

        $collection = $this->_productsFactory->create()
        ->addAttributeToSelect(
            '*'
        )->addViewsCount()->setStoreId(
                $currentStoreId
        )->addStoreFilter(
                $currentStoreId
        );
        $items = $collection->getItems();

        echo '<pre>';
            print_r($items->getData());
        echo '</pre>';
        die;
        return $items;
    }
}