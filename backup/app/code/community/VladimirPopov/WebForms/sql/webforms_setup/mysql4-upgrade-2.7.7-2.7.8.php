<?php
/**
 * @author        Vladimir Popov
 * @copyright    Copyright (c) 2016 Vladimir Popov
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('webforms/files'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary' => true,
    ), 'Id')
    ->addColumn('result_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => false,
        'unsigned' => true,
    ), 'Result ID')
    ->addColumn('field_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => false,
        'unsigned' => true,
    ), 'Field ID')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => false
    ), 'File Name')
    ->addColumn('size', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => true,
        'unsigned' => true
    ))
    ->addColumn('mime_type', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable' => false
    ), 'Mime Type')
    ->addColumn('path', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => false
    ), 'File Path')
    ->addColumn('link_hash', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable' => false
    ), 'Link Hash')
    ->addColumn('created_time', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
            'nullable' => false
        )
    );

$table->addForeignKey(
    $installer->getFkName('webforms/files', 'result_id', 'webforms/results', 'id'),
    'result_id',
    $installer->getTable('webforms/results'),
    'id');

$table->addForeignKey(
    $installer->getFkName('webforms/files', 'field', 'webforms/fields', 'id'),
    'field_id',
    $installer->getTable('webforms/fields'),
    'id');

$installer->getConnection()->createTable($table);

$installer->endSetup();
