<?php
/**
 * Collection model for Carousel
 */
class CrysD_Carousel_Model_Mysql4_Carousel_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('carousel/carousel');
    }
}
