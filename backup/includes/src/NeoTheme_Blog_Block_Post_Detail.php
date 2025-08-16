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
class NeoTheme_Blog_Block_Post_Detail
extends Mage_Core_Block_Template 
implements Mage_Widget_Block_Interface
{	
    function _construct(){
        $this->setTemplate('neotheme/blog/post/detail.phtml');
        //$this->addData(array(
        //    'cache_lifetime' => 1500,
        //    'cache_tags' => array(NeoTheme_Blog_Model_Post::CACHE_TAG, NeoTheme_Blog_Model_Category::CACHE_TAG),
        //));
    }
    //public function getCacheKeyInfo()
    //{
    //    $cacheKeyInfo = array(
    //            'BLOCK_TPL',
    //            Mage::app()->getStore()->getCode(),
    //            $this->getTemplateFile(),
    //            'template' => $this->getTemplate()
    //        );
    //     if ($this->getPostId()) {
    //        $cacheKeyInfo[] = $this->getPostId();
    //     }
    //     $cacheKeyInfo[] = Mage::getSingleton('customer/session')->getCustomerGroupId();
    //     return $cacheKeyInfo;
    //}  

    function getPost(){
        if (Mage::registry('current_post') == NULL){
            if ($this->getPostId() != NULL){
                Mage::register('current_post', Mage::getModel('neotheme_blog/post')->load($this->getPostId()));
            }
        }
        return Mage::registry('current_post');
    }
	
	function _prepareLayout(){
	  $breadcrumb = $this->getLayout()->getBlock('breadcrumbs');
	  $breadcrumb->addCrumb('home',array(
	  'label'=>'Home',
	  'title'=>'Go to Home Page',
	  'link'=>Mage::getBaseUrl()
	  ));
	  
	  $breadcrumb->addCrumb('About crystal D',array(
		  'label'=>'About crystal D',
		  'title'=>'About crystal D',
		  'link'=>Mage::getUrl('about-crystal-d')
	  )); 
 }
    

}