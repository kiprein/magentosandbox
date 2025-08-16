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
class NeoTheme_Blog_Block_Adminhtml_Post_Edit_Tab_Meta extends Mage_Adminhtml_Block_Widget_Form //NeoTheme_Blog_Block_Adminhtml_Form { {
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('meta_form_tab');
        $this->setTabId('meta_form_tab');
    }

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        $form->setDataObject(Mage::registry('current_post'));
        $fieldset = $form->addFieldset('neotheme_blog_form', array('legend' => Mage::helper('neotheme_blog')->__('Meta Data')));

        $form->setFieldNameSuffix('meta_data');

        $fieldset->addField('meta_description', 'textarea', array(
            'label' => Mage::helper('neotheme_blog')->__('Meta Description'),
            'name' => 'meta_description',
            'required' => false,
            'index' => 'meta_description',
        ));
        
        $fieldset->addField('meta_keywords', 'text', array(
            'label' => Mage::helper('neotheme_blog')->__('Meta Keywords'),
            'name' => 'meta_keywords',
            'required' => false,
            'index' => 'timeta_keywordstle',
        ));
        
        $fieldset->addField('meta_title', 'text', array(
            'label' => Mage::helper('neotheme_blog')->__('Meta Title'),
            'name' => 'meta_title',
            'required' => false,
            'index' => 'meta_title',
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