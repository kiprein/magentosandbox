<?php

class Js_Category_Helper_Data extends Mage_Core_Helper_Abstract {

	public function getFeaturedGalleryUrl() {
		return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'featured_gallery/';
	}

	/**
	 * Need the ability to change the sort order based on the page.  $isLanding takes care of this
	 * @param $parentId
	 * @param bool $isLanding
	 *
	 * @return mixed
	 */
	public function getCategoryChildrenCollection($parentId, $isLanding = false) {
		$categories = Mage::getModel( 'catalog/category' )->getCollection()
		                  ->addAttributeToSelect( '*' )
		                  ->addAttributeToFilter( 'is_active', '1' )
		                  ->addAttributeToFilter( 'is_anchor', '1' )
		                  ->addAttributeToFilter( 'parent_id', array( 'eq' => $parentId ) );

		if ( $isLanding ) {
			$categories->addAttributeToSort( 'landing_page_order', 'ASC' );
		} else {
			$categories->addAttributeToSort( 'position', 'ASC' );
		}


		return $categories;
	}

	/**
	 * THe Amasty extension doesn't have a way to show children categories and allow multi select so use this and the
	 * child tree category function to make things work the way you need
	 *
	 * @param $parentId
	 * @param bool|false $isChild
	 * @param $separateFilter
	 *
	 * @return string
	 */
	public function getCategoryTree( $parentId, $isChild = false, $separateFilter = false ) {
		$categories = Mage::getModel( 'catalog/category' )->getCollection()
		                  ->addAttributeToSelect( '*' )
		                  ->addAttributeToFilter( 'is_active', '1' )
		                  ->addAttributeToFilter( 'is_anchor', '1' )
		                  ->addAttributeToFilter( 'parent_id', array( 'eq' => $parentId ) )
		                  ->addAttributeToSort( 'position', 'ASC' );

		$route = Mage::app()->getRequest()->getRouteName();
		$currentBaseUrl = $this->getCurrentBaseUrl();

		if ( !$separateFilter ) {
			$categories->addAttributeToFilter( 'separate_category_filter', '0' );
		} else {
			$categories->addAttributeToFilter( 'separate_category_filter', '1' );
		}

		$listClass = ( $isChild ) ? "sub-cat-list" : "cat-list ";
		$html      = '';
		$html .= '<div class="' . $listClass . '">';

		//Based on the separate filter there will be a different layout
		foreach ( $categories as $category ) {
			if($separateFilter) {
				$html .= '<div class="filter-group filter-wrapper">';
				$html .= '<dt class="separate-filter amshopby-collapsed"><div><div class="filter-icon"></div>' . $category->getName() . '</div></dt>';
			} else {
				$html .= '<div class="filter-group"><div class="parent-category collapsed">' . $category->getName() . '</div>';
			}

			//If the category is level 3 you need to return the children since this is the meat and potatoes of everything
			//Instead of calling the same function again I'm breaking it into a separate one so I have more control
			if ( $category->getChildren() != '' ) {
				if($separateFilter) {
					$html .= $this->getSeparateFilterCategoryTree( $category->getId(), $route, $currentBaseUrl );
				} else {
					$html .= $this->getChildrenCategoryTree( $category->getId(), $route, $currentBaseUrl );
				}

			}

			$html .= '</div>';
		}
		$html .= '</div>';

		return $html;
	}

