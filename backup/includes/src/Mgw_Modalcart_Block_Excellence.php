<?php
//http://excellencemagentoblog.com/blog/2011/10/10/magento-onestep-checkout-add-step/
class Mgw_Modalcart_Block_Excellence extends Mage_Checkout_Block_Onepage_Abstract{
	protected function _construct()
	{
		$this->getCheckout()->setStepData('excellence', array(
            'label'     => Mage::helper('checkout')->__('Additional Information'),
            'is_show'   => $this->isShow()
		));
		parent::_construct();
	}
}