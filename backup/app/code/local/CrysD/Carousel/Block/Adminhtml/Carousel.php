<?php
/**
 * Admin “Manage Carousels” container
 */
class CrysD_Carousel_Block_Adminhtml_Carousel
  extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        // this must match the folder under Block/Adminhtml/
        $this->_controller = 'adminhtml_carousel';
        // this must match the <blocks><carousel> group in config.xml
        $this->_blockGroup = 'carousel';
        // the text that appears at the top of the page
        $this->_headerText = Mage::helper('carousel')->__('Manage Carousels');
        // label for the “Add New” button
        $this->_addButton('add', [
            'label'   => Mage::helper('carousel')->__('Add New Carousel'),
            'onclick' => 'setLocation(\'' . $this->getUrl('*/*/new') . '\')',
            'class'   => 'add',
        ]);

        parent::__construct();
    }
}