	/**
	 * This is used to get the children categories of Occasion and product galleries
	 * @param $parentId
	 *
	 * @return string
	 */
	public function getChildrenCategoryTree( $parentId ) {
		$categories = Mage::getModel( 'catalog/category' )->getCollection()
		                  ->addAttributeToSelect( '*' )
		                  ->addAttributeToFilter( 'is_active', '1' )
		                  ->addAttributeToFilter( 'is_anchor', '1' )
		                  ->addAttributeToFilter( 'parent_id', array( 'eq' => $parentId ) )
		                  ->addAttributeToSort( 'position', 'ASC' );
		$html       = '<div class="sub-category-list">';

		$selectedCategories = $this->getSelectedCategories();

		//There was issues at one point with duplicates in the url.  This makes sure it doesn't happen
		if(!$selectedCategories) {
			$selectedArray      = array();
		} else {
			$selectedArray      = array_unique(explode( ',', $selectedCategories ));
		}

		$html .= '<ul>';
		foreach ( $categories as $category ) {
			//Add in class if selected
			$selected = '';
			$checkboxImg = Mage::getDesign()->getSkinUrl('images/unchecked.png', array('_secure'=>true));
			if ( in_array( $category->getId(), $selectedArray ) ) {
				$selected = 'selected';
				$checkboxImg = Mage::getDesign()->getSkinUrl('images/ch.png', array('_secure'=>true));
			}

			$html .= '<li>';
			$html .= '<a class="' . $selected . ' category-checkbox light-grey" data-category-id="' 
				. $category->getId() . '"><img class="checkbox-img" src="'.$checkboxImg.'" alt="Unchecked Box"/>' 
				. $category->getName() . '</a>';
			$html .= '</li>';
		}
		$html .= '</ul></div>';

		return $html;
	}

	/**
	 * At this point this is basically the same method as above.  I'm leaving this for now until all filtering is done since
	 * I've changed it so much.
	 *
	 * @param $parentId
	 *
	 * @return string
	 */
	public function getSeparateFilterCategoryTree( $parentId ) {
		$categories = Mage::getModel( 'catalog/category' )->getCollection()
		                  ->addAttributeToSelect( '*' )
		                  ->addAttributeToFilter( 'is_active', '1' )
		                  ->addAttributeToFilter( 'is_anchor', '1' )
		                  ->addAttributeToFilter( 'parent_id', array( 'eq' => $parentId ) )
		                  ->addAttributeToSort( 'position', 'ASC' );
		$html       = '<dd class="separate-sub-category-list"><div>';

		//Current categories selected
		$selectedCategories = $this->getSelectedCategories();

		//There was issues at one point with duplicates in the url.  This makes sure it doesn't happen
		if(!$selectedCategories) {
			$selectedArray      = array();
		} else {
			$selectedArray      = array_unique(explode( ',', $selectedCategories ));
		}

		//Setup the correct url with any other filters selected
		$currentUrl = Mage::getUrl('*/*/*', array('_use_rewrite' => true, '_forced_secure' => true));
		$urlParams = Mage::app()->getRequest()->getParams();

		//Remove the id as it's not need
		unset($urlParams['id']);

		$html .= '<ul>';
		foreach ( $categories as $category ) {
			//Add in class if selected
			$selected = '';
			$checkboxImg = Mage::getDesign()->getSkinUrl('images/unchecked.png', array('_secure'=>true));
			if ( in_array( $category->getId(), $selectedArray ) ) {
				$selected = 'selected';
				$checkboxImg = Mage::getDesign()->getSkinUrl('images/ch.png', array('_secure'=>true));
			}

			//Now that the selected check is done build the final url
			$html .= '<li>';
			$html .= '<a class="' . $selected . ' category-checkbox light-grey" data-category-id="' . $category->getId() . '"><img class="checkbox-img" src="'.$checkboxImg.'" />' . $category->getName() . '</a>';
			$html .= '</li>';
		}
		$html .= '</ul></div>';
		$html .= '<div class="filter-buttons"><a href="" class="button category-submit">Apply Filter</a>';
		$html .= '<a class="clear-filter" href="'.Mage::helper('js_product')->getRemoveUrl('cat').'">Clear Filter</a></div>';
		$html .= '</dd>';


		return $html;
	}

	/**
	 * Get the param of the currently select categories so you can set the right class on page reload
	 */
	public function getSelectedCategories() {
		return $selectedCategories = Mage::app()->getRequest()->getParam( 'cat' ) ?: false;
	}

	public function getCurrentBaseUrl() {
		$currentUrl = Mage::helper('core/url')->getCurrentUrl();
		$url = Mage::getSingleton('core/url')->parseUrl($currentUrl);

		return $url->getHost().$url->getPath();
	}
}