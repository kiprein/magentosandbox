<?php
 
class Mgw_Payment_Model_Standard extends Mage_Payment_Model_Method_Abstract
{
    // This is the identifier of our payment method
    protected $_code = 'terms';
    protected $_isInitializeNeeded      = true;
    protected $_canUseInternal          = false;
    protected $_canUseForMultishipping  = false;
}