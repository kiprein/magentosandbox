<?php
class CrysD_Carousel_Model_Source_Style
{
    /**
     * Options for the Style dropdown
     *
     * @return array [ ['value'=>'key','label'=>'Label'], … ]
     */
    public function toOptionArray()
    {
        return [
            ['value'=>'pageheader', 'label'=>'Page Header'],
            ['value'=>'horiz', 'label'=>'Stacked Horizontal List'],
            ['value'=>'split_horiz', 'label'=>'Split Horizontal List'],
            ['value'=>'testimonials', 'label'=>'Testimonials'],
        ];
    }
}
