<?php
// shell/full_product_sync.php

require_once 'abstract.php';
require_once 'class_product_sync.php';

if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}

$logFile = 'full_product_sync.log';

try {
    //Mage::log("Starting FULL product sync at " . date('Y-m-d H:i:s'), null, $logFile);
    echo "[Full Sync] Starting at " . date('Y-m-d H:i:s') . "\n";

    $shell = new Product_Sync();

    $shell->run(true);

    //Mage::log("Completed FULL product sync at " . date('Y-m-d H:i:s'), null, $logFile);
    echo "[Full Sync] Completed at " . date('Y-m-d H:i:s') . "\n";

    exit(0);
} catch (Exception $e) {
    //Mage::log("Error during FULL product sync: " . $e->getMessage(), null, $logFile);
    echo "[Full Sync] Error: " . $e->getMessage() . "\n";
    exit(1);
}
