<?php
 
class Js_Banners_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getBannerUrl()
	{
		return Mage::getBaseUrl( Mage_Core_Model_Store::URL_TYPE_MEDIA ) . 'banners/';
	}
}
