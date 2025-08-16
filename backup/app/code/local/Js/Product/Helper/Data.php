<?php

/**
 * Created by PhpStorm.
 * User: Jon Saverda
 * Date: 1/27/2017
 * Time: 6:55 AM
 */
class Js_Product_Helper_Data extends Mage_Core_Helper_Abstract {

	/**
	 * Get the url for the product catalog from config setting
	 * @return mixed
	 */
	public function getCatalogUrl() {
		return Mage::getStoreConfig( 'catalog/site_options/catalog_url' );
	}

	/**
	 * Check if the user came from a category and see if there are previous and next links to display for the product
	 *
	 * @param $productId
	 *
	 * @return array
	 */
	public function getPreviousNextLinks( $productId ) {
		$_resource = Mage::getResourceSingleton('catalog/product');

		//Check if the user has come from a category page or search.  If the category is in the registry use that
		if ( Mage::registry( 'current_category' ) ) {
			$category = Mage::registry( 'current_category' );
		} else {
			$category = Mage::getModel( 'catalog/category' )->load( '4' );
		}

		//Get the current product collection
		$categoryProducts = $category->getProductCollection()
		                             ->addAttributeToSort( 'sku', 'asc' )
		                             ->addAttributeToFilter( 'status', 1 )
		                             ->addAttributeToFilter( 'b_offline', 0 )
		                             ->addAttributeToFilter( 'b_private', 0 );

		$categoryProductIds = $categoryProducts->getData(); // get all products from the category
		$categoryProducts = array();
		foreach ( $categoryProductIds as $categoryProductId ) {
			$categoryProducts[] = $categoryProductId['entity_id'];

		}
		$productPosition    = array_search( $productId, $categoryProducts ); // get position of current product
		$nextProductId      = $productPosition + 1;
		$previousProductId  = $productPosition - 1;

		if ( isset( $categoryProducts[ $previousProductId ] ) ) {
			$previousLink = $_resource->getAttributeRawValue( $categoryProducts[ $previousProductId ], 'url_key', Mage::app()->getStore() );
		} else {
			$previousLink = false;
		}

		if ( isset( $categoryProducts[ $nextProductId ] ) ) {
			$nextLink = $_resource->getAttributeRawValue( $categoryProducts[ $nextProductId ], 'url_key', Mage::app()->getStore() );
		} else {
			$nextLink = false;
		}
		return array( $previousLink, $nextLink );
	}

	/**
	 * There are a bunch of different spots where the images come in since they are not hosted on Magento.  This
	 * function is meant to just clean the template up.
	 */

