<?php
$installer = $this;

$installer->startSetup();

$this->addAttribute( Mage_Catalog_Model_Category::ENTITY, 'url_override', array(
	'group'            => 'General Information',
	'input'            => 'text',
	'type'             => 'varchar',
	'label'            => 'Url Override',
	'visible'          => true,
	'required'         => false,
	'visible_on_front' => true,
	'global'           => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
	'note'             => 'This needs to be the full url path. Ex: http://www.crystal-d.com/'
) );
$installer->endSetup();