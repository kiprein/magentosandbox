<?php
/**
 * @author		Sashas
 * @category    Sashas
 * @package     Sashas_LoginCustomer
 * @copyright   Copyright (c) 2015 Sashas IT Support Inc. (http://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

class Sashas_LoginCustomer_Model_Log extends Mage_Core_Model_Abstract
{
    const CACHE_TAG     = 'logincustomer_log';
    protected $_cacheTag= 'logincustomer_log';

    protected function _construct()
    {
        $this->_init('logincustomer/log');
    }


}