	public function getImages( $_product ) {
		
		$images = array();
		//$alt = htmlspecialchars($_product->getShortDescription()); MGW UPDATE 8/15/19
		$alt = htmlspecialchars($_product->getImageAlt());

		if ( ! empty( $_product->getMainProductImage() ) ) {
			$images[] = '<img class=""
			      src="https://image.crystal-d.com/img/u494-y/jpg/' . $_product->getMainProductImage() . '.jpg"
			      data-big="https://image.crystal-d.com/img/142/' . $_product->getMainProductImage() . '.jpg"
			      alt="'. $alt .'" />';
		}

		if ( ! empty( $_product->getEtcImage() ) ) {
			$images[] = '<img class=""
			      src="https://image.crystal-d.com/images/proddb/current/' . $_product->getEtcImage() . '"
			      data-big="https://image.crystal-d.com/images/proddb/current/' . $_product->getEtcImage() . '" 
		          alt="'. $alt .'" />';
		}

		if (!empty($_product->getGiftBoxImage())) {
			$images[] = '<img class=""
			      src="https://image.crystal-d.com/img/i500-n/package/'. $_product->getSku().'.jpg"
			      data-big="https://image.crystal-d.com/img/i500-n/package/' . $_product->getSku() . '.jpg"
			      alt="' . $alt . '" />';

			//Adds the Quick Ship image if it's a Quick Ship Item
			if (  $_product->getData('quick_ship') == 397 ) {
				$images[] = '<img class=""
					  src="https://image.crystal-d.com/images/proddb/current/man/quick-ship.jpg"
					  data-big="https://image.crystal-d.com/images/proddb/current/man/quick-ship.jpg" 
					  alt="'. $alt .'" />';
			}	

			//Adds the black lasered giftbox image
            if(count(explode("|", $_product->getGiftBoxImage())) == 2){
                $images[] = '<img class=""
			      src="https://image.crystal-d.com/images/proddb/current/pkg/Dome_Paperweight_2010.png"
			      data-big="https://image.crystal-d.com/images/proddb/current/pkg/Dome_Paperweight_2010.png"
			      alt="' . $alt . '" />';
            }
		}


		if (!empty($_product->getData('three_sixty_video_1') || !empty($_product->getData('three_sixty_video_2')))) {

            $build = [];
            $videoOne = $_product->getData('three_sixty_video_1');
            $videoTwo = $_product->getData('three_sixty_video_2');

            if(!empty($videoOne)){
                foreach(unserialize($videoOne) as $row){
                    $build["{$row['i_seq']}"]['webm'] = $row['s_file_name'];
                }
            }
            if(!empty($videoTwo)){
                foreach(unserialize($videoTwo) as $row){
                    $build["{$row['i_seq']}"]['mp4'] = $row['s_file_name'];
                }
            }

            foreach($build as $video){
                $webm = isset($video['webm'])? $video['webm'] : 'throwError';
                $mp4 = isset($video['mp4'])? $video['mp4'] : 'throwError';
                $images[] = '
                <div class="video-wrapper" style="text-align: center; display: flex;">
                    <video autoplay loop muted
                        title= "' . $alt . '"
                        style="max-height: 100%; position: relative; max-width: 100%; margin: 0 auto;"
                        data-big="https://image.crystal-d.com/images/proddb/current/' . $webm . '"
                        data-big-backup="https://image.crystal-d.com/images/proddb/current/' . $mp4 . '">
                        <source src="https://image.crystal-d.com/images/proddb/current/'. $webm .'" type="video/webm">
                        <source src="https://image.crystal-d.com/images/proddb/current/'. $mp4 .'" type="video/mp4">
                        Your browser does not support the video tag
                    </video>
                    <img src="' . Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'images/CopyrightNotice.png"
                    style="position: absolute; left: 3px; bottom: 0; top: auto; max-width: 13%; height: auto;">
                </div>
                ';
            }
		}

		if ( ! empty( $_product->getInfoImage() ) ) {
			$infoImages = Mage::helper( 'js_utility' )->separator( $_product->getInfoImage() );

			foreach ( $infoImages as $infoImage ) {
				$images[] = '<img class=""
			      src="https://image.crystal-d.com/images/proddb/current/' . $infoImage . '"
			      data-big="https://image.crystal-d.com/images/proddb/current/' . $infoImage . '"
			       alt="'. $alt .'" />';
			}
		}

		if ( ! empty( $_product->getCatImg() ) ) {
			//This needs to get broken up
			$catImages = Mage::helper( 'js_utility' )->separator( $_product->getCatImg() );

			foreach ( $catImages as $catImage ) {
				$images[] = '<img class=""
			      src="https://image.crystal-d.com/images/proddb/current/' . $catImage . '"
			      data-big="https://image.crystal-d.com/images/proddb/current/' . $catImage . '" 
			       alt="'. $alt .'" />';
			}
		}


		//Remove any values that are empty
		$images = array_filter( $images );

		return $images;
	}

	public function getRetailPriceRange( $_product ) {
		$_helper = Mage::helper( 'core' );
		if ( ! empty( $_product->getRetailprice3() ) ) {
			$priceRange = $_helper->currency( $_product->getRetailprice3() ) . " - " . $_helper->currency( $_product->getRetailprice1() );
		} else {
			$priceRange = $_helper->currency( $_product->getRetailprice1() );
		}

		return $priceRange;
	}

	public function getDiscountLabel( $discount ) {
		$whole    = floor( $discount );      // 1
		$fraction = $discount - $whole; // .25

		$percent = substr( number_format( $fraction, 2 ), - 2 );

		return "Save " . $percent . "%!";
	}

