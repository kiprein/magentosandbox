<?php

$installer = $this;

$installer->startSetup();

$installer->run( "
DROP TABLE IF EXISTS `{$this->getTable('inventory_search_temp')}`;
CREATE TABLE `{$this->getTable('inventory_search_temp')}` (
    `id` int(11) unsigned NOT NULL auto_increment,
    `date` datetime null,
	`product_id` varchar(45) null,
	`sku` varchar(45) null,
	`qty` int(10) null,
    PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
" );

$installer->endSetup();