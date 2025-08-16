<?php

$installer = $this;

$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS `{$this->getTable('guest_wishlist')}`;
CREATE TABLE `{$this->getTable('guest_wishlist')}` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `key` VARCHAR(20) NOT NULL,
  `sku` VARCHAR(100) NULL,
  `product_id` INT(10) NULL,
  `date_added` DATETIME NULL,
  `email` VARCHAR(150) NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#Clear out the wishlist tables the first time
TRUNCATE TABLE wishlist;
TRUNCATE TABLE wishlist_item_option;

#Remove netgo and mlx tables
DROP TABLE `netgo_gwishlist_gwishlist`;
DROP TABLE `netgo_gwishlist_gwishlist_store`;
DROP TABLE `mlx_license`;

#Remove core config and resource records
DELETE FROM core_config_data WHERE path LIKE '%mlx%';
DELETE FROM core_config_data WHERE path LIKE '%netgo%';
DELETE FROM core_resource WHERE `code` = 'netgo_gwishlist_setup';
DELETE FROM core_resource WHERE `code` = 'mlx_setup';
DELETE FROM core_resource WHERE `code` = 'guestwishlist_setup';
");

$installer->endSetup();
