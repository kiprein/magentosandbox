<?php

class Js_Banners_Block_Adminhtml_Banners_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('bannersGrid');

        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('js_banners/banners')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('link', array(
                'header'    => Mage::helper('js_banners')->__('Link'),
                'index'     => 'link',
        ));

        $this->addColumn('image', array(
                'header'    => Mage::helper('js_banners')->__('Image'),
                'index'     => 'image',
                'renderer' => 'Js_Banners_Block_Adminhtml_Template_Grid_Renderer_Image'
        ));
 
        $this->addColumn('sort_order', array(
                'header'    => Mage::helper('js_banners')->__('Sort Order'),
                'index'     => 'sort_order',
        ));

	    $this->addColumn('active', array(
		    'header'    => Mage::helper('js_banners')->__('Active'),
		    'index'     => 'active',
		    'renderer' => 'Js_Banners_Block_Adminhtml_Template_Grid_Renderer_Active'
	    ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}
