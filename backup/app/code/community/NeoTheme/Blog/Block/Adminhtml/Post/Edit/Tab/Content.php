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
class NeoTheme_Blog_Block_Adminhtml_Post_Edit_Tab_Content extends Mage_Adminhtml_Block_Widget_Form //NeoTheme_Blog_Block_Adminhtml_Form { {
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('main_form_tab');
        $this->setTabId('main_form_tab');
    }

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $form->setDataObject(Mage::registry('current_post'));
        $fieldset = $form->addFieldset('neotheme_blog_form', array('legend' => Mage::helper('neotheme_blog')->__('Post Configuration')));

        $form->setFieldNameSuffix('content_data');

        $isElementDisabled = false;

        $wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig(
            array('tab_id' => $this->getTabId())
        );

        $useSummaryField =$fieldset->addField('use_summary', 'select', array(
            'label' => Mage::helper('neotheme_blog')->__('Use Summary'),
            'index' => 'use_summary',
            'name' => 'use_summary',
            'note'      => 'If not, you can use a WordPress style <&#33;--more--> break tag in content. <br/> Be sure to hide the WYSIWYG Editor and edit in HTML for this to work.',
            'values' => array(
                array(
                    'value' => '1',
                    'label' => Mage::helper('neotheme_blog')->__('True'),
                ),
                array(
                    'value' => '0',
                    'label' => Mage::helper('neotheme_blog')->__('False')
                )
            ),
        ));
        $summaryField = $fieldset->addField('summary', 'editor', array(
            'name'      => 'summary',
            'style'     => 'min-width:615px;',
            'required'  => false,
            'label'     => Mage::helper('cms')->__('Summary'),
            'title'     => Mage::helper('cms')->__('Summary'),
            'disabled'  => $isElementDisabled,
            'config'    => $wysiwygConfig
        ));

        $contentField = $fieldset->addField('content_html', 'editor', array(
            'name'      => 'content_html',
            'style'     => 'min-width:615px;',
           // 'style'     => 'height:36em;',
            'required'  => true,
            'label'     => Mage::helper('cms')->__('Content'),
            'title'     => Mage::helper('cms')->__('Content'),
            'disabled'  => $isElementDisabled,
            'config'    => $wysiwygConfig,
        ));



        $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
                ->addFieldMap($useSummaryField->getHtmlId(), $useSummaryField->getName())
                ->addFieldMap($summaryField->getHtmlId(), $summaryField->getName())
                ->addFieldDependence(
                    $summaryField->getName(),
                    $useSummaryField->getName(),
                    '1'
                )

        );




        if (Mage::getSingleton('adminhtml/session')->getBlogPostData()) {
			
            $form->setValues(Mage::getSingleton('adminhtml/session')->getBlogPostData());
            Mage::getSingleton('adminhtml/session')->setBlogPostData(null);
		} elseif (Mage::registry('current_post')) {
			$data =  (!Mage::registry('current_post')->getId())? $this->getDefaultValues() : Mage::registry('current_post')->getData(); 
			$form->setValues($data);
		} 
        return parent::_prepareForm();
    }
	
	function getDefaultValues(){
		return array(
			'store_ids'    => array(Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID),
			'category_ids' => array(0),
			'status'	   => NeoTheme_Blog_Model_Post::STATUS_DRAFT
		);
	}

}