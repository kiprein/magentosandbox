<?php

$installer = $this;

$installer->startSetup();

$installer->run("
	UPDATE eav_attribute SET frontend_input = 'boolean', source_model = 'eav/entity_attribute_source_boolean' WHERE attribute_code = 'quick_ship'
");

$installer->endSetup();
