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
class NeoTheme_Blog_Block_Adminhtml_Comment_Edit_Tab_Publishing extends Mage_Adminhtml_Block_Widget_Form //NeoTheme_Blog_Block_Adminhtml_Form { {
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('author_form_tab');
        $this->setTabId('author_form_tab');
    }

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        $form->setDataObject(Mage::registry('current_post'));
        $fieldset = $form->addFieldset('neotheme_blog_publishing', array('legend' => Mage::helper('neotheme_blog')->__('Author Data')));

        $form->setFieldNameSuffix('publishing_data');

        $fieldset->addField('author', 'text', array(
            'label' => Mage::helper('neotheme_blog')->__('Author'),
            'class' => 'required-entry',
            'name' => 'author',
            'required' => true,
            'index' => 'author',
        ));
        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        $fieldset->addField('post_date', 'date', array(
            'name'   => 'post_date',
            'label'  => Mage::helper('neotheme_blog')->__('Show Post Date'),
            'title'  => Mage::helper('neotheme_blog')->__('Show Post Date'),
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'format'       => $dateFormatIso
        ));

        $publich_fieldset = $form->addFieldset('neotheme_blog_publish', array('legend' => Mage::helper('neotheme_blog')->__('Publishing Data')));

        $publich_fieldset->addField('publish_date', 'date', array(
            'name'   => 'publish_date',
            'label'  => Mage::helper('neotheme_blog')->__('Publish Date'),
            'title'  => Mage::helper('neotheme_blog')->__('Publish Date'),
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'format'       => $dateFormatIso
        ));

        
        
        
        if (Mage::getSingleton('adminhtml/session')->getBlogPostData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getBlogPostData());
            Mage::getSingleton('adminhtml/session')->setBlogPostData(null);
        } elseif (Mage::registry('current_post')) {
            $form->setValues(Mage::registry('current_post')->getData());
        }
        return parent::_prepareForm();
    }

}