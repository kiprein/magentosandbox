<?php

if(!isset($_SERVER['SHELL'])){
    echo "Ah ah ah, you didn\'t say the magic word!";
    exit;
}

/**
 * Syncs categories from product database into the magento system.
 * 
 * runs via crontab every 4 hours.
 * 
 * This script could use an overhaul at some point.
 * 
 * @author John Severda. Modified by Mark Wickline
 */
require_once( 'abstract.php' );

class Category_Sync extends Mage_Shell_Abstract {

	public function getMagentoCategoryId($name) {
		$category = Mage::getResourceModel('catalog/category_collection')
			->addFieldToFilter('name', $name)
			->addAttributeToFilter('parent_id', array('eq' => 4))
			->getFirstItem();

		return $category->getId();
	}

	/**
	 * The group id relates to a child category of the Awards and Gifts
	 * The key can be found in the t_group table in the product DB
	 */
	public function getParentCategoryMapping() {
		$categoryMappings = array(
			1  => 'Product Gallery',
			5  => 'Functionality',
			6  => 'Shapes & Styles',
			9  => 'Material Type',
			11 => 'Occasion',
			15 => 'Imprint Process',
			25 => '3D Subsurface',
			28 => 'Plaques',
		);

		$magentoCategories = array();
		foreach ( $categoryMappings as $key => $name ) {
			$categoryId = $this->getMagentoCategoryId( $name );

			if ( $categoryId ) {
				$magentoCategories[ $key ] = $categoryId;
			}
		}

		return $magentoCategories;
	}

	public function flushCategoryProducts() {
		$magentoCategories = Mage::getModel( 'catalog/category' )->getCollection()
		                         ->addAttributeToSelect( '*' )
		                         ->addAttributeToFilter( 'is_active', '1' )
		                         ->addAttributeToFilter( 'is_anchor', '1' )
		                         ->addAttributeToFilter( 'parent_id', array( 'eq' => 4 ) )
		                         ->addAttributeToSort( 'position', 'ASC' );

		foreach ( $magentoCategories as $magentoCategory ) {
			$childrenCategories = Mage::getModel( 'catalog/category' )->getCollection()
			                          ->addAttributeToSelect( '*' )
			                          ->addAttributeToFilter( 'is_active', '1' )
			                          ->addAttributeToFilter( 'is_anchor', '1' )
			                          ->addAttributeToFilter( 'parent_id', array( 'eq' => $magentoCategory->getId() ) )
			                          ->addAttributeToSort( 'position', 'ASC' );

			foreach ( $childrenCategories as $childCategory ) {
				$category = Mage::getModel( 'catalog/category' )->load( $childCategory->getId() );
				echo $category->getName() . "\r\n";
				$category->setPostedProducts( array() );
				$category->save();
				$category->clearInstance();
			}
		}
	}

	public function getImprintProcessIds(){
		$attribute = [
			174 => "Deep Etch",
			167 => "3D Subsurface",
			169 => "Illumachrome™",
			178 => "Laser Engraving",
			177 => "Sublimation",
			168 => "Colorfill"
		];
		$processIds = [];
		foreach($attribute as $optionId => $optionString){
			$processIds[$optionId] = $this->attributeValueExists('imprint_processes',  $optionString);
		}
		return $processIds;
	}

	public function getImprintProcessId($catId){
		$string = "";
		switch($catId){
			case 174 :
			$string = "Deep Etch";
			break;
			case 167 :
			$string = "3D Subsurface";
			break;
			case 169 :
			$string = "Illumachrome™";
			break;
			case 178 :
			$string = "Laser Engraving";
			break;
			case 177 :
			$string = "Sublimation";
			break;
			case 168 :
			$string = "Colorfill";
				break;
			default:
				return false;
		}
		return $this->attributeValueExists('imprint_processes', $string);
	}

	//https://magento.stackexchange.com/questions/105366/set-dropdown-value-using-text-value
	function attributeValueExists($argAttribute, $argValue)
	{
		$attributeModel        = Mage::getSingleton('eav/entity_attribute');
		$attributeOptionsModel = Mage::getSingleton('eav/entity_attribute_source_table');
		
		$attribute             = $attributeModel->loadByCode('catalog_product', $argAttribute);
		
		$attributeOptionsModel->setAttribute( $attribute );
		$options               = $attributeOptionsModel->getAllOptions(false);

		foreach ($options as $option) {
			if ($option['label'] == $argValue) {
				return $option['value'];
			}
		}

		return false;
	}

