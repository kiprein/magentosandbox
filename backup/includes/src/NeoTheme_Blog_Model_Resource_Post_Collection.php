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
class NeoTheme_Blog_Model_Resource_Post_Collection 
extends NeoTheme_Blog_Model_Resource_Abstract_Store_Collection
{
    function _construct()
    {
        $this->_init('neotheme_blog/post');
    }
    /**
     * Adds a filter on the serialized id/s provided. This will addFieldToFilter to a collection where the field is stored as a serailzed number (1,2,3,5,8)
     * @param int|string|array $attributeCode
     * @param int|string|array $id_to_serach
     * @param bool $includeNull Whether or not add an OR to the select clause allowing a NULL.
     * @return NeoTheme_Blog_Model_Resource_Post_Collection 
     */
    private function addSerialFilter($attributeCode, $id_to_serach, $includeNull = false){
        if (!is_array($id_to_serach)){
            $id_to_serach = array($id_to_serach);
        }
        $allIds = ($includeNull)? array(array('null' => true)) : array() ;
        foreach($id_to_serach as $sid){
           $allIds[] = array('finset' => $sid);
        }
        $this->addFieldToFilter($attributeCode,$allIds);
        return $this;
    }
    
    /**
     * Adds a filter on the category id/s provided. This will produce a collection of posts where posts are in ANY categories provided
     * @param int|string|array $category_id
     * @return NeoTheme_Blog_Model_Resource_Post_Collection 
     */
    function addCategoryFilter($category_id){
        return $this->addSerialFilter('category_ids', $category_id );
    }
    
    /**
     * Adds a filter on the customer group id/s provided. This will produce a collection of posts where posts are in ANY customer group ids provided
     * @param int|string|array $customer_group_id
     * @return NeoTheme_Blog_Model_Resource_Post_Collection 
     */
    function addCustomerGroupFilter($customer_group_id){
        return $this->addSerialFilter('customer_group_ids', $customer_group_id, true);
    }
    /**
     * Adds a filter on the category id/s provided. This will produce a collection of posts where posts are in ALL categories provided
     * @param int|string|array $category_id
     * @return NeoTheme_Blog_Model_Resource_Post_Collection 
     */
    function addExclusionaryCategoryFilter($category_id){
        if (!is_array($category_id)){
            $category_id = array($category_id);
        }
        foreach($category_id as $cid){
            $this->addFieldToFilter('category_ids',array(array('finset' => $cid)));
        }
        return $this;
    }
    
    /**
     * Tags not implemented yet, but this will add in the tag filter when they are
     * @param type $tag_id
     * @return NeoTheme_Blog_Model_Resource_Post_Collection 
     */
    function addTagFilter($tag_id){
        return $this;
    }
    
    /**
     * Sets the order in which the posts will appear.
     * @return NeoTheme_Blog_Model_Resource_Post_Collection 
     */
    function setPostOrder(){
        $this->setOrder('post_date', Mage_Core_Model_Mysql4_Collection_Abstract::SORT_ORDER_DESC);
        return $this;
    }
    
    /**
     * adds a filter that removes all posts except from the range given
     * @param type $publishDateFrom
     * @param type $publishDateTo
     * @return \NeoTheme_Blog_Model_Resource_Post_Collection 
     */
    function addPublishFilter($publishDateFrom = null, $publishDateTo = null){
        if (!$publishDateTo){
            $publishDateTo = now();
        }
        $conditions = array();
        $conditions[0] = array('null' => true);
        $conditions[1] = array('to' => $publishDateTo,
                               'date' => true
                                );
        if ($publishDateFrom){
            $conditions[1]['from'] = $publishDateFrom;
        }
        $this->addFieldToFilter('publish_date', $conditions);
        $this->addFieldToFilter('post_date', 
                    array(
                        array('to' => $publishDateTo )
                        )
                );
        return $this;
    }

    
}
