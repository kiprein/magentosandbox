<?php

class Js_Customer_Model_Observer {
	/**
	 * The old developer made ALL of the pages category pages for some reason.  Because of this the regular
	 * customer dashboard is now located at "dashboard" instead of the normal path.  This means that anyone
	 * can access this section.  This is the easiest fix for now since I can't figure out the redirect or why
	 * someone would do that.
	 *
	 * @param $observer
	 */
	public function accountRedirect( $observer ) {
		$urlString = Mage::helper( 'core/url' )->getCurrentUrl();
		$parseUrl  = Mage::getSingleton( 'core/url' )->parseUrl( $urlString );
		$path      = $parseUrl->getPath();
		$accountPaths = array('/customer/account/', '/customer/account', '/customer/account/index/', '/customer/account/index');

		//If logged out customer tries to access dashboard redirect to home page
		if ( ! Mage::getSingleton( 'customer/session' )->isLoggedIn() && $path === '/dashboard' ) {
			$redirectUrl = Mage::getUrl();
			$response    = Mage::app()->getFrontController()->getResponse();
			$response->setRedirect( $redirectUrl );
			$response->sendResponse();
			exit;
		} elseif ( Mage::getSingleton( 'customer/session' )->isLoggedIn() && in_array($path, $accountPaths) ) {
			//If logged in customer tries to access base Magento account dashboard need to redirect to custom dashboard
			$redirectUrl = Mage::getUrl( 'dashboard' );
			$response    = Mage::app()->getFrontController()->getResponse();
			$response->setRedirect( $redirectUrl );
			$response->sendResponse();
			exit;
		}
	}
}