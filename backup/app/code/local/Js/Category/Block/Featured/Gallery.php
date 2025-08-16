<?php

class Js_Category_Block_Featured_Gallery extends Mage_Core_Block_Template {
	public function getGalleries() {
		$collection = Mage::getModel('js_category/featured_gallery')
			->getCollection()
			->addFieldToFilter('active', 1)
			->setOrder('sort_order', 'ASC');

		return $collection;
	}
}