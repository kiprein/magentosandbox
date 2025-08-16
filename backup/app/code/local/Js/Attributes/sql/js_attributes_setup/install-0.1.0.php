<?php

$attributeSets  = array(
	'Default'
);
$attributeLabel = 'Trophy Assembly Required';
$attributeCode  = 'trophy_assembly_required';
$attributeGroup = '23';
$attributeOrder = 999;

$attributeOptions = array(
	'label'            => $attributeLabel,
	'input'            => 'select',
	'type'             => 'int',
	'source'           => 'eav/entity_attribute_source_boolean',
	'frontend'         => null,
	'global'           => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
	'required'         => false,
	'user_defined'     => true,
	'class'            => null,
	'unique'           => false,
	'searchable'       => false,
	'filterable'       => false,
	'comparable'       => false,
	'visible_on_front' => true,
	'is_configurable'  => false,
	'visible'          => true,
	'default'          => 0,
);

/*
 * The following method will finally create the attribute
 */

$this->addAttribute(
	Mage_Catalog_Model_Product::ENTITY,
	$attributeCode,
	$attributeOptions, $attributeCode
);

/*
 * This block of code will add the attribute to the attribute sets
 * which are set in the $attributeSets variable
 */
foreach ( $attributeSets as $attributeSet ) {
	$this->addAttributeToSet(
		Mage_Catalog_Model_Product::ENTITY, $attributeSet,
		$attributeGroup, $attributeCode, $attributeOrder
	);
}

$this->endSetup();