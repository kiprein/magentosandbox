<?php

require_once 'Mage/Customer/controllers/AccountController.php';

class Js_Customer_AccountController extends Mage_Customer_AccountController {
	/**
	 * Login post action
	 */
	public function loginPostAction() {
		if ( ! $this->_validateFormKey() ) {
			$this->_redirect( '*/*/' );

			return;
		}

		if ( $this->_getSession()->isLoggedIn() ) {
			$this->_redirect( '*/*/' );

			return;
		}
		$session = $this->_getSession();

		if ( $this->getRequest()->isPost() ) {
			$login = $this->getRequest()->getPost( 'login' );
			if ( ! empty( $login['username'] ) && ! empty( $login['password'] ) ) {
				try {
					$session->login( $login['username'], $login['password'] );

					//Need to check if the customer is in goldmine.  If so update Magento address information
					$email    = $login['username'];
					$customerStaus = Mage::helper('js_customer')->goldmineCustomerUpdate($email);

					if ( $session->getCustomer()->getIsJustConfirmed() ) {
						$this->_welcomeCustomer( $session->getCustomer(), true );
					}
				} catch ( Mage_Core_Exception $e ) {
					switch ( $e->getCode() ) {
						case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
							$value   = $this->_getHelper( 'customer' )->getEmailConfirmationUrl( $login['username'] );
							$message = $this->_getHelper( 'customer' )->__( 'This account is not confirmed. <a href="%s">Click here</a> to resend confirmation email.', $value );
							break;
						case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
							$customerStatus = Mage::helper('js_customer')->goldmineCustomerUpdate($login['username']);
							if($customerStatus == 'new') {
								$session->addSuccess('This is your first visit to our web site. Login instructions have been sent to your email address.');
							}
							$message = $e->getMessage();

							break;
						default:
							$message = $e->getMessage();
					}
					$session->addError( $message );
					$session->setUsername( $login['username'] );
				} catch ( Exception $e ) {
					// Mage::logException($e); // PA DSS violation: this exception log can disclose customer password
				}
			} else {
				$session->addError( $this->__( 'Login and password are required.' ) );
			}
		}

		$this->_loginPostRedirect();
	}

	/**
	 * There is a weird spam issue where users are somehow being creating going through this action.
	 */
	public function createPostAction()
	{
		$this->_redirect("/");
	}

}