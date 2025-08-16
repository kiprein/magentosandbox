<?php

class Js_Search_Model_Observer
{
	/**
	 * The client needed an easy way to redirect the user back to the search page when on a product.  The reason
	 * for this is they have a previous / next links on the product page that cycle through the current category
	 * and instead of clicking the back button a bunch the user should only be a click away from the search.
	 *
	 * @param $observer
	 * @return $this
	 */
	public function searchCheck($observer) {
		$fullRoute = Mage::app()->getRequest()->getControllerName().'/'. Mage::app()->getRequest()->getRouteName().'/'.Mage::app()->getRequest()->getActionName();

//		Mage::log('Controller Name: '. Mage::app()->getRequest()->getControllerName());
//		Mage::log('Action Name: '. Mage::app()->getRequest()->getActionName());
//		Mage::log('Route Name: '. Mage::app()->getRequest()->getRouteName());

		if($fullRoute == 'category/catalog/view' || $fullRoute == 'result/catalogsearch/index') {
			Mage::getSingleton('core/session')->setSearchUrl(Mage::helper('core/url')->getCurrentUrl());
		}

		return $this;
	}
}