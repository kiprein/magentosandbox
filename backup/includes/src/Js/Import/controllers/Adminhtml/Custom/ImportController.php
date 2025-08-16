<?php

class Js_Import_Adminhtml_Custom_ImportController extends Mage_Adminhtml_Controller_Action {
	protected function _initAction() {
		$this->loadLayout()
		     ->_setActiveMenu( 'js_import' )
		     ->_title( $this->__( 'Custom Import' ) )
		     ->_addBreadcrumb( Mage::helper( 'js_import' )->__( 'Custom Import' ), Mage::helper( 'js_import' )->__( 'Custom Import' ) );
	}

	public function indexAction() {
		$this->_initAction();
		$this->_addContent( $this->getLayout()->createBlock( 'js_import/adminhtml_import' ) );
		$this->renderLayout();
	}

	public function syncSetupAction() {
		//Initial Vars
		$_helper = Mage::helper('js_import');
		$resource = Mage::getSingleton( 'core/resource' );
		$read     = $resource->getConnection( 'js_import_read' );
		$productSetup = array();

		//Query all of the products
		$products  = $read->query( "SELECT s_item_num, s_short_desc, s_end_user_desc, n_weight, b_new_item, b_new_item_future, b_exclusive, s_catalog_page, k_product_id, b_quick_ship, b_private, b_offline, b_discontinued, b_not_manufactured, n_discount, b_special_order, b_personalization_extra, b_colorfill_included, b_trans_royal_blue, b_trans_light_blue, b_trans_turquoise, b_trans_dark_green, b_trans_kelly_green, b_trans_lemon, b_trans_yellow_golden, b_trans_orange, b_trans_red, b_trans_hot_pink, b_trans_brown, b_opaq_gold, b_opaq_silver, b_opaq_bronze, b_opaq_black, b_opaq_white, b_opaq_red, b_opaq_navy, s_material_type, k_process_id, s_image_area1, s_image_area2, s_image_area3, s_dimensions1, s_dimensions2, s_dimensions3, n_weight, s_packaging, s_frontback_etch, s_country, b_prop64, b_assembly_required, b_will_sell_blank, b_ground_shipping_anywhere, b_illumachrome_decal, s_illumachrome_direct_print, b_priced_as_blank, i_units_left, n_height, s_award_name, n_gift_box_width, n_gift_box_length, n_gift_box_height, n_ship_weight, s_canonical_url, b_trophy_ok, is_giveback FROM t_product WHERE dt_updated > date_sub(now(), interval 5 hour)" );

		//Setup array for sync action
		$i = 0;
		foreach($products as $product) {
			$erpProductId = $product['k_product_id'];
			$productSetup[$i]['qty'] = $product['i_units_left'];
			$productSetup[$i]['sku'] = $product['s_item_num'];
			$productSetup[$i]['name'] = $product['s_award_name'];
			$productSetup[$i]['description'] = $product['s_end_user_desc'];
			$productSetup[$i]['weight'] = $product['n_weight'];
			$productSetup[$i]['b_new_item'] = $product['b_new_item'];
			$productSetup[$i]['b_new_item_future'] = $product['b_new_item_future'];
			$productSetup[$i]['is_giveback'] = $product['is_giveback'];
			$productSetup[$i]['b_exclusive'] = $product['b_exclusive'];
			$productSetup[$i]['s_catalog_page'] = $product['s_catalog_page'];
			$productSetup[$i]['b_quick_ship'] = $product['b_quick_ship'];
			$productSetup[$i]['b_private'] = $product['b_private'];
			$productSetup[$i]['b_offline'] = $product['b_offline'];
			$productSetup[$i]['b_discontinued'] = $product['b_discontinued'];
			$productSetup[$i]['b_not_manufactured'] = $product['b_not_manufactured'];
			$productSetup[$i]['n_discount'] = $product['n_discount'];
			$productSetup[$i]['b_special_order'] = $product['b_special_order'];
			$productSetup[$i]['personalization_extra'] = $product['b_personalization_extra'];
			$productSetup[$i]['colorfill_included'] = $product['b_colorfill_included'];
			$productSetup[$i]['b_trans_royal_blue'] = $product['b_trans_royal_blue'];
			$productSetup[$i]['b_trans_light_blue'] = $product['b_trans_light_blue'];
			$productSetup[$i]['b_trans_turquoise'] = $product['b_trans_turquoise'];
			$productSetup[$i]['b_trans_dark_green'] = $product['b_trans_dark_green'];
			$productSetup[$i]['b_trans_kelly_green'] = $product['b_trans_kelly_green'];
			$productSetup[$i]['b_trans_lemon'] = $product['b_trans_lemon'];
			$productSetup[$i]['b_trans_yellow_golden'] = $product['b_trans_yellow_golden'];
			$productSetup[$i]['b_trans_orange'] = $product['b_trans_orange'];
			$productSetup[$i]['b_trans_red'] = $product['b_trans_red'];
			$productSetup[$i]['b_trans_hot_pink'] = $product['b_trans_hot_pink'];
			$productSetup[$i]['b_trans_brown'] = $product['b_trans_brown'];
			$productSetup[$i]['b_opaq_gold'] = $product['b_opaq_gold'];
			$productSetup[$i]['b_opaq_silver'] = $product['b_opaq_silver'];
			$productSetup[$i]['b_opaq_bronze'] = $product['b_opaq_bronze'];
			$productSetup[$i]['b_opaq_black'] = $product['b_opaq_black'];
			$productSetup[$i]['b_opaq_white'] = $product['b_opaq_white'];
			$productSetup[$i]['b_opaq_red'] = $product['b_opaq_red'];
			$productSetup[$i]['b_opaq_navy'] = $product['b_opaq_navy'];
			$productSetup[$i]['material_type'] = $product['s_material_type'];
			$productSetup[$i]['process_id'] = $product['k_process_id'];
			$productSetup[$i]['image_area'] = $product['s_image_area1'];
			$productSetup[$i]['image_area2'] = $product['s_image_area2'];
			$productSetup[$i]['image_area3'] = $product['s_image_area3'];
			$productSetup[$i]['dimension1'] = $product['s_dimensions1'];
			$productSetup[$i]['dimension2'] = $product['s_dimensions2'];
			$productSetup[$i]['dimension3'] = $product['s_dimensions3'];
			$productSetup[$i]['package_shipping'] = $product['s_packaging'];
			$productSetup[$i]['standard_imprict_surface'] = $product['s_frontback_etch'];
			$productSetup[$i]['country'] = $product['s_country'];
			$productSetup[$i]['prop_64'] = $product['b_prop64'];
			$productSetup[$i]['assembly_required'] = $product['b_assembly_required'];
			$productSetup[$i]['will_sell_blank'] = $product['b_will_sell_blank'];
			$productSetup[$i]['shipping_anywhere'] = $product['b_ground_shipping_anywhere'];
			$productSetup[$i]['illumachrome_decal'] = $product['b_illumachrome_decal'];
			$productSetup[$i]['illumachrome_direct_print'] = $product['s_illumachrome_direct_print'];
			$productSetup[$i]['priced_as_blank'] = $product['b_priced_as_blank'];
			$productSetup[$i]['main_product_image'] = $product['s_canonical_url'];
			$productSetup[$i]['etc_image'] = $_helper->getImage($read, $erpProductId, 'etc');
			$productSetup[$i]['info_image'] = $_helper->getImage($read, $erpProductId, 'info');
			$productSetup[$i]['cat_img'] = $_helper->getImage($read, $erpProductId, 'catimage');
			$productSetup[$i]['three_sixtty_image'] = $_helper->getThreeSixtyImage($read, $erpProductId);
			$productSetup[$i]['what_include_snotes'] = $_helper->getIncludedNotes($read, $erpProductId);
			$productSetup[$i]['inspiration_card'] = $_helper->getIncludedCards($read, $erpProductId, 'inspiration');
			$productSetup[$i]['included_plate'] = $_helper->getIncludedCards($read, $erpProductId, 'plates');
			$productSetup[$i]['included_accent'] = $_helper->getIncludedCards($read, $erpProductId, 'accent');
			$productSetup[$i]['goal_setter_block'] = $_helper->getIncludedCards($read, $erpProductId, 'goal_setter');
			$productSetup[$i]['colored_spheres'] = $_helper->getIncludedCards($read, $erpProductId, 'sphere');
			$productSetup[$i]['optional_services'] = $_helper->getOptionalServices($read, $erpProductId);
			$productSetup[$i]['optional_charge'] = $_helper->getOptionalCharges($read, $erpProductId);
			$productSetup[$i]['additional_goal_setter'] = $_helper->getOptionalCards($read, $erpProductId, 'goal_setter');
			$productSetup[$i]['accent_product'] = $_helper->getOptionalCards($read, $erpProductId, 'accent');
			$productSetup[$i]['optional_base'] = $_helper->getOptionalCards($read, $erpProductId, 'base');
			$productSetup[$i]['optional_plates'] = $_helper->getOptionalCards($read, $erpProductId, 'plates');
			$productSetup[$i]['easels'] = $_helper->getOptionalCards($read, $erpProductId, 'easels');
			$productSetup[$i]['certificate_paper'] = $_helper->getOptionalCards($read, $erpProductId, 'certificate');
			$productSetup[$i]['imprint_shown'] = $_helper->getImprintProcess($read, $product['k_process_id']);
			$productSetup[$i]['tiff_img'] = $_helper->getImageByType( $read, $erpProductId, 'TIFF');
			$productSetup[$i]['product_template'] = $_helper->getImage($read, $erpProductId, 'template');
			$productSetup[$i]['gift_box_image'] = $_helper->getImage($read, $erpProductId, 'package');
			$productSetup[$i]['orderinginformation'] = $_helper->getOrderinginformation($read, $erpProductId);
			$productSetup[$i]['gift_box_width'] = $product['n_gift_box_width'];
			$productSetup[$i]['gift_box_length'] = $product['n_gift_box_length'];
			$productSetup[$i]['gift_box_height'] = $product['n_gift_box_height'];
			$productSetup[$i]['giftbox_ship_weight'] = $product['n_ship_weight'];
			$productSetup[$i]['b_trophy_ok'] = $product['b_trophy_ok'];
			$productSetup[$i]['product_category_keywords'] = $_helper->getProductCategoryKeywords( $read, $erpProductId);;

			$i++;
		}

        $this->_getSession()->setSyncProducts($productSetup);

		$this->loadLayout();
		$this->renderLayout();
	}

