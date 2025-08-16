<?php
class CrysD_Carousel_Model_Source_Theme
{
    /**
     * Options for the Theme dropdown
     */
    public function toOptionArray()
    {
        return [
            ['value'=>'light', 'label'=>'Light'],
            ['value'=>'dark',  'label'=>'Dark'],
            ['value'=>'primary',  'label'=>'Primary']
        ];
    }
}
