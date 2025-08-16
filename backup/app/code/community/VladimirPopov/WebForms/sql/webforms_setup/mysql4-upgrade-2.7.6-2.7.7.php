<?php
/**
 * @author 		Vladimir Popov
 * @copyright  	Copyright (c) 2016 Vladimir Popov
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->getConnection()
    ->addColumn(
        $this->getTable('webforms'),
        'accept_url_parameters',
        'TINYINT ( 1 )'
    )
;

$installer->endSetup();