	public function run() {
		ini_set( 'memory_limit', '4000M' );

		$_helper   = Mage::helper( 'js_import' );
		$resource  = Mage::getSingleton( 'core/resource' );
		$read      = $resource->getConnection( 'js_import_read' );
		$coreRead  = $resource->getConnection( 'core_read' );
		$coreWrite = $resource->getConnection( 'core_write' );
		$_productResource = Mage::getSingleton('catalog/product')->getResource();


		//Flush all the products out of main product category if something goes wrong
		//$this->flushCategoryProducts();
		
		//get ids for product attribute imprint_processes
		$imprintDropdownIds = $this->getImprintProcessIds();

		//Need to get the attribute id of the visibilty attribute to make sure works every where
		$attributeId = $coreRead->fetchOne( "SELECT attribute_id FROM `eav_attribute` WHERE `attribute_code` = 'visibility'" );

		/**
		 * Start by looping through all the main parents.
		 * This is need for when saving an extra product information since you need to set the parent id otherwise
		 * shit breaks
		 */
		$productParentCategories = $this->getParentCategoryMapping();
		foreach ( $productParentCategories as $productParentCategoryId => $magentoParentId ) {
			echo "PARENT: {$productParentCategoryId}\n";
			//First query the main categories based on the parent id
			$results = $read->fetchAssoc( "SELECT * FROM `t_category` WHERE `k_group_id` = '$productParentCategoryId' AND (i_magento_id IS NOT NULL OR i_magento_id != '')" );

			$product          = Mage::getModel( 'catalog/product' );
			$categoryApiModel = Mage::getSingleton( 'catalog/category_api' );
			$imprintStorage = new stdClass();

			foreach ( $results as $result ) {
				echo "Memory Usage: " . ( memory_get_peak_usage( true ) / 1024 / 1024 ) . " MiB\n";

				//Check to make sure Magento product exist
				$productCategoryId = $result['i_magento_id'];
				$magentoCategory   = Mage::getModel( 'catalog/category' )->load( $productCategoryId );
				$magentoCategoryId = $magentoCategory->getId();
				$magentoCategoryPosition = $result['i_seq'];

				if ( $magentoCategoryId ) {
					//Magento category logic here
					$magentoCategory->setDescription( $result['s_category_desc'] );
					$magentoCategory->setName( $result['s_category_name'] );
					$magentoCategory->setMetaTitle( $result['s_category_name'] );
					$magentoCategory->setMetaKeywords( $result['s_keywords'] );
					//$magentoCategory->setPosition( $magentoCategoryPosition );

					if($magentoCategoryPosition < 0) {
						$magentoCategory->setIsActive(0);
					} else {
						$magentoCategory->setIsActive(1);
						$magentoCategory->setIsAnchor(1);
					}

					$magentoCategory->save();



					//For some reason the parent id is not updated correctly so just use a direct query and call it a day
					$parentIdUpdate = "UPDATE `catalog_category_entity` SET `parent_id` = $magentoParentId WHERE entity_id = $productCategoryId";
					$positionUpdate = "UPDATE `catalog_category_entity` SET `position` = $magentoCategoryPosition WHERE entity_id = $productCategoryId";
					$coreWrite->query( $parentIdUpdate );
					$coreWrite->query( $positionUpdate );

					/**
					 * Next query all of the products in the given category and join the t_product table to get the sku
					 * so you can check if the category has any products assigned to it
					 */
					$goldmineCategoryProducts = $read->fetchAssoc( 'SELECT b.`k_product_id`, `s_item_num` FROM `t_cat_prod` AS a
INNER JOIN `t_product` AS b ON a.`k_product_id` = b.`k_product_id` WHERE a.`k_category_id` = "' . $result['k_category_id'] . '"' );
					if ( count( $goldmineCategoryProducts ) > 0 ) {
						foreach ( $goldmineCategoryProducts as $productDb ) {
							$productDbSku = $productDb['s_item_num'];
							$productDbId = $productDb['k_product_id'];

							//Finally check if the product is in Magento.  If found add it in
							$productInfo = $coreRead->fetchRow( 'SELECT `entity_id`, `sku` FROM `catalog_product_entity` WHERE `sku` = "' . $productDbSku . '"' );
							$productId   = $productInfo['entity_id'];
							$sku         = $productInfo['sku'];

							if ( $productId  ) {
								
								$position = 1;
								$visibility = 4;

								/**
								 * MGW 8/26/19 need to sync the imprint process with the categories chosen for 
								 * proper filter sorting. This has to be hardcoded for now.
								 */
								if($productParentCategoryId == 15 && isset( $imprintDropdownIds[$productCategoryId] ) ){
									$proccess = $imprintDropdownIds[$productCategoryId];
									/**
									 * Because imprint_processes is multislect we need to save the last selections again
									 * with the new selections. For that we store the product in an object and recal it again.
									 */
									if(isset($imprintStorage->$productId)){
										$imprintSelections = $imprintStorage->$productId . "," . strval( $proccess ) ;
										$imprintStorage->$productId = $imprintSelections;
									} else {
										$imprintSelections = $proccess;
										$imprintStorage->$productId = strval( $imprintSelections );
									}
									echo "Imprint " . $imprintSelections . " ";

									$currentProduct = $product->setStoreId( 1 )->load( $productId );
									echo "Current id " . $currentProduct->getId() . " ";

									$currentProduct->setData( 'imprint_processes', $imprintSelections );
									$currentProduct->save();
									$currentProduct->clearInstance();
								}

								$discontinued = $_productResource->getAttributeRawValue($productId,  'b_discontinued', Mage::app()->getStore());
								if ($discontinued) {
									$position = 800;
								}


								if ( ( strpos( $sku, "X" ) === 0 ) || ( strpos( $sku, "V" ) === 0 ) || ( strpos( $sku, "A" ) === 0 ) || ( strpos( $sku, "Z" ) === 0 ) ) {
									$position = 999;
									$visibility = 3;
								}

								//Check if anything is checked in the relation table
								$relations = $read->fetchOne( 'SELECT COUNT(*) FROM t_rel_prod AS a INNER JOIN t_relation AS b ON a.k_relation_id = b.k_relation_id WHERE k_product_id_child = "' . $productDbId . '" AND b_optional = 1');
								if( $relations > 0 ) {
									$position = 999;
									$visibility = 3;
								}

								$childRelations = $read->fetchOne( "SELECT COUNT(*) FROM t_rel_child AS a INNER JOIN t_relation AS b ON a.k_relation_id = b.k_relation_id WHERE a.k_product_id = '$productDbId'");
								if( $childRelations > 0 ) {
									$position = 999;
									$visibility = 3;
								}
								
								//if product is a Base, moved this down #MGW
								if ( strpos( $magentoCategory->getName(), 'Base' ) !== false ) {
									
									$position = 850;
									$visibility = 4;
								}

								
								
								
								echo "ID: " .  $productId . " Vis: " . $visibility  . " Pos: " . $position . " Category: " . $magentoCategoryId . "\r\n";
								
								$insert = 'INSERT INTO `catalog_category_product`
								SET `category_id` = "' . $magentoCategoryId . '",
                                    `product_id` = "' . $productId . '",
                                    `position` = "' . $position . '"
								ON DUPLICATE KEY UPDATE
                                    `position` = "' . $position . '"';
								$coreWrite->query( $insert );
								//$categoryApiModel->assignProduct( $magentoCategoryId, $productId, $position );

								$insertTopLevel = 'INSERT INTO `catalog_category_product`
								SET `category_id` = "' . 4 . '",
                                    `product_id` = "' . $productId . '",
                                    `position` = "' . $position . '"
								ON DUPLICATE KEY UPDATE
                                    `position` = "' . $position . '"';
								$coreWrite->query( $insertTopLevel );

								//This is a fix for weird search results ordering coming from the catalog search page
								///catalogsearch/result/?q=econo
								//I'm not really sure how the catalog product indexer table works at this point
								//so I'm just updating the position in that table for now
								$insertSearchFix = 'INSERT INTO `catalog_category_product_index`
								SET `category_id` = "' . 2 . '",
                                    `product_id` = "' . $productId . '",
                                    `position` = "' . $position . '",
                                    `store_id` = "' . 1 . '",
                                    `visibility` = "' . $visibility . '"
								ON DUPLICATE KEY UPDATE
                                    `position` = "' . $position . '"';
								$coreWrite->query( $insertSearchFix );

								//The same issue happens with when using the regular category filters as well with the index
								//so need to make sure to update root product category
								$insertCategoryFix = 'INSERT INTO `catalog_category_product_index`
								SET `category_id` = "' . 4 . '",
                                    `product_id` = "' . $productId . '",
                                    `position` = "' . $position . '",
                                    `store_id` = "' . 1 . '",
                                    `visibility` = "' . $visibility . '"
								ON DUPLICATE KEY UPDATE
                                    `position` = "' . $position . '"';
								$coreWrite->query( $insertCategoryFix );

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
						$magentoCategory->clearInstance();
					} else {
						//Mage::log($categoryName.' not found');
						//Category creation should go here
					}
				}
			}
		}
		$indexer = Mage::getSingleton('index/indexer');
		$attributesIndex = $indexer->getProcessByCode('catalog_product_attribute');
		$attributesIndex->reindexEverything();
		$categoryIndex = $indexer->getProcessByCode('catalog_category_product');
		$categoryIndex->reindexEverything();
	}
}

$shell = new Category_Sync();

$shell->run();
echo 'Done';
