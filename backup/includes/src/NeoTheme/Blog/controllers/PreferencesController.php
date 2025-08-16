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
class NeoTheme_Blog_PreferencesController extends NeoTheme_Blog_Controller_Abstract {

    public function indexAction(){
        $this->_init();
        $this->renderLayout();
    }
    
    public function saveAction(){
        $session = Mage::getSingleton('customer/session');
        try{
            $session->getCustomer()
                    ->setData('default_blog_category_ids', $this->getRequest()->getParam('default_blog_category_ids'))
                    ->save();
            $session->addSuccess('Successfully Saved Preferences');
            
        }
        catch(Exception $ex){
            $session->addError('Failed to Save, please notify support');
        }
        $this->_redirect('customer/account/index');
    }
    
    protected function _setBreadCrumbs(){
        $breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbBlock){
        $breadcrumbBlock->addCrumb('home', 
                                    array('label'=>Mage::helper('neotheme_blog')->__('Home'), 
                                          'title'=>Mage::helper('neotheme_blog')->__('Go to Home Page'), 
                                          'link'=>Mage::getBaseUrl())); 
        $breadcrumbBlock->addCrumb('blog', 
                                    array('label'=>Mage::helper('neotheme_blog')->__('Blog'), 
                                          'title'=>Mage::helper('neotheme_blog')->__('Go to Blog'), 
                                          'link'=> Mage::getSingleton('core/url')->getUrl('blog'))
                                        ); 
       }
    }
    

}