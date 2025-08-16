<?php

class Js_Menu_Block_Adminhtml_Menu_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('menu_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('js_menu')->__('Menu Detail'));
    }
    
    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label'     => Mage::helper('js_menu')->__('Menu Info'),
            'title'     => Mage::helper('js_menu')->__('Menu Info'),
            'content'   => $this->getLayout()->createBlock('js_menu/adminhtml_menu_edit_tab_form')->toHtml(),
        ));

        return parent::_beforeToHtml();
    }
}
