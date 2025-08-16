<?php

/**
 * Created by PhpStorm.
 * User: Jon Saverda
 * Date: 12/11/2016
 * Time: 9:14 AM
 */
class Js_Customer_Helper_Data extends Mage_Core_Helper_Abstract {

	/**
	 * Try to update or create the user if they are found in goldmine
	 * @param $email
	 *
	 * @return bool|string
	 * @throws Exception
	 */
	public function goldmineCustomerUpdate( $email ) {
		//Check if anything about user can be found
		//$email = 'No';
		$arrContextOptions= [
			'ssl' => [
				 'verify_peer'=> false,
				 'verify_peer_name'=> false,
			],
	  	];
		$json     = file_get_contents(
			'https://db.crystal-d.com/scripts/GoldmineAPI.php?email=' . $email . '',
			false,
			stream_context_create($arrContextOptions)
		);
		$response = json_decode( $json, true );

		//The array has a ton of spaces everywhere when coming in so you need to clean it up
		$response = array_map( 'trim', $response );

		//Only start customer creation / update if they are found
		if ( $response['status'] == 'OK' ) {
			$websiteId = Mage::app()->getWebsite()->getId();
			$store     = Mage::app()->getStore();

			$customer = Mage::getModel( 'customer/customer' );
			$customer->setWebsiteId( $websiteId );
			$customer->loadByEmail( $email );

			$userExist = $customer->getId();

			//Setup customer group
			$roleId = $response['Role'];
			if ( $roleId == "Dist" ) {
				$groupId = "5";
			} elseif ( $roleId == "Employee" ) {
				$groupId = "6";
			} else {
				$groupId = "4";
			}

			//If the customer does not exist in Magento create them
			if ( ! $userExist ) {
				$loginPassword = Mage::helper( 'core' )->getRandomString( $length = 7 );
				$customer      = Mage::getModel( "customer/customer" );
				$customer->setWebsiteId( $websiteId )
				         ->setStore( $store )
				         ->setEmail( $email )
				         ->setPassword( $loginPassword );
			}

			$customer->setFirstname( $response['FirstName'] );
			$customer->setLastname( $response['LastName'] );
			$customer->setGroupId( $groupId );
            //TODO, these can be stored for injection later.
            $customer->setHasTerms( empty($response['terms'])? 'no' : 'yes');
			$customer->save();

			//Some users may not have address information attached need to check this first and only run this section if they do
			if(isset($response['City']) && isset($response['State']) && isset($response['Address1']) && isset($response['ASI'])) {
				//If the customer exist removed the addresses.  My guess is this was the only way to update
				if ( $userExist ) {
					$addresses = $customer->getAddresses();
					foreach ( $addresses as $address ) {
						$address->delete();
					}
				}

				//Save the address to the customer record
				//Need to get the region id
				$region  = Mage::getModel( 'directory/region' )->loadByCode( $response['State'], 'US' );
				$stateId = $region->getId();

                $address = Mage::getModel( "customer/address" );
                $street = $response['Address1'];
                if(isset($response['Address2']))
                    $street .= "\n" . $response['Address2'];
				$address->setCustomerId( $customer->getId() )
				        ->setFirstname( $response['FirstName'] )
				        ->setLastname( $response['LastName'] )
				        ->setAraAsi( $response['ASI'] )
				        ->setCompany( $response['Company'] )
				        ->setTelephone( $response['Phone'] )
				        ->setStreet( $street )
				        ->setCity( $response['City'] )
				        ->setRegion_id( $stateId )
				        ->setCountry_id( 'US' )
				        ->setPostcode( $response['Zip'] )
				        ->setIsDefaultBilling( '1' )
				        ->setIsDefaultShipping( '1' )
				        ->setSaveInAddressBook( '1' );

				try {
					$address->save();
				} catch ( Exception $e ) {
					Zend_Debug::dump( $e->getMessage() );
				}
			}

			//Once everything is done and the user is new need to send them an email
			if(!$userExist) {
				$mailTemplate = Mage::getModel( 'core/email_template' );
				$translate    = Mage::getSingleton( 'core/translate' );

				$templateId          = 19; //template for sending customer data
				$template_collection = $mailTemplate->load( $templateId );
				$template_data       = $template_collection->getData();
				$mailSubject         = $template_data['template_subject'];

				//https://magento.stackexchange.com/questions/122779/how-to-get-magento-store-email-address-and-name-in-my-custom-module
				$sender = array(
					'name'  => Mage::getStoreConfig('trans_email/ident_general/name'),
					'email' => Mage::getStoreConfig('trans_email/ident_general/email')
				);

				$vars = array(
					'fname'    => $response['FirstName'],
					'emailid'  => $email,
					'password' => $loginPassword
				);

				$storeId = Mage::app()->getStore()->getId();
				$model   = $mailTemplate->setReplyTo( $sender['email'] )->setTemplateSubject( $mailSubject );

				$model->sendTransactional( $templateId, $sender, $email, $response['FirstName'], $vars, $storeId );
				if ( ! $mailTemplate->getSentSuccess() ) {
					return false;
				}
				return 'new';
			} else {
				return 'update';
			}
		}
	}
}
