<?php
/**
 * @author		Sashas
 * @category    Sashas
 * @package     Sashas_LoginCustomer
 * @copyright   Copyright (c) 2015 Sashas IT Support Inc. (http://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

class Sashas_LoginCustomer_Adminhtml_LogincustomerController extends Mage_Adminhtml_Controller_Action {
	    
	protected function _construct()
	{	 
		$this->setUsedModuleName('Sashas_LoginCustomer');
	}
	
	/**
	 * Check for is allowed
	 *
	 * @return boolean
	 */
	protected function _isAllowed()
	{
	    $action = strtolower($this->getRequest()->getActionName());
	    switch ($action) {
	    	case 'log':
	    	    $aclResource = 'system/tools/logincustomer';
	    	    break;	    	
	    	default:
	    	    $aclResource = 'customer/manage/logincustomer';
	    	    break;
	    }
	    return Mage::getSingleton('admin/session')->isAllowed($aclResource);
	}
		
	public function indexAction()
	{
	    $this->_forward('logincustomer');	 
	}	
	
	public function logincustomerAction(){
	   	 
	    $customerId=$this->getRequest()->getParam('customer_id');
	    $loginCustomer=Mage::getModel('logincustomer/log');
	     
	    $admin_user=Mage::getSingleton('admin/session')->getUser();
	    
	    $saveLog=Mage::getStoreConfig('logincustomer/logincustomer_group/save_log');
	    
	    if ($saveLog) {	         	    
		    $loginCustomer->setCustomerId($customerId);
		    $loginCustomer->setAdminId($admin_user->getUserId());
		    $loginCustomer->setAdminUsername($admin_user->getUsername());
		    $loginCustomer->setStatus('pending');
		    $loginCustomer->setCreatedAt(Mage::getModel('core/date')->timestamp(time()));
		    $loginCustomer->save();
		    $log_id=$loginCustomer->getId();	
		    $params = array('_query'=>array('id'=>Mage::helper('core')->encrypt($customerId), 'log_id'=>Mage::helper('core')->encrypt($log_id)));
	    } else {
	        $params = array('_query'=>array('id'=>Mage::helper('core')->encrypt($customerId)));
	    }
	    
	    $customer = Mage::getModel('customer/customer')->load($customerId);
	    if ($customer->getStoreId()){	    	 
	    	 $params['_query']['___store']= Mage::app()->getStore($customer->getStoreId())->getCode();
	    }
	  
	    Mage::log('Login as customer ID: '.$customerId.' Admin:  '.$admin_user->getUsername(), null, 'loginCustomer.log');
	   
        $this->_redirect('logincustomer/logincustomer/logincustomer',$params);
	}		

	public function logAction(){
	   $this->_title($this->__('logincustomer'))->_title($this->__('Login As Customer Log'));
	   $this->loadLayout()->_setActiveMenu('system') ->_addBreadcrumb(
				Mage::helper('logincustomer')->__('Login As Customer Log'),
				Mage::helper('logincustomer')->__('Login As Customer Log')
		);
	    $content_block=$this->getLayout()->createBlock('logincustomer/adminhtml_log');
	    $this->getLayout()->getBlock('content')->append($content_block);
	    $this->renderLayout();
	}
}