	public function syncAction() {
		//Initial Vars
		@ini_set('max_execution_time', 3600);
		@ini_set('memory_limit', 734003200);

		$_helper = Mage::helper('js_import');
		$resource = Mage::getSingleton( 'core/resource' );
		$read     = $resource->getConnection( 'js_import_read' );

		$current = intval($this->getRequest()->getParam('current', 0));
		$result = array();

		$products = $this->_getSession()->getSyncProducts();
		$total = count($products);

		if ($current < $total) {
			//Setup the product to be save
			$productId = Mage::getModel( 'catalog/product' )->getIdBySku( $products[$current]['sku'] );
			$qty = $products[$current]['qty'];

			echo "Memory Usage: " . ( memory_get_peak_usage( true ) / 1024 / 1024 ) . " MiB\n";

			//Quick check to load the product if it's already present
			if ( $productId ) {
				$product = Mage::getModel( 'catalog/product' )->setStoreId( 1 )->load( $productId );

				//Update the inventory here
				$stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productId);
				if ($stockItem->getId() > 0) {
					$stockItem->setQty($qty);
					$stockItem->setIsInStock((int)($qty > 0));
					$stockItem->save();
				}
			} else {
				$product = Mage::getModel( 'catalog/product' )->setStoreId( 1 );
				$product->setWebsiteIds( array( 1 ) )//website ID the product is assigned to, as an array
				        ->setAttributeSetId( 4 )//ID of a attribute set named 'default'
				        ->setTypeId( 'simple' )//product type
				        ->setCreatedAt( strtotime( 'now' ) )//product creation time
				        ->setStatus( 1 )//product status (1 - enabled, 2 - disabled)
				        ->setTaxClassId( 2 )//tax class (0 - none, 1 - default, 2 - taxable, 4 - shipping)
				        ->setVisibility( Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH )//catalog and search visibility
				        ->setPrice( '0.00' )
				        ->setCost( '0.00' )
				        ->setMediaGallery( array(
					        'images' => array(),
					        'values' => array()
				        ) )//media gallery initialization
				        ->setStockData( array(
						'use_config_manage_stock' => 0, //'Use config settings' checkbox
						'manage_stock'            => 1, //manage stock
						'min_sale_qty'            => 1, //Minimum Qty Allowed in Shopping Cart
						'max_sale_qty'            => 999, //Maximum Qty Allowed in Shopping Cart
						'is_in_stock'             => 1, //Stock Availability
						'qty'                     => $qty //qty
					) );
			}

			$product->setData($products[$current]);

			$product = $_helper->setPricing($read, $product, $product[$current]['old_product_id']);
			$product->save();
			$product->clearInstance();

			$current += 1;

			$result['text'] = $this->__('Total %1$s, processed %2$s file(s) (%3$s%%)...', $total, $current, round($current * 100 / $total, 2));
			$result['url'] = $this->getUrl('*/*/sync/', array('current' => $current));
		}

