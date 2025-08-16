<?php
/**
 * Resource model for Carousel
 */
class CrysD_Carousel_Model_Mysql4_Carousel extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        // table name, primary key
        $this->_init('carousel/carousel', 'carousel_id');
    }
}
