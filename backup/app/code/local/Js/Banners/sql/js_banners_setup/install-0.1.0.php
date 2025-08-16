<?php

$installer = $this;

$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS `{$this->getTable('banners')}`;
CREATE TABLE `{$this->getTable('banners')}` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `image` varchar(100) NULL,
  `link` VARCHAR(255) NULL,
  `sort_order` INT(10) unsigned NULL,
  `active` VARCHAR(5) NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();
