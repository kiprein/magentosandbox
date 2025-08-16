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
class NeoTheme_Blog_CategoryController extends NeoTheme_Blog_Controller_Abstract {
    public function indexAction(){
        $this->_initCategory();
        $this->_init();
        if ($this->getCategory()->getRootTemplate() && $this->getCategory()->getRootTemplate() != 'use_default') {
            $this->getLayout()->helper('page/layout')->applyTemplate($this->getCategory()->getRootTemplate());
        }
        else{
            $this->_setDefaultLayout();
        }
        $this->renderLayout();
    }

    protected function _setMetaData(){
        parent::_setMetaData();
        $headBlock = $this->getLayout()->getBlock('head');
        if ($this->getCategory()->getMetaTitle() !=  NULL){
            $headBlock->setTitle($this->getCategory()->getMetaTitle());
        }
        elseif($this->getCategory()->getTitle() != NULL){
            $headBlock->setTitle($this->getCategory()->getTitle());
        }
        if ($this->getCategory()->getMetaDescription() !=  NULL){
            $headBlock->setDescription($this->getCategory()->getMetaDescription());
        }
        if ($this->getCategory()->getMetaKeywords() !=  NULL){
            $headBlock->setKeywords($this->getCategory()->getMetaKeywords());
        }
    }

    protected function _setBlockConfig(){
        parent::_setBlockConfig();
        if ($this->getLayout()->getBlock('post.list')){
            $this->getLayout()->getBlock('post.list')->setCategoryId($this->getRequest()->getParam('id'));
        }
    }
    
    protected function _setBreadCrumbs(){
        $breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbBlock){
        $frontendName = Mage::helper('neotheme_blog')->getFrontendName();
        $UcFrontendName = ucfirst($frontendName);
        $breadcrumbBlock->addCrumb('home', 
                                    array('label'=>Mage::helper('neotheme_blog')->__('Home'), 
                                          'title'=>Mage::helper('neotheme_blog')->__('Go to Home Page'), 
                                          'link'=>Mage::getBaseUrl())); 
        $breadcrumbBlock->addCrumb('blog', 
                                    array('label'=>Mage::helper('neotheme_blog')->__($UcFrontendName), 
                                          'title'=>Mage::helper('neotheme_blog')->__('Go to ' . $UcFrontendName), 
                                          'link'=> Mage::getSingleton('core/url')->getUrl($frontendName))
                                        ); 
        $breadcrumbBlock->addCrumb('category', 
                                    array('label'=>Mage::helper('neotheme_blog')->__($this->getCategory()->getName()), 
                                          'title'=>Mage::helper('neotheme_blog')->__('Go to ' . $this->getCategory()->getName()), 
                                          'link'=>  $this->getCategory()->getListUrl())
                ); //  Mage::getSingleton('core/url')->getUrl('*/category/index', array('id' => $this->getCategory()->getId())))); 
        }
    }
}