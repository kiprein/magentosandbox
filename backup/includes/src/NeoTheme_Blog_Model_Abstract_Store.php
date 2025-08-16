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
class NeoTheme_Blog_Model_Abstract_Store extends  NeoTheme_Blog_Model_Abstract {
    
	const DEFAULT_STORE_ID = 0;
	
    function getStoreIds(){
        $storeIds = parent::getStoreIds();
        if (is_string($storeIds)){
            //convert to array;
            $storeIds = explode(",", $storeIds);
        }
        return $storeIds;
    }
    
    function save(){
        if ($this->getStoreIds() != NULL && is_array($this->getStoreIds())){
            $this->setStoreIds(implode(",", $this->getStoreIds()));
        }
        parent::save();
    }
    
}