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
class NeoTheme_Blog_Block_Adminhtml_Category_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
   public function __construct()
   {
        parent::__construct();
        $this->setId('category_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('neotheme_blog')->__('Categories'));
   }
   
  
    protected function _prepareLayout()
    {
        $this->addTab('form_section', array(
        			  'label'     => Mage::helper('neotheme_blog')->__('Item Information'),
        			  'title'     => Mage::helper('neotheme_blog')->__('Item Information'),
         			  'content'   => $this->getLayout()->createBlock('neotheme_blog/adminhtml_category_edit_tab_form')->toHtml(),
         			  
        ));
        $this->addTab('design_section', array(
            'label'     => Mage::helper('neotheme_blog')->__('Design'),
            'title'     => Mage::helper('neotheme_blog')->__('Design'),
            'content'   => $this->getLayout()->createBlock('neotheme_blog/adminhtml_category_edit_tab_design')->toHtml(),

        ));
        $this->addTab('design_section', array(
            'label'     => Mage::helper('neotheme_blog')->__('Design'),
            'title'     => Mage::helper('neotheme_blog')->__('Design'),
            'content'   => $this->getLayout()->createBlock('neotheme_blog/adminhtml_category_edit_tab_design')->toHtml(),

        ));
        $this->addTab('meta_section', array(
            'label'     => Mage::helper('neotheme_blog')->__('Meta Data'),
            'title'     => Mage::helper('neotheme_blog')->__('Meta Data'),
            'content'   => $this->getLayout()->createBlock('neotheme_blog/adminhtml_category_edit_tab_meta')
                    ->toHtml(),
        ));
        return parent::_prepareLayout();
    }

    protected function _translateHtml($html)
    {
        Mage::getSingleton('core/translate_inline')->processResponseBody($html);
        return $html;
    }
}