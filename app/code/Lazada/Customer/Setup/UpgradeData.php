<?php
namespace Lazada\Customer\Setup;
use Magento\Customer\Model\Customer;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\Set as AttributeSet;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;

class UpgradeData implements UpgradeDataInterface
{

//     private $customerSetupFactory;

    private $attributeSetFactory;

    /**
     * Constructor
     *
     * @param \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        AttributeSetFactory $attributeSetFactory
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
    }

//     /**
//      * {@inheritdoc}
//      */
    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {

        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        /** @var $attributeSet AttributeSet */
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

         if (version_compare($context->getVersion(), '1.0.6') < 0) {            
            $customerSetup->addAttribute(
                \Magento\Customer\Model\Customer::ENTITY, 'sample_multiselect', [
                'type' => 'varchar',
                'label' => 'My Sample Multiselect Attribute',
                'input' => 'multiselect',
                'required' => false,
                'visible' => true,
                'default' => '',
                'searchable' => false,
                'user_defined' => true,
                'sort_order' => 5,
                'position' => 5,
                'system' => 0,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'source' => 'Lazada\Customer\Model\Entity\Attribute\Source\MyMultiSelect',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'is_used_in_grid'              => 1,
                'is_visible_in_grid'           => 1,
                'is_filterable_in_grid'        => 1,
            ]);

            $sample_multiselect = $customerSetup->getEavConfig()->getAttribute(\Magento\Customer\Model\Customer::ENTITY, 'sample_multiselect')
            ->addData(['used_in_forms' => [
                    'adminhtml_customer',
                    'customer_account_create',
                    'customer_account_edit',
                ]
            ]);
            $sample_multiselect->save();  


//             $customerSetup->addAttribute('customer', 'civil_status', [
//                 'type' => 'varchar',
//                 'label' => 'Civil Status',
//                 'input' => 'text',
//                 'required' => false,
//                 'sort_order' => 110,
//                 'visible' => true,
//                 'system' => false,
//                 'position' => 110,
//                 'user_defined' => true,
//                 'is_used_in_grid'              => 1,
//                 'is_visible_in_grid'           => 1,
//                 'is_filterable_in_grid'        => 1,
//             ]);

//             $civil_status = $customerSetup->getEavConfig()->getAttribute('customer', 'civil_status')
//             ->addData(['used_in_forms' => [
//                     'adminhtml_customer',
//                     'customer_account_create',
//                     'customer_account_edit',
//                 ]
//             ]);
//             $civil_status->save();   

//             $customerSetup->addAttribute('customer', 'sample_option', [

//                 'type'          => 'varchar',
//                 'label'         => 'Sample Option',
//                 'input'         => 'select',
//                 'source'        => 'Magento\Eav\Model\Entity\Attribute\Source\Table',
//                 'required'      => false,
//                 'sort_order'    => 210,
//                 'visible'       => true,
//                 'system'        => false,
//                 'position'      => 210,
//                 'admin_checkout' => 1,
//                 'option'         => ['values' => ['Yes', 'No']],
//                 'is_used_in_grid'              => 1,
//                 'is_visible_in_grid'           => 1,
//                 'is_filterable_in_grid'        => 1
//             ]);
    
//             $sample_option = $customerSetup->getEavConfig()->getAttribute('customer', 'sample_option')
//             ->addData([
//                 'attribute_set_id' => $attributeSetId,
//                 'attribute_group_id' => $attributeGroupId,
//                 'used_in_forms' => ['adminhtml_customer', 'customer_account_create', 'customer_account_edit']
//             ]);
    
//             $sample_option->save();
                                   
        }

    }
}