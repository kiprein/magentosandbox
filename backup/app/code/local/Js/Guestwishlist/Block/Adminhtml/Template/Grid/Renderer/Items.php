<?php

class Js_Guestwishlist_Block_Adminhtml_Template_Grid_Renderer_Items
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
	{
		$_products = unserialize($row->product_info);
		$_resource = Mage::getSingleton('catalog/product')->getResource();
		$out = '';

		foreach ($_products as $_productId) {
			$productName = $_resource->getAttributeRawValue($_productId,  'name', Mage::app()->getStore());
			$out .= $productName.'<br>';
		}

		return $out;
	}
}