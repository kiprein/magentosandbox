<?php

class Product_Sync extends Mage_Shell_Abstract {

	protected function reconnectIfNeeded($connection) {
		try {
			// Run a lightweight query to test if the connection is alive
			$connection->query('SELECT 1');
		} catch (PDOException $e) {
			if (strpos($e->getMessage(), 'MySQL server has gone away') !== false) {
				// Force reconnect
				$connection->closeConnection();
				$connection->getConnection();
				echo "Reconnected to MySQL.\n";
			} else {
				throw $e; // bubble up unexpected exceptions
			}
		}
	}
	

	public function run($full = false) {
		ini_set( 'memory_limit', '4000M' );

		$_helper   = Mage::helper( 'js_import' ); //Js_Import_Helper_Data
		$resource  = Mage::getSingleton( 'core/resource' );
		$read      = $resource->getConnection( 'js_import_read' );
		$coreRead  = $resource->getConnection( 'core_read' );
		$coreWrite = $resource->getConnection( 'core_write' );

		$selectQuery = "SELECT s_item_num, s_short_desc, s_image_file_base, s_end_user_desc, n_weight, n_weight_override, b_new_item, b_new_item_future, b_exclusive, 
		s_catalog_page, k_image_id, k_image_id_thumb, k_product_id, b_quick_ship, b_private, b_offline, b_discontinued, 
		b_not_manufactured, n_discount, b_special_order, b_personalization_extra, b_colorfill_included, b_trans_royal_blue, 
		b_trans_light_blue, b_trans_turquoise, b_trans_dark_green, b_trans_kelly_green, b_trans_lemon, b_trans_yellow_golden, 
		b_trans_orange, b_trans_red, b_trans_hot_pink, b_trans_brown, b_opaq_gold, b_opaq_silver, b_opaq_bronze, b_opaq_black, 
		b_opaq_white, b_opaq_red, b_opaq_navy, s_material_type, k_process_id, s_image_area1, s_image_area2, s_image_area3, 
		s_dimensions1, s_dimensions2, s_dimensions3, s_packaging, s_frontback_etch, s_country, b_prop64, b_imperfections, b_assembly_required, 
		b_will_sell_blank, b_ground_shipping_anywhere, b_ground_shipping_1_day, b_illumachrome_decal, s_illumachrome_direct_print, 
		b_priced_as_blank, i_units_left, n_height, s_award_name, n_gift_box_width, n_gift_box_length, n_gift_box_height, n_ship_weight, 
		s_canonical_url, b_trophy_ok, s_award_size, i_min_qty_custom_gift_box, b_subsurface_only, s_important_note1, s_important_note2, 
		s_packaging, b_trophy_assembly_required, b_price_has_personalization, is_giveback FROM t_product";
		if($full){
			$selectQuery .= " WHERE b_offline = 0";
		} else {
			$selectQuery .= " WHERE dt_updated > date_sub(now(), interval 4 hour)";

		}

		$this->reconnectIfNeeded($read);
		try {
			$results = $read->query($selectQuery);
		} catch (PDOException $e) {
			if (strpos($e->getMessage(), 'MySQL server has gone away') !== false) {
				$this->reconnectIfNeeded($read);
				$results = $read->query($selectQuery); // retry once
			} else {
				throw $e;
			}
		}

		$processes = Mage::getSingleton('index/indexer')->getProcessesCollection();
		foreach ($processes as $process) {
			$process->setMode(Mage_Index_Model_Process::MODE_MANUAL)->save();
		}
		
		$i = 0;
		foreach ( $results as $result ) {

			unset($product);

			if ($i % 20 == 0) { // every 20 products
				$this->reconnectIfNeeded($read);
				$this->reconnectIfNeeded($coreRead);
				$this->reconnectIfNeeded($coreWrite);
			}
			
			//Setup the product to be save
			$productId = Mage::getModel( 'catalog/product' )->getIdBySku( $result['s_item_num'] );
			//$qty       = $result['i_units_left'];
			$qty = $coreRead->fetchOne("select qty
				from inventory_avail
				where product_id = '{$productId}'
				and date = '1990-01-01 00:00:00'");


			//Quick check to load the product if it's already present
			if ( $productId ) {
				$product = Mage::getModel( 'catalog/product' )->setStoreId( 1 )->load( $productId );

				//Update the inventory here

				$stockItem = Mage::getModel( 'cataloginventory/stock_item' )->loadByProduct( $productId );
				if ( $stockItem->getId() > 0 ) {
					$stockItem->setQty( $qty );
					$stockItem->setIsInStock( (int) ( $qty > 0 ) );
					$stockItem->save();
				}
			} else {
				if ( ! empty( $result['s_item_num'] ) ) {
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
				} else {
					echo "Product failed: " . $result['k_product_id'] . "\n";
					Mage::log( "Product failed: " . $result['k_product_id'] );
					continue;
				}

			}

			echo "Sku: " . $result['s_item_num'] . "\n";
			echo "M Id: " . $product->getId() . "\n";
			echo "ERP Id: " . $product->getOldProductId() . "\n";
			echo "Url Key: " . $result['s_canonical_url'] . "\n";
			echo "Memory Usage: " . ( memory_get_peak_usage( true ) / 1024 / 1024 ) . " MiB\n";

            //START SETTING UP REST OF PRODUCT and get the view page synced up
            $canonicalUrl = $_helper->getCanonicalUrl( $read,  $result['s_award_name']);
			$erpProductId = $result['k_product_id'];
			$product->setName( $result['s_award_name'] )
			        ->setShortDescription( $result['s_short_desc'] )
					->setMetaTitle( $result['s_short_desc'] )
			        ->setDescription( $result['s_end_user_desc'] )
			        ->setSku( $result['s_item_num'] )
			        ->setWeight( !empty($result['n_weight_override'])? $result['n_weight_override'] : $result['n_weight'] )
			        ->setBNewItem( $result['b_new_item'] )
			        ->setBNewItemFuture( $result['b_new_item_future'] )
			        ->setBExclusive( $result['b_exclusive'] )
					->setImportantNote1($result['s_important_note1'])
					->setImportantNote2($result['s_important_note2'])
					->setSCatalogPage( $result['s_catalog_page'] )
					->setOldProductId( $result['k_product_id'] )
					->setSubsurfaceOnly( $result['b_subsurface_only'] )
				->setMainProductImage( $result['s_canonical_url'] )
				->setUrlKey( $result['s_canonical_url'] )
				->setCanonicalUrl( $canonicalUrl ? $canonicalUrl : $result['s_canonical_url'] )
					->setEtcImage( $_helper->getImage( $read, $erpProductId, 'etc' ) )
					->setImageAlt( $result['s_image_file_base'] ) //<img> alt attribute
			        ->setInfoImage( $_helper->getImage( $read, $erpProductId, 'info' ) )
			        ->setData('three_sixty_video_1', $_helper->getImage( $read, $erpProductId, '360_video_1' ) )
			        ->setData('three_sixty_video_2', $_helper->getImage( $read, $erpProductId, '360_video_2' ) )
			        ->setCatImg( $_helper->getImage( $read, $erpProductId, 'catimage' ) )
			        ->setThreeSixttyImage( $_helper->getThreeSixtyImage( $read, $erpProductId ) )
			        ->setQuickShip( $_helper->quickShipValue( $result['b_quick_ship'] ) )
			        ->setBPrivate( $result['b_private'] )
			        ->setBOffline( $result['b_offline'] )
			        ->setBDiscontinued( $result['b_discontinued'] )
			        ->setNotManufactured( $result['b_not_manufactured'] )
			        ->setNDiscount( $result['n_discount'] )
			        ->setBSpecialOrder( $result['b_special_order'] )
			        ->setPersonalizationExtra( $result['b_personalization_extra'] )
			        ->setWhatIncludeSnotes( $_helper->getIncludedNotes( $read, $erpProductId ) )
			        ->setColorfillIncluded( $result['b_colorfill_included'] )
			        ->setBTransRoyalBlue( $result['b_trans_royal_blue'] )
			        ->setBTransLightBlue( $result['b_trans_light_blue'] )
			        ->setBTransTurquoise( $result['b_trans_turquoise'] )
			        ->setBTransDarkGreen( $result['b_trans_dark_green'] )
			        ->setBTransKellyGreen( $result['b_trans_kelly_green'] )
			        ->setBTransLemon( $result['b_trans_lemon'] )
			        ->setBTransYellowGolden( $result['b_trans_yellow_golden'] )
			        ->setBTransOrange( $result['b_trans_orange'] )
			        ->setBTransRed( $result['b_trans_red'] )
			        ->setBTransHotPink( $result['b_trans_hot_pink'] )
			        ->setBTransBrown( $result['b_trans_brown'] )
			        ->setBOpaqGold( $result['b_opaq_gold'] )
			        ->setBOpaqSilver( $result['b_opaq_silver'] )
			        ->setBOpaqBronze( $result['b_opaq_bronze'] )
			        ->setBOpaqBlack( $result['b_opaq_black'] )
			        ->setBOpaqWhite( $result['b_opaq_white'] )
			        ->setBOpaqRed( $result['b_opaq_red'] )
					->setBOpaqNavy( $result['b_opaq_navy'] )
					/**
					 * Js_Import_Helper_Data::getIncludedCards() returns a | delimited list of s_item_num
					 */
			        ->setInspirationCard( $_helper->getIncludedCards( $read, $erpProductId, 'inspiration' ) )
			        ->setBatteryPackIncluded( $_helper->getIncludedCards( $read, $erpProductId, 'battery' ) )
			        ->setIncludedPlate( $_helper->getIncludedCards( $read, $erpProductId, 'plates' ) )
			        ->setIncludedPanels( $_helper->getIncludedCards( $read, $erpProductId, 'panels' ) )
			        ->setIncludedAccent( $_helper->getIncludedCards( $read, $erpProductId, 'accent' ) )
			        ->setGoalSetterBlock( $_helper->getIncludedCards( $read, $erpProductId, 'goal_setter' ) )
					->setColoredSpheres( $_helper->getIncludedCards( $read, $erpProductId, 'sphere' ) )
					
			        ->setOptionalServices( $_helper->getServices( $read, $erpProductId, 'Customization' ) )
			        ->setOptionalCharge( $_helper->getServices( $read, $erpProductId, 'Ordering' ) )
			        ->setAdditionalGoalSetter( $_helper->getOptionalCards( $read, $erpProductId, 'goal_setter' ) )
			        ->setBatteryPackOptional( $_helper->getOptionalCards( $read, $erpProductId, 'battery' ) )
			        ->setAccentProduct( $_helper->getOptionalCards( $read, $erpProductId, 'accent' ) )
			        ->setOptionalBase( $_helper->getOptionalCards( $read, $erpProductId, 'base' ) )
			        ->setOptionalPlates( $_helper->getOptionalCards( $read, $erpProductId, 'plates' ) )
			        ->setOptionalPanels( $_helper->getOptionalCards( $read, $erpProductId, 'panels' ) )
			        ->setEasels( $_helper->getOptionalCards( $read, $erpProductId, 'easels' ) )
			        ->setCertificatePaper( $_helper->getOptionalCards( $read, $erpProductId, 'certificate' ) )
			        ->setMaterialType( $result['s_material_type'] )
					->setImprintShown( $_helper->getImprintProcess( $read, $result['k_process_id'] ) )
			        ->setImageArea( $result['s_image_area1'] )
			        ->setImageArea2( $result['s_image_area2'] )
			        ->setImageArea3( $result['s_image_area3'] )
			        ->setDimension( $result['s_dimensions1'] )
			        ->setDimension2( $result['s_dimensions2'] )
			        ->setDimension3( $result['s_dimensions3'] )
			        ->setTiffImg( $_helper->getImageByType( $read, $erpProductId, 'TIFF' ) )
			        ->setProductTemplate( $_helper->getImage( $read, $erpProductId, 'template' ) )
			        ->setGiftBoxImage( $_helper->getGiftBoxImages( $read, $erpProductId) )
			        ->setPackageShipping( $result['s_packaging'] )
			        ->setOrderinginformation( $_helper->getOrderinginformation( $read, $erpProductId ) )
			        ->setStandardImprictSurface( $result['s_frontback_etch'] )
			        ->setCountry( $result['s_country'] )
			        ->setProp64( $result['b_prop64'] )
                    ->setImperfections( $result['b_imperfections'])
					->setAssemblyRequired( $result['b_assembly_required'] )
					->setTrophyAssemblyRequired($result['b_trophy_assembly_required'])
					->setPriceHasPersonalization($result['b_price_has_personalization'])
					->setWillSellBlank( $result['b_will_sell_blank'] )
			        ->setPricedAsBlank( $result['b_priced_as_blank'] )
			        ->setData( 'shipping_anywhere', $result['b_ground_shipping_anywhere'] )
			        ->setData( 'shipping_1_day', $result['b_ground_shipping_1_day'] )
			        ->setIllumachromeDecal( $result['b_illumachrome_decal'] )
			        ->setIllumachromeDirectPrint( $result['s_illumachrome_direct_print'] )
			        ->setGiftBoxWidth( $result['n_gift_box_width'] )
			        ->setGiftBoxLength( $result['n_gift_box_length'] )
			        ->setGiftBoxHeight( $result['n_gift_box_height'] )
			        ->setGiftboxShipWeight( $result['n_ship_weight'] )
			        ->setBTrophyOk( $result['b_trophy_ok'] )
			        ->setProductCategoryKeywords( $_helper->getProductCategoryKeywords( $read, $erpProductId ) )
			        ->setImprintProcessServices( $_helper->getServices( $read, $erpProductId, 'Included' ) )
			        ->setGiftBoxCustomQty( $result['i_min_qty_custom_gift_box'] )
			        ->setGiftBoxAltText($result['s_packaging'])
							->setIsGiveback( $result['is_giveback'] );

			$product = $_helper->setPricing( $read, $product, $erpProductId );
			$product = $_helper->formatHeight( $result['s_award_size'], $result['n_height'], $product );
			$product = $_helper->setRelatedSizes($read, $erpProductId, $result['s_award_name'], $product);

			//Update the "category" filters
			$_helper->getCategoryAttributes( $read, $product, $erpProductId, $coreWrite );

			//Need to disable product if offline
			if ( $result['b_offline'] == 1 ) {
				$product->setStatus( 2 );
			} elseif($result['b_offline'] == 0) {
				$product->setStatus( 1 );
			}

			//If the product is private make sure only employees can see
			if ( $result['b_private'] ) {
				$product->setGroupscatalog2Groups( '0,1,4,5' );
			} else {
				$product->setGroupscatalog2Groups( '-2' );
			}

			// — gather option IDs from your PDB category links —
			$erpId = $result['k_product_id'];
			$rows  = $read->fetchCol("
				SELECT c.s_category_name
					FROM t_cat_prod cp
					JOIN t_category c
						ON cp.k_category_id = c.k_category_id
				WHERE cp.k_product_id = '{$erpId}'
					AND c.k_group_id = 15
			");

			$optionIds = [];
			foreach ($rows as $label) {
					// use $_helper (Js_Import_Helper_Data instance)
					$opt = $_helper->attributeValueExists('imprint_processes', $label);
					if ($opt && !in_array($opt, $optionIds)) {
							$optionIds[] = $opt;
					}
			}

			if (!empty($result['k_process_id'])) {
					$label = $_helper->getImprintProcess($read, $result['k_process_id']);
					$opt   = $_helper->attributeValueExists('imprint_processes', $label);
					if ($opt && !in_array($opt, $optionIds)) {
							$optionIds[] = $opt;
					}
			}

			if (count($optionIds)) {
					$product->setData('imprint_processes', implode(',', $optionIds));
			}

			$categoryNames = $read->fetchCol("
        SELECT c.s_category_name
          FROM t_cat_prod cp
          JOIN t_category c
            ON cp.k_category_id = c.k_category_id
         WHERE cp.k_product_id = '{$erpId}'
           AND c.k_group_id NOT IN (5,6,9,15)
    	");

			if (!empty($categoryNames)) {
        // 2) Load Magento category collection by matching the 'name' attribute
        $categoryCollection = Mage::getModel('catalog/category')->getCollection()
            ->addAttributeToSelect('entity_id')
            ->addAttributeToFilter('name', ['in' => $categoryNames]);

        $magentoCategoryIds = $categoryCollection->getAllIds();

        // 3) Assign those IDs to the product
        if (!empty($magentoCategoryIds)) {
            $product->setCategoryIds($magentoCategoryIds);
        }
    }

			$product->save();
			$product->clearInstance();

			//usleep(200000);

			$i ++;

		}

		foreach ($processes as $process) {
			$process->setMode(Mage_Index_Model_Process::MODE_REAL_TIME)->save();
		}

        $indexer = Mage::getModel('index/indexer')->getProcessByCode('cataloginventory_stock');
        $indexer->reindexEverything();
	}
}