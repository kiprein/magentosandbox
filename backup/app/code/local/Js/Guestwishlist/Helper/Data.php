<?php
/**
 * Created by PhpStorm.
 * User: Jon Saverda
 * Date: 3/10/2017
 * Time: 6:00 AM
 */ 
class Js_Guestwishlist_Helper_Data extends Mage_Core_Helper_Abstract {

	public function generateKey() {
		return substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);
	}

	public function getDefaultShareMessage() {
		return Mage::getStoreConfig('wishlist/general/wishlist_share_message');
	}

	public function getShareBccEmails() {
		return Mage::getStoreConfig('wishlist/general/bcc_wishlist');
	}

	public function getWishlistKey() {
		return Mage::getSingleton( 'core/session' )->getWishlistKey();
	}

	public function getGuestWishlist($wishlistKey = false) {
		if(!$wishlistKey) {
			$wishlistKey = $this->getWishlistKey();
		}

		if($wishlistKey) {
			$collection = Mage::getModel( 'js_guestwishlist/guest_wishlist' )->getCollection()
			                  ->addFieldToFilter( 'key', $wishlistKey );
			return $collection;
		} else {
			return array();
		}
	}

	public function getGuestWishlistCount() {
		$wishlistKey = $this->getWishlistKey();
		if($wishlistKey) {
			$collection = Mage::getModel('js_guestwishlist/guest_wishlist')->getCollection()
			                  ->addFieldToFilter( 'key', $wishlistKey );

			return $collection->count();
		} else {
			return '';
		}
	}

	public function getCustomerWishlistCount() {
		$customer = Mage::getSingleton('customer/session')->getCustomer();
		$wishlist = Mage::getModel('wishlist/wishlist')->loadByCustomer($customer, true);

		return $wishlist->getItemsCount();
	}

	public function getWishlistCount() {
		if(Mage::getSingleton('customer/session')->isLoggedIn()) {
			return $this->getCustomerWishlistCount();
		} else {
			return $this->getGuestWishlistCount();
		}
	}

	/**
	 * Based on customer login get the current wishlist product ids to check if an item is already in the wishlist
	 * @return array
	 */
	public function getWishlistProductIds() {
		if ( Mage::getSingleton( 'customer/session' )->isLoggedIn() ) {
			$customer               = Mage::getSingleton( 'customer/session' )->getCustomer();
			$wishlist               = Mage::getModel( 'wishlist/wishlist' )->loadByCustomer( $customer, true );
			$wishListItemCollection = $wishlist->getItemCollection();
		} else {
			$wishlistKey = $this->getWishlistKey();

			$wishListItemCollection = array();
			if($wishlistKey) {
				$wishListItemCollection = Mage::getModel( 'js_guestwishlist/guest_wishlist' )->getCollection()
				                              ->addFieldToFilter( 'key', $wishlistKey );
			}
		}

		$wishlistProductIds = array();
		foreach ($wishListItemCollection as $item) {
			$wishlistProductIds[] = $item->getProductId();
		}

		return $wishlistProductIds;
	}

	public function getSearchUrl() {
		$referer = Mage::app()->getRequest()->getServer('HTTP_REFERER');

		if(strpos($referer, 'catalogsearch') !== false) {
			return $referer;
		}elseif (strpos($referer, 'products') === false) {
			$referer = '/products';
		}

		return $referer;
	}

	public function deleteWishlistFlag() {
		return Mage::getStoreConfig('wishlist/general/delete_wishlist_after_send');
	}

	/**
	 * Get the add to wishlist url based on user stage.
	 * Product page is used for the controller and changes the redirect
	 * @param $_product
	 * @param bool|false $productPage
	 *
	 * @return string
	 */
	public function wishlistAddUrl($_product, $productPage = false) {
		if ( Mage::getSingleton( 'customer/session' )->isLoggedIn() ) {
			$url = Mage::helper( 'wishlist' )->getAddUrlWithParams( $_product, array('product_page' => $productPage) );
		} else {
			$url = Mage::getUrl('guest-wishlist/index/addGuest', array('product_id' => $_product->getId(), 'sku' => $_product->getSku(), 'product_page' => $productPage));
		}

		return $url;
	}

	public function wishlistViewUrl() {
		if ( Mage::getSingleton( 'customer/session' )->isLoggedIn() ) {
			$url = Mage::getUrl('wishlist');
		} else {
			$url = Mage::getUrl('guest-wishlist/view');
		}

		return $url;
	}
}