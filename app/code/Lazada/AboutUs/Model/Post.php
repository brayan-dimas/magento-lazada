<?php
namespace Lazada\AboutUs\Model;
class Post extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
	const CACHE_TAG = 'lazada_aboutus';

	protected $_cacheTag = 'lazada_aboutus';

	protected $_eventPrefix = 'lazada_aboutus';

	protected function _construct()
	{
		$this->_init('Lazada\AboutUs\Model\ResourceModel\Post');
	}

	public function getIdentities()
	{
		return [self::CACHE_TAG . '_' . $this->getId()];
	}

	public function getDefaultValues()
	{
		$values = [];

		return $values;
	}
}