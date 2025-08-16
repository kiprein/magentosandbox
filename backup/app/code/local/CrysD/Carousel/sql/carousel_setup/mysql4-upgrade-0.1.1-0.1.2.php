<?php
/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

// add two new columns to `slide`
$connection = $installer->getConnection();
$table      = $installer->getTable('carousel/slide');

$connection->addColumn($table, 'slide_link_cta', [
    'type'     => Varien_Db_Ddl_Table::TYPE_VARCHAR,
    'length'   => 255,
    'nullable' => true,
    'comment'  => 'Slide CTA Link Text',
]);

$connection->addColumn($table, 'slide_image_mobile', [
    'type'     => Varien_Db_Ddl_Table::TYPE_VARCHAR,
    'length'   => 255,
    'nullable' => true,
    'comment'  => 'Slide Mobile Image Filename',
]);

$installer->endSetup();
