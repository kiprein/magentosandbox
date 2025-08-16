<?php

class Js_Banners_Block_Adminhtml_Template_Form_Renderer_Image
    extends Varien_Data_Form_Element_Image {

	protected function _getUrl(){
		$url = false;
		if ($this->getValue()) {
			$url = Mage::getBaseUrl('media').'banners/'.$this->getValue();
		}
		return $url;
	}
}