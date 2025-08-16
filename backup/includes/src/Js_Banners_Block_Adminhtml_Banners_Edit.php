<?php

class Js_Banners_Block_Adminhtml_Banners_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'js_banners';
        $this->_controller = 'adminhtml_banners';

        $this->_updateButton('save', 'label', Mage::helper('js_banners')->__('Save Banner'));
        $this->_updateButton('delete', 'label', Mage::helper('js_banners')->__('Delete Banner'));
    }
    
    public function getHeaderText()
    {
        if( Mage::registry('banners_data') && Mage::registry('banners_data')->getId() ) {
                return Mage::helper('js_banners')->__("Edit Item '%s'", Mage::registry('banners_data')->getLabel());
        } else {
                return Mage::helper('js_banners')->__('Add Item');
        }
    }
}
