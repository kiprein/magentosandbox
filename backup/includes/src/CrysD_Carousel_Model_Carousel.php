<?php
/**
 * Carousel model
 */
class CrysD_Carousel_Model_Carousel extends Mage_Core_Model_Abstract
{
    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('carousel/carousel');
    }

    /**
     * Load a carousel by its identifier
     *
     * @param string $identifier
     * @return $this
     */
    public function loadByIdentifier($identifier)
    {
        return $this->load($identifier, 'identifier');
    }

    /**
     * Retrieve slide collection for this carousel, ordered by position
     *
     * @return CrysD_Carousel_Model_Mysql4_Slide_Collection
     */
    public function getSlides()
    {
        if (!$this->getId()) {
            return Mage::getModel('carousel/slide')->getCollection()->addFieldToFilter('carousel_id', 0);
        }
        return Mage::getModel('carousel/slide')
            ->getCollection()
            ->addFieldToFilter('carousel_id', $this->getId())
            ->setOrder('position', 'ASC');
    }

    /**
     * Prepare slides data array for dynamicRows form field
     *
     * @return array
     */
    public function getSlidesDataArray()
    {
        $data = [];
        foreach ($this->getSlides() as $slide) {
            $data[] = $slide->getData();
        }
        return $data;
    }
}
