<?php
$installer = $this;

$installer->startSetup();

$this->addAttribute( Mage_Catalog_Model_Category::ENTITY, 'open_new_tab', array(
	'group'            => 'General Information',
	'input'         => 'select',
	'type'          => 'int',
	'label'            => 'Open Category In New Tab',
	'source'           => 'eav/entity_attribute_source_boolean',
	'visible'          => true,
	'required'         => false,
	'visible_on_front' => false,
	'global'           => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
	'note'             => 'If set to "Yes" the category will open in a new tab'
) );
$installer->endSetup();