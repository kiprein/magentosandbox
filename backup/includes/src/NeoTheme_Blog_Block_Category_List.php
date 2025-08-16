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
class NeoTheme_Blog_Block_Category_List
extends Mage_Core_Block_Template 
implements Mage_Widget_Block_Interface
{	
    function _construct(){
        $this->setTemplate('neotheme/blog/category/list.phtml');
        
    }
    private $_collection;
    function  _prepareCollection(){

       $this->_collection = Mage::getModel('neotheme_blog/category')
                ->getCollection()
                ->addStoreFilter()
                ->addStatusFilter(NeoTheme_Blog_Model_Post::STATUS_ACTIVE)
                ->setOrder('created_at', Mage_Core_Model_Mysql4_Collection_Abstract::SORT_ORDER_DESC);
        if ($this->_showDrafts()) {
            $this->_collection->addStatusFilter(NeoTheme_Blog_Model_Post::STATUS_DRAFT);
        }

       if ($this->getShowPopulatedOnly() || Mage::getStoreConfig(NeoTheme_Blog_Helper_Data::XPATH_CATEGORY_SHOW_POPULATED) ){
           $this->_collection->addPopulatedOnlyFilter();
       }
       if (Mage::getStoreConfig(NeoTheme_Blog_Helper_Data::XPATH_CONFIG_CUSTOMER_PREFERENCES_ENABLE)){
            $allCategories = Mage::getModel('neotheme_blog/category');
            $allCategories->setListUrl(Mage::getUrl('blog/index/all'));
            $allCategories->setName(Mage::helper('neotheme_blog')->__('All Categories'));
            $this->_collection->addItem($allCategories);
       }
       return $this->_collection;
    }
    
    function _showDrafts() {
        return Mage::helper('neotheme_blog')->isIpPermitted();
    }

    function getCollection(){
        if ($this->_collection == null){
            $this->_prepareCollection();
        }
        return $this->_collection;
    }
    
    function _toHtml(){
        $this->_prepareCollection();
        return parent::_toHtml();
    }   
}