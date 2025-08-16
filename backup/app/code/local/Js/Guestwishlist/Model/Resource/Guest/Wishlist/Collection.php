<?php
/**
 * Created by PhpStorm.
 * User: Jon Saverda
 * Date: 3/10/2017
 * Time: 7:51 AM
 */ 
class Js_Guestwishlist_Model_Resource_Guest_Wishlist_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('js_guestwishlist/guest_wishlist');
    }

}