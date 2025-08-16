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
class NeoTheme_Blog_Model_Resource_Category_Collection 
extends NeoTheme_Blog_Model_Resource_Abstract_Store_Collection
{
    function _construct()
    {
        $this->_init('neotheme_blog/category');
    }
    protected $_storeFilterFlag;
    protected $_storeFilterId;
    
    function addIdFilter($ids){
        $this->addFieldToFilter('entity_id', $ids);
		return $this;
    }
    
    protected $_group_by_added = false;
    private function _addGroupBy(){
        if (!$this->_group_by_added){
            $this->getSelect()->group('main_table.entity_id');
            $this->_group_by_added = true;
        }
    }
    
    function addPopulatedOnlyFilter(){
        $cond =  "FIND_IN_SET(main_table.entity_id, posts.category_ids) ";
        // $cond .= "AND posts.status = " . NeoTheme_Blog_Model_Post::STATUS_ACTIVE;
        //foreach($this->_statuses as $status){
            $cond .= "AND posts.status IN (".implode(",",$this->_statuses).") ";
        //}
        if ($this->_storeFilterFlag){
            $cond .= " AND (FIND_IN_SET(".$this->_storeFilterId.", posts.store_ids) OR FIND_IN_SET(".NeoTheme_Blog_Model_Abstract_Store::DEFAULT_STORE_ID.", posts.store_ids))";
        }
        $this->join(array('posts' => 'post'), $cond, array());
        $this->_addGroupBy();
    }
    
    function addStoreFilter($storeId = NULL){
        if ($storeId == NULL){
            $storeId = Mage::app()->getStore()->getId();
        }
        //Store ID 0 should give all store view data
        if ((int)$storeId == 0){
            return $this;
        }
        $this->addFieldToFilter('main_table.store_ids', array(array('finset' => $storeId), array('eq' => 0)));
        $this->_storeFilterFlag = true;
        $this->_storeFilterId = $storeId;
        return $this;
    }
    
}
