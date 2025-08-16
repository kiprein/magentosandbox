<?php

class Js_Banners_Block_Banners extends Mage_Core_Block_Template
{
	public function getBanners()
	{
		$collection = Mage::getModel( 'js_banners/banners' )
		                  ->getCollection()
		                  ->addFieldToFilter('active', 1)
		                  ->setOrder( 'sort_order', 'ASC' );

		return $collection;
	}
}