<?php

require_once 'Mage/Wishlist/controllers/IndexController.php';

class Js_Guestwishlist_IndexController extends Mage_Wishlist_IndexController {
	public function preDispatch() {
		$guestActions = array( 'addGuest', 'removeGuest' );
		$action       = $this->getRequest()->getActionName();
		$route        = $this->getRequest()->getRouteName();
		//If doing any of the guest action skip over the authentication
		if ( in_array( $action, $guestActions ) ) {
			$this->_skipAuthentication = true;
		}

		parent::preDispatch();

		//Need to blocked logged in users from every getting to guest url
		if ( $route == 'js_guestwishlist' && Mage::getSingleton( 'customer/session' )->isLoggedIn() ) {
			return $this->_redirect( 'wishlist' );
		}

		if ( ! $this->_skipAuthentication && ! Mage::getSingleton( 'customer/session' )->authenticate( $this ) ) {
			$this->setFlag( '', 'no-dispatch', true );
			if ( ! Mage::getSingleton( 'customer/session' )->getBeforeWishlistUrl() ) {
				Mage::getSingleton( 'customer/session' )->setBeforeWishlistUrl( $this->_getRefererUrl() );
			}
			Mage::getSingleton( 'customer/session' )->setBeforeWishlistRequest( $this->getRequest()->getParams() );
		}
		if ( ! Mage::getStoreConfigFlag( 'wishlist/general/active' ) ) {
			$this->norouteAction();

			return;
		}
	}

	/**
	 * Add the item to wish list
	 * Rewritten to stay on same page as adding and show different success message
	 *
	 * @return Mage_Core_Controller_Varien_Action|void
	 */
	protected function _addItemToWishList()
	{
		$wishlist = $this->_getWishlist();
		if (!$wishlist) {
			return $this->norouteAction();
		}

		$session = Mage::getSingleton('customer/session');

		$productId = (int)$this->getRequest()->getParam('product');
		if (!$productId) {
			$this->_redirect('*/');
			return;
		}

		$product = Mage::getModel('catalog/product')->load($productId);
		if (!$product->getId() || !$product->isVisibleInCatalog()) {
			$session->addError($this->__('Cannot specify product.'));
			$this->_redirect('*/');
			return;
		}

		try {
			$requestParams = $this->getRequest()->getParams();
			if ($session->getBeforeWishlistRequest()) {
				$requestParams = $session->getBeforeWishlistRequest();
				$session->unsBeforeWishlistRequest();
			}
			$buyRequest = new Varien_Object($requestParams);

			$result = $wishlist->addNewItem($product, $buyRequest);
			if (is_string($result)) {
				Mage::throwException($result);
			}
			$wishlist->save();

			Mage::dispatchEvent(
				'wishlist_add_product',
				array(
					'wishlist' => $wishlist,
					'product' => $product,
					'item' => $result
				)
			);

			$referer = $session->getBeforeWishlistUrl();
			if ($referer) {
				$session->setBeforeWishlistUrl(null);
			} else {
				$referer = $this->_getRefererUrl();
			}

			/**
			 *  Set referer to avoid referring to the compare popup window
			 */
			$session->setAddActionReferer($referer);

			Mage::helper('wishlist')->calculate();
		} catch (Mage_Core_Exception $e) {
			$session->addError($this->__('An error occurred while adding item to wishlist: %s', $e->getMessage()));
		}
		catch (Exception $e) {
			$session->addError($this->__('An error occurred while adding item to wishlist.'));
		}

		if(isset($requestParams['product_page']) && $requestParams['product_page'] == 1) {
//			Mage::getSingleton( 'core/session' )->addSuccess($product->getShortDescription() .' has been added to your award idea list.');
//			return $this->_redirect('*');
			Mage::getSingleton( 'core/session' )->addSuccess('<a href="'.Mage::getUrl('wishlist').'">View Award Idea List</a>' );
			$this->_redirectReferer();
		} else {
			Mage::getSingleton( 'core/session' )->addSuccess('<a href="'.Mage::getUrl('wishlist').'">View Award Idea List</a>' );
			$this->_redirectReferer();
		}

	}

	/**
	 * Remove item
	 *
	 * Over written to add a message that the item has been successfully removed from the wishlist
	 */
	public function removeAction()
	{
		$id = (int) $this->getRequest()->getParam('item');
		$item = Mage::getModel('wishlist/item')->load($id);
		if (!$item->getId()) {
			return $this->norouteAction();
		}
		$wishlist = $this->_getWishlist($item->getWishlistId());
		if (!$wishlist) {
			return $this->norouteAction();
		}
		try {
			$item->delete();
			$wishlist->save();
		} catch (Mage_Core_Exception $e) {
			Mage::getSingleton('customer/session')->addError(
				$this->__('An error occurred while deleting the item from wishlist: %s', $e->getMessage())
			);
		} catch (Exception $e) {
			Mage::getSingleton('customer/session')->addError(
				$this->__('An error occurred while deleting the item from wishlist.')
			);
		}

		Mage::helper('wishlist')->calculate();

		$_resource   = Mage::getSingleton( 'catalog/product' )->getResource();
		$productName = $_resource->getAttributeRawValue( $item->getProductId(), 'short_description', Mage::app()->getStore() );
		Mage::getSingleton( 'core/session' )->addSuccess($productName .' has been removed from your Award Idea List.');

		$this->_redirectReferer(Mage::getUrl('*/*'));
	}

	public function sendAction() {
		if ( ! $this->_validateFormKey() ) {
			return $this->_redirect( '*/*/' );
		}

		$wishlist = $this->_getWishlist();
		if ( ! $wishlist ) {
			return $this->norouteAction();
		}

		$emails        = explode( ',', $this->getRequest()->getPost( 'emails' ) );
		$message       = nl2br( htmlspecialchars( (string) $this->getRequest()->getPost( 'message' ) ) );
		$ccSelf        = $this->getRequest()->getPost( 'cc_myself' );
		$personalEmail = $this->getRequest()->getPost( 'personal_email' );
		$personalName  = $this->getRequest()->getPost( 'full_name' );
		$bccEmails     = Mage::helper( 'js_guestwishlist' )->getShareBccEmails();
		$recipientName = $this->getRequest()->getPost( 'recipient_name' );

		//Used to send the email from the actual person filling out the form
		$senderInfo = array( 'name' => $personalName, 'email' => $personalEmail );

		if ( ! $message ) {
			$message = Mage::helper( 'js_guestwishlist' )->getDefaultShareMessage();
		}

		//Check if should be sent to self
		if ( $ccSelf ) {
			$emails[] = $personalEmail;
		}

		//Add in an bcc emails
		if ( $bccEmails ) {
			$bccEmails = explode( ',', $bccEmails );
			foreach ( $bccEmails as $bccEmail ) {
				$emails[] = $bccEmail;
			}
		}

		$error = false;
		if ( empty( $emails ) ) {
			$error = $this->__( 'Email address can\'t be empty.' );
		} else {
			foreach ( $emails as $index => $email ) {
				$email = trim( $email );
				if ( ! Zend_Validate::is( $email, 'EmailAddress' ) ) {
					$error = $this->__( 'Please input a valid email address.' );
					break;
				}
				$emails[ $index ] = $email;
			}
		}
		if ( $error ) {
			Mage::getSingleton( 'wishlist/session' )->addError( $error );
			Mage::getSingleton( 'wishlist/session' )->setSharingForm( $this->getRequest()->getPost() );
			$this->_redirect( '*/*/share' );

			return;
		}

		$translate = Mage::getSingleton( 'core/translate' );
		/* @var $translate Mage_Core_Model_Translate */
		$translate->setTranslateInline( false );

		try {
			$customer = Mage::getSingleton( 'customer/session' )->getCustomer();

			/*if share rss added rss feed to email template*/
			if ( $this->getRequest()->getParam( 'rss_url' ) ) {
				$rss_url = $this->getLayout()
				                ->createBlock( 'wishlist/share_email_rss' )
				                ->setWishlistId( $wishlist->getId() )
				                ->toHtml();
				$message .= $rss_url;
			}
			$wishlistBlock = $this->getLayout()->createBlock( 'wishlist/share_email_items' )->toHtml();

			$emails = array_unique( $emails );
			/* @var $emailModel Mage_Core_Model_Email_Template */
			$emailModel = Mage::getModel( 'core/email_template' );

			$sharingCode = $wishlist->getSharingCode();
			foreach ( $emails as $email ) {
				$emailModel->sendTransactional(
					Mage::getStoreConfig( 'wishlist/email/email_template' ),
					$senderInfo,
					$email,
					$recipientName,
					array(
						'customer'       => $personalName,
						'full_name'      => $personalName,
						'salable'        => $wishlist->isSalable() ? 'yes' : '',
						'items'          => $wishlistBlock,
						'addAllLink'     => Mage::getUrl( '*/shared/allcart', array( 'code' => $sharingCode ) ),
						'viewOnSiteLink' => Mage::getUrl( '*/shared/index', array( 'code' => $sharingCode ) ),
						'message'        => $message,
						'recipient_name' => $recipientName
					)
				);
			}

			$wishlist->setShared( 1 );
			$wishlist->save();

			$translate->setTranslateInline( true );

			Mage::dispatchEvent( 'wishlist_share', array( 'wishlist' => $wishlist ) );
			Mage::getSingleton( 'customer/session' )->addSuccess('Your Award Idea List has been sent.');

			$sentItems = array();
			//Clear out the wishlist now that it has been sent
			foreach ( $wishlist->getItemCollection() as $item ) {
				//Used for the wishlist sent model
				$sentItems[] = $item->getProductId();
				if(Mage::helper( 'js_guestwishlist' )->deleteWishlistFlag() == 1) {
					$item->delete();
				}
			}

			//Add record to sent model for the admin
			$sentModel = Mage::getModel('js_guestwishlist/wishlist_sent');
			$sentModel->setKey(Mage::helper( 'js_guestwishlist' )->generateKey())
				->setName($personalName)
				->setEmail($personalEmail)
				->setType('general')
				->setDateSent(Mage::getModel( 'core/date' )->date( 'Y-m-d H:i:s' ))
				->setProductInfo(serialize($sentItems));

			$sentModel->save();

			$this->_redirect( '*/*', array( 'wishlist_id' => $wishlist->getId() ) );
		} catch ( Exception $e ) {
			$translate->setTranslateInline( true );

			Mage::getSingleton( 'wishlist/session' )->addError( $e->getMessage() );
			Mage::getSingleton( 'wishlist/session' )->setSharingForm( $this->getRequest()->getPost() );
			$this->_redirect( '*/*/share' );
		}
	}

	public function addGuestAction() {
		//Setup intitial cars
		$_helper     = Mage::helper( 'js_guestwishlist' );
		$wishlistKey = Mage::getSingleton( 'core/session' )->getWishlistKey();
		$params      = Mage::app()->getRequest()->getParams();
		$dateAdded   = Mage::getModel( 'core/date' )->date( 'Y-m-d H:i:s' );
		$_resource   = Mage::getSingleton( 'catalog/product' )->getResource();

		//Need to set the wishlist key if not set yet
		if ( ! $wishlistKey ) {
			$wishlistKey = $_helper->generateKey();
			Mage::getSingleton( 'core/session' )->setWishlistKey( $wishlistKey );
		}

		$model = Mage::getModel( 'js_guestwishlist/guest_wishlist' );
		$model->setKey( $wishlistKey )
		      ->setSku( $params['sku'] )
		      ->setProductId( $params['product_id'] )
		      ->setDateAdded( $dateAdded );

		$model->save();

		if(isset($params['product_page']) && $params['product_page'] == 1) {
//			$productName = $_resource->getAttributeRawValue( $params['product_id'], 'short_description', Mage::app()->getStore() );
//			Mage::getSingleton( 'core/session' )->addSuccess($productName .' has been added to your award idea list.');
//			$this->_redirect('guest-wishlist/view/');
			Mage::getSingleton( 'core/session' )->addSuccess('<a href="'.Mage::getUrl('guest-wishlist/view/').'">View Award Idea List</a>' );
			$this->_redirectReferer();
		} else {
			Mage::getSingleton( 'core/session' )->addSuccess('<a href="'.Mage::getUrl('guest-wishlist/view/').'">View Award Idea List</a>' );
			$this->_redirectReferer();
		}
	}

	public function removeGuestAction() {
		$wishlistKey = Mage::getSingleton( 'core/session' )->getWishlistKey();
		$params      = Mage::app()->getRequest()->getParams();

		$model = Mage::getModel( 'js_guestwishlist/guest_wishlist' )->deleteByProductId( $wishlistKey, $params['product_id'] );

		$_resource   = Mage::getSingleton( 'catalog/product' )->getResource();
		$productName = $_resource->getAttributeRawValue( $params['product_id'], 'short_description', Mage::app()->getStore() );
		Mage::getSingleton( 'core/session' )->addSuccess($productName .' has been removed from your Award Idea List.');
		$this->_redirectReferer();
	}
}
