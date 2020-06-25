<?php
namespace Lazada\Catalog\Model\Category;
use Magento\Framework\App\ObjectManager;
  
class DataProvider extends \Magento\Catalog\Model\Category\DataProvider
{
  
    protected function getFieldsMap()
    {
        $fields = parent::getFieldsMap();
        $fields['custom_fields'][] = 'list_icon'; // custom image field
        $fields['custom_fields'][] = 'list_title'; // custom image field
        // echo '<pre>';
        // print_r($fields);
        // echo '</pre>';
        // exit;
        return $fields;
    }

    private $fileInfo;

    public function getData()
    {
        $data = parent::getData();
        $category = $this->getCurrentCategory();
        if ($category) {
            $categoryData = $category->getData();
            $categoryData = $this->addUseConfigSettings($categoryData);
            $categoryData = $this->filterFields($categoryData);
            $categoryData = $this->convertValues($category, $categoryData);

            $this->loadedData[$category->getId()] = $categoryData;
        }
        if (isset($data[$category->getId()]['list_icon'])) {
            unset($data[$category->getId()]['list_icon']);

            $fileName = $category->getData('list_icon');
            $stat = $this->getFileInfo()->getStat($fileName);
            $mime = $this->getFileInfo()->getMimeType($fileName);
            
            $data[$category->getId()]['list_icon'][0]['name'] = $category->getData('list_icon');
            $data[$category->getId()]['list_icon'][0]['url']  = $category->getImageUrl('list_icon');
            $data[$category->getId()]['list_icon'][0]['size'] = isset($stat) ? $stat['size'] : 0;
            $data[$category->getId()]['list_icon'][0]['type'] = $mime;
        }
        
        
        return $data;
    }
    /**
     * Get FileInfo instance
     *
     * @return FileInfo
     *
     * @deprecated 101.1.0
     */
    private function getFileInfo()
    {
        if ($this->fileInfo === null) {
            $this->fileInfo = ObjectManager::getInstance()->get(\Magento\Catalog\Model\Category\FileInfo::class);
        }
        return $this->fileInfo;
    }



    private function convertValues($category, $categoryData)
    {
        foreach ($category->getAttributes() as $attributeCode => $attribute) {
            if (!isset($categoryData[$attributeCode])) {
                continue;
            }

            if ($attribute->getBackend() instanceof ImageBackendModel) {
                unset($categoryData[$attributeCode]);

                $fileName = $category->getData($attributeCode);
                if ($this->getFileInfo()->isExist($fileName)) {
                    $stat = $this->getFileInfo()->getStat($fileName);
                    $mime = $this->getFileInfo()->getMimeType($fileName);

                    $categoryData[$attributeCode][0]['name'] = $fileName;
                    $categoryData[$attributeCode][0]['url'] = $category->getImageUrl($attributeCode);
                    $categoryData[$attributeCode][0]['size'] = isset($stat) ? $stat['size'] : 0;
                    $categoryData[$attributeCode][0]['type'] = $mime;
                }
            }
        }

        return $categoryData;
    }
}