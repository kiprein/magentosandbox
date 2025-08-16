<?php
/**
 * Created by PhpStorm.
 * User: Jon Saverda
 * Date: 3/10/2017
 * Time: 7:51 AM
 */ 
class Js_Guestwishlist_Model_Resource_Guest_Wishlist extends Mage_Core_Model_Resource_Db_Abstract
{

    protected function _construct()
    {
        $this->_init('js_guestwishlist/guest_wishlist', 'id');
    }

	public function deleteByProductId($key, $productId)
	{
		$table = $this->getMainTable();
		$where = array();
		$where[] =  $this->_getWriteAdapter()->quoteInto('`key` = ?',$key);
		$where[] =  $this->_getWriteAdapter()->quoteInto('`product_id` = ?', $productId);
		$result = $this->_getWriteAdapter()->delete($table, $where);
		return $result;
	}
}