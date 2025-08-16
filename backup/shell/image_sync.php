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
class Image_Sync extends Mage_Shell_Abstract {

	public function run() {
		ini_set( 'memory_limit', '4000M' );
		$resource         = Mage::getSingleton( 'core/resource' );
		$read             = $resource->getConnection( 'js_import_read' );

		//Start by just getting all the distinct owner ids in the image table.
		// These match the k_product_id in the t_product table.
		//Bringing in the s_item_num allows for less queries for the Magento site of things
		$productInformation = $read->query(
			"SELECT DISTINCT(a.i_owner_id) AS erp_id, b.s_item_num, b.b_quick_ship 
				FROM t_image AS a 
				INNER JOIN t_product AS b on a.i_owner_id = b.k_product_id 
				WHERE a.dt_updated > date_sub(now(), interval 30 minute)
" );

		//WHERE b.k_product_id = 2550
		//

		foreach ($productInformation as $productInfo) {
			$productId = Mage::getModel( 'catalog/product' )->getIdBySku( $productInfo['s_item_num'] );
			$erpProductId = $productInfo['erp_id'];
			//Quickship gets a quick ship image added to the info 
			$isQuickShip = $productInfo['b_quick_ship'];

			echo "Memory Usage: " . ( memory_get_peak_usage( true ) / 1024 / 1024 ) . " MiB\n";
			echo "Sku: " . $productInfo['s_item_num'] . "\n";
			echo $this->getGiftBoxImage($read, $erpProductId) . "\n";;
			echo "M Id: " . $productId . "\r\n";

			//Only run if the product has already been found
			if ( $productId ) {
				$product = Mage::getModel( 'catalog/product' )->setStoreId( 1 )->load( $productId );

				$product->setEtcImage( $this->getImage( $read, $erpProductId, 'etc' ) )
					->setInfoImage( $this->getImage( $read, $erpProductId, 'info', $isQuickShip ) )
					->setCatImg( $this->getImage( $read, $erpProductId, 'catimage' ) )
					->setTiffImg( $this->getImageByType( $read, $erpProductId, 'TIFF' ) )
					->setProductTemplate( $this->getImage( $read, $erpProductId, 'template' ) )
					->setGiftBoxImage( $this->getGiftBoxImage( $read, $erpProductId ) );



				$product->save();
				$product->clearInstance();
			}
		}
	}

	public function getImageByType( $connection, $productId, $imageType ) {
		$images = $connection->fetchCol( "SELECT s_file_name FROM t_image WHERE s_image_type = '$imageType' AND i_owner_id = '$productId'" );
		$image = implode( '|', $images );
		return $image;
	}

	public function getImage( $connection, $productId, $role, $isQuickShip = NULL ) {
		//Fields have the possiblity to have multiple images.  Add more to array if you should lookup multiples
		$multipleImages = array( 'catimage', 'info' );

		if ( in_array( $role, $multipleImages ) ) {
			$images = $connection->fetchCol( "SELECT s_file_name FROM t_image WHERE i_owner_id = '$productId' AND s_role = '$role' AND s_file_name NOT LIKE '%tif%'" );

			//QuickShip gets a manual quick ship image added
			if($isQuickShip)
				$images = array_merge(['man/quick-ship.jpg'],$images);

			//Keep with logic already in place with these
			$image = implode( '|', $images );



		} else {
			$image = $connection->fetchOne( "SELECT s_file_name FROM t_image WHERE i_owner_id = '$productId' AND s_role = '$role' AND (s_image_type != 'TIFF' OR s_image_type != 'PDF')" );
		}

		//If image still not set put to empty string to remove
		if(empty($image)) {
			$image = '';
		}

		return $image;
	}

	public function getGiftBoxImage($connection, $productId) {
		$sql   = "SELECT COALESCE( p.k_image_id_package,
   (SELECT n.k_image_id
     FROM t_code n
     WHERE n.s_column = 's_packaging'
     AND n.s_value = p.s_packaging))
FROM t_product p
WHERE p.k_product_id = '$productId'";
		$image = $connection->fetchOne($sql);

		//If image still not set put to empty string to remove
		if (empty($image)) {
			$image = '';
		}

		return $image;
	}
}

$shell = new Image_Sync();

$shell->run();
echo 'Done';
