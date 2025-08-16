<?php
/**
 * Created by PhpStorm.
 * User: Jon Saverda
 * Date: 3/10/2017
 * Time: 7:51 AM
 */ 
class Js_Guestwishlist_Model_Guest_Wishlist extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        $this->_init('js_guestwishlist/guest_wishlist');
    }

	public function deleteByProductId($key, $productId)
	{
		return $this->getResource()->deleteByProductId($key, $productId);
	}

}