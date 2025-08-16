<?php
$installer = $this;
$installer->startSetup();

$this->getConnection()->addColumn(
	$this->getTable('banners'),
	'active_from', "DATETIME"
);

$this->getConnection()->addColumn(
	$this->getTable('banners'),
	'active_to', "DATETIME"
);

$installer->endSetup();