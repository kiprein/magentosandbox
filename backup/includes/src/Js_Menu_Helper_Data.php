<?php
/**
 * Created by PhpStorm.
 * User: fab_5
 * Date: 6/14/2018
 * Time: 3:58 PM
 */ 
class Js_Menu_Helper_Data extends Mage_Core_Helper_Abstract {

	public function getMenuOptions() {
		$options = array(0 => 'None');
		$menuItems = Mage::getModel('js_menu/menu')->getCollection()
			->addFieldToFilter('parent_id', 0);

		foreach($menuItems as $menuItem) {
			$options[$menuItem->getId()] = $menuItem->getTitle();
		}

		return $options;
	}

	public function getMenuType($value) {
		$value = explode(',', $value);
		return json_encode($value);
	}

	public function getPermission($value) {
		if($value == '') {
			$value = 'guest';
		}

		return $value;
	}

	public function getTarget($value) {
		if($value == '') {
			$value = '_self';
		}

		return $value;
	}

	public function getParentMenu($menuType) {
		$menuItems = Mage::getModel('js_menu/menu')->getCollection()
			->addFieldToFilter('parent_id', 0)
			->addFieldToFilter('active', 1)
			->addFieldToFilter('menu_type', array('like' => '%'.$menuType.'%'))
			->setOrder('position', 'ASC');

		return $menuItems;
	}

	public function getChildMenu($parentId) {
		$menuItems = Mage::getModel('js_menu/menu')->getCollection()
			->addFieldToFilter('active', 1)
			->addFieldToFilter('parent_id', $parentId)
			->setOrder('position', 'ASC');;

		return $menuItems;
	}

	public function menuUrl($menuItem) {
		$url = $menuItem->getUrl();
		$loggedIn = Mage::getSingleton('customer/session')->isLoggedIn();


		if($menuItem->getTitle() == 'Dealer Tools' && $loggedIn) {
			$url = Mage::getUrl('dashboard');
		}

		//HARDCODED Trophy Retailer Price Guide only available to trophy retailers and employees, keep it hidden when not logged in or
// when logged in as a distributor https://www.crystal-d.com/price_guide/mobile/index.html

		return $url;
	}
}