<?php
/**
 * @author		Sashas
 * @category    Sashas
 * @package     Sashas_LoginCustomer
 * @copyright   Copyright (c) 2015 Sashas IT Support Inc. (http://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */
 
class Sashas_LoginCustomer_Block_Adminhtml_Customer_Edit extends Mage_Adminhtml_Block_Customer_Edit
{
    public function __construct()
    {        
        parent::__construct();
        
        $isEnabled=Mage::getStoreConfig('logincustomer/logincustomer_group/enable_extension');
        
        if (!$isEnabled)
            return;
        
        $this->_addButton('logincustomer', array(
                'label' => Mage::helper('customer')->__('Login as Customer'),
                'onclick' => 'popWin(\'' . $this->getLoginCustomerUrl() . '\', \'_blank\' )',
                'class' => '',
        ), 50);
   
    }

    public function getLoginCustomerUrl()
    {
        return $this->getUrl('*/logincustomer/logincustomer/index', array('customer_id' => $this->getCustomerId()));
    }

   
}
