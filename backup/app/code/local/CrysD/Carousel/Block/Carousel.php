<?php
class CrysD_Carousel_Block_Carousel extends Mage_Core_Block_Template
{
    /** @var CrysD_Carousel_Model_Carousel|null */
    protected $_carousel = null;

    /**
     * Before rendering, read the CMS attribute 'identifier' and load the model.
     */
    protected function _beforeToHtml()
{
    // What did CMS actually pass?
    $identifier = $this->getData('identifier');
    Mage::log("Carousel Block: identifier passed = '{$identifier}'", null, 'carousel-debug.log', true);

    // Attempt to load it
    $this->_carousel = Mage::getModel('carousel/carousel')
        ->load($identifier, 'identifier');
    Mage::log("Carousel Block: loaded ID = " . ($this->_carousel->getId() ?: 'none'), null, 'carousel-debug.log', true);

    return parent::_beforeToHtml();
}


    /**
     * Return the loaded carousel model (or false if none)
     *
     * @return CrysD_Carousel_Model_Carousel|false
     */
    public function getCarousel()
    {
        if ($this->_carousel && $this->_carousel->getId()) {
            return $this->_carousel;
        }
        return false;
    }

    /**
     * Return slide collection or empty if no carousel loaded.
     *
     * @return Varien_Data_Collection
     */
    public function getSlides()
    {
        if (!$this->getCarousel()) {
            return new Varien_Data_Collection();
        }
        return Mage::getModel('carousel/slide')
            ->getCollection()
            ->addFieldToFilter('carousel_id', $this->_carousel->getId())
            ->setOrder('position','ASC');
    }
}
