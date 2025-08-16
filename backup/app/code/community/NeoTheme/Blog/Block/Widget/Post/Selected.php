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
class NeoTheme_Blog_Block_Widget_Post_Selected
extends NeoTheme_Blog_Block_Post_Detail
implements Mage_Widget_Block_Interface
{	
    function _construct(){
        //$this->setTemplate('neotheme/blog/widget/post/list_block.phtml');
        $this->setTemplate('neotheme/blog/post/detail.phtml');
        //$this->addData(array(
        //    'cache_lifetime' => 1500,
        //    'cache_tags' => array(NeoTheme_Blog_Model_Post::CACHE_TAG, NeoTheme_Blog_Model_Category::CACHE_TAG),
        //));
    }

    /**
     * Returns if Drafts are permitted
     * @return mixed
     */
    protected function _showDrafts() {
        return Mage::helper('neotheme_blog')->isIpPermitted();
    }

    /**
     * Returns the render of the html of the block
     *
     * @return string
     */
    public function _toHtml() {
        //setTemplate is run on construct, the data 'template' doesn't get retrieved in 'getTemplate'
        //so we have to do the following before render.

        $this->_collection = Mage::getModel('neotheme_blog/post')
            ->getCollection()
            ->addStoreFilter()
            ->addStatusFilter(NeoTheme_Blog_Model_Post::STATUS_ACTIVE)
            ->addPublishFilter()
            ->setPostOrder();
        if ($this->_showDrafts()){
            $this->_collection->addStatusFilter(NeoTheme_Blog_Model_Post::STATUS_DRAFT);
        }
        if ((int)$this->getData('post_id') > 0){
            $this->_collection->addFieldToFilter('entity_id', $this->getData('post_id'));
        }
        if (!($this->_collection->getFirstItem() && $this->_collection->getFirstItem()->getId())){
            return '';
        }
        $this->setData('post_id',$this->_collection->getFirstItem()->getId());
        Mage::register('current_post',  $this->_collection->getFirstItem());

        if ($this->getData('template')){
            $this->setTemplate($this->getData('template'));
        }
        return parent::_toHtml();
    }

}