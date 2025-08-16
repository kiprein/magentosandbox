<?php
$installer = $this;

$installer->startSetup();

$this->addAttribute( Mage_Catalog_Model_Category::ENTITY, 'landing_page_order', array(
	'group'            => 'General Information',
	'input'            => 'text',
	'type'             => 'int',
	'label'            => 'Category Landing Page Order',
	'visible'          => true,
	'required'         => false,
	'visible_on_front' => false,
	'default'          => 999,
	'global'           => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
	'sort_order'       => 7
) );
$installer->endSetup();