	/**
	 * Array of all possible import processes currently
	 */
	public function getImportProcessArray() {
		return array( '73', '91', '9', '86', '52', '53', '4' );
	}

	/**
	 * Setup the information for the pricing chart on product view page
	 *
	 * @param $_product
	 *
	 * @return mixed
	 */
	public function getProductPricing( $_product, $loggedIn, $groupId ) {
		//Check if the first row has information.  If there are no prices returned just send back a message.
		if ( ! $_product->getProductquantity1() && ! $_product->getRetailprice1() ) {
			$pricing['error']   = true;
			$pricing['message'] = 'Please call to check for pricing information.';

			return $pricing;
		}

		$pricing[1]['qty']          = $_product->getProductquantity1() ?: '-------';
		$pricing[1]['retail_price'] = Mage::helper( 'core' )->currency( $_product->getRetailprice1() ) ?: '-------';
		$pricing[1]['net_price']    = Mage::helper( 'core' )->currency( $_product->getNetprice1() ) ?: '-------';
		$pricing[1]['trophy_price'] = Mage::helper( 'core' )->currency( $_product->getTrophyblankprice1() ) ?: '-------';

		$pricing[2]['qty']          = $_product->getProductquantity2() ?: '-------';
		$pricing[2]['retail_price'] = Mage::helper( 'core' )->currency( $_product->getRetailprice2() ) ?: '-------';
		$pricing[2]['net_price']    = Mage::helper( 'core' )->currency( $_product->getNetprice2() ) ?: '-------';
		$pricing[2]['trophy_price'] = Mage::helper( 'core' )->currency( $_product->getTrophyblankprice2() ) ?: '-------';

		$pricing[3]['qty']          = $_product->getProductquantity3() ?: '-------';
		$pricing[3]['retail_price'] = Mage::helper( 'core' )->currency( $_product->getRetailprice3() ) ?: '-------';
		$pricing[3]['net_price']    = Mage::helper( 'core' )->currency( $_product->getNetprice3() ) ?: '-------';
		$pricing[3]['trophy_price'] = Mage::helper( 'core' )->currency( $_product->getTrophyblankprice3() ) ?: '-------';

		$pricing[4]['qty'] = $_product->getProductquantity4() ?: '-------';
		if ( ! empty( $_product->getNetprice4() ) && (!$loggedIn || $groupId != 6) ) {
			$pricing[4]['net_price'] = "<span>Call for Special Pricing</span>";
		} else {
			if($groupId == 6) {
				$pricing[4]['net_price'] = Mage::helper( 'core' )->currency( $_product->getNetprice4() ) ?: '-------';
			} else {
				$pricing[4]['net_price'] = "<span>Call for Special Pricing</span>";
			}
		}

		if ( ! empty( $_product->getRetailprice4() ) && (!$loggedIn || $groupId != 6)  ) {
			$pricing[4]['retail_price'] = "<span>Call for Special Pricing</span>";
		} else {
			if($groupId == 6) {
				$pricing[4]['retail_price'] = Mage::helper( 'core' )->currency( $_product->getRetailprice4() ) ?: '-------';
			} else {
				$pricing[4]['retail_price'] = "<span>Call for Special Pricing</span>";
			}
		}

		if ( ! empty( $_product->getTrophyblankprice4() ) && (!$loggedIn || $groupId != 6) ) {
			$pricing[4]['trophy_price'] = "<span>Call for Special Pricing</span>";
		} else {
			if($groupId == 6) {
				$pricing[4]['trophy_price'] = Mage::helper( 'core' )->currency( $_product->getTrophyblankprice4() ) ?: '-------';
			} else {
				$pricing[4]['trophy_price'] = "<span>Call for Special Pricing</span>";
			}
		}

		if($groupId == 6) {
			$pricing[5]['qty']          = $_product->getProductquantity5() ?: '-------';
			$pricing[5]['retail_price'] = Mage::helper( 'core' )->currency( $_product->getRetailprice5() ) ?: '-------';
			$pricing[5]['net_price']    = Mage::helper( 'core' )->currency( $_product->getNetprice5() ) ?: '-------';
			$pricing[5]['trophy_price'] = Mage::helper( 'core' )->currency( $_product->getTrophyblankprice5() ) ?: '-------';
		}


		//Need to per

		return $pricing;
	}

