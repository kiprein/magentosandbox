<?php

class Js_Category_Block_Category_View extends Mage_Catalog_Block_Category_View
{
	/**
	 * Check if category display mode is "Static Block Only"
	 * For anchor category with applied filter Static Block Only mode not allowed
	 *
	 * Needed to add in a check if date or inventory is set since they are both custom attributes
	 *
	 * @return bool
	 */
	public function isContentMode()
	{
		$category = $this->getCurrentCategory();
		$res = false;
		if ($category->getDisplayMode()==Mage_Catalog_Model_Category::DM_PAGE) {
			$res = true;
			if ($category->getIsAnchor()) {
				$state = Mage::getSingleton('catalog/layer')->getState();
				if ($state && $state->getFilters()) {
					$res = false;
				}
			}
		}

		//Make sure list show if inventory or date set
		$params = Mage::helper('js_utility')->cleanUrlParams();
		if(isset($params['inventory']) || isset($params['available_before'])) {
			$res = false;
		}

		return $res;
	}
}