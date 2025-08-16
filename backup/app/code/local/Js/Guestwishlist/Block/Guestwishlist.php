<?php

class Js_Guestwishlist_Block_Guestwishlist extends Mage_Core_Block_Template {

	public function getGuestWishlist() {
		$wishlistKey = Mage::getSingleton( 'core/session' )->getWishlistKey();

		if($wishlistKey) {
			$collection = Mage::getModel('js_guestwishlist/guest_wishlist')->getCollection()
				->addFieldToFilter( 'key', $wishlistKey );

			return $collection;
		} else {
			return false;
		}
	}
}