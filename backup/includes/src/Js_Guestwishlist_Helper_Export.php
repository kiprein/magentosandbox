<?php

class Js_Guestwishlist_Helper_Export extends Mage_Core_Helper_Abstract {
	public function getCsvHeader()
	{
		$header = array(
			'Customer Name',
			'Customer Email',
			'Date Sent',
			'Item',
			'Item Number'
		);

		return $header;
	}

	/**
	 * Build the csv of all the wishlist users base on the user type
	 * @param $userType - guest / general
	 * @param $name - filename
	 *
	 * @return array
	 */
	public function generateWishlistExport($userType, $name) {
		$io = new Varien_Io_File();
		$path = Mage::getBaseDir('var') . DS . 'export' . DS;
		$file = $path . DS . $name;
		$io->setAllowCreateFolders(true);
		$io->open(array('path' => $path));
		$io->streamOpen($file, 'w+');
		$io->streamLock(true);

		//Start by adding the header to the csv
		$io->streamWriteCsv($this->getCsvHeader());

		//Need to get all of the customers
		$wishlists = Mage::getModel('js_guestwishlist/wishlist_sent')->getCollection();
		$wishlists->addFieldToFilter('type', array('eq' => $userType));

		foreach($wishlists as $wishlist) {
			$wishlistItems = unserialize($wishlist->getProductInfo());
			$_resource = Mage::getSingleton('catalog/product')->getResource();

			foreach($wishlistItems as $productId) {
				$dateSent = date('m/d/Y', strtotime($wishlist->getDateSent()));
				$productName = $_resource->getAttributeRawValue($productId,  'name', Mage::app()->getStore());
				$sku = $_resource->getAttributeRawValue($productId,  'sku', Mage::app()->getStore());

				$wishlist_array = array(
					$wishlist->getName(),
					$wishlist->getEmail(),
					$dateSent,
					$productName,
					$sku
				);
				$io->streamWriteCsv($wishlist_array);
			}

			//Add line separator
			$wishlist_array = array(
				'',
				'',
				'',
				''
			);
			$io->streamWriteCsv($wishlist_array);
		}

		return array(
			'type'  => 'filename',
			'value' => $file,
			'rm'    => false // can delete file after use
		);
	}
}