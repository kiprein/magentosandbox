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
class NeoTheme_Blog_Block_Account_Preferences_Edit
extends Mage_Core_Block_Template 
implements Mage_Widget_Block_Interface
{	
    function __construct(){
        $this->setTitle('Blog Preferences');
    }
    
    function getSaveUrl(){
        return  Mage::getUrl('*/*/save');
    }
    
    function getSelectedCategories(){
        if (!$this->getData("selected_category_ids")){
            $categoryIdsStr = Mage::getSingleton('customer/session')->getCustomer()->getData('default_blog_category_ids');
            $this->setData("selected_category_ids", explode(",",$categoryIdsStr));
        }
        return $this->getData("selected_category_ids");
    }
    
    function getCategories(){
        $categories = Mage::getModel('neotheme_blog/category')->getCollection()->addStoreFilter();
        return $categories;
    }


}