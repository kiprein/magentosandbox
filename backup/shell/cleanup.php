<?php

if(!isset($_SERVER['SHELL'])){
    echo "Ah ah ah, you didn\'t say the magic word!";
    exit;
}

require_once( 'abstract.php' );

class CleanUp extends Mage_Shell_Abstract {
	public function run() {
		umask(0);
		Mage::app('default');
		Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

		try {
			$allTypes = Mage::app()->useCache();
			foreach($allTypes as $type => $value) {
				Mage::app()->getCacheInstance()->cleanType($type);
				Mage::dispatchEvent('adminhtml_cache_refresh_type', array('type' => $type));
				echo "{$type}\r\n";
			}
			echo "Magento cache cleared\r\n";
		} catch (Exception $e) {
			echo $e->getMessage();
		}

        /**
         * There is something odd going on where certain cache files are not stored in the main
         * Magento directory but instead stored in the file path below.  If these folders are not removed
         * the login takes a long time to finish so this hopefully takes care of the issue.
         */
        system("rm -rf /tmp/magento/var/cache");
	}
}

$shell = new CleanUp();
$shell->run();
