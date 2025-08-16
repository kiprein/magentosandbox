<?php

class Js_Product_Block_Product_View extends Mage_Catalog_Block_Product_View {

	/**
	 * These are all of the fields currently used in the related products collection
	 * @return array
	 */
	public function displayFields() {
		$fields = array(
			'name',
			'dimension',
			'dimension2',
			'dimension3',
			'weight',
			'productquantity1',
			'productquantity2',
			'productquantity3',
			'productquantity4',
			'productquantity5',
			'retailprice1',
			'retailprice2',
			'retailprice3',
			'retailprice4',
			'retailprice5',
			'netprice1',
			'netprice2',
			'netprice3',
			'netprice4',
			'netprice5',
			'trophyblankprice1',
			'trophyblankprice2',
			'trophyblankprice3',
			'trophyblankprice4',
			'trophyblankprice5',
			'url_key',
		);

		return $fields;
	}

	/**
	 * There are multiple sliders under the included and additional option sections that all use the
	 * same options so I just moved them here to make the templates easier on myself.
	 *
	 * @return string
	 */
	public function getSliderOptions() {
		return 'auto: false,
                slideWidth: 120,
                adaptiveHeight: true,
                moveSlides: 1,
                slideMargin: 5,
                minSlides: 2,
				maxSlides: 3,
				prevText: \'<\',
				nextText: \'>\'';
	}

	public function formatPriceRowHeader($product, $groupId) {
		$mobileCheck = Mage::helper('js_product')->mobileCheck();

		$html = '<td class="sku">Item #</td>';
		if(!$mobileCheck) {
			$html .= '<td class="">Weight</td>';
			$html .= '<td class="">Dimensions</td>';
		}

		$html .= '<td class="">' . $product->getProductquantity1() . '</td>';
		$html .= '<td class="">' . $product->getProductquantity2() . '</td>';
		$html .= '<td class="">' . $product->getProductquantity3() . '</td>';

		if($product->getProductquantity4()) {
			if($groupId == 6) {
				$html .= '<td class="special-price">' . $product->getProductquantity4() . '</td>';
			} else {
				$html .= '<td class="special-price">' . $product->getProductquantity4() . ' +</td>';
			}
		}

		if ($groupId == 6) {
			$html .= '<td class="special-price">' . $product->getProductquantity5() . '</td>';
		}

		return $html;
	}

	public function formatPriceRow($product, $productType, $priceType, $groupId, $loggedIn) {
		$coreHelper = Mage::helper('core');
		$mobileCheck = Mage::helper('js_product')->mobileCheck();

		//Build out the price attribute you need to retrieve based on the price type
		$price1 = 'get' . $priceType . 'price1';
		$price2 = 'get' . $priceType . 'price2';
		$price3 = 'get' . $priceType . 'price3';
		$price4 = 'get' . $priceType . 'price4';
		$price5 = 'get' . $priceType . 'price5';

		if($productType == 'related') {
			$html = '<td class="sku"><a href="'.$product->getUrlKey().'">' . $product->getSku() . '</a></td>';
		} else {
			$html = '<td class="sku">' . $product->getSku() . '</td>';
		}

		if (!$mobileCheck) {
			$html .= '<td class="light-grey">' . floatval($product->getWeight()) . '</td>';
			$html .= '<td class="light-grey">' . $this->getDimensions($product, false) . '</td>';
		}

		//Only the first price row should display a dollar sign
		$html .= '<td class="price">' . $coreHelper->currency($product->$price1()) . '</td>';
		$html .= '<td class="price">' . number_format($product->$price2(), 2) . '</td>';
		$html .= '<td class="price">' . number_format($product->$price3(), 2) . '</td>';

		if($product->$price4()) {
			if($groupId == 6) {
				$html .= '<td class="special-price">' . number_format($product->$price4(), 2) . '</td>';
			} else {
				$html .= '<td class="special-price">Call</td>';
			}
		}

		if ($groupId == 6) {
			$html .= '<td class="special-price">' . number_format($product->$price5(), 2) . '</td>';
		}

		return $html;
	}