		if ($current == $total) {

			if ($total === 0) {
				$result['text'] = $this->__('No files to import');
			}

			$skippedCnt = $this->_getSession()->getSkippedCnt();
			$result['skipped_ids'] = implode(',', array_unique($this->_getSession()->getSkippedIds()));
			$result['skipped_cnt'] = $skippedCnt;
			$result['total_imported'] = $total - $skippedCnt;
			$result['stop'] = true;
			//$result['text'] = $this->__('Done');
			$result['url'] = '';
		}

		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
	}

	public function syncSingleAction() {
		//Initial Vars
		$_helper = Mage::helper('js_import');
		$resource = Mage::getSingleton( 'core/resource' );
		$read     = $resource->getConnection( 'js_import_read' );
		$baseQuery = $_helper->getImportQuery();

		$productSetup = array();

		$sku = $this->getRequest()->getParam( 'sku' );
		$productId = Mage::getModel( 'catalog/product' )->getIdBySku( $sku );

		if($productId) {
			$product = Mage::getModel( 'catalog/product' )->setStoreId( 1 )->load( $productId );
			$productQuery = $baseQuery." WHERE s_item_num = '$sku'";
			$productInfo  = $read->fetchRow( $productQuery );
			$erpProductId = $productInfo['k_product_id'];
			$updatedProductData = $_helper->setUpdatedProductData($productInfo, $read);

			/**
			 * There is an issue when saving the product this way where the url key changes if the product name changes
			 * This makes sure it stays the same
			 */
			$productUrlKey = $product->getUrlKey();
			$updatedProductData['url_key'] = $productUrlKey;

			$product->setData($updatedProductData);

			$product = $_helper->setPricing($read, $product, $erpProductId);
			$product->save();
			$product->clearInstance();

			Mage::getSingleton( 'adminhtml/session' )->addSuccess( Mage::helper( 'js_import' )->__( 'Product: '.$productInfo['s_short_desc'].' was synced' ) );
		} else {
			Mage::getSingleton( 'adminhtml/session' )->addError( Mage::helper( 'js_import' )->__( 'Product: '.$sku.' could not be found. Please run the full sync first.' ) );
		}

		$this->_redirect( '*/custom_import/' );
	}


	public function _importInventory() {
		@ini_set( 'max_execution_time', 1800 );
		@ini_set( 'memory_limit', 734003200 );
		//Setup the csv to be looped through
		$csvObject = new Varien_File_Csv();
		$fileName  = $_FILES['import_file']['tmp_name'];
		$csvData   = $csvObject->getData( $fileName );

		//Additional vars
		$_helper = Mage::helper( 'js_import' );

		foreach ( $csvData as $row => $column ) {

			if ( $row == 0 ) {
				//Verify that all default import columns are found in the csv
				$inventoryColumns = array( 'Sku', 'Quantity', 'Min Qty' );
				foreach ( $inventoryColumns as $inventoryColumn ) {
					if ( ! in_array( $inventoryColumn, $csvData[0] ) ) {
						Mage::getSingleton( 'adminhtml/session' )->addError( Mage::helper( 'js_import' )->__( 'The columns do not match. Please make sure the following columns are present and in the following order ' . implode( ", ", $inventoryColumns ) ) );
						$this->_redirect( '*/custom_import/' );

						return;
					}
				}
				continue;
			} else {
				if ( $column[0] ) {
					$sku       = $column[0];
					$qty       = $column[1];
					$minQty    = $column[2];
					$notifyQty = $column[3];

					//Setup the product to be save
					$productId = Mage::getModel( 'catalog/product' )->getIdBySku( $sku );

					//Only set price for exisiting products
					if ( $productId ) {
						$stockItem = Mage::getModel( 'cataloginventory/stock_item' )->loadByProduct( $productId );
						if ( $stockItem->getId() > 0 ) {
							$stockItem->setQty( $qty );

							if ( isset( $minQty ) && $minQty > 0 ) {
								$stockItem->setMinQty( $minQty );
							}

							if ( isset( $notifyQty ) && $notifyQty > 0 ) {
								$stockItem->setNotifyStockQty( $notifyQty );
							}

							if ( $qty > 0 ) {
								$stockItem->setIsInStock( 1 );
							}

							if ( $qty <= 0 ) {
								$stockItem->setIsInStock( 0 );
							}
							$stockItem->setUseConfigNotifyStockQty( 0 );
							$stockItem->setUseConfigMinQty( 0 );
							$stockItem->save();
						}
						$stockItem->clearInstance();
					}
				}
			}
		}

		Mage::getSingleton( 'adminhtml/session' )->addSuccess( Mage::helper( 'js_import' )->__( 'The import has successfully completed' ) );
		$this->_redirect( '*/custom_import/' );

		return;
	}
}