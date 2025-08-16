<?php
/**
 * Created by PhpStorm.
 * User: Jon Saverda
 * Date: 1/27/2017
 * Time: 6:05 PM
 */ 
class Js_Utility_Helper_Data extends Mage_Core_Helper_Abstract {

	public function getCustomer() {
		return Mage::getSingleton( 'customer/session' )->getCustomer();
	}

	public function getCustomerGroup() {
		$customerGroup = Mage::getSingleton( 'customer/group' )->load( $this->getCustomer()->getGroupId() )->getData( 'customer_group_code' );

		return $customerGroup;
	}

	public function gitHeaderEnabled() {
		return Mage::getStoreConfig('design/header/git_branch_header');
	}

	public function separator($string, $separator = '|') {
		$string = explode($separator, $string);

		return array_filter($string);
	}

	/**
	 * Kept running into issue where I need the params minus the id and got sick of writing comments about it.  The
	 * id is usually the product or category and not needed currently.  Named a little differently to not mess with getParams.
	 * TODO pass in param to remove certain fields
	 */
	public function cleanUrlParams() {
		$params = Mage::app()->getRequest()->getParams();
		unset($params['id']);

		return $params;
	}

	public function formatParam($params, $value) {

		if(isset($params[$value])) {
			return $params[$value];
		} else {
			return '';
		}
	}

}