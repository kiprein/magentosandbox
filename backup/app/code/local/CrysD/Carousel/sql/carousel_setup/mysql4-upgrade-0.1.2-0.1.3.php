<?php
/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$conn  = $installer->getConnection();
$slidetable = $installer->getTable('carousel/slide');

// add CTA link
$conn->addColumn($slidetable, 'slide_link_cta', [
    'type'     => Varien_Db_Ddl_Table::TYPE_VARCHAR,
    'length'   => 255,
    'nullable' => true,
    'comment'  => 'Slide CTA Link Text',
]);

// add mobile image
$conn->addColumn($slidetable, 'slide_image_mobile', [
    'type'     => Varien_Db_Ddl_Table::TYPE_VARCHAR,
    'length'   => 255,
    'nullable' => true,
    'comment'  => 'Slide Mobile Image Filename',
]);

$installer->endSetup();
