<?php

/**
 * Created by PhpStorm.
 * User: Jon Saverda
 * Date: 1/21/2017
 * Time: 4:49 PM
 */
class Js_Import_Helper_Data extends Mage_Core_Helper_Abstract {

    protected function reconnectIfNeeded($connection) {
        try {
            $connection->query('SELECT 1');
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'MySQL server has gone away') !== false) {
                $connection->closeConnection();
                $connection->getConnection();
                Mage::log("Reconnected inside Js_Import_Helper_Data", null, 'product_sync.log');
            } else {
                throw $e;
            }
        }
    }


	public function getImportQuery() {
		return "SELECT s_item_num, s_short_desc, s_end_user_desc, n_weight, b_new_item, b_new_item_future, b_exclusive, s_catalog_page, k_image_id, k_image_id_thumb, k_product_id, b_quick_ship, b_private, b_offline, b_discontinued, b_not_manufactured, n_discount, b_special_order, b_personalization_extra, b_colorfill_included, b_trans_royal_blue, b_trans_light_blue, b_trans_turquoise, b_trans_dark_green, b_trans_kelly_green, b_trans_lemon, b_trans_yellow_golden, b_trans_orange, b_trans_red, b_trans_hot_pink, b_trans_brown, b_opaq_gold, b_opaq_silver, b_opaq_bronze, b_opaq_black, b_opaq_white, b_opaq_red, b_opaq_navy, s_material_type, k_process_id, s_image_area1, s_image_area2, s_image_area3, s_dimensions1, s_dimensions2, s_dimensions3, n_weight, s_packaging, s_frontback_etch, s_country, b_prop64, b_assembly_required, b_will_sell_blank, b_ground_shipping_anywhere, b_illumachrome_decal, s_illumachrome_direct_print, b_priced_as_blank, i_units_left, n_height, s_award_name, n_gift_box_width, n_gift_box_length, n_gift_box_height, n_ship_weight, s_canonical_url, b_trophy_ok, is_giveback FROM t_product ";
	}

	public function setUpdatedProductData( $productInfo, $read ) {
		$newProductData                      = array();
		$erpProductId                        = $productInfo['k_product_id'];
		$newProductData['name']              = $productInfo['s_award_name'];
		$newProductData['short_description'] = $productInfo['s_short_desc'];
		$newProductData['description']       = $productInfo['s_end_user_desc'];

		$newProductData['sku']        = $productInfo['s_item_num'];
		$newProductData['weight']     = $productInfo['n_weight'];
		$newProductData['qty']        = $productInfo['i_units_left'];
		$newProductData['b_new_item'] = $productInfo['b_new_item'];
		$newProductData['b_new_item_future'] = $productInfo['b_new_item_future'];
		$newProductData['is_giveback'] = $productInfo['is_giveback'];

		$newProductData['b_exclusive']    = $productInfo['b_exclusive'];
		$newProductData['s_catalog_page'] = $productInfo['s_catalog_page'];
		$newProductData['b_quick_ship']   = $productInfo['b_quick_ship'];
		$newProductData['b_private']      = $productInfo['b_private'];

		$newProductData['b_offline']          = $productInfo['b_offline'];
		$newProductData['b_discontinued']     = $productInfo['b_discontinued'];
		$newProductData['b_not_manufactured'] = $productInfo['b_not_manufactured'];
		$newProductData['n_discount']         = $productInfo['n_discount'];

		$newProductData['b_special_order']       = $productInfo['b_special_order'];
		$newProductData['personalization_extra'] = $productInfo['b_personalization_extra'];
		$newProductData['colorfill_included']    = $productInfo['b_colorfill_included'];
		$newProductData['b_trans_royal_blue']    = $productInfo['b_trans_royal_blue'];

		$newProductData['b_trans_light_blue']  = $productInfo['b_trans_light_blue'];
		$newProductData['b_trans_turquoise']   = $productInfo['b_trans_turquoise'];
		$newProductData['b_trans_dark_green']  = $productInfo['b_trans_dark_green'];
		$newProductData['b_trans_kelly_green'] = $productInfo['b_trans_kelly_green'];

		$newProductData['b_trans_lemon']         = $productInfo['b_trans_lemon'];
		$newProductData['b_trans_yellow_golden'] = $productInfo['b_trans_yellow_golden'];
		$newProductData['b_trans_orange']        = $productInfo['b_trans_orange'];
		$newProductData['b_trans_red']           = $productInfo['b_trans_red'];

		$newProductData['b_trans_hot_pink'] = $productInfo['b_trans_hot_pink'];
		$newProductData['b_trans_brown']    = $productInfo['b_trans_brown'];
		$newProductData['b_opaq_gold']      = $productInfo['b_opaq_gold'];
		$newProductData['b_opaq_silver']    = $productInfo['b_opaq_silver'];

		$newProductData['b_opaq_bronze'] = $productInfo['b_opaq_bronze'];
		$newProductData['b_opaq_black']  = $productInfo['b_opaq_black'];
		$newProductData['b_opaq_white']  = $productInfo['b_opaq_white'];
		$newProductData['b_opaq_red']    = $productInfo['b_opaq_red'];

		$newProductData['b_opaq_navy']   = $productInfo['b_opaq_navy'];
		$newProductData['material_type'] = $productInfo['s_material_type'];
		$newProductData['process_id']    = $this->getImprintProcess( $read, $productInfo['k_process_id'] );
		$newProductData['image_area']    = $productInfo['s_image_area1'];

		$newProductData['image_area2'] = $productInfo['s_image_area2'];
		$newProductData['image_area3'] = $productInfo['s_image_area3'];
		$newProductData['dimension1']  = $productInfo['s_dimensions1'];
		$newProductData['dimension2']  = $productInfo['s_dimensions2'];

		$newProductData['dimension3']               = $productInfo['s_dimensions3'];
		$newProductData['package_shipping']         = $productInfo['s_packaging'];
		$newProductData['standard_imprict_surface'] = $productInfo['s_frontback_etch'];
		$newProductData['country']                  = $productInfo['s_country'];

		$newProductData['prop_64']           = $productInfo['b_prop64'];
		$newProductData['assembly_required'] = $productInfo['b_assembly_required'];
		$newProductData['will_sell_blank']   = $productInfo['b_will_sell_blank'];
		$newProductData['shipping_anywhere'] = $productInfo['b_ground_shipping_anywhere'];

		$newProductData['illumachrome_decal']        = $productInfo['b_illumachrome_decal'];
		$newProductData['illumachrome_direct_print'] = $productInfo['s_illumachrome_direct_print'];
		$newProductData['priced_as_blank']           = $productInfo['b_priced_as_blank'];
		$newProductData['gift_box_width']            = $productInfo['n_gift_box_width'];

		$newProductData['gift_box_length']           = $productInfo['n_gift_box_length'];
		$newProductData['gift_box_height']           = $productInfo['n_gift_box_height'];
		$newProductData['giftbox_ship_weight']       = $productInfo['n_ship_weight'];
		$newProductData['product_category_keywords'] = $this->getProductCategoryKeywords( $read, $erpProductId );;

		$newProductData['main_product_image'] = $productInfo['s_canonical_url'];
		$newProductData['etc_image']          = $this->getImage( $read, $erpProductId, 'etc' );
		$newProductData['info_image']         = $this->getImage( $read, $erpProductId, 'info' );
		$newProductData['cat_img']            = $this->getImage( $read, $erpProductId, 'catimage' );

		$newProductData['three_sixtty_image']  = $this->getThreeSixtyImage( $read, $erpProductId );
		$newProductData['what_include_snotes'] = $this->getIncludedNotes( $read, $erpProductId );
		$newProductData['inspiration_card']    = $this->getIncludedCards( $read, $erpProductId, 'inspiration' );
		$newProductData['included_plate']      = $this->getIncludedCards( $read, $erpProductId, 'plates' );
		$newProductData['included_accent']      = $this->getIncludedCards( $read, $erpProductId, 'accent' );

		$newProductData['goal_setter_block'] = $this->getIncludedCards( $read, $erpProductId, 'goal_setter' );
		$newProductData['colored_spheres']   = $this->getIncludedCards( $read, $erpProductId, 'sphere' );
		$newProductData['optional_services'] = $this->getOptionalServices( $read, $erpProductId );
		$newProductData['optional_charge']   = $this->getOptionalCharges( $read, $erpProductId );

		$newProductData['additional_goal_setter'] = $this->getOptionalCards( $read, $erpProductId, 'goal_setter' );
		$newProductData['accent_product']         = $this->getOptionalCards( $read, $erpProductId, 'accent' );
		$newProductData['optional_base']          = $this->getOptionalCards( $read, $erpProductId, 'base' );
		$newProductData['optional_plates']        = $this->getOptionalCards( $read, $erpProductId, 'plates' );

		$newProductData['easels']            = $this->getOptionalCards( $read, $erpProductId, 'easels' );
		$newProductData['certificate_paper'] = $this->getOptionalCards( $read, $erpProductId, 'certificate' );
		$newProductData['tiff_img']          = $this->getImageByType( $read, $erpProductId, 'TIFF' );
		$newProductData['product_template']  = $this->getImage( $read, $erpProductId, 'template' );

		$newProductData['gift_box_image']      = $this->getImage( $read, $erpProductId, 'package' );
		$newProductData['orderinginformation'] = $this->getOrderinginformation( $read, $erpProductId );
		$newProductData['b_trophy_ok']         = $productInfo['b_trophy_ok'];


		return $newProductData;
	}

	/**
	 * Queries product database for similar product sizes. If similar sizes are found
	 * the smallest sizes canonical URL is return. If not false is returned.
	 * 
	 * Added by MGW 9/25/19
	 */
	public function getCanonicalUrl($connection, $awardName){
        $this->reconnectIfNeeded($connection);
        $awardName = $this->quoteEscape($awardName);
		$url = $connection->fetchOne("
			SELECT s_canonical_url
			FROM t_product
			WHERE s_award_name = '{$awardName}'
			AND '{$awardName}' > ' '
			AND s_award_size > ' '
			AND b_private = 0
			AND b_offline = 0
			ORDER BY s_award_size ASC
		");
		if(empty($url)){
			return false;
		}
		return $url;
	}

	public function getProductCategoryKeywords( $connection, $erpProductId ) {
        $this->reconnectIfNeeded($connection);
		$productCategoryKeywords = $connection->fetchCol( "SELECT s_keywords FROM t_category AS a
INNER JOIN t_cat_prod AS b ON a.k_category_id = b.k_category_id WHERE k_product_id = '$erpProductId'" );
		$keyword = array();
		foreach($productCategoryKeywords as $productCategoryKeyword) {
			if(!empty($productCategoryKeyword) && $productCategoryKeyword != '') {
				$keyword[] = $productCategoryKeyword;
			}
		}

		$keywordString = implode( ',', $keyword );
		return $keywordString;
	}

	public function setPricing($connection, $product, $erpProductId)
	{

		echo "Entered setPricing() for ERP ID: $erpProductId\n";

		$url = 'https://genesis.crystal-d.com/api/product/' . $erpProductId . '/website-prices';
		$apiKey = 'h2kznVAK7ybA4FHoPDZBblaJDQ6jb8';

		$options = [
			'http' => [
				'method' => 'GET',
				'header' =>
					"Accept: application/json\r\n" .
					"X-API-KEY: $apiKey\r\n" .
					'User-Agent: PHP/' . phpversion() . "\r\n",
				'timeout' => 15
			],
			'ssl' => [
				'verify_peer' => false,
				'verify_peer_name' => false,
				'allow_self_signed' => true,
			],
		];

		$context = stream_context_create($options);
		$response = @file_get_contents($url, false, $context);

		echo "Fetching pricing for ERP ID: $erpProductId\n";

		if ($response === false) {
			echo "Failed to fetch pricing from API for ERP ID: $erpProductId\n";
			return $product;
		}

		echo "Raw pricing response:\n$response\n";

		try {
			$pricing = json_decode($response, true);
			if (!is_array($pricing) || $pricing === null) {
				throw new Exception('Invalid JSON returned from API');
			}
		} catch (Exception $e) {
			echo "ERROR DECODING PRICING for ERP ID $erpProductId: " . $e->getMessage() . "\n";
			return $product;
		}

		echo "Parsed pricing data:\n";
		print_r($pricing);

		for ($i = 1; $i <= 5; $i++) {
			$productQtyKey = 'setProductquantity' . $i;
			$retailKey     = 'setRetailprice' . $i;
			$netKey        = 'setNetprice' . $i;
			$trophyKey     = 'setTrophyblankprice' . $i;

			if (isset($pricing[$i])) {
				echo "Tier $i => Qty: {$pricing[$i]['qty']}, Retail: {$pricing[$i]['retail']}, Net: {$pricing[$i]['net']}";

				$product->$productQtyKey($pricing[$i]['qty']);
				$product->$retailKey($pricing[$i]['retail']);
				$product->$netKey($pricing[$i]['net']);

				if (!empty($pricing['trophy_ok']) && isset($pricing[$i]['trophy'])) {
					echo ", Trophy: {$pricing[$i]['trophy']}";
					$product->$trophyKey($pricing[$i]['trophy']);
				}
				echo "\n";
			} else {
				echo "Tier $i: no pricing data available.\n";
			}
		}

		return $product;
	}




	public function getIncludedNotes( $connection, $erpProductId ) {
        $this->reconnectIfNeeded($connection);
		$notes = $connection->fetchCol( "SELECT s_value FROM t_code AS a
										INNER JOIN t_note_prod AS b On a.k_code_id = b.k_code_id
										WHERE b.k_product_id = '$erpProductId' AND i_seq2 > 0 ORDER BY i_seq2 ");

		if ( empty( $notes ) ) {
			return '';
		}

		foreach ( $notes as $note ) {
			$includedNotes[] = $note;
		}

		return implode( '|', $includedNotes );
	}

	public function getImage( $connection, $productId, $role ) {
        $this->reconnectIfNeeded($connection);

        if(in_array($role, ['360_video_1', '360_video_2'])){
            //360 videos need a sequence to determine which are backups these will be handled differently than most
            $images = $connection->fetchAll( "SELECT s_file_name, i_seq FROM t_image WHERE i_owner_id = '$productId' 
                AND s_role = '$role' ORDER BY i_seq" );
            if(empty($images))
                return '';
            return serialize($images);
        }

		//Fields have the possiblity to have multiple images.  Add more to array if you should lookup multiples
		$multipleImages = array( 'catimage', 'info');
		if ( in_array( $role, $multipleImages ) ) {

            $images = $connection->fetchCol( "SELECT s_file_name FROM t_image WHERE i_owner_id = '$productId' 
                AND s_role = '$role' AND s_file_name NOT LIKE '%tif%'" );

			//Keep with logic already in place with these
			$image = implode( '|', $images );
		} else {
			$image = $connection->fetchOne( "SELECT s_file_name FROM t_image WHERE i_owner_id = '$productId' 
                AND s_role = '$role' AND (s_image_type != 'TIFF' OR s_image_type != 'PDF')" );
		}

		//If image still not set put to empty string to remove
		if(empty($image)) {
			$image = '';
		}

		return $image;
	}

	public function getGiftBoxImage($connection, $productId) {
        $this->reconnectIfNeeded($connection);
		$sql = "SELECT COALESCE( p.k_image_id_package,
			   (SELECT n.k_image_id
			     FROM t_code n
			     WHERE n.s_column = 's_packaging'
			     AND n.s_value = p.s_packaging))
			FROM t_product p
			WHERE p.k_product_id = '$productId'";
		$image = $connection->fetchOne($sql);

		//If image still not set put to empty string to remove
		if (empty($image)) {
			$image = '';
		}

		return $image;
	}

    /**
     * Mark Wickline 2021-11-19
     * Quick hack to add two gift box images.
     */
	public function getGiftBoxImages($connection, $productId) {
        $this->reconnectIfNeeded($connection);
		$sql = "SELECT COALESCE( p.k_image_id_package,
			   (SELECT n.k_image_id
			     FROM t_code n
			     WHERE n.s_column = 's_packaging'
			     AND n.s_value = p.s_packaging)) as image_id,
                 p.s_packaging
			FROM t_product p
			WHERE p.k_product_id = '$productId'";
		$results = $connection->fetchAll($sql);

        $row = $results[0];

        if(empty($row['image_id'])){
            return '';
        }

        $image = $row['image_id'];

		// If s_packaging starts with "Black Gift" add this box image
        //33749
        if(!empty($row['s_packaging']) && preg_match('/^black gift/', strtolower($row['s_packaging']))){
            $image = "33749|" . $image;
        }
        return $image;
	}

	public function getImageByType( $connection, $productId, $imageType ) {
        $this->reconnectIfNeeded($connection);
		$images = $connection->fetchCol( "SELECT s_file_name FROM t_image WHERE s_image_type = '$imageType' AND i_owner_id = '$productId'" );
		$image  = implode( '|', $images );

		return $image;
	}

	/**
	 * I needed a work around to better control the 360 image popup with the iframe.  So do a lookup of each images and compare the previous
	 * dimensions.  Once done looping use the larger image sizes for everything
	 *
	 * @param $connection
	 * @param $productId
	 *
	 * @return string
	 */
	public function getThreeSixtyImage( $connection, $productId ) {
        $this->reconnectIfNeeded($connection);
		$images = $connection->fetchAll( "SELECT s_name, i_box_width, i_box_height, i_colorbox_width, i_colorbox_height FROM t_360_image AS a
											INNER JOIN t_360_prod AS b ON a.k_360 = b.k_360
											WHERE b.k_product_id = '$productId'" );

		//If there is not a three sixty image just move on
		if ( empty( $images ) ) {
			return '';
		}

		$popupWidth     = 0;
		$popupHeight    = 0;
		$colorboxWidth  = 0;
		$colorboxHeight = 0;

		//This will make sure the largest dimensions are chosen for the 360 image
		foreach ( $images as $image ) {
			if ( $image['i_box_width'] > $popupWidth ) {
				$popupWidth = $image['i_box_width'];
			}
			if ( $image['i_box_height'] > $popupHeight ) {
				$popupHeight = $image['i_box_height'];
			}
			if ( $image['i_colorbox_width'] > $colorboxWidth ) {
				$colorboxWidth = $image['i_colorbox_width'];
			}
			if ( $image['i_colorbox_height'] > $colorboxHeight ) {
				$colorboxHeight = $image['i_colorbox_height'];
			}

			$threeSixtyImage[] = $image['s_name'];
		}

		//Build the dimensions on the end of the area
		$threeSixtyImage[] = $popupWidth . 'x' . $popupHeight . 'x' . $colorboxWidth . 'x' . $colorboxHeight;

		//Keep with logic already in place with these
		$threeSixtyImage = implode( '|', $threeSixtyImage );

		return $threeSixtyImage;
	}

	public function getIncludedCards( $connection, $productId, $type ) {
        $this->reconnectIfNeeded($connection);
		$query = "SELECT s_item_num 
            FROM t_rel_prod 
            INNER JOIN t_product 
                ON t_rel_prod.k_product_id_child = t_product.k_product_id 
            WHERE k_product_id_parent = '$productId' AND b_included = 1";

		if ( $type == 'inspiration' ) {
			$query .= " AND k_relation_id = 19";
		} elseif ( $type == 'plates' ) {
			$query .= " AND k_relation_id IN (4,22)";
		} elseif ( $type == 'goal_setter' ) {
			$query .= " AND k_relation_id = 21";
		} elseif ( $type == 'sphere' ) {
			$query .= " AND k_relation_id = 26";
		} elseif ( $type == 'accent' ) {
			$query .= " AND k_relation_id IN (16,17,31,33)";
		} elseif ( $type == 'battery' ) {
			$query .= " AND k_relation_id = 28";
		} elseif ( $type == 'panels' ) {
			$query .= " AND k_relation_id = 29";
		}

		$cards = $connection->fetchCol( $query );

		//Keep with logic already in place with these
		$card = implode( '|', $cards );

		return $card;
	}

	public function getOptionalCards( $connection, $productId, $type ) {
        $this->reconnectIfNeeded($connection);
		$query = "SELECT s_item_num 
            FROM t_rel_prod 
            INNER JOIN t_product 
                ON t_rel_prod.k_product_id_child = t_product.k_product_id 
            WHERE k_product_id_parent = '$productId' 
            AND b_optional = 1";

		if ( $type == 'inspiration' ) {
			$query .= " AND k_relation_id = 19";
		} elseif ( $type == 'plates' ) {
			$query .= " AND k_relation_id IN (4,22)";
		} elseif ( $type == 'goal_setter' ) {
			$query .= " AND k_relation_id = 21";
		} elseif ( $type == 'sphere' ) {
			$query .= " AND k_relation_id = 26";
		} elseif ( $type == 'accent' ) {
			$query .= " AND k_relation_id IN (16,17,30)";
		} elseif ( $type == 'base' ) {
			$query .= " AND k_relation_id = 2";
		} elseif ( $type == 'easels' ) {
			$query .= " AND k_relation_id = 25";
		} elseif ( $type == 'certificate' ) {
			$query .= " AND k_relation_id = 24";
		} elseif ( $type == 'battery' ) {
			$query .= " AND k_relation_id = 28";
		} elseif ( $type == 'panels' ) {
			$query .= " AND k_relation_id = 29";
		}

		$cards = $connection->fetchCol( $query );

		//Keep with logic already in place with these
		$card = implode( '|', $cards );

		return $card;
	}

	public function getImprintProcess( $connection, $processId ) {
        $this->reconnectIfNeeded($connection);
		$query          = "SELECT s_process_desc FROM t_process WHERE k_process_id = '$processId'";
		$imprintProcess = $connection->fetchOne( $query );

		return $imprintProcess;
	}

	public function getOrderinginformation( $connection, $productId ) {
        $this->reconnectIfNeeded($connection);
		$query = "SELECT s_service_desc FROM t_service_prod AS a
					INNER JOIN t_service AS b ON a.k_service_id = b.k_service_id
					WHERE s_type = 'Order' AND b_secondary = 0 AND k_product_id = '$productId'";

		$info = $connection->fetchOne( $query );

		return $info;
	}

	public function getOptionalCharges( $connection, $productId ) {
        $this->reconnectIfNeeded($connection);
		$query = "SELECT a.n_price, a.s_service_desc, a.s_disclaimer, a.b_end_user FROM t_service AS a
					INNER JOIN t_service_prod AS b ON a.k_service_id =  b.k_service_id
				WHERE b.k_product_id = '$productId' AND s_type = 'Order' AND i_seq > 0 ORDER BY i_seq";

		$charges     = $connection->query( $query );
		$chargeArray = array();

		$i = 0;
		foreach ( $charges as $charge ) {
			$chargeArray[ $i ]['price']       = $charge['n_price'];
			$chargeArray[ $i ]['description'] = $charge['s_service_desc'];
			$chargeArray[ $i ]['disclaimer']  = $charge['s_disclaimer'];
			$i ++;
		}

		return serialize( $chargeArray );
	}

	public function getIncludedServices( $connection, $productId ) {
        $this->reconnectIfNeeded($connection);
		$query = "SELECT a.k_service_id FROM t_service_prod AS a
					INNER JOIN t_service AS b ON a.k_service_id = b.k_service_id
					  INNER JOIN t_service_prod_included AS c ON a.k_product_id = c.k_product_id AND a.k_service_id = c.k_service_id
					WHERE a.k_product_id = '$productId' AND b.s_service_code NOT LIKE 'ADDILLD%' AND b.i_seq > 0";

		$services = $connection->fetchCol( $query );

		//Keep with logic already in place with these
		$service = implode( '|', $services );
		return $service;
	}


	public function getServices( $connection, $productId, $serviceCode ) {
        $this->reconnectIfNeeded($connection);
		$query = "SELECT s_service_code, s_service_desc, s_disclaimer, n_price, s_eu_desc FROM t_code AS tc
INNER JOIN t_web_box_prod AS twbp ON tc.s_code = twbp.s_web_box_code
  INNER JOIN t_service AS ts ON twbp.k_service_id = ts.k_service_id
WHERE tc.s_column = 's_web_box_code' AND k_product_id = '$productId' AND tc.s_value LIKE '%$serviceCode%' AND ts.i_seq > 0 AND s_service_code NOT LIKE '%ADDILLD%' ORDER BY ts.i_seq";

		$services     = $connection->query( $query );
		$serviceArray = array();

		$i = 0;
		foreach ( $services as $service ) {
			$serviceArray[ $i ]['price']       = $service['n_price'];
			$serviceArray[ $i ]['description'] = $service['s_service_desc'];
			$serviceArray[ $i ]['disclaimer']  = $service['s_disclaimer'];
			$serviceArray[ $i ]['short_description']  = $service['s_eu_desc'];
			$serviceArray[ $i ]['code']  = $service['s_service_code'];
			$i ++;
		}

		return serialize( $serviceArray );
	}

	/**
	 * Get attribute code for category attributes. Input k_group_id.
	 * 
	 * These are all multiselect attributes
	 */
	public function getAttributeCode($groupId){
		if($groupId == '5') {
			return 'functionality';
		}elseif($groupId == '6') {
			return 'shapes_and_styles';
		}elseif($groupId == '9') {
			return 'material';
		}elseif($groupId == '15') {
			return 'imprint_processes';
		} else{
			return '';
		}
	}


	protected function stripAnsi($s)
{
    return preg_replace('/\x1B\[[\d;]*[A-Za-z]/', '', $s);
}

	/**
	 * Check if an attribute id exists for an attribute=>option set.
	 * If so return the id.
	 * 
	 * https://magento.stackexchange.com/questions/105366/set-dropdown-value-using-text-value
	 */
	public function attributeValueExists($attributeCode, $value)
{
    $attributeModel        = Mage::getSingleton('eav/entity_attribute');
    $attributeOptionsModel = Mage::getModel('eav/entity_attribute_source_table');
    $attribute             = $attributeModel->loadByCode('catalog_product', $attributeCode);
    $attributeOptionsModel->setAttribute($attribute);
    $options               = $attributeOptionsModel->getAllOptions(false);

    // normalize the incoming label
    $needle = html_entity_decode(trim($value), ENT_QUOTES, 'UTF-8');
    $needle = $this->stripAnsi($needle);

    foreach ($options as $option) {
        // normalize Magento’s stored label
        $label = html_entity_decode(trim($option['label']), ENT_QUOTES, 'UTF-8');
        $label = $this->stripAnsi($label);

        // for extra visibility, log _clean_ labels
        Mage::log("   comparing “{$label}” to “{$needle}”", null, 'imprint-debug.log');

        if (strcasecmp($label, $needle) === 0) {
            return $option['value'];
        }
    }

    return false;
}

	/**
	 * There are a "secondary" set of categories in the product db.  Internally there are four categories that should be
	 * thought of as category filters instead of actual categories.  This loops through everything and add / updates
	 * the different options.
	 *
	 * This was the route to go instead of making a ridculious category nesting structure
	 *
	 * @param $connection
	 * @param $product
	 * @param $productId
	 * @param $write
	 */
	public function getCategoryAttributes( $connection, $product, $erpProductId, $write ) {
        $this->reconnectIfNeeded($connection);
		$query = "SELECT GROUP_CONCAT(s_category_name) AS category_name,
						k_group_id,
						GROUP_CONCAT(i_seq) AS sort_order
				FROM t_product AS a
				INNER JOIN t_cat_prod AS b
				ON a.k_product_id = b.k_product_id
				INNER JOIN t_category AS c
				ON b.k_category_id = c.k_category_id
				WHERE a.k_product_id = '$erpProductId'
					AND k_group_id IN (6,9,5)
				GROUP BY  k_group_id";
				//MGW 8/27/19 imprint_processes are now being set by the category sync script.
				//Removed 15 from k_product_id where clause

		$categoryAttributes     = $connection->query( $query );

		foreach($categoryAttributes as $categoryAttribute) {

			$attributeCode = $this->getAttributeCode($categoryAttribute['k_group_id']);


			//Bail early if the attribute code is empty for some reason
			if(!isset($attributeCode) || empty($attributeCode)) {
				continue;
			}

			//Need to combine this into one array so works correctly with setting sort order on the option
			$optionsNames = explode(',', $categoryAttribute['category_name']);
			$sortOrder = explode(',', $categoryAttribute['sort_order']);
			$options = array_combine($optionsNames, $sortOrder);
			$attributeValues = array();

			foreach($options as $optionName => $sortOrder) {
				//Skip over if the sort order is lower than 1
				if($sortOrder > 0) {
					$attributeValues[] = $this->updateSelectAttribute( $optionName, $attributeCode, $sortOrder, $write );
				}
			}

			//Update the correct attributes base on what was selected
			$product->setData($attributeCode, implode(',',$attributeValues));
		}
	}

	/**
	 *Finds attribute option value, or creates the Option on the attribute if it doens't exist.

	 * @param $optionName
	 * @param $attributeCode
	 * @param $sortOrder
	 * @param $write
	 *
	 * @return mixed
	 */
	public function updateSelectAttribute( $optionName, $attributeCode, $sortOrder, $write ) {
		/**
		 * I've run into a lot of odd issues over the years using always make sure to clean option before moving on here
		 */
		$optionName = uc_words( trim( $optionName ) );
		$optionName = str_replace('_', ' ', $optionName);

		//Check if the attribute value is already there
		$attribute = Mage::getModel( 'eav/config' )->getAttribute( 'catalog_product', $attributeCode );
		foreach ( $attribute->getSource()->getAllOptions( true, true ) as $option ) {
			if ( $optionName == $option['label'] ) {
				return $option['value'];
			}
		}

		$attributeModel         = Mage::getModel( 'eav/entity_attribute' );
		$attribute_code = $attributeModel->getIdByCode( 'catalog_product', $attributeCode );
		$attribute      = $attributeModel->load( $attribute_code );

		$value['option'] = array( $optionName, $optionName );
		$result          = array( 'value' => $value );

		$attribute->setData( 'option', $result );
		$attribute->save();

		/**
		 * After saving need to run through all options one last time to get correct values
		 * This section is also used to correctly update the sort order since for some reason I have not seen how to do this
		 * in Magento core when creating the attribute
		 */
		$attribute = Mage::getModel( 'eav/config' )->getAttribute( 'catalog_product', $attributeCode );
		foreach ( $attribute->getSource()->getAllOptions( true, true ) as $option ) {
			if ( $optionName == $option['label'] ) {
				$optionId = $option['value'];
				//Update the old fashioned way why magento why
				$updatePosition = "UPDATE eav_attribute_option SET sort_order = '$sortOrder' WHERE option_id = '$optionId'";
				$write->query( $updatePosition );
				return $option['value'];
			}
		}
	}

	/**
	 * The award size is not in decimal format so this checks if the award size has a measurement with decimals
	 * the dash in award size tells if there is a decimal number if this is the case it looks like the height is
	 * always the correct one to use.
	 */
	public function formatHeight($awardSize, $height, $product) {

		if (strpos($awardSize, '-') !== false) {
			//This is the only way I see to do this for now given the award size is a text field
			$awardSizeArray = explode('-', $awardSize);
			$wholeNumber = (int) $awardSizeArray[0];

			$fraction = explode('/', $awardSizeArray[1]);
			$numerator = (int)$fraction[0];
			$denominator = (int)$fraction[1];

			$decimal = $numerator / $denominator;
			$awardSize = $wholeNumber + $decimal;
		}

		if (strpos($awardSize, 'x') !== false) {
			$awardSize = $height;
		}

		$product->setHeight( $awardSize );

		return $product;
	}

	/**
	 * Check if the product has any related products
	 * @param $erpProductId
	 * @param $awardSize
	 */
	public function setRelatedSizes($connection, $erpProductId, $awardSize, $product) {
        $this->reconnectIfNeeded($connection);
		$productModel = Mage::getModel( 'catalog/product' );
		$data           = array();

		//Clean up award size
		$awardSize = str_replace('"', '', $awardSize);
		$query = 'SELECT s_item_num FROM t_product
    				WHERE s_award_name = "'.$awardSize.'" AND k_product_id <> "'.$erpProductId.'" AND s_award_size > " "
    				ORDER BY s_award_size';

		$relatedProducts     = $connection->fetchCol( $query );
		$relatedCollection = $product->getRelatedProductCollection();
		$currentRelatedSkus = array();

		if($relatedProducts) {
			if($relatedCollection->count() > 0) {
				//Start by getting all of the old related products
				foreach ( $relatedCollection as $related ) {
					$currentRelatedSkus[] = $related->getSku();
					$data[ $related->getEntityId() ]['position'] = $related->getPosition();
				}
			}

			$i     = 0;
			foreach ( $relatedProducts as $relatedProduct ) {
				$productId = $productModel->getIdBySku( trim( $relatedProduct ) );
				//Need to first check if product exists
				if($productId) {
					$data[ $productId ] = array(
						'position' => $i
					);

					$i ++;
				}
			}

			if ( count( $data ) > 0 ) {
				$product->setRelatedLinkData( $data );
			}
		}

		unset($relatedCollection);
		unset($relatedProducts);
		unset($currentRelatedSkus);
		$productModel->clearInstance();
		return $product;
	}

	public function quickShipValue($quickShip) {
		$_product = Mage::getModel('catalog/product');
		$attribute = $_product->getResource()->getAttribute('quick_ship');
		$attributeValueId = 0;

		if ($attribute->usesSource()) {
			if($quickShip == 1) {
				$attributeValueId = $attribute->getSource()->getOptionId("Yes");
			} else {
				$attributeValueId = $attribute->getSource()->getOptionId("No");
			}
		}

		return $attributeValueId;
	}
}
