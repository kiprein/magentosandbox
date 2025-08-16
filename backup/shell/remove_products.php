<?php
if(!isset($_SERVER['SHELL'])){
    echo "Ah ah ah, you didn\'t say the magic word!";
    exit;
}

/**
 * Script to remove products from Magento that are no longer in goldmine
 */
require_once( 'abstract.php' );

class Remove_Products extends Mage_Shell_Abstract {

	/**
	 * Get all if the magento products
	 * @return object
	 *
	 */
	public function getProducts() {
		return $_productCollection = Mage::getModel('catalog/product')
		                          ->getCollection()
		                          ->addAttributeToSelect('sku');
	}

	public function run() {
		ini_set( 'memory_limit', '4000M' );

		$resource = Mage::getSingleton( 'core/resource' );
		$read     = $resource->getConnection( 'js_import_read' );

		$magentoProducts = $this->getProducts();
		$product   = Mage::getModel( 'catalog/product' );
		foreach($magentoProducts as $magentoProduct) {
			//Query the goldmine db and see if the item is still there
			$result = $read->fetchOne( 'SELECT k_product_id FROM `t_product` WHERE `s_item_num` = "'.$magentoProduct->getSku().'"' );

			//If the item is not found double check Magneto db again and than finally remove
			if(!$result) {
				$productId = $product->getIdBySku( $magentoProduct->getSku() );
				if($productId) {
					$product = Mage::getModel('catalog/product')->load($productId);
					echo $product->getName(). " removed\r\n";
					$product->delete();
				}
			}
		}
	}
}

$shell = new Remove_Products();

$shell->run();
echo 'Done';