	public function getTranslucentColors( $_product ) {

		$colors['Royal Blue']    = $_product->getBTransRoyalBlue() ? 'trans-royal-blue.png' : '';
		$colors['Light Blue']    = $_product->getBTransLightBlue() ? 'trans-light-blue.png' : '';
		$colors['Turquoise']     = $_product->getBTransTurquoise() ? 'trans-turquoise.png' : '';
		$colors['Dark Green']    = $_product->getBTransDarkGreen() ? 'trans-dark-green.png' : '';
		$colors['Kelly Green']   = $_product->getBTransKellyGreen() ? 'trans-kelly-green.png' : '';
		$colors['Lemon']         = $_product->getBTransLemon() ? 'trans-lemon.png' : '';
		$colors['Yellow Golden'] = $_product->getBTransYellowGolden() ? 'trans-yellow-golden.png' : '';
		$colors['Orange']        = $_product->getBTransOrange() ? 'trans-orange.png' : '';
		$colors['Red']           = $_product->getBTransRed() ? 'trans-red.png' : '';
		$colors['Hot Pink']      = $_product->getBTransHotPink() ? 'trans-hot-pink.png' : '';
		$colors['Brown']         = $_product->getBTransBrown() ? 'trans-brown.png' : '';

		return array_filter( $colors );
	}

	public function getOpaqueColors( $_product ) {
		$colors['Gold']   = $_product->getBOpaqGold() ? 'opaque-gold.png' : '';
		$colors['Silver'] = $_product->getBOpaqSilver() ? 'opaque-silver.png' : '';
		$colors['Bronze'] = $_product->getBOpaqBronze() ? 'opaque-bronze.png' : '';
		$colors['Black']  = $_product->getBOpaqBlack() ? 'opaque-black.png' : '';
		$colors['White']  = $_product->getBOpaqWhite() ? 'opaque-white.png' : '';
		$colors['Red']    = $_product->getBOpaqRed() ? 'opaque-red.png' : '';
		$colors['Navy']   = $_product->getBOpaqNavy() ? 'opaque-navy.png' : '';

		return array_filter( $colors );
	}

	public function formatCards( $cards ) {
		$cards = $this->separator( $cards );
		//Needed to make sure the count stays correct for the cards
		$hiddenCount = 0;
		$formattedCards = '';

		if ( count( $cards ) > 0 && $cards[0] ) {

			$_resource = Mage::getSingleton( 'catalog/product' )->getResource();
			$baseUrl   = Mage::getUrl();

			foreach ( $cards as $card ) {
				$productId        = Mage::getModel( 'catalog/product' )->getIdBySku( $card );

				$private = $_resource->getAttributeRawValue( $productId, 'b_private', Mage::app()->getStore() );
				$offline = $_resource->getAttributeRawValue( $productId, 'b_offline', Mage::app()->getStore() );

				//Don't add in any of these
				if($private == 1 || $offline == 1) {
					$hiddenCount++;
					continue;
				}

				$urlKey           = $_resource->getAttributeRawValue( $productId, 'url_key', Mage::app()->getStore() );
				$mainProductImage = $_resource->getAttributeRawValue( $productId, 'main_product_image', Mage::app()->getStore() );

				//Need to use short description since the name's are no longer unique to make the filter and order by work correctly
				$productName      = $_resource->getAttributeRawValue( $productId, 'short_description', Mage::app()->getStore() );

				$retailPrice1 = $_resource->getAttributeRawValue( $productId, 'retailprice1', Mage::app()->getStore() );
				$retailPrice3 = $_resource->getAttributeRawValue( $productId, 'retailprice3', Mage::app()->getStore() );

				if ( ! empty( $retailPrice3 ) ) {
					$priceRange = Mage::helper( 'core' )->currency( $retailPrice3 ) . "<span class='price-dash'> - </span>" . Mage::helper( 'core' )->currency( $retailPrice1 );
				} else {
					$priceRange = Mage::helper( 'core' )->currency( $retailPrice1 );
				}

				$formattedCards .= '<div class="col-xs-3 center accordion-slides zero-padding">';
				$formattedCards .= '<div class="slide-image-wrapper">';
				$formattedCards .= '<a href="' . $baseUrl . $urlKey . '">';
				$formattedCards .= '<img alt="'.$this->escapeHtml( $productName).' Image" src="https://image.crystal-d.com/img/u245-y/jpg/' . $mainProductImage . '.jpg">';
				$formattedCards .= '</a>';
				$formattedCards .= '</div>';
				$formattedCards .= '<div class="slide-text">';
				$formattedCards .= '<a class="dark-grey" href="' . $baseUrl . $urlKey . '">#' . $card . '</a>';
				$formattedCards .= '<div class="line-items dark-red mt-5">' . $priceRange . '</div>';
				$formattedCards .= '</div>';
				$formattedCards .= '</div>';
			}
		}

		//Used for the sliders
		$cardCount = count( $cards ) - $hiddenCount;

		return array( $formattedCards, $cardCount );
	}

