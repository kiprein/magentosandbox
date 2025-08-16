<?php
$installer = $this;

$installer->startSetup();

$this->addAttribute( Mage_Catalog_Model_Category::ENTITY, 'separate_category_filter', array(
	'group'            => 'General Information',
	'input'         => 'select',
	'type'          => 'int',
	'label'            => 'Separate Category Filter',
	'source'           => 'eav/entity_attribute_source_boolean',
	'visible'          => true,
	'required'         => false,
	'visible_on_front' => false,
	'global'           => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
	'note'             => 'This will create a new category filter section with this category as the header and children underneath'
) );
$installer->endSetup();