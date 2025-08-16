<?php
/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$installer->run("
  ALTER TABLE `{$this->getTable('carousel/carousel')}`
    ADD COLUMN `carousel_link` VARCHAR(255) NULL AFTER `theme`,
    ADD COLUMN `carousel_cta` VARCHAR(255) NULL AFTER `carousel_link`,
    ADD COLUMN `video` VARCHAR(255) NULL AFTER `carousel_link`;
  
    ALTER TABLE `{$this->getTable('carousel/slide')}`
    ADD COLUMN `slide_video` VARCHAR(255) NULL AFTER `slide_link_cta`;
");

$installer->endSetup();