<?php
/**
 * @author		Sashas
 * @category    Sashas
 * @package     Sashas_LoginCustomer
 * @copyright   Copyright (c) 2015 Sashas IT Support Inc. (http://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */


$installer = $this;
$installer->startSetup();

/* Log Table */
$table = $installer->getConnection()->newTable($installer->getTable('logincustomer/log'))
	->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
			'unsigned' => true,
			'nullable' => false,
			'primary' => true,
			'identity' => true,
	), 'Log ID')
	->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
			'nullable' => false,
	        'unsigned'  => true,	        
	), 'Customer ID')
	->addColumn('admin_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
	        'nullable' => false,
	        'unsigned'  => true,
	), 'Admin User ID')	
	->addColumn('admin_username', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
			'nullable' => false,
	), 'Admin username') 	 
	->addColumn('status', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
	        'nullable' => false,
	), 'Login Status')
	->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
	        'nullable' => false,
	), 'Created At')	
	->setComment('Log table');
$installer->getConnection()->createTable($table);

$installer->endSetup();