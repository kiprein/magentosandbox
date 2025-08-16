<?php
/**
 * @author		Sashas
 * @category    Sashas
 * @package     Sashas_LoginCustomer
 * @copyright   Copyright (c) 2015 Sashas IT Support Inc. (http://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

class Sashas_LoginCustomer_Block_Adminhtml_Log extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{ 
		$this->_controller = 'adminhtml_log';
		$this->_blockGroup = 'logincustomer';
		$this->_headerText = Mage::helper('logincustomer')->__('Manage Login As Customer Log');		 
		parent::__construct();
		$this->_removeButton('add');
	}
}
