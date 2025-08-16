<?php

class Js_Menu_Block_Adminhtml_Menu_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('menusGrid');

        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('js_menu/menu')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
	    $this->addColumn('title', array(
		    'header'    => Mage::helper('js_menu')->__('Title'),
		    'index'     => 'title'
	    ));

	    $this->addColumn('menu_type', array(
		    'header'    => Mage::helper('js_menu')->__('Type'),
		    'index'     => 'menu_type',
		    'renderer' => 'Js_Menu_Block_Adminhtml_Template_Grid_Renderer_Menutype'
	    ));

	    $this->addColumn('position', array(
		    'header'    => Mage::helper('js_menu')->__('Position'),
		    'index'     => 'position',
	    ));

	    $this->addColumn('active', array(
		    'header'    => Mage::helper('js_menu')->__('Active'),
		    'index'     => 'menu_type',
		    'renderer' => 'Js_Menu_Block_Adminhtml_Template_Grid_Renderer_Active'
	    ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}
