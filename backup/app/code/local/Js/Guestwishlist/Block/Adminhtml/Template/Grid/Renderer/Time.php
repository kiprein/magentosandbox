<?php

class Js_Guestwishlist_Block_Adminhtml_Template_Grid_Renderer_Time
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
	{
		$out = date('m/d/Y h:i a', strtotime($row->date_sent));

		return $out;
	}
}