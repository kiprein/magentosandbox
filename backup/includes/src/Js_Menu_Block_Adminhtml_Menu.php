<?php

class Js_Menu_Block_Adminhtml_Menu extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_menu';
        $this->_blockGroup = 'js_menu';
        $this->_headerText = Mage::helper('js_menu')->__('Menu Manager');
        $this->_addButtonLabel = Mage::helper('js_menu')->__('Add Menu Item');

        $this->_addButton('Import Menu', array(
			'label'     => Mage::helper('adminhtml')->__('Import Menu'),
			'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/import') . '\')',
			'class'     => 'add',
		),-1,15);

        parent::__construct();
    }
}