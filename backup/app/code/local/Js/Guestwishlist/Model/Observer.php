<?php

class Js_Guestwishlist_Model_Observer
{
	public function moveGuestWishlist() {
		//Initial Vars
		$_helper = Mage::helper('js_guestwishlist');
		$customer = Mage::getSingleton('customer/session')->getCustomer();

		//The second parameter will create the wishlist if set to true
		$wishlist = Mage::getModel('wishlist/wishlist')->loadByCustomer($customer, true);

		if($wishlist->getId() && $_helper->getWishlistKey()) {
			$collection = Mage::getModel('js_guestwishlist/guest_wishlist')->getCollection()
			                  ->addFieldToFilter( 'key', $_helper->getWishlistKey() );
			$buyRequest = new Varien_Object(array());

			foreach($collection as $item) {
				$result = $wishlist->addNewItem($item->getProductId(), $buyRequest);
				Mage::getModel( 'js_guestwishlist/guest_wishlist' )->deleteByProductId($_helper->getWishlistKey(), $item->getProductId());
			}

			$wishlist->save();
		}

	}
}