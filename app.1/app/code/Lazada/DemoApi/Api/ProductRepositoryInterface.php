<?php

namespace Lazada\DemoApi\Api;

use Magento\Framework\Exception\NoSuchEntityException;

interface ProductRepositoryInterface 
{
    /**
     * Get Product by its ID
     * @param int $id
     * @return \Lazada\DemoApi\Api\Data\ProductInterface
     * @throws NoSuchEntityException
     */
    public function getProductById($id);
}