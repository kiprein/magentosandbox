<?php

$installer = $this;

$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS `{$this->getTable('wishlist_sent')}`;
CREATE TABLE `{$this->getTable('wishlist_sent')}` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `key` VARCHAR(20) NOT NULL,
  `name` VARCHAR(100) NULL,
  `email` VARCHAR(150) NULL,
  `type` VARCHAR(20) NULL,
  `date_sent` DATETIME NULL,
  `product_info` TEXT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();
