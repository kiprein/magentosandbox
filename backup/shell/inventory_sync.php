<?php
// if(!isset($_SERVER['SHELL'])){
//     echo "Ah ah ah, you didn\'t say the magic word!";
//     exit;
// }

/**
 * A script to quickly sync inventory from the inventory_avail custom
 * table to the magento proper database
 * 
 * @author Mark Wickline 8/1/19
 */

require_once( 'abstract.php' );

if(session_id() == '' || !isset($_SESSION)) {
    // session isn't started
    session_start();
}

class Product_Sync extends Mage_Shell_Abstract {

	public function run() {
		ini_set( 'memory_limit', '4000M' );

		$resource  = Mage::getSingleton( 'core/resource' );

		$coreRead  = $resource->getConnection( 'core_read' );
		$coreWrite = $resource->getConnection( 'core_write' );

		$processes = Mage::getSingleton('index/indexer')->getProcessesCollection();
		foreach ($processes as $process) {
			$process->setMode(Mage_Index_Model_Process::MODE_MANUAL)->save();
		}
		
        $results = $coreRead->fetchAll("SELECT * FROM inventory_avail WHERE date = '1990-01-01 00:00:00'");
        
		foreach ( $results as $result ) {

            $stockItem = Mage::getModel( 'cataloginventory/stock_item' )->loadByProduct( $result['product_id'] );
            if ( $stockItem->getId() > 0 ) {
                $stockItem->setQty( (int) $result['qty'] );
				if($result['qty'] > 0) {
                	$stockItem->setIsInStock( (int) ( 1 ) );
				} else {
					$stockItem->setIsInStock( (int) ( 0 ) );
				}
                $stockItem->save();
            }
        }


		foreach ($processes as $process) {
			$process->setMode(Mage_Index_Model_Process::MODE_REAL_TIME)->save();
		}

        $indexer = Mage::getModel('index/indexer')->getProcessByCode('cataloginventory_stock');
        $indexer->reindexEverything();
	}
}

$shell = new Product_Sync();

$shell->run();
echo 'Done';
