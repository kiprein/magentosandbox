<?php
/**
 * @author		Sashas
 * @category    Sashas
 * @package     Sashas_LoginCustomer
 * @copyright   Copyright (c) 2015 Sashas IT Support Inc. (http://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

class Sashas_LoginCustomer_LogincustomerController extends Mage_Core_Controller_Front_Action {
    
    public function logincustomerAction() {
       
        $customer_id=$this->getRequest()->getParam('id',0);
        $log_id=$this->getRequest()->getParam('log_id',0);
        $session=Mage::getSingleton('customer/session', array('name'=>'frontend'));
        
        $saveLog=Mage::getStoreConfig('logincustomer/logincustomer_group/save_log');                   
        
        if (!$customer_id || ( !$log_id && $saveLog) ) {
            $message = $this->__('You have no pemission to use this option');
            $session->addError($message);  
            if ($log_id && $saveLog){
                $log_id=Mage::helper('core')->decrypt($log_id);
                $loginCustomer=Mage::getModel('logincustomer/log')->load($log_id);
                $loginCustomer->setStatus('fail')->save();
            }
            $this->_redirect('customer/account/login');            
        }
        
      	$customer_id=Mage::helper('core')->decrypt($customer_id);
      	if ($saveLog){
	      	$log_id=Mage::helper('core')->decrypt($log_id);
	      	$loginCustomer=Mage::getModel('logincustomer/log')->load($log_id);
      	}
      	
        try{             
            $customer = Mage::getModel('customer/customer')->load($customer_id);
            $session->setCustomerAsLoggedIn($customer);
            $session->loginById($customer_id);
            if ($saveLog)
            	$loginCustomer->setStatus('success')->save();           
            $this->_redirect('customer/account/index');
            return;
        }catch (Exception $e){
            $session->addError($e->getMessage());
            if ($saveLog)
            	$loginCustomer->setStatus('fail')->save();
        }
        if ($saveLog)
        	$loginCustomer->setStatus('fail')->save();
        $this->_redirect('customer/account/login');        
    }
    
}