<?php
$installer = $this;
$installer->startSetup();

$installer->run("
#Remove old banner tables
DROP TABLE `mksresponsivebannerslider`;
");

$installer->endSetup();