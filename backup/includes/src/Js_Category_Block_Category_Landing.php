<?php

class Js_Category_Block_Category_Landing extends Mage_Core_Block_Template {

	public function getLandingPageCategories( $parentId ) {

		$collection = $this->helper( 'js_category' )->getCategoryChildrenCollection( $parentId, true );
		return $collection;
	}

	public function getLandingImage( $_category, $_mediaUrl ) {
		$landingImage = '';

		if ( ! empty( $_category->getCategoryLandingImage() ) ) {
			$landingImage = '<img alt="' . $this->escapeHtml( $_category->getName() ) . '" class="" src="' . $_mediaUrl . $_category->getCategoryLandingImage() . '">';
		} else {
			$landingImage = '<img alt="' . $this->escapeHtml( $_category->getName() ) . '" class="" src="' . Mage::getDesign()->getSkinUrl( 'images/green-placeholder.png', array( '_secure' => true ) ).'">';
       }

		return $landingImage;
	}

	public function categoryStaticBlock($_category) {
		$content = '';

		$mode  = $_category->getDisplayMode();
		if($mode == 'PAGE' || $mode == 'PAGE_AND_PRODUCTS'){
			//get static block id
			$page = $_category->getLandingPage();

			//cms block
			$cmsBlock = Mage::getModel('cms/block')->load($page);

			//retrieve cms block data
			$content = $cmsBlock->getContent(); //get entire content of cms block

		}
		return $content;
	}
}