<?php

class Js_Customer_Model_Session extends Mage_Customer_Model_Session
{
	/**
	 * Customer authorization.  This was rewritten to take into account that Magento and Goldmine (ERP) email
	 * address could be different cases.  This just adds in an addition try to login in if the first one fails
	 *
	 * @param   string $username
	 * @param   string $password
	 * @return  bool
	 */
	public function login($username, $password)
	{
		/** @var $customer Mage_Customer_Model_Customer */
		$customer = Mage::getModel('customer/customer')
		                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId());

		if ($customer->authenticate($username, $password)) {
			$this->setCustomerAsLoggedIn($customer);
			return true;
		} else {
			$username = strtolower($username);
			if ($customer->authenticate($username, $password)) {
				$this->setCustomerAsLoggedIn($customer);
				return true;
			}
			return false;
		}
	}
}