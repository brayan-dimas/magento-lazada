<?php
namespace Lazada\AboutUs\Model\ResourceModel\Post;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	protected $_idFieldName = 'id';
	protected $_eventPrefix = 'lazada_aboutus_post_collection';
	protected $_eventObject = 'post_collection';

	/**
	 * Define resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('Lazada\AboutUs\Model\Post', 'Lazada\AboutUs\Model\ResourceModel\Post');
	}

}