<?php
class CrysD_Carousel_Block_Adminhtml_Carousel_Edit_Slides
    extends Mage_Adminhtml_Block_Template
{
    /** @var array */
    protected $_slides = [];

    public function __construct()
    {
        parent::__construct();
        // point to your slides.phtml under adminhtml/default/default/template/crysd_carousel/
        $this->setTemplate('crysd_carousel/slides.phtml');
    }

    /**
     * @param array $slides
     * @return $this
     */
    public function setSlides(array $slides)
    {
        $this->_slides = $slides;
        return $this;
    }

    /**
     * @return array
     */
    public function getSlides()
    {
        return $this->_slides;
    }
}
