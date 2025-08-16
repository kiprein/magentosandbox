<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Text grid column filter
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class NeoTheme_ImageRotator_Block_Adminhtml_Imagerotator_Widget_Grid_Column_Renderer_Ordering extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
{
    protected $_values;

    public function getValues()
    {
        if (is_null($this->_values)) {
            $this->_values = $this->getColumn()->getData('values') ? $this->getColumn()->getData('values') : array();
        }
        return $this->_values;
    }
    
    protected $_javascript_id;
    protected function getJavascriptId(){
        if ($this->_javascript_id ==  NULL){
            $this->_javascript_id = uniqid("neotheme-ordering-hidden");
        }
        return $this->_javascript_id;
    }
    
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        //TODO: install up and down arrows for this object
        //$html = '<a href="##" onclick="varienGlobalEvents.removeEventHandler(this, \'ongridRowClick\');" class="neotheme-ordering-mvupbtn"  >move up</a>';
        $html = '<input type="text" ';
        $html .= 'name="' . $this->getColumn()->getId() . '" ';
        $html .= 'value="' . $row->getData($this->getColumn()->getIndex()) . '" ';
        $html .= 'onchange="nwareposchange(this);" class="input-text ' . $this->getColumn()->getInlineCss() . '"/>';
        //$html .= '<a href="##" class="neotheme-ordering-mvdownbtn"  >move down</a>';
        return $html;
    }
}
