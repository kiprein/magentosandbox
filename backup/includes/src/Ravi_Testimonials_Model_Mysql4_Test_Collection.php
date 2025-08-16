<?php
class Ravi_Testimonials_Model_Mysql4_Test_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('Testimonials/test');
    }
}