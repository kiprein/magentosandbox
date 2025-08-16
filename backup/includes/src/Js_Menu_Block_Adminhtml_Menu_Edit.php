<?php

class Js_Menu_Block_Adminhtml_Menu_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'js_menu';
        $this->_controller = 'adminhtml_menu';

        $this->_updateButton('save', 'label', Mage::helper('js_menu')->__('Save Menu Item'));
        $this->_updateButton('delete', 'label', Mage::helper('js_menu')->__('Delete menu Item'));
    }
    
    public function getHeaderText()
    {
        if( Mage::registry('menu_item_data') && Mage::registry('menu_item_data')->getId() ) {
            return Mage::helper('js_menu')->__("Edit Menu Item '%s'", Mage::registry('menu_item_data')->getTitle());
        } else {
            return Mage::helper('js_menu')->__('Add Menu Item');
        }
    }
}
