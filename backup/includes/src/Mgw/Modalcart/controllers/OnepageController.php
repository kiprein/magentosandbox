<?php
require_once 'Mage/Checkout/controllers/OnepageController.php';
/**
 * Added step to the checkout process via
 * http://excellencemagentoblog.com/blog/2011/10/10/magento-onestep-checkout-add-step/
 */
class Mgw_Modalcart_OnepageController extends  Mage_Checkout_OnepageController{
	public function saveExcellenceAction(){
		if ($this->_expireAjax()) {
			return;
		}
		if ($this->getRequest()->isPost()) {
			$data = $this->getRequest()->getPost('excellence', array());
			
			$result = $this->getOnepage()->saveExcellence($data);
			
			if (!isset($result['error'])) {
				$this->loadLayout('checkout_onepage_review');
                $result['goto_section'] = 'review';
                $result['update_section'] = array(
					'name' => 'review',
                    'html' => $this->_getReviewHtml()
                );
			}

			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
		}
	}
}