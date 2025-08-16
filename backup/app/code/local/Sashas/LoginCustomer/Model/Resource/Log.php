<?php
/**
 * @author		Sashas
 * @category    Sashas
 * @package     Sashas_LoginCustomer
 * @copyright   Copyright (c) 2015 Sashas IT Support Inc. (http://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

class Sashas_LoginCustomer_Model_Resource_Log extends Mage_Core_Model_Resource_Db_Abstract
{
   
	/**
	 * Initialize resource model
	 *
	 */
	protected function _construct()
	{
		$this->_init('logincustomer/log', 'id');
	}
	
	public function cleanLog() {
	    $readAdapter    = $this->_getReadAdapter();
	    $writeAdapter   = $this->_getWriteAdapter();
	    
	    /* 15 days */
	    $timeLimit = $this->formatDate(Mage::getModel('core/date')->gmtTimestamp() - 1296000);
	    
	    $select = $readAdapter->select()
	    ->from(
	            array('log_table' => $this->getTable('logincustomer/log')),
	            array('id' => 'log_table.id'))
	            ->where('log_table.created_at < ?', $timeLimit);
	    
	    
	    $logIds = $readAdapter->fetchCol($select);
	    
	    if (!$logIds) 
	        return;
 
	    $condition = array('id IN (?)' => $logIds);
	    $writeAdapter->delete($this->getTable('logincustomer/log'), $condition);
	    return;
	}

}
