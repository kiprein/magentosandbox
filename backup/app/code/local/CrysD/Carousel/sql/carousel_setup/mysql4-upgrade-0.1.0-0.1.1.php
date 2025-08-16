<?php
/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$installer->run("
  ALTER TABLE `{$this->getTable('carousel/slide')}`
    ADD COLUMN `slide_image` VARCHAR(255) NULL AFTER `slide_theme`;
");

$installer->endSetup();
