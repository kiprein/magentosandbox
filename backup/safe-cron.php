<?php

file_put_contents('/tmp/cron_ping', date('c') . "\n", FILE_APPEND);

date_default_timezone_set('UTC');
require 'app/Mage.php';
Mage::app('admin');

$logFile = 'cron-debug.log';

// Safely bypass Aoe_Scheduler override and generate cron schedule
$observer = new Mage_Cron_Model_Observer();
$observer->generate();
Mage::log("Cron job schedule generated", null, $logFile);
echo "Cron job schedule generated\n";

// Product Sync Dispatcher
$hour = (int) date('G');
$minute = (int) date('i');

// Run full sync between 2:00 AM and 2:04 AM UTC
if ($hour === 2 && $minute < 5) {
    Mage::log("Running FULL product sync", null, $logFile);
    echo "[Product Sync] Running FULL sync at " . date('Y-m-d H:i:s') . "\n";
    passthru('php -f shell/full_product_sync.php');
} else {
    Mage::log("Running INCREMENTAL product sync", null, $logFile);
    echo "[Product Sync] Running incremental sync at " . date('Y-m-d H:i:s') . "\n";
    passthru('php -f shell/product_sync.php');
}

// Run all pending Magento cron jobs
Mage::log("Starting cron job execution...", null, $logFile);

$pendingJobs = Mage::getModel('cron/schedule')->getCollection()
    ->addFieldToFilter('status', 'pending')
    ->addFieldToFilter('scheduled_at', ['lteq' => now()]);

foreach ($pendingJobs as $job) {
    $code = $job->getJobCode();
    Mage::log("Trying job: $code", null, $logFile);
    echo "Running job: $code\n";

    try {
        $job->setExecutedAt(now());
        $job->runNow();
        $job->setStatus('success');
        $job->setFinishedAt(now());
        $job->save();
        echo "Success: $code\n";
        Mage::log("Success: $code", null, $logFile);
    } catch (Exception $e) {
        echo "Failed: $code — " . $e->getMessage() . "\n";
        Mage::log("Failed: $code — " . $e->getMessage(), null, $logFile);
        $job->setStatus('error')
            ->setMessages($e->getMessage())
            ->setExecutedAt(now())
            ->setFinishedAt(now())
            ->save();
    }
}

// Clean old cache files (older than 1 hour)
$cacheDir = Mage::getBaseDir('cache');
$maxAgeSeconds = 3600;

if (is_dir($cacheDir)) {
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($cacheDir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($files as $file) {
        if ($file->isFile() && (time() - $file->getMTime()) > $maxAgeSeconds) {
            unlink($file->getPathname());
        } elseif ($file->isDir() && iterator_count(new FilesystemIterator($file)) === 0) {
            rmdir($file->getPathname());
        }
    }
    Mage::log("Old cache files cleaned", null, $logFile);
}
