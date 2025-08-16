<?php
/**
 * Created by PhpStorm.
 * User: fab_5
 * Date: 8/25/2018
 * Time: 6:50 AM
 */
class Js_Category_Block_Adminhtml_Featured_Gallery_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct()
    {
        parent::__construct();
        $this->setId('grid_id');
        // $this->setDefaultSort('COLUMN_ID');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('js_category/featured_gallery')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

	    $this->addColumn('title', array(
		    'header' => Mage::helper('js_category')->__('Title'),
		    'index'  => 'title',
	    ));

	    $this->addColumn('link', array(
		    'header' => Mage::helper('js_category')->__('Link'),
		    'index'  => 'link',
	    ));

	    $this->addColumn('image', array(
		    'header'   => Mage::helper('js_category')->__('Image'),
		    'index'    => 'image',
		    'renderer' => 'Js_Category_Block_Adminhtml_Template_Grid_Renderer_Image'
	    ));

	    $this->addColumn('sort_order', array(
		    'header' => Mage::helper('js_category')->__('Sort Order'),
		    'index'  => 'sort_order',
	    ));

	    $this->addColumn('active', array(
		    'header'   => Mage::helper('js_category')->__('Active'),
		    'index'    => 'active',
		    'renderer' => 'Js_Category_Block_Adminhtml_Template_Grid_Renderer_Active'
	    ));
        
        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
       return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    }
