<?php

class Js_Banners_Model_Resource_Banners_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
	protected function _construct()
	{
		$this->_init('js_banners/banners');
	}
}
