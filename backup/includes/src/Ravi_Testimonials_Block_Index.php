<?php   
class Ravi_Testimonials_Block_Index extends Mage_Core_Block_Template{   


    public function getAddtestimonialUrl()
    {
        return $this->getUrl('testimonials/add/new', array('_secure' => true));
    }



}