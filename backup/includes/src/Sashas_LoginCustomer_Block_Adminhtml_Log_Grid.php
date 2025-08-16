<?php
/**
 * @author		Sashas
 * @category    Sashas
 * @package     Sashas_LoginCustomer
 * @copyright   Copyright (c) 2015 Sashas IT Support Inc. (http://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

class Sashas_LoginCustomer_Block_Adminhtml_Log_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('id');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('logincustomer/log')->getCollection();       
        $collection->getSelect()->joinInner(array('customer' => Mage::getSingleton('core/resource')->getTableName('customer/entity')),
                'main_table.customer_id = customer.entity_id', array('email'));
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'    => Mage::helper('logincustomer')->__('ID'),
            'align'     =>'right',
            'width'     => '60',
            'index'     => 'id',
        ));
        
        $this->addColumn('created_at', array(
			'header'    => Mage::helper('logincustomer')->__('Created Date'),
			'align'     =>'left',
			'index'     => 'created_at',
			'type' =>'date'                
        ));        
        
        $this->addColumn('admin_username', array(
			'header'    => Mage::helper('logincustomer')->__('Admin Username'),
			'align'     =>'left',                
			'index'     => 'admin_username',
            'renderer'	=> 'Sashas_LoginCustomer_Block_Adminhtml_Renderer_Admin',
        ));
                
        $this->addColumn('email', array(
            'header'    => Mage::helper('logincustomer')->__('Customer Email'),
            'align'     =>'center',
            'width'     => '300',
            'index'     => 'email',
            'renderer'	=> 'Sashas_LoginCustomer_Block_Adminhtml_Renderer_Customer',
                 
        )); 
         
        $this->addColumn('status', array(
			'header'    => Mage::helper('logincustomer')->__('Status'),
			'align'     =>'center',
			'width'     => '150',
			'index'     => 'status',
			'renderer'	=> 'Sashas_LoginCustomer_Block_Adminhtml_Renderer_Status',
        ));
        
        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return 'javascript:void(0);';
    }
 
 
}
