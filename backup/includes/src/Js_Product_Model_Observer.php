<?php

class Js_Product_Model_Observer {
	public function submitFriendForm($observer) {
		$webform = $observer->getWebform();
		$webformCode = $webform->getCode();

		if($webformCode == 'email-friend') {
			$this->form   = $webform;
			$this->result = Mage::getModel('webforms/results')->load($observer->getResult()->getId());

			$templateId = 24;

			$sku = $this->getValueByCode('product_sku');
			$sendName  = $this->getValueByCode('your_name');
			$sendEmail = $this->getValueByCode('your_email_address');
			$recipientName = $this->getValueByCode('recipient_name');  //Not used currently

			$ccSelf = $this->getValueByCode('cc_myself');
			$emails = explode(',', $this->getValueByCode('email_addresses'));

			$productId = Mage::getModel('catalog/product')->getIdBySku(trim($sku));
			$product   = Mage::getModel('catalog/product')->load($productId);
			$productBlock = $this->getProductBlock($product);

			$sender = Array('name' => $sendName, 'email' => $sendEmail);

			if ($ccSelf) {
				$emails[] = $sendEmail;
			}

			$vars = array(
				'recipient_name'     => $recipientName,
				'email_message'      => $this->getValueByCode('message'),
				'product_block' => $productBlock,
				'send_name' => $sendName,
			);

			$translate          = Mage::getSingleton('core/translate');
			$transactionalEmail = Mage::getModel('core/email_template');

			foreach ($emails as $email) {
				$transactionalEmail->sendTransactional(
					$templateId,
					$sender,
					$email,
					$recipientName,
					$vars
				);
			}
			$translate->setTranslateInline(true);
		}

		return $observer;
	}

	protected function getValueByCode($code) {
		if (empty($this->result) || empty($this->form)) return false;

		foreach ($this->form->getFieldsToFieldsets() as $fieldset) {
			foreach ($fieldset['fields'] as $field) {
				if ($field->getCode() == $code) {
					return $this->result->getData('field_' . $field->getId());
				}
			}
		}

	}

	protected function getProductBlock($product) {
		$baseUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
		$productUrl = $baseUrl . $product->getUrlPath();
		$imageName = $product->getMainProductImage();
		$helper = Mage::helper('js_product');

		$html = '<div style="border:1px solid #E0E0E0; padding:15px; background:#F9F9F9;">';
		$html .= '<table cellspacing="0" cellpadding="0" border="0" width="800">';
		$html .= '<tr>';
		$html .= '<td width="23.5%">';
		$html .= '<p style="font-size:12px;font-family:Verdana,Arial;font-weight:normal; text-align:center">';
		$html .= '<a href="'. $productUrl.'" style="color:#3696c2" target="_blank">';
		$html .= '<img style="border:1px solid #ccc" width="135" height="135" src="https://image.crystal-d.com/img/u245-y/jpg/'.$imageName.'" />';
		$html .= '</a><br>';
		$html .= '<a href="'.$productUrl.'" style="color:#203548;display: block;" target="_blank">';
		$html .= '<strong style="font-family:Verdana,Arial;font-weight:normal">'.$product->getName().'</strong>';
		$html .= '</a><br>';
		$html .= '<span style="display: block;">';
		$html .= '#'.$product->getSku().'<br>';
		$html .= $helper->getRetailPriceRange($product).'<br>';
		$html .= round($product->getWeight(), 2) . ' lbs<br>';
		$html .= $product->getDimension();
		$html .= '</span>';
		$html .= '</p>';
		$html .= '</td>';
		$html .= '</tr>';
		$html .= '</table>';

		return $html;
	}
}