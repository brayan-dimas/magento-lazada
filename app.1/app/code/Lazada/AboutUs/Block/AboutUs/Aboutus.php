<?php

namespace Lazada\AboutUs\Block\AboutUs;

class Aboutus extends \Magento\Framework\View\Element\Template
{
    protected $_postFactory;
	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Lazada\AboutUs\Model\PostFactory $postFactory
	)
	{
		$this->_postFactory = $postFactory;
		parent::__construct($context);
    }
    
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }


    public function getPostCollection(){
        $post = $this->_postFactory->create();
        // $collection = $post->getCollection();
        // echo '<pre>';
        //     print_r($collection->getData());
        // echo '</pre>';
        // die;
		return $post->getCollection();
	}
}