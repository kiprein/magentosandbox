<?php
if(!isset($_SERVER['SHELL'])){
    echo "Ah ah ah, you didn\'t say the magic word!";
    exit;
}


require_once( 'abstract.php' );

class Non_Category_Sync extends Mage_Shell_Abstract {

	public function run() {
		ini_set( 'memory_limit', '4000M' );

		$_helper          = Mage::helper( 'js_import' );
		$resource         = Mage::getSingleton( 'core/resource' );
		$read             = $resource->getConnection( 'js_import_read' );
		$coreRead         = $resource->getConnection( 'core_read' );
		$coreWrite        = $resource->getConnection( 'core_write' );
		$_productResource = Mage::getSingleton( 'catalog/product' )->getResource();

		$_resource = Mage::getSingleton( 'catalog/product' )->getResource();

		$products = $coreRead->fetchAssoc( "SELECT * FROM catalog_product_entity" );

		//Need to get the attribute id of the visibilty attribute to make sure works every where
		$attributeId = $coreRead->fetchOne( "SELECT attribute_id FROM `eav_attribute` WHERE `attribute_code` = 'visibility'" );

		foreach ( $products as $product ) {
			$productId = $product['entity_id'];
			$sku       = $product['sku'];

			//Get all of the current categories
			$categories = $coreRead->fetchCol( "SELECT * FROM catalog_category_product WHERE product_id = '$productId'" );
			//Need to make a string so you can use in the category query
			$categoriesString = implode( ',', $categories );
			//Get the name of all of the current categories
			if(!empty($categoriesString)) {
				$categoryNames = $coreRead->fetchCol( "SELECT `value` as `name` FROM catalog_category_entity_varchar AS a
INNER JOIN catalog_category_entity AS b on a.entity_id = b.entity_id WHERE attribute_id = 41 AND b.entity_id IN ($categoriesString)" );
				//Not the cleanest but works for strpos and bases
				$categoryNamesString = $categoriesString = implode( ',', $categoryNames );

				/**
				 * Position logic
				 **/
				$position = 1;
				$visibility = 4;

				$discontinued = $_productResource->getAttributeRawValue( $productId, 'b_discontinued', Mage::app()->getStore() );
				$erpProductId = $_productResource->getAttributeRawValue( $productId, 'old_product_id', Mage::app()->getStore() );
				if ( $discontinued ) {
					$position = 800;
				}

				if ( strpos( $categoryNamesString, 'Base' ) !== false ) {
					$position = 850;
					$visibility = 3;
				}

				if ( ( strpos( $sku, "X" ) === 0 ) || ( strpos( $sku, "V" ) === 0 ) || ( strpos( $sku, "A" ) === 0 ) || ( strpos( $sku, "Z" ) === 0 ) ) {
					$position = 999;
					$visibility = 3;
				}

				//Check if anything is checked in the relation table
				$relations = $read->fetchOne( 'SELECT COUNT(*) FROM t_rel_prod AS a INNER JOIN t_relation AS b ON a.k_relation_id = b.k_relation_id WHERE k_product_id_child = "' . $erpProductId . '" AND b_optional = 1');
				if($relations > 0) {
					$position = 999;
					$visibility = 3;
				}

				$childRelations = $read->fetchOne( "SELECT COUNT(*) FROM t_rel_child AS a INNER JOIN t_relation AS b ON a.k_relation_id = b.k_relation_id WHERE a.k_product_id = '$erpProductId'");
				if($childRelations > 0) {
					$position = 999;
					$visibility = 3;
				}

				echo 'Assigning product: ' . $productId . "\r\n";

				$updatePosition = "UPDATE catalog_category_product SET position = '$position' WHERE product_id = '$productId'";
				$coreWrite->query( $updatePosition );

				//Update the visiblity as well
				$visibilityUpdate = 'INSERT INTO `catalog_product_entity_int`
								SET `entity_type_id` = "' . 4 . '",
                                    `attribute_id` = "' . $attributeId . '",
                                    `store_id` = "' . 0 . '",
                                    `entity_id` = "' . $productId . '",
                                    `value` = "' . $visibility . '"
								ON DUPLICATE KEY UPDATE
                                    `value` = "' . $visibility . '"';
				$coreWrite->query( $visibilityUpdate );
			}
		}
	}
}

$shell = new Non_Category_Sync();

$shell->run();
echo 'Done';
