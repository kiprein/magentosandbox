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


 * NeoTheme Austrlia (Neo Industries Pty Ltd)
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to Neo Industries Pty LTD Non-Distributable Software Modification License (NDSML)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.neotheme.com/legal/licenses/NDSM.html
 * If the license is not included with the package or for any other reason, 
 * you did not receive your licence please send an email to 
 * license@neotheme.com so we can send you a copy immediately.
 *
 * This software comes with no warrenty of any kind. By Using this software, the user agrees to hold 
 * Neo Industries Pty Ltd harmless of any damage it may cause.
 *
 * @category    modules
 * @module      NeoTheme_Blog
 * @copyright   Copyright (c) 2011 Neo Industries Pty Ltd (http://www.neotheme.com)
 * @license     http://www.neotheme.com/  Non-Distributable Software Modification License(NDSML 1.0)
 */
class NeoTheme_Blog_Block_Adminhtml_Category_Widget_Chooser
extends Mage_Adminhtml_Block_Widget_Grid {
    protected $_selectedCategories = array();

    /**
     * Block construction, prepare grid params
     *
     * @param array $arguments Object data
     */
    public function __construct($arguments=array())
    {
        parent::__construct($arguments);
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
    }

    /**
     * Prepare chooser element HTML
     *
     * @param Varien_Data_Form_Element_Abstract $element Form Element
     * @return Varien_Data_Form_Element_Abstract
     */
    public function prepareElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $uniqId = Mage::helper('core')->uniqHash($element->getId());
        $sourceUrl = $this->getUrl('*/neotheme_blog_widget/categorychooser', array(
            'uniq_id' => $uniqId,
            'use_massaction' => false,
        ));

        $chooser = $this->getLayout()->createBlock('widget/adminhtml_widget_chooser')
            ->setElement($element)
            ->setTranslationHelper($this->getTranslationHelper())
            ->setConfig($this->getConfig())
            ->setFieldsetId($this->getFieldsetId())
            ->setSourceUrl($sourceUrl)
            ->setUniqId($uniqId);

        if ($element->getValue()) {
            $categoryId = $element->getValue();

            $label = '';

            if (intval($categoryId) > 0) {
                $category = Mage::getModel('neotheme_blog/category')->load($categoryId);
                $label .= $category->getName();
            }
            $chooser->setLabel($label);
        }

        $element->setData('after_element_html', $chooser->toHtml());
        return $element;
    }

    /**
     * Checkbox Check JS Callback
     *
     * @return string
     */
    public function getCheckboxCheckCallback()
    {
        if ($this->getUseMassaction()) {
            return "function (grid, event) {
                $(grid.containerId).fire('category:changed', {});
            }";
        }
    }

    /**
     * Grid Row JS Callback
     *
     * @return string
     */
    public function getRowClickCallback()
    {
        if (!$this->getUseMassaction()) {
            $chooserJsObject = $this->getId();
            return '
                function (grid, event) {
                    var trElement = Event.findElement(event, "tr");
                    var categoryId = trElement.down("td").innerHTML;
                    var categoryName = trElement.down("td").next().innerHTML;
                    var optionLabel = categoryName;
                    var optionValue = "" + categoryId.replace(/^\s+|\s+$/g,"");
                    '.$chooserJsObject.'.setElementValue(optionValue);
                    '.$chooserJsObject.'.setElementLabel(optionLabel);
                    '.$chooserJsObject.'.close();
                }
            ';
        }
    }


    /**
     * Prepare products collection, defined collection filters (category, product type)
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('neotheme_blog/category_collection')
                        ->addFieldToSelect('entity_id')
                        ->addFieldToSelect('name')
                        ->addFieldToSelect('status');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare columns for products grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
   protected function _prepareColumns()
    {


        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('neotheme_blog')->__('ID'),
            'sortable'  => true,
            'width'     => '60px',
            'index'     => 'entity_id'
        ));
        $this->addColumn('chooser_name', array(
            'header'    => Mage::helper('neotheme_blog')->__('Category Name'),
            'name'      => 'chooser_name',
            'index'     => 'name'
        ));
        $this->addColumn('chooser_status', array(
             'header'    => Mage::helper('neotheme_blog')->__('Status'),
             'align'     => 'left',
             'index'     => 'status',
             'type'      => 'options',
             'name'      => 'chooser_status',
             'options'   => Mage::getModel('neotheme_blog/attribute_source_post_status')->toOptionArray()
        ));
        return parent::_prepareColumns();
    }



    /**
     * Setter
     *
     * @param array _selectedCategories
     * @return Mage_Adminhtml_Block_Catalog_Product_Widget_Chooser
     */
    public function setSelectedCategories($selectedCategories)
    {
        $this->_selectedCategories = $selectedCategories;
        return $this;
    }

    /**
     * Getter
     *
     * @return array
     */
    public function getSelectedCategories()
    {
        if ($selectedCategories = $this->getRequest()->getParam('selected_categories', null)) {
            $this->setSelectedCategories($selectedCategories);
        }
        return $this->_selectedCategories;
    }


}