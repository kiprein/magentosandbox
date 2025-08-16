<?php

class Js_Banners_Block_Adminhtml_Banners extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_banners';
        $this->_blockGroup = 'js_banners';
        $this->_headerText = Mage::helper('js_banners')->__('Banner Manager');
        $this->_addButtonLabel = Mage::helper('js_banners')->__('Add Banner');
        parent::__construct();
    }
}
