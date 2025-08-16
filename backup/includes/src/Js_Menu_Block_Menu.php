<?php

class Js_Menu_Block_Menu extends Mage_Core_Block_Template {
	public $menuHelper;

	private $customerGroupClassMap = [
		'4' => 'tr', // Trophy Retailer
		'5' => 'dist', // Distributor
		'6' => 'emp', // Employee
	];

	/**
	 * Js_Menu_Block_Menu constructor.
	 */
	public function __construct() {
		parent::__construct();

		$this->menuHelper = Mage::helper('js_menu');
	}

	public function getParentMenu($menuType) {
		return $this->menuHelper->getParentMenu($menuType);
	}

	public function getChildMenu($parentId) {
		return $this->menuHelper->getChildMenu($parentId);
	}

	public function renderParentMenu($parentMenu, $menuCount) {
		$html = '';
		$parentClass = '';
		$hasChildren = '';
		$heightFix = '';
		$employeeGroup = array(4,6);
		$groupId = Mage::getSingleton('customer/session')->getCustomerGroupId();

		//Check if the current menu has any children
		$children = $this->getChildMenu($parentMenu->getId());
		$childrenCount = $children->count();
		if($childrenCount > 0) {
			$parentClass = 'parent';
			$hasChildren = 'has-children';

		}

		//Special class used to make the height of all ul's even if over a certain count
		if($childrenCount > 11) {
			$heightFix = 'height-fix';
		}

		//Any special logic for parent url
		$parentUrl = $this->menuHelper->menuUrl($parentMenu);
		$parentMenuClasses = $parentMenu->getClass();
		$parentMenuClassesExploded = array_map('trim', explode(' ', $parentMenuClasses));
		$parentMenuGroupClassesPresent = array_intersect(array_values($this->customerGroupClassMap), $parentMenuClassesExploded);

		// Check if this menu item should be visible to the current customer group
		if(count($parentMenuGroupClassesPresent) > 0){
			if(isset($this->customerGroupClassMap[$groupId])){
				if(!in_array($this->customerGroupClassMap[$groupId], $parentMenuGroupClassesPresent)){
					// This menu item is not visible to the current customer group
					return '';
				}
			} else {
				// User is not logged in, so they can't see this menu item
				return '';
			}
		}


		$html .= '<li class="level0 nav-'.$menuCount.' '.$parentClass.' '.$parentMenuClasses.'">';
		$html .= '<a target="'.$parentMenu->getTarget().'" href="'.$parentUrl.'" class="level0 '.$hasChildren.'">'.$parentMenu->getTitle().'</a>';

		if($childrenCount > 0) {
			$html .= '<ul class="level0 first-list '.$heightFix.'">';
			$j = 0;
			foreach ($children as $child) {

				$childMenuClasses = $child->getClass();
				$childMenuClassesExploded = array_map('trim', explode(' ', $childMenuClasses));
				$childMenuGroupClassesPresent = array_intersect(array_values($this->customerGroupClassMap), $childMenuClassesExploded);
				if(count($childMenuGroupClassesPresent) > 0){
					
					if(isset($this->customerGroupClassMap[$groupId])){
						if(!in_array($this->customerGroupClassMap[$groupId], $childMenuGroupClassesPresent)){
							// This menu item is not visible to the current customer group
							continue;
						}
					} else {
						// User is not logged in, so they can't see this menu item
						continue;
					}
				}

				//Hide trophy price guide from all other groups
				$customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
				if($child->getTitle() == 'Trophy Retailer Price Guide' && !in_array($customerGroupId,$employeeGroup)) {
					continue;
				}

				if($j % 12 == 0) {
					$html .= '</ul><ul class="level0 second-list position-'.$j.' '.$heightFix.'">';
				}

				//Any special logic for child url
				$childUrl = $this->menuHelper->menuUrl($child);

				$html .= '<li class="level1 '.$childMenuClasses.'">';
				$html .= '<a target="'.$child->getTarget().'" class="level1" href="'.$childUrl.'">'.$child->getTitle().'</a>';
				$html .= '</li>';
				$j++;
			}
			$html .= '</ul>';
		}

		//Close everything up
		$html .= '</li>';

		return $html;
	}
}