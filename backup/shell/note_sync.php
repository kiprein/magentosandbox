<?php
if(!isset($_SERVER['SHELL'])){
    echo "Ah ah ah, you didn\'t say the magic word!";
    exit;
}


require_once( 'abstract.php' );

/**
 * The image and product tables each have their own updated field so they need two different scripts to update everything
 * Class Image_Sync
 */
class Note_Sync extends Mage_Shell_Abstract {

	public function run() {
		ini_set( 'memory_limit', '4000M' );
		$resource         = Mage::getSingleton( 'core/resource' );
		$read             = $resource->getConnection( 'js_import_read' );
		$importHelper = Mage::helper('js_import');

		//Start by just getting all of the notes that have been updated in the last 30 minutes
		$productInformation = $read->query( "
			SELECT twbp.k_product_id, tp.s_item_num FROM t_web_box_prod AS twbp
				INNER JOIN t_product AS tp ON twbp.k_product_id = tp.k_product_id
				WHERE tp.dt_updated > date_sub(now(), interval 30 minute)
			UNION
			SELECT tnp.k_product_id, tp.s_item_num FROM t_note_prod AS tnp
				INNER JOIN t_product AS tp ON tnp.k_product_id = tp.k_product_id
				WHERE tp.dt_updated > date_sub(now(), interval 30 minute)
		" );
        //WHERE tp.dt_updated > date_sub(now(), interval 30 minute)

		foreach ($productInformation as $productInfo) {
			$productId = Mage::getModel( 'catalog/product' )->getIdBySku( $productInfo['s_item_num'] );
			$erpProductId = $productInfo['k_product_id'];

			echo "Memory Usage: " . ( memory_get_peak_usage( true ) / 1024 / 1024 ) . " MiB\n";
			echo "Sku: " . $productInfo['s_item_num'] . "\n";
			echo "M Id: " . $productId . "\r\n";
			echo "ERP Id: " . $erpProductId . "\r\n";

			//Only run if the product has already been found
			if ( $productId ) {
				$product = Mage::getModel( 'catalog/product' )->setStoreId( 1 )->load( $productId );
				$product->setWhatIncludeSnotes( $importHelper->getIncludedNotes( $read, $erpProductId ) );
				$product->setOptionalServices( $importHelper->getServices( $read, $erpProductId, 'Customization' ) );
                $product->setOptionalCharge( $importHelper->getServices( $read, $erpProductId, 'Ordering' ) );
				$product->setImprintProcessServices( $importHelper->getServices( $read, $erpProductId, 'Included' ) );

				$product->save();
				$product->clearInstance();
			}
		}
	}
}

$shell = new Note_Sync();

$shell->run();
echo 'Done';
