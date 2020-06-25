<?php

namespace Lazada\DemoApi\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Lazada\DemoApi\Api\Data\ProductInterfaceFactory;
use Lazada\DemoApi\Api\ProductRepositoryInterface;

class ProductRepository implements ProductRepositoryInterface
{
    private $_productInterfaceFactory;
    // private $productHelper;
    private $_productRepository;



    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        ProductInterfaceFactory $productInterfaceFactory
        // ProductHelper $productHelper
    )
    {
        $this->_productInterfaceFactory = $productInterfaceFactory;
        // $this->productHelper = $productHelper;
        $this->_productRepository = $productRepository;
    }


    public function getProductById($id) 
    {
        $productInterface = $this->_productInterfaceFactory->create();
        try {
            $product = $this->_productRepository->getById($id);
            $productInterface->setId($product->getId());
            return $productInterface;
        }catch(NoSuchEntityException $e) {
            throw NoSuchEntityException::singleField("id", $id);
        }
    }

}
