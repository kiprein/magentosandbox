<?php

/**
 * NeoTheme (Neo Industries Pty Ltd)
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to Neo Industries Pty LTD Non-Distributable Software Modification License (NDSML)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.neotheme.com/legal/licenses/NDSM.html
 * If the license is not included with the package or for any other reason, 
 * you did not receive your licence please send an email to 
 * license@neotheme.com so we can send you a copy immediately.
 *
 * This software comes with no warrenty of any kind. By Using this software, the user agrees to hold 
 * Neo Industries Pty Ltd harmless of any damage it may cause.
 *
 * @category    modules
 * @module      NeoTheme_Blog
 * @copyright   Copyright (c) 2011 Neo Industries Pty Ltd (http://www.neotheme.com)
 * @license     http://www.neotheme.com/  Non-Distributable Software Modification License(NDSML 1.0)
 */
class NeoTheme_Blog_Block_Adminhtml_Category_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('neotheme_blog_categoryGrid');

        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);

    }

    private function _getStoreId() {
        return (int) $this->getRequest()->getParam('store', 0);
    }

    protected function _getStore() {
        return Mage::app()->getStore((int) $this->getStoreId());
    }

    protected function _prepareCollection() {
        $store = $this->_getStore();
        $collection = Mage::getModel('neotheme_blog/category')
                ->setStoreId((int) $this->_getStoreId())
                ->getCollection();
        $collection->addStoreFilter($this->_getStoreId());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareMassaction() {

        return $this;
    }

    protected function _prepareColumns() {

        $this->addColumn('entity_id', array(
            'header' => Mage::helper('neotheme_blog')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'entity_id',
        ));
        $this->addColumn('category_name', array(
            'header' => Mage::helper('neotheme_blog')->__('Category Name'),
            'align' => 'left',
            'index' => 'name',
        ));
        $this->addColumn('cms_identifier', array(
            'header' => Mage::helper('neotheme_blog')->__('URL Key'),
            'align' => 'left',
            'index' => 'cms_identifier',
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('neotheme_blog')->__('Status'),
            'align' => 'left',
            'index' => 'status',
            'type' => 'options',
            'options' => Mage::getModel('neotheme_blog/attribute_source_status')->toOptionArray()
        ));
        return parent::_prepareColumns();
    }

    protected function getRowJs($row) {
        return "#";
    }

    public function getGridUrl() {
        return $this->getUrl('*/*/grid', array('_current' => true, 'store' => $this->getRequest()->getParam('store')));
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getData("entity_id"), 'store' => $this->getRequest()->getParam('store')));
    }

}