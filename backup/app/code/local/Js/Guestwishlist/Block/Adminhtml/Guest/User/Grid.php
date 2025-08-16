<?php

class Js_Guestwishlist_Block_Adminhtml_Guest_User_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('wishlistGrid');
		$this->setFilterVisibility(false);
		$this->setSaveParametersInSession(true);
	}

	protected function _prepareCollection()
	{
		$collection = Mage::getModel('js_guestwishlist/wishlist_sent')->getCollection();
		$collection->addFieldToFilter('type', array('eq' => 'guest'));

		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	protected function _prepareColumns()
	{
		$this->addColumn('name', array(
			'header'    => Mage::helper('js_guestwishlist')->__('Customer Name'),
			'index'     => 'name',
		));

		$this->addColumn('email', array(
			'header'    => Mage::helper('js_guestwishlist')->__('Customer Email'),
			'index'     => 'email',
		));

		$this->addColumn('date_sent', array(
			'header'    => Mage::helper('js_guestwishlist')->__('Date Sent'),
			'index'     => 'date_sent',
			'renderer' => 'Js_Guestwishlist_Block_Adminhtml_Template_Grid_Renderer_Time'
		));

		$this->addColumn('product_info', array(
			'header'    => Mage::helper('js_guestwishlist')->__('Items'),
			'index'     => 'product_info',
			'renderer' => 'Js_Guestwishlist_Block_Adminhtml_Template_Grid_Renderer_Items'
		));

		return parent::_prepareColumns();
	}
}
