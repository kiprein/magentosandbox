<?php

class Js_Guestwishlist_Block_Adminhtml_Guest_User extends Mage_Adminhtml_Block_Widget_Grid_Container {
	public function __construct()
	{
		$this->_controller = 'adminhtml_wishlist';
		$this->_blockGroup = 'js_guestwishlist/adminhtml_guest_user_grid';
		$this->_headerText = Mage::helper('js_guestwishlist')->__('Guest Users Wishlists');

		$this->_addButton('Export Wishlists', array(
			'label'     => Mage::helper('adminhtml')->__('Export Wishlists'),
			'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/exportGuests') . '\')',
			'class'     => 'save',
		),-1,15);

		parent::__construct();
		$this->_removeButton('add');
	}
}