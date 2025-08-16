<?php
$installer = $this;

$installer->startSetup();

$this->addAttribute( Mage_Catalog_Model_Category::ENTITY, 'category_landing_image', array(
	'group'            => 'General Information',
	'input'            => 'image',
	'type'             => 'varchar',
	'label'            => 'Category Landing Image',
	'visible'          => true,
	'backend'          => 'catalog/category_attribute_backend_image',
	'required'         => false,
	'visible_on_front' => false,
	'global'           => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
	'sort_order'       => 7
) );
$installer->endSetup();