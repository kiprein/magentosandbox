<?php
$installer = $this;
$installer->startSetup();
$attribute  = array(
    'type'          =>  'text',
	'backend'  		=> "catalog/category_attribute_backend_image",
    'label'         =>  'Category Group Image',
    'input'         =>  'image',
    'global'        =>  Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'       =>  true,
    'required'      =>  false,
    'user_defined'  =>  true,
    'default'       =>  "",
	'sort_order'    => 6,
    'group'         =>  "General Information"
);
$installer->addAttribute('catalog_category', 'custom_category_image', $attribute);
$installer->endSetup();
?>