<?php
/**
 * Collection model for Slide
 */
class CrysD_Carousel_Model_Mysql4_Slide_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('carousel/slide');
    }
}
