<?php

namespace Lazada\DemoApi\Model\Data;

use Lazada\DemoApi\Api\Data\ProductInterface;
use Magento\Framework\DataObject;

class Product extends DataObject implements ProductInterface 
{
    /**
     * @return int
     */
    public function getId() 
    {
        return $this->getData('id');
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id) 
    {
        return $this->setData('id', $id);
    }
}