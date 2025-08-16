<?php
/**
 * @author		Sashas
 * @category    Sashas
 * @package     Sashas_LoginCustomer
 * @copyright   Copyright (c) 2015 Sashas IT Support Inc. (http://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */


class Sashas_LoginCustomer_Block_Adminhtml_Renderer_Admin extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
	{
		$customerUsername =  $row->getData($this->getColumn()->getIndex());
		$customerId =  $row->getData('admin_id');
		 	 
		return '<a href="'.Mage::helper("adminhtml")->getUrl("adminhtml/permissions_user/edit",array('user_id'=>$customerId)).'" target="_blank">'.$customerUsername.'</a>';
	}
}