<?php
/**
 * Created by PhpStorm.
 * User: fab_5
 * Date: 8/23/2018
 * Time: 12:32 PM
 */ 
class Js_Category_Model_Resource_Featured_Gallery_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('js_category/featured_gallery');
    }

}