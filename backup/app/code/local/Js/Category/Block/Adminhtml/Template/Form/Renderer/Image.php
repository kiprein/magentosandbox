<?php

class Js_Category_Block_Adminhtml_Template_Form_Renderer_Image
    extends Varien_Data_Form_Element_Image {

	protected function _getUrl(){
		$url = false;
		if ($this->getValue()) {
			$url = Mage::getBaseUrl('media').'featured_gallery/'.$this->getValue();
		}
		return $url;
	}
}