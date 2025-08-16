<?php
class Ravi_Testimonials_Model_Mysql4_Test extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("testimonials/test", "id");
    }
}