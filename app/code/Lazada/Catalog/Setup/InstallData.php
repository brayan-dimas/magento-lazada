<?php
namespace lazada\Catalog\Setup;

use Magento\Framework\Module\Setup\Migration;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{

    private $eavSetupFactory;

    public function __construct(
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
    )
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY, 
            'list_title', [
                'type'  			=> 'varchar',
                'input' 			=> 'text', 
                'group' 			=> 'Custom Fields',
                'label'         	=> 'List Title',
                'visible'       	=> 1,
                'required'      	=> 0,
                'user_defined'  	=> 1,
                'frontend_input' 	=> '',
                'global'       	 	=> \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'visible_on_front'  => 1,
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'list_icon', [
                'type'  			=> 'varchar',
                'input' 			=> 'image',
                'backend'			=> 'Magento\Catalog\Model\Category\Attribute\Backend\Image',
                'group' 			=> 'Custom Fields',
                'label'         	=> 'List Icon',
                'visible'       	=> 1,
                'required'      	=> 0,
                'user_defined' 		=> 1,
                'frontend_input' 	=> '',
                'global'        	=> \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'visible_on_front'  => 1,
            ]
        );
    	

        $setup->endSetup();
    }
}