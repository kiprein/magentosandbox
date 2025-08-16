<?php
$installer = $this;
$installer->startSetup();

$installer->run("
ALTER TABLE banners ADD banner_text TEXT NULL;
");

$installer->endSetup();