<?php
/**
 * Created by PhpStorm.
 * User: fab_5
 * Date: 6/14/2018
 * Time: 3:58 PM
 */ 
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS `{$this->getTable('js_menu')}`;
CREATE TABLE `{$this->getTable('js_menu')}` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `menu_type` varchar(25) NULL,
  `title` varchar(100) NULL,
  `parent_id` INT(10) unsigned NULL,
  `position` INT(10) unsigned NULL,
  `url` VARCHAR(255) NULL,
  `permission` VARCHAR(15) NULL,
  `target` VARCHAR(25) NULL,
  `class` VARCHAR(25) NULL,
  `active` INT(10) unsigned NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();