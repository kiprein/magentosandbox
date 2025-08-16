<?php

class Js_Category_Block_Adminhtml_Template_Grid_Renderer_Active
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        if($row->active == 1) {
            $out = 'Yes';
        } else {
            $out = 'No';
        }
        return $out;
    }
}