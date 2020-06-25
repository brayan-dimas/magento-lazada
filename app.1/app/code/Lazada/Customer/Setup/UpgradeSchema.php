<?php

// namespace Lazada\Customer\Setup;

// use Magento\Framework\Setup\UpgradeSchemaInterface;
// use Magento\Framework\Setup\ModuleContextInterface;
// use Magento\Framework\Setup\SchemaSetupInterface;

// class UpgradeSchema implements UpgradeSchemaInterface
// {

//     /**
//      * {@inheritdoc}
//      */

//     public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
//     {
//         if (version_compare($context->getVersion(), '2.0.7') < 0) {
//             $connection = $setup->getConnection();
//             $connection->addColumn(
//                 $setup->getTable('customer_entity'),
//                 'civil_status',
//                 [
//                     'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
//                     'length' => 255,
//                     'nullable' => true,
//                     'default' => '',
//                     'comment' => 'Civil status'
//                 ]
//             );
//         }
//     }
    // /**
    //  * {@inheritdoc}
    //  */
    // public function upgrade(
    //     SchemaSetupInterface $setup,
    //     ModuleContextInterface $context
    // ) {
    //     $installer = $setup;

    //     $installer->startSetup();
    //     if (version_compare($context->getVersion(), "1.0.0", "<")) {
    //     //Your upgrade script
    //     }
    //     if (version_compare($context->getVersion(), '1.0.1', '<')) {
    //       $installer->getConnection()->addColumn(
    //             $installer->getTable('lime_eleveniacategory'),
    //             'category_depth',
    //             [
    //                 'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    //                 'length' => 10,
    //                 'nullable' => true,
    //                 'comment' => 'Category Depth'
    //             ]
    //         );
    //     }
    //     $installer->endSetup();
    // }
// }