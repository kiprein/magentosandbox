<?php
/**
 * Created by PhpStorm.
 * User: fab_5
 * Date: 6/15/2018
 * Time: 10:17 AM
 */ 
class Js_Menu_Model_Resource_Menu extends Mage_Core_Model_Resource_Db_Abstract
{

    protected function _construct()
    {
        $this->_init('js_menu/menu', 'id');
    }

}