	/**
	 * Need to get an array of all the possible product details.  These currently show up in the
	 * private info popup.  Needs to start as array since you have to do a count to make the display
	 * nice
	 *
	 * @param $product
	 */
	public function getProductPrivateInfo($product) {
		$details = array();

		$personalization = ($product->getPriceHasPersonalization() == '1' ? 'Yes' : 'No');
		$details['personalization'] = '<span>Personalization:</span> '. $personalization;

		if ( $product->getStandardImprictSurface() ) {
			$details['standard_surface'] = '<span>Standard Imprint Surface:</span> '.$product->getStandardImprictSurface();
		}

		if ( $product->getCountry() ) {
			$details['country'] = '<span>Country of Origin:</span> '.$product->getCountry();
		}

		$prop64 = ($product->getProp64() == 1 ? 'Yes' : 'No');
		$details['prop65'] = '<span>Subject to Prop 65:</span> '.$prop64;

		$assemblyRequired = ($product->getAssemblyRequired() == 1 ? 'Yes' : 'No');
		$details['assembly'] = '<span>Blank - Assembly Required by Crystal D:</span> '.$assemblyRequired;

		$trophyAssemblyRequired = ($product->getTrophyAssemblyRequired() == 1 ? 'Yes' : 'No');
		$details['customer_assembly'] = '<span>Blank - Assembly Required by Customer:</span> '.$trophyAssemblyRequired;

		$sellBlank = ($product->getWillSellBlank() == 1 ? 'Yes' : 'No');
		$details['sell_blank'] = '<span>We Will Sell Blank:</span> '.$sellBlank;

		if ( $product->getPricedAsBlank() ) {
			$details['blank_item'] = '<span>Blank Item:</span> Do Not Sell Blanks at EQP';
		}

		$shipText = false;
		if($product->getData('shipping_1_day') == 1 && $product->getData('shipping_anywhere') == 1) {
			$shipText = 'Ground Ship Anywhere OK';
		} elseif($product->getData('shipping_1_day') == 1 && $product->getData('shipping_anywhere') == 0) {
			$shipText = '1 Day Ground Shipping OK';
		}elseif($product->getData('shipping_1_day') == 0 && $product->getData('shipping_anywhere') == 0) {
			$shipText = 'No Ground Shipping';
		}

		if($shipText != false ) {
			$details['shipping'] = '<span>Approved Ship Method:</span> '.$shipText;
		}

		$decal = ($product->getIllumachromeDecal() == 0 ? 'No' : 'Yes');
		$details['illumachrome'] = '<span>Illumachrome Decal:</span> '.$decal;

		if ( $product->getIllumachromeDirectPrint() ) {
			$details['illumachrome_details'] = '<span>Illumachrome Direct Print:</span> '.$product->getIllumachromeDirectPrint();
		}

		if ( $product->getGiftBoxWidth() > 0 && $product->getGiftBoxLength() > 0 && $product->getGiftBoxHeight() > 0 ) {
			$details['gift_box'] = '<span>Gift Box:</span> '.$product->getGiftBoxWidth() .'" x '
			                            . $product->getGiftBoxLength() .'" x '.$product->getGiftBoxHeight().'"';

			if($product->getGiftboxShipWeight() > 0) {
				$details['shipping_weight'] = '<span>Shipping Weight:</span> '.round($product->getGiftboxShipWeight(), 2) . ' lbs';
			}
		}

		if($product->getGiftBoxCustomQty()) {
			$details['gift_box_qty'] = '<span>Gift Box Custom Qty:</span> '.$product->getGiftBoxCustomQty();
		}

		return $details;

	}

