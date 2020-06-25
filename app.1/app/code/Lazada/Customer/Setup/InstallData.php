<?php

namespace Lazada\Customer\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Config;
use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Eav\Model\Entity\Attribute\Set as AttributeSet;

class InstallData implements InstallDataInterface
{
    /**
     * @var CustomerSetupFactory
     */
    protected $customerSetupFactory;

    /**
     * @var AttributeSetFactory
     */
    private $attributeSetFactory;

    /**
     * @var Config
     */
    private $eavConfig;

    /**
     * @var
     */
    private $eavSetupFactory;

    public function __construct(EavSetupFactory $eavSetupFactory, CustomerSetupFactory $customerSetupFactory, AttributeSetFactory $attributeSetFactory) 
    {
       $this->eavSetupFactory = $eavSetupFactory;
       $this->customerSetupFactory = $customerSetupFactory;
       $this->attributeSetFactory = $attributeSetFactory;
    }

   /**
    * {@inheritdoc}
    * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
    */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
        $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        /** @var $attributeSet AttributeSet */
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        $customerSetup->addAttribute(Customer::ENTITY, 'sample_multiselect', [
            'type' => 'int',
            'label' => 'My Sample Multiselect Attribute',
            'input' => 'multiselect',
            'required' => false,
            'visible' => true,           
            'user_defined' => false,
            'is_user_defined' => false,
            'sort_order' => 5,
            'is_used_in_grid' => false,
            'is_visible_in_grid' => false,
            'is_filterable_in_grid' => false,
            'is_searchable_in_grid' => false,
            'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
            'source' => 'Lazada\Customer\Model\Entity\Attribute\Source\MyMultiSelect',
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
            'position' => 5,
            'default' => 0,
            'system' => 0,
         ]);

         $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'sample_multiselect')
            ->addData([
                 'attribute_set_id' => $attributeSetId,
                 'attribute_group_id' => $attributeGroupId,
                 'used_in_forms' => ['adminhtml_customer'],
          ]);

         $attribute->save();
    }
}
