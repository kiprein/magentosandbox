<?php

class Js_Category_Block_Adminhtml_Template_Grid_Renderer_Image
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $out = '<img height="50px" width="50px" 
            src="'. Mage::getBaseUrl('media') . 'featured_gallery/' . $row->image.'" />';
        return $out;
    }
}