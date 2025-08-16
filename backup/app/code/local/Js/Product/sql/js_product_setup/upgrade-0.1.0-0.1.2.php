<?php

$installer = $this;

$installer->startSetup();

$installer->addAttribute('catalog_product', 'product_category_keywords', array(
	'label' => 'Product Category Keywords',
	'group' => 'Meta Information',
	'type' => 'text',
	'input' => 'textarea',
	'sort_order' => 200,
	'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
	'required' => 0,
	'user_defined' => 0,
	'filterable_in_search' => 0,
	'is_configurable' => 0,
	'used_in_product_listing' => 1,
));

$installer->run("
UPDATE `catalog_eav_attribute` SET `is_searchable`='0', `is_visible_in_advanced_search`='0' WHERE `attribute_id`='72';
");
$installer->endSetup();