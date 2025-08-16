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
abstract class NeoTheme_Blog_Model_Resource_Abstract_Status_Collection 
extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    function __construct()
    {
        $this->_statuses = array();
        $this->_statusFiltered = false;
        parent::__construct();

    }
    
    
    protected $_statuses;
    protected $_statusFiltered;
    /*
     * function addStatusFilter:
     * params: (int) $status - object specific Status, defined in the object itself eg: NeoTheme_Blog_Model_Post
     */
    public function addStatusFilter($status){
         //setting the key as the data insures uniqueness without an extra check
         $this->_statuses[$status] = $status;
         return $this;
    }
   
    protected function _beforeLoad(){
        if (!$this->_statusFiltered && count($this->_statuses) > 0 ){
            $this->addFieldToFilter('main_table.status',$this->_statuses);
            $this->_statusFiltered = true;
        }
    }
    
    public function getSelect() {
        $this->_beforeLoad();
        return parent::getSelect();
    }
}
