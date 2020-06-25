<?php
namespace Lazada\Catalog\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{   

	protected $registry;

	const DIRECTORY = '';

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $_mediaDirectory;

    /**
     * @var \Magento\Framework\Image\Factory
     */
    protected $_imageFactory;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

	public function __construct(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Framework\Registry $registry,
		\Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
		) 
	{
		
		$this->registry = $registry;
		$this->_mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->_imageFactory = $imageFactory;
        $this->_storeManager = $storeManager;

		return parent::__construct($context);
    }

    public function getCurrentCategory(){
        return $this->registry->registry('current_category');
	}
	
	protected function _fileExists($filename)
    {
        if ($this->_mediaDirectory->isFile($filename)) {
            return true;
        }
        return false;
    }

    /**
     * Resize image
     * @return string
     */
    public function resize($image, $width = null, $height = null)
    {
        $mediaFolder = self::DIRECTORY;

        $path = $mediaFolder . '/cache';
        if ($width !== null) {
            $path .= '/' . $width . 'x';
            if ($height !== null) {
                $path .= $height ;
            }
        }

		$image = str_replace($this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA), '',$image);
        $absolutePath = $this->_mediaDirectory->getAbsolutePath($mediaFolder) . $image;
        $imageResized = $this->_mediaDirectory->getAbsolutePath($path) . '/' . $image;

        if (!$this->_fileExists($path . $image)) {
            $imageFactory = $this->_imageFactory->create();
            $imageFactory->open($absolutePath);
            $imageFactory->constrainOnly(true);
            $imageFactory->keepTransparency(true);
            $imageFactory->backgroundColor(array(255,255,255));
            $imageFactory->keepFrame(true);
            $imageFactory->keepAspectRatio(true);
            $imageFactory->resize($width, $height);
            $imageFactory->save($imageResized);
        }

        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $path . '/' . $image;
    }

    
}