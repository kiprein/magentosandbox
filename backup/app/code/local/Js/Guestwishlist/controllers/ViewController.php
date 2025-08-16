<?php

class Js_Guestwishlist_ViewController extends Mage_Core_Controller_Front_Action
{
	public function preDispatch()
	{
		parent::preDispatch();
		$route = $this->getRequest()->getRouteName();

		//Need to blocked logged in users from every getting to guest url
		if($route == 'js_guestwishlist' && Mage::getSingleton('customer/session')->isLoggedIn()) {
			return $this->_redirect('wishlist');
		}
	}

	public function indexAction() {
		$this->loadLayout();
		$this->renderLayout();
	}
}