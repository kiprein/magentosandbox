<?php
/**
 * @author		Sashas
 * @category    Sashas
 * @package     Sashas_LoginCustomer
 * @copyright   Copyright (c) 2015 Sashas IT Support Inc. (http://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0 GNU License, version 3 (GPL-3.0)
 */

class Sashas_LoginCustomer_Model_Observer {
    static protected $_singletonFlag = false;
    
    public function cleanLog() {    
    	Mage::getModel('logincustomer/log')->getResource()->cleanLog();       
        return $this;
    }
 
}