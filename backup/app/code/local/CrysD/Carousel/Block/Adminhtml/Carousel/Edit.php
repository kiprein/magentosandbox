<?php
class CrysD_Carousel_Block_Adminhtml_Carousel_Edit
    extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_objectId   = 'id';
        $this->_blockGroup = 'carousel';
        $this->_controller = 'adminhtml_carousel';

        $this->_updateButton('save', 'label', 'Save Carousel');
        $this->_updateButton('delete', 'label', 'Delete Carousel');

        $this->_addButton('saveandcontinue', [
            'label'   => 'Save and Continue Edit',
            'onclick' => 'saveAndContinueEdit()',
            'class'   => 'save',
        ], -100);
    }

    public function getHeaderText()
    {
        if (Mage::registry('current_carousel')->getId()) {
            return 'Edit Carousel "' . $this->escapeHtml(Mage::registry('current_carousel')->getHeadline()) . '"';
        }
        return 'New Carousel';
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action + 'back/edit/');
            }
        ";
        return $this;
    }
}
