<?php

class Js_Menu_Block_Adminhtml_Template_Grid_Renderer_Menutype
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        if($row->menu_type) {
	        $menuTypeArray = json_decode($row->menu_type);
	        $out = implode(', ', $menuTypeArray);
        }
        return $out;
    }
}