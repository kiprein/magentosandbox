<?php
$installer = $this;
$installer->startSetup();

$installer->run("
ALTER TABLE banners ADD label VARCHAR(150) NULL;
");

$installer->endSetup();