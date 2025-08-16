<?php
class Mage_Adminhtml_Model_System_Config_Source_Diyoptions13590061872
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
		
            array('value' => 1, 'label'=>Mage::helper('adminhtml')->__('Left Column')),
            array('value' => 2, 'label'=>Mage::helper('adminhtml')->__('Right Column')),
            array('value' => 3, 'label'=>Mage::helper('adminhtml')->__('None')),
        );
    }

}
