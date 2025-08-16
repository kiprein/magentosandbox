<?php
/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

// carousel table
$installer->run("
CREATE TABLE {$this->getTable('carousel/carousel')} (
  `carousel_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `identifier` VARCHAR(64) NOT NULL,
  `headline` VARCHAR(255) DEFAULT NULL,
  `subheadline` VARCHAR(255) DEFAULT NULL,
  `body` TEXT DEFAULT NULL,
  `style` VARCHAR(50) DEFAULT NULL,
  `theme` VARCHAR(50) DEFAULT NULL,
  PRIMARY KEY (`carousel_id`),
  UNIQUE KEY (`identifier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

// slide table
$installer->run("
CREATE TABLE {$this->getTable('carousel/slide')} (
  `slide_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `carousel_id` INT UNSIGNED NOT NULL,
  `position` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `slide_headline` VARCHAR(255) DEFAULT NULL,
  `slide_subheadline` VARCHAR(255) DEFAULT NULL,
  `slide_body` TEXT DEFAULT NULL,
  `slide_link` VARCHAR(255) DEFAULT NULL,
  `slide_theme` VARCHAR(50) DEFAULT NULL,
  PRIMARY KEY (`slide_id`),
  KEY (`carousel_id`),
  CONSTRAINT `FK_CAROUSEL_SLIDE` FOREIGN KEY (`carousel_id`)
    REFERENCES {$this->getTable('carousel/carousel')} (`carousel_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();
