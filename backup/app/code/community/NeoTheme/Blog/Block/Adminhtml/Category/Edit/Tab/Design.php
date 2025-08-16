<?php

/**
 * NeoTheme (Neo Industries Pty Ltd)
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
class NeoTheme_Blog_Block_Adminhtml_Category_Edit_Tab_Design extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('author_form_tab');
        $this->setTabId('author_form_tab');
    }

    /**
     * Prepares the publishing form
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        $form->setDataObject(Mage::registry('current_category'));
        //$fieldset = $form->addFieldset('neotheme_blog_publishing', array('legend' => Mage::helper('neotheme_blog')->__('Author Data')));

        $form->setFieldNameSuffix('design_data');


        $layoutFieldset = $form->addFieldset('layout_fieldset', array(
            'legend' => Mage::helper('cms')->__('Page Layout'),
            'class'  => 'fieldset-wide',
         //   'disabled'  => $isElementDisabled
        ));

        $layoutFieldset->addField('root_template', 'select', array(
            'name'     => 'root_template',
            'label'    => Mage::helper('cms')->__('Layout'),
            'required' => true,
            'values'   => Mage::getSingleton('neotheme_blog/attribute_source_layout')->toOptionArray(),
           // 'disabled' => $isElementDisabled
        ));
        if (!$form->getDataObject()->getId()) {
            $form->getDataObject()->setRootTemplate(Mage::getSingleton('page/source_layout')->getDefaultValue());
        }

        $layoutFieldset->addField('layout_update_xml', 'textarea', array(
            'name'      => 'layout_update_xml',
            'label'     => Mage::helper('cms')->__('Layout Update XML'),
            'style'     => 'height:24em;',
        ));


        if (Mage::getSingleton('adminhtml/session')->getBlogCategoryData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getBlogCategoryData());
            Mage::getSingleton('adminhtml/session')->setBlogCategoryData(null);
        } elseif (Mage::registry('current_category')) {
            $form->setValues(Mage::registry('current_category')->getData());
        }
        return parent::_prepareForm();
    }

    /**
     * Override to set TimeZone of Date Fields so that the backend user can enter in the time in their locale.
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    //protected function _initFormValues(){
      //  $returnable = parent::_initFormValues();
      //  $timeZone = Mage::getStoreConfig('general/locale/timezone');
        //convert to localized Timezone
        //This is then converted back in
        //NeoTheme_Blog_Adminhtml_Neotheme_Blog_PostController::_filterDates
      //  if (!$timeZone){
      //      $timeZone = Mage_Core_Model_Locale::DEFAULT_TIMEZONE;
      //  }
      //  $timeElementNames = array('publish_date','post_date');
      //  foreach($timeElementNames as $timeElementName){
      //      $zendDate = $this->getForm()->getElement($timeElementName)->getValueInstance();
      //      if ($zendDate){
      ///          $zendDate->setTimezone($timeZone);
      //      }
      //  }
      //  return $returnable;
    //}
}