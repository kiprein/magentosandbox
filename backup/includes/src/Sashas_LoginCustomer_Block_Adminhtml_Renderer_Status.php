<?php
/**
 * @author		Sashas
 * @category    Sashas
 * @package     Sashas_LoginCustomer
 * @copyright   Copyright (c) 2015 Sashas IT Support Inc. (http://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */


class Sashas_LoginCustomer_Block_Adminhtml_Renderer_Status extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
	{
		$value =  $row->getData($this->getColumn()->getIndex());
		 
		switch ($value) {
			case 'fail' :
				$value='<span class="grid-severity-critical"><span>'.$value.'</span></span>';
				break;
				
			case 'pending' :
				$value='<span class="grid-severity-minor"><span>'.$value.'</span></span>';
				break;
				 
			case 'success' :
				$value='<span class="grid-severity-notice"><span>'.$value.'</span></span>';
				break;							 
			default:
				$value=$value;
				break;
		}
		
		return $value;
	}
}