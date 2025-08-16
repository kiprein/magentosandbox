<?php

class Js_Category_Model_Observer {

	/**
	 * Due to the fact that the filters need to use the id and not the name to be able to have multiple selected
	 * We need a way to make sure the canonical url is correct.  Do this by checking the url params and then removing
	 * the current url and then add in the current category.
	 *
	 * I'm also using this to set the page title and meta description
	 *
	 * @param $observer
	 */
	public function categoryCanonicalFix($observer) {
		$block = $observer->getBlock();
		if($block instanceof Mage_Page_Block_Html_Head ) {
			$categoryParam = Mage::app()->getRequest()->getParam('cat');
			$categoryIds = explode(',', $categoryParam);

			if(count($categoryIds) == 1 && $categoryIds[0] > 0) {
				$url = Mage::registry('current_category')->getUrl();
				$block->removeItem('link_rel', $url);

				//Time to get the new url
				$_category = Mage::getModel('catalog/category')->load($categoryIds[0]);
				$block->addLinkRel('canonical', $_category->getUrl());
				$block->setTitle($_category->getName());

				if($_category->getMetaDescription()) {
					$block->setDescription($_category->getMetaDescription());
				}

			}  elseif (Mage::registry('current_product')) {
				$urlKey = Mage::registry('current_product')->getUrlKey();
				$url = Mage::getBaseUrl().$urlKey;
				$block->addLinkRel('canonical', $url);
			} else if (Mage::registry('current_category')) {
				$_category = Mage::registry('current_category');
				$url = $_category->getUrl();
				$block->addLinkRel('canonical', $url);
				$block->setTitle($_category->getName());
			}
		}
	}
}