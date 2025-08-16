<?php

class Js_Banners_Block_Adminhtml_Banners_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('banners_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('js_banners')->__('Banner Detail'));
    }
    
    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label'     => Mage::helper('js_banners')->__('Banner Info'),
            'title'     => Mage::helper('js_banners')->__('Banner Info'),
            'content'   => $this->getLayout()->createBlock('js_banners/adminhtml_banners_edit_tab_form')->toHtml(),
        ));

        return parent::_beforeToHtml();
    }
}
