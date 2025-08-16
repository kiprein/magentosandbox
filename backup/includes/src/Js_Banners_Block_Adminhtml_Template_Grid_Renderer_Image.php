<?php

class Js_Banners_Block_Adminhtml_Template_Grid_Renderer_Image
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $out = '<img height="100px" width="300px" src="'.Mage::helper('js_banners')->getBannerUrl().$row->image.'" />';
        return $out;
    }
}