	public function getImprintServicesList($imprintServices) {
		$html            = '';
		$count           = 0;
		$i               = 0;
		$ignoreService = array('addsub', 'addlaser');
		$imprintServices = @unserialize($imprintServices);

		//Check to make sure there is information to use
		if ( $imprintServices !== false || $imprintServices === 'b:0;' ) {
			/**
			 * array column is the correct way to do this however with the current version of php
			 * I'm going this route for now
			 */
			$count = count($imprintServices);
			foreach ( $imprintServices as $imprintService ) {
				$serviceCode = strtolower($imprintService['code']);

				if(in_array($serviceCode, $ignoreService)) {
					$description[$i] = $imprintService['short_description'];
				} else {
					$popupTrigger    = $serviceCode . '-trigger';
					$description[$i] = '<a class="dark-grey ' . $popupTrigger . '">' . $imprintService['short_description'] . '</a>';
				}

				$i++;
			}

			$html = $this->naturalLanguageJoin($description);
		}

		return array($html, $count);
	}
	/**
	 * The optional services come in from a searlized array.  The reason for this is becayse the information in the ERP is
	 * in a few different spots.  In addition it would take a bunch of different attributes to bring all this information in
	 * or you would have to create a whole new model / table structure.  This is easier.
	 *
	 * 2/12/17
	 * Client Request = If Add Illumachrome Imprint-Per Location or Add Illumachrome Plus only show Add Illumachrome
	 *
	 * @param $optionalServices
	 * @param $loggedIn
	 *
	 * @return string
	 */
	public function getOptionalServicesRows( $optionalServices, $loggedIn ) {

		$illFound = false;
		$labelCheck = array('Add Illumachrome Imprint-Per Location', 'Add Illumachrome Plus', 'Illumachrome Direct', 'Illumachrome Decal');
		$skipRows = array('Add Rush Order Service');

		$rows     = '';
		$rowCount = 0;
		$optionalServices = @unserialize( $optionalServices );

		//Check to make sure there is information to use
		if ( $optionalServices !== false || $optionalServices === 'b:0;' ) {
			foreach ( $optionalServices as $optionalService ) {
				$price       = $optionalService['price'];
				$description = $optionalService['description'];

				if ( in_array( $description, $skipRows ) ) {
					continue;
				}

				if ( $price == 0 ) {
					$n_price_msg = "FREE";
					$n_price_net = "";
				} elseif ( $price == - 1 ) {
					$n_price_msg = "Call for Assistance";
					$n_price_net = "";
				} else {
					$n_price_msg_dec = $price / .6;
					$n_price_msg     = "$" . number_format( $n_price_msg_dec, 2 );
					$n_price_net     = "$" . number_format( $price, 2 );
				}

				//Sort of a mess to make this work but basically the first time something is in the array need to output
				//a single value.  After this check the array again but this time skip if found
				if ( in_array( $description, $labelCheck ) && ! $illFound ) {
					$description = 'Add Illumachrome';
					$illFound    = true;
				} elseif ( $illFound && in_array( $description, $labelCheck ) ) {
					continue;
				}

				$rows .= '<tr>';
				$rows .= '<td class="tooltip1 dark-grey">';
				$rows .= $description;
				$rows .= '<span class="has-tooltip">';
				$rows .= $optionalService['disclaimer'];
				$rows .= '</span>';
				$rows .= '</td>';
				$rows .= '<td class="price-row dark-grey align-right">' . $n_price_msg . '</td >';
				if ( $loggedIn ) {
					$rows .= '<td class="price-row dark-grey align-right">' . $n_price_net . '</td >';
				}
				$rows .= '</tr>';
				$rowCount++;
			}
		}
		
		return array($rows, $rowCount);
	}

	public function getOptionalChargeRows($optionalCharges, $loggedIn) {
		$rows     = '';
		$skipValues = array('Copy Change - Per Reorder', 'Giftbox Foil Stamp: Gold', 'Giftbox Foil Stamp: Silver' );

		foreach ( $optionalCharges as $charge ) {
			$price = $charge['price'];
			$description = $charge['description'];

			//Need to skip over some values.  This makes it easier to add more in the future
			if(in_array($description, $skipValues)) {
				continue;
			}

			if ( $price == 0 ) {
				$n_price_msg = "FREE";
				$n_price_net = "";
			} elseif ( $price == - 1 ) {
				$n_price_msg = "Call for Assistance";
				$n_price_net = "";
			} else {
				$n_price_msg_dec = $price / .6;
				$n_price_msg     = "$" . number_format( $n_price_msg_dec, 2 );
				$n_price_net     = "$" . number_format( $price, 2 );
			}

			$rows .= '<tr>';
			$rows .= '<td class="tooltip1">';
			$rows .= $description;
			$rows .= '<span class="has-tooltip" style= "display: none; opacity: 1;">';
			$rows .= $charge['disclaimer'];
			$rows .= '<div class="arrow" ></div >';
			$rows .= '</span>';
			$rows .= '</td>';
			$rows .= '<td class="price-row">' . $n_price_msg . '</td >';
			if($loggedIn) {
				$rows .= '<td  class="price-row">' . $n_price_net . '</td >';
			}

			$rows .= '</tr>';
		}

		return $rows;
	}

	/**
	 * There is an issue where Magento thinks the price attribute is super special and should always be nicely formated
	 * however the same is not for anything else that is "marked" as a price.  Instead those get assigned to a decimal
	 * block.  Oh Magento after all these years you still amaze me.  Because of this we need to do some formatting with
	 * the values to make it look nice
	 *
	 * @param $label
	 * @param $attribute
	 * @return mixed
	 */
	public function labelFix($label, $attribute = false) {
		//Mage::log($label);
		$labels = explode(' - ', $label);

		$labelString = '';
		if(isset($labels[0])) {
			$labelString .= '<span class="price">'.Mage::helper('core')->currency($labels[0], true, false).'</span>';
		}
		if(isset($labels[1])) {
			$priceFix = $labels[1] - .01;
			$labelString .= ' - <span class="price">'.Mage::helper('core')->currency($priceFix, true, false).'</span>';
		}

		return $labelString;
	}

	/**
	 * Used to get the exact link you need for the custom inventory filters
	 */
	public function getInventoryLink() {
		return Mage::helper('core/url')->getCurrentUrl();
	}

	/**
	 * Have not found a better way to unset the filter then this.  The if statements could go if I could ever figure out
	 * how to pass the correct url param but the model where this comes from does not have access to this information
	 *
	 * @param $filterLabel
	 *
	 * @return string
	 */
	public function getRemoveUrl($filterLabel) {
		$_url = Mage::getUrl('*/*/*', array('_use_rewrite' => true, '_forced_secure' => true));
		$urlParams = Mage::app()->getRequest()->getParams();
		//Remove the id as it's not need
		unset($urlParams['id']);

		if($filterLabel == 'Retail Price') {
			$unset = 'price';
		}elseif($filterLabel == 'Award Size') {
			$unset = 'height';
		}elseif($filterLabel == 'before') {
			$unset = 'available_before';
		}elseif($filterLabel == 'Category') {
			$unset = 'cat';
		}elseif($filterLabel == 'Material Type') {
			$unset = 'material';
		}elseif($filterLabel == 'Net Price') {
			$unset = 'netprice1';
		}elseif($filterLabel == 'Trophy Blank Price') {
			$unset = 'trophyblankprice1';
		}else {
			$unset = str_replace(' ', '_', strtolower($filterLabel));
		}

		unset($urlParams[$unset]);

		if(count($urlParams)) {
			$_url = $_url . '?' . urldecode(http_build_query($urlParams,'','&'));
		}

		return $_url;
	}

	public function getQuickShipUrl() {
		$params = Mage::helper('js_utility')->cleanUrlParams();
		$_url = Mage::getUrl('*/*/*', array('_use_rewrite' => true, '_forced_secure' => true));

		if(array_key_exists('quick_ship', $params)) {
			unset($params['quick_ship']);
		} else {
			$params['quick_ship'] = 397;
		}

		return $_url . '?' . urldecode(http_build_query($params,'','&'));
	}

	public function getExclusiveUrl() {
		$params = Mage::helper('js_utility')->cleanUrlParams();
		$_url = Mage::getUrl('*/*/*', array('_use_rewrite' => true, '_forced_secure' => true));

		if(array_key_exists('b_exclusive_dropdown', $params)) {
			unset($params['b_exclusive_dropdown']);
		} else {
			$params['b_exclusive_dropdown'] = 411;
		}

		return $_url . '?' . urldecode(http_build_query($params,'','&'));
	}

	public function inventoryFilterDisplay() {
		//Initial Vars
		$inventory = Mage::app()->getRequest()->getParam('inventory');
		$before = Mage::app()->getRequest()->getParam('available_before');

		$displayFilter = '';

		if($inventory) {
			$displayFilter .= '<span class="filter-label">Inventory </span>';
			$displayFilter .= '<li class="inline-block"><a href="'. $this->getRemoveUrl('inventory') .'" class="btn-remove" title="Remove This Item">Remove This Item</a>'. $inventory .'</li>';
		}

		if($before) {
			$displayFilter .= '<span class="filter-label">Available Before </span>';
			$displayFilter .= '<li class="inline-block"><a href="'. $this->getRemoveUrl('before') .'" class="btn-remove" title="Remove This Item">Remove This Item</a>'. $before .'</li>';
		}

		return $displayFilter;
	}

	/**
	 * Need to allow the categories to be removed one by one.
	 * @return string
	 */
	public function categoryFilterDisplay() {
		$categories = Mage::app()->getRequest()->getParam('cat');
		$_resource = Mage::getSingleton('catalog/category')->getResource();
		$params = Mage::helper('js_utility')->cleanUrlParams();
		$_url = Mage::getUrl('*/*/*', array('_use_rewrite' => true, '_forced_secure' => true));

		$filter = '';
		$singleCategory = false;
		if($categories) {
			$_categories = explode(',', $categories);

			if(count($_categories) == 1) {
				$singleCategory = true;
			}

			foreach($_categories as $categoryId) {
				$categoryName = $_resource->getAttributeRawValue($categoryId,  'name', Mage::app()->getStore());

				//Need to add a label in for the first record

				$filter .= '<li class="inline-block"><a href="'. $this->getRemoveCategoryUrl($categoryId, $_categories, $singleCategory, $params, $_url) .'" class="btn-remove" title="Remove This Item">Remove This Item</a>'. $categoryName .'</li>';
			}
		}

		return $filter;
	}

	/**
	 * Check what the current category in filter is and remove from url.  If only one remove all together.
	 *
	 * @param $categoryId
	 * @param $categories
	 * @param $singleCategory
	 * @param $urlParams
	 * @param $_url
	 *
	 * @return string
	 */
	public function getRemoveCategoryUrl($categoryId, $categories, $singleCategory, $urlParams, $_url) {

		if($singleCategory) {
			return $this->getRemoveUrl('cat');
		}

		$key = array_search($categoryId, $categories);
		if (false !== $key) {
			unset($categories[$key]);
		}

		$urlParams['cat'] = implode(',', $categories);
		$_url = $_url . '?' . urldecode(http_build_query($urlParams,'','&'));

		return $_url;
	}

	public function separator( $string ) {
		return Mage::helper( 'js_utility' )->separator( $string );
	}

	public function mobileCheck() {
		$isMobile = Mage::helper('mobiledetect')->isMobile();
		$isTablet = Mage::helper('mobiledetect')->isTablet();

		if ($isMobile) {
			return true;
		}

		if ($isTablet) {
			return true;
		}

		return false;
	}

	function naturalLanguageJoin($list, $conjunction = 'and') {
		$last = array_pop($list);
		if ($list) {
			return implode(', ', $list) . ', ' . $conjunction . ' ' . $last;
		}
		return $last;
	}
}
