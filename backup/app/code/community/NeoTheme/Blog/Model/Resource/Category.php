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
class NeoTheme_Blog_Model_Resource_Category extends Mage_Core_Model_Mysql4_Abstract
{
    function _construct()
    {   
        $this->_init('neotheme_blog/category', 'entity_id');
    }
    
    function checkIdentifier($identifier, $storeId){    
        $adapter = $this->_getReadAdapter();
        $binds = array(
            'status' => NeoTheme_Blog_Model_Category::STATUS_ACTIVE,
            'store_id' => $storeId,
            'url_key' => $identifier
        );
        $select = $adapter->select('entity_id')
                ->from(array('cat' => $this->getMainTable()))
                ->where('cat.cms_identifier = :url_key')
                ->where('cat.status = :status')
                ->where('FIND_IN_SET(:store_id, cat.store_ids) OR FIND_IN_SET(0, cat.store_ids)');
        return $adapter->fetchOne($select, $binds);
    }
}