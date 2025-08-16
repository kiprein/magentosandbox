<?php
/**
 * Created by PhpStorm.
 * User: fab_5
 * Date: 8/25/2018
 * Time: 6:50 AM
 */
class Js_Category_Block_Adminhtml_Featured_Gallery extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct()
    {
        $this->_blockGroup      = 'js_category';
        $this->_controller      = 'adminhtml_featured_gallery';
        // $this->_headerText      = $this->__('Grid Header Text');
        // $this->_addButtonLabel  = $this->__('Add Button Label');
        parent::__construct();
            }

    public function getCreateUrl()
    {
        return $this->getUrl('*/*/new');
    }

}