	/**
	 * Build out the dimensions for the current product.  There was a request to change which dimensions
	 * show in different areas.  To make a quick fix I added a displayAll flag that when set to true
	 * will show all dimensions instead of the first one.
	 * @param $product
	 * @return string
	 */
	public function getDimensions($product, $displayAll = true) {
		$dimensions = '';
		if ($product->getDimension()) {

			$dimensions .= $product->getDimension();
			if ($product->getDimension2() && $displayAll) {

				$dimensions .= ', ' . $product->getDimension2();
				if ($product->getDimension3()) {
					$dimensions .= ', ' . $product->getDimension3();
				}
			}
		}

		return $dimensions;
	}

	/**
	 * The product details are a big list of information from different spots.  I moved all the code here since it was
	 * super messy on the template page.
	 *
	 * @param $product
	 *
	 * @return string
	 */
	public function getProductDetails($product) {
		$productDetails = '';

		if ($product->getMaterialType()) {
			$productDetails .= '<li class="dark-grey"><span class="red">Material Type:</span> ' . $product->getMaterialType() . '</li>';
		}

		if ($product->getImprintShown()) {
			$productDetails .= '<li class="dark-grey"><span class="red">Imprint Shown:</span> ' . $product->getImprintShown() . '</li>';
		};

		$dimensions = '<span class="red">Dimension:</span> '.$this->getDimensions($product, true);
		$productDetails .= '<li class="dark-grey">' . $dimensions . '</li>';

		//Only check the next one in line if the previous value is present.  Not really much that can be done
		$imageArea = '<span class="red">Image Area:</span> ';
		if ($product->getImageArea()) {
			$imageArea .= $product->getImageArea();
			if ($product->getImageArea2()) {
				$imageArea .= ', ' . $product->getImageArea2();
				if ($product->getImageArea3()) {
					$imageArea .= ', ' . $product->getImageArea3();
				}
			}
		}
		$productDetails .= '<li class="dark-grey">' . $imageArea . '</li>';

		if ($product->getWeight()) {
			$productDetails .= '<li class="dark-grey"><span class="red">Weight:</span> ' . round($product->getWeight(), 2) . ' lbs</li>';
		};

		return $productDetails;
	}

	/**
	 * Different pricing gets shown passed on the user status
	 *
	 * @param $loggedIn
	 * @param $priceType
	 * @return string
	 */
	public function getActivePrice($loggedIn, $priceType, $groupId, $trophyOk = false) {
		if(!$loggedIn && $priceType == 'retail') {
			return 'active';
		}
		if($loggedIn && $priceType == 'trophy' && $groupId == 4 && $trophyOk) {
			return 'active';
		}
		if($loggedIn && $priceType == 'net' && in_array($groupId, Array( 5, 6))) {
			return 'active';
		}
		if($loggedIn && $priceType == 'net' && $groupId == 4 && !$trophyOk) {
			return 'active';
		}
	}

	/**
	 * This acts as a special row if there are related products.  Need to have the login check since
	 * price 5 is hidden from guest users
	 *
	 * @param $loggedIn
	 * @return string
	 */
	public function getRelatedTitle($groupId) {
		$mobileCheck = Mage::helper('js_product')->mobileCheck();

		$html = '<td colspan="5" class="related-title">';
		$html .= 'More Sizes<span class="fs-12 light-grey italic"> First column quantities may vary.</span>';
		$html .= '</td>';
//		$html .= '<td>&nbsp;</td>';
//		$html .= '<td>&nbsp;</td>';
//		$html .= '<td>&nbsp;</td>';
//		$html .= '<td>&nbsp;</td>';

		if(!$mobileCheck) {
			$html .= '<td>&nbsp;</td>';
			$html .= '<td>&nbsp;</td>';
		}

		if($groupId == 6 && !$mobileCheck) {
			$html .= '<td>&nbsp;</td>';
		}

		return $html;
	}
}