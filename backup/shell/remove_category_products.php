<?php
if(!isset($_SERVER['SHELL'])){
    echo "Ah ah ah, you didn\'t say the magic word!";
    exit;
}

/**
 * Script to remove category products
 */
require_once( 'abstract.php' );

class Remove_Category_Products extends Mage_Shell_Abstract {

	public function run() {
		ini_set( 'memory_limit', '4000M' );
		$_helper   = Mage::helper( 'js_import' ); //Js_Import_Helper_Data

		$resource = Mage::getSingleton( 'core/resource' );
		$read     = $resource->getConnection( 'js_import_read' );
		$removeProducts = $read->fetchAll( 'SELECT del.*,
						cat.s_category_name,
						cat.k_group_id FROM `t_cat_prod_deleted` del
						LEFT JOIN `t_category` cat
						ON del.k_category_id = cat.k_category_id
						WHERE del.i_magento_id <> 0
						AND del.dt_updated > date_sub(now(),interval 32 minute)' );

		foreach($removeProducts as $removeProduct) {
			try {
				$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$removeProduct['s_item_num']);
				if ($product) {
					$productId = $product->getId();
					/**
					 * If category is one being used for an attribute, we need to update the product attribute
					 */
					if(in_array( intval($removeProduct['k_group_id']) , [6,9,5,15])){
						$attributeCode = $_helper->getAttributeCode( $removeProduct['k_group_id'] );
						$categoryName = trim( str_replace( '&trade;', '™', $removeProduct['s_category_name']) ) ;
						$id = $_helper->attributeValueExists( $attributeCode, $categoryName);
						if($attributeCode && $id){
							//Get old value
							$id = strval( $id ); 
							$value = $product->getData( $attributeCode );
							if($value){
								echo "Product: " . $removeProduct['s_item_num'] . " Value: " . $value . " ID: " . $id;
								$values = explode(',', $value);
								$key = array_search( $id, $values);
								if($key !== FALSE){
									//Remove id from old value
									unset($values[$key]);
									//Save new value
									echo " New Value: " . implode( ',', $values) . "\n";
									$product->setData( $attributeCode, implode( ',', $values));
									$product->save();
									$product->clearInstance();
								} else {
									echo " NO MATCH\n";
								}
							} else {
								echo "No value for " . $removeProduct['s_item_num'] . "\n";
							}
							
						}
					}
					Mage::getSingleton('catalog/category_api')->removeProduct($removeProduct['i_magento_id'], $productId);
				}
			} catch (Exception $e) {
				//Skip over any products that are already removed
				continue;
			}
		}
	}
}

$shell = new Remove_Category_Products();

$shell->run();
echo 'Done';
