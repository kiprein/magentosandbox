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
class NeoTheme_Blog_Block_Adminhtml_Comment_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form //NeoTheme_Blog_Block_Adminhtml_Form { {
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('main_form_tab');
        $this->setTabId('main_form_tab');
    }

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $isElementDisabled = false;
        $this->setForm($form);
        $form->setDataObject(Mage::registry('current_post'));
        $fieldset = $form->addFieldset('neotheme_blog_form', array('legend' => Mage::helper('neotheme_blog')->__('Post Configuration')));

        $form->setFieldNameSuffix('neotheme_blog');

        $fieldset->addField('title', 'text', array(
            'label' => Mage::helper('neotheme_blog')->__('Post Title'),
            'class' => 'required-entry',
            'name' => 'title',
            'required' => true,
            'index' => 'title',
        ));

        
        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('neotheme_blog')->__('Status'),
            'index' => 'status',
            'name' => 'status',
            'values' => array(
                array(
                    'value' => 2,
                    'label' => Mage::helper('neotheme_blog')->__('Draft'),
                ),
                array(
                    'value' => 1,
                    'label' => Mage::helper('neotheme_blog')->__('Active'),
                ),
                array(
                    'value' => 0,
                    'label' => Mage::helper('neotheme_blog')->__('Inactive'),
                ),
            ),
        ));
        

        
        $renderer = $this->getLayout()->createBlock('adminhtml/widget_form_renderer_fieldset_element')
                                      ->setTemplate('neotheme/blog/edit/form/renderer/tag.phtml');
        $tags_field =  $fieldset->addField('tag_ids', 'text', array(
            'name'      => 'tag_ids',
            'label'     => Mage::helper('cms')->__('Tags'),
            'title'     => Mage::helper('cms')->__('Tags'),
            'disabled'  => $isElementDisabled,
            'json_list' => "[ 'test', 'another' ]"
        ));
        $tags_field->setRenderer($renderer);
        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('store_ids', 'multiselect', array(
                'name' => 'store_ids[]',
                'label' => Mage::helper('cms')->__('Store View'),
                'title' => Mage::helper('cms')->__('Store View'),
                'required' => true,
                'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
                'disabled' => $isElementDisabled
            ));
        } else {
            $fieldset->addField('store_ids', 'hidden', array(
                'name' => 'store_ids[]',
                'value' => Mage::app()->getStore(true)->getId()
            ));
            $model->setStoreId(Mage::app()->getStore(true)->getId());
        }
        //TODO: replace the categories
        $fieldset->addField('category_ids', 'multiselect', array(
            'name' => 'category_ids[]',
            'label' => Mage::helper('neotheme_blog')->__('Categories'),
            'title' => Mage::helper('neotheme_blog')->__('Categories'),
            'required' => true,
            'values' => Mage::helper('neotheme_blog')->getCategoryOptionValues(),
            'disabled' => $isElementDisabled,
            'index' => 'category_ids'
        ));
        
        $wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig(
            array('tab_id' => $this->getTabId())
        );
        
        $summaryField = $fieldset->addField('summary', 'editor', array(
            'name'      => 'summary',
            'style'     => 'height:36em;',
            'required'  => true,
            'label'     => Mage::helper('cms')->__('Summary'),
            'title'     => Mage::helper('cms')->__('Summary'),
            'disabled'  => $isElementDisabled,
            'config'    => $wysiwygConfig
        ));

        $contentField = $fieldset->addField('content_html', 'editor', array(
            'name'      => 'content_html',
            'style'     => 'height:36em;',
            'required'  => true,
            'label'     => Mage::helper('cms')->__('Content'),
            'title'     => Mage::helper('cms')->__('Content'),
            'disabled'  => $isElementDisabled,
            'config'    => $wysiwygConfig,
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