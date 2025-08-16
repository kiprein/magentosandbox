<?php

class Js_Guestwishlist_ShareController extends Mage_Core_Controller_Front_Action {
	public function preDispatch() {
		parent::preDispatch();
		$route = $this->getRequest()->getRouteName();

		//Need to blocked logged in users from every getting to guest url
		if ( $route == 'js_guestwishlist' && Mage::getSingleton( 'customer/session' )->isLoggedIn() ) {
			return $this->_redirect( 'wishlist' );
		}
	}

	public function indexAction() {
		$this->loadLayout();
		$this->renderLayout();
	}

	public function sendAction() {

		if ( ! $this->_validateFormKey() ) {
			return $this->_redirect( '*/*/' );
		}

		$_helper = Mage::helper( 'js_guestwishlist' );

		$wishlistCount = $_helper->getGuestWishlistCount();
		if ( $wishlistCount == 0 ) {
			return $this->norouteAction();
		}

		//Intital form vars
		$emails        = explode( ',', $this->getRequest()->getPost( 'emails' ) );
		$message       = nl2br( htmlspecialchars( (string) $this->getRequest()->getPost( 'message' ) ) );
		$ccSelf        = $this->getRequest()->getPost( 'cc_myself' );
		$personalEmail = $this->getRequest()->getPost( 'personal_email' );

		$personalName  = $this->getRequest()->getPost( 'full_name' );
		$bccEmails     = Mage::helper( 'js_guestwishlist' )->getShareBccEmails();
		$recipientName = $this->getRequest()->getPost( 'recipient_name' );
		$error         = false;

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

			$wishlistBlock = $this->getLayout()->createBlock( 'js_guestwishlist/share_email_items' )->toHtml();

			$emails = array_unique( $emails );
			/* @var $emailModel Mage_Core_Model_Email_Template */
			$emailModel = Mage::getModel( 'core/email_template' );

			foreach ( $emails as $email ) {
				$emailModel->sendTransactional(
					23,
					$senderInfo,
					$email,
					$recipientName,
					array(
						'customer'  => $personalName,
						'full_name' => $personalName,
						'items'     => $wishlistBlock,
						'message'   => $message,
						'recipient_name' => $recipientName
					)
				);
			}

			$translate->setTranslateInline( true );

			//Clear out the wishlist now that it has been sent
			$guestWishlist = Mage::getModel( 'js_guestwishlist/guest_wishlist' );
			$wishlistKey   = $_helper->getWishlistKey();
			$sentItems = array();
			foreach ( $_helper->getGuestWishlist() as $item ) {
				//Used for the wishlist sent model
				$sentItems[] = $item->getProductId();
				if($_helper->deleteWishlistFlag() == 1) {
					$guestWishlist->deleteByProductId( $wishlistKey, $item->getProductId() );
				}
			}

			//Add record to sent model for the admin
			$sentModel = Mage::getModel('js_guestwishlist/wishlist_sent');
			$sentModel->setKey(Mage::helper( 'js_guestwishlist' )->generateKey())
			          ->setName($personalName)
			          ->setEmail($personalEmail)
			          ->setType('guest')
			          ->setDateSent(Mage::getModel( 'core/date' )->date( 'Y-m-d H:i:s' ))
			          ->setProductInfo(serialize($sentItems));

			$sentModel->save();

			Mage::getSingleton( 'core/session' )->addSuccess('Your Award Idea List has been sent.');
			$this->_redirect( 'guest-wishlist/view/' );
		} catch ( Exception $e ) {
			$translate->setTranslateInline( true );

			Mage::getSingleton( 'wishlist/session' )->addError( $e->getMessage() );
			Mage::getSingleton( 'wishlist/session' )->setSharingForm( $this->getRequest()->getPost() );
			$this->_redirect( '*/*/share' );
		}
	}
}