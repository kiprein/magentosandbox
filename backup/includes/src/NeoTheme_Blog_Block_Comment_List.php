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
class NeoTheme_Blog_Block_Comment_List extends Mage_Core_Block_Template implements Mage_Widget_Block_Interface {

    function _construct() {
        if (!Mage::helper('neotheme_blog')->isDefaultCommentingEnabled()) {
            $this->setIsDisabled(true);
            $this->setTemplate(null);
            return;
        }
        $this->setCommentsCount(Mage::getStoreConfig(NeoTheme_Blog_Helper_Data::XPATH_COMMENTS_COUNT));
        $this->setTemplate('neotheme/blog/comment/list.phtml');
        $this->setSummaryBlockType('neotheme_blog/comment_summary');
        $this->setSummaryTemplate('neotheme/blog/comment/summary.phtml');
    }

    function getPost() {
        if (!$this->getData('post')) {
            $this->setData('post', Mage::registry('current_post'));
        }
        return $this->getData('post');
    }

    private $_collection;

    function _prepareCollection() {
        $this->_collection = Mage::getModel('neotheme_blog/comment')
                ->getCollection()
                ->addFieldToFilter('post_id', $this->getPost()->getId())
                ->addStatusFilter(NeoTheme_Blog_Model_Post::STATUS_ACTIVE)
                ->setOrder('created_at', Mage_Core_Model_Mysql4_Collection_Abstract::SORT_ORDER_DESC);
        if (is_numeric($this->getCommentsCount())) {
            $this->_collection->setPageSize($this->getCommentsCount());
        }
        return $this->_collection;
    }

    function getCollection() {
        if ($this->_collection == null) {
            $this->_prepareCollection();
        }
        return $this->_collection;
    }

    function _toHtml() {
        if ($this->getIsDisabled()) {
            return '';
        }
        return parent::_toHtml();
    }

    function _prepareLayout() {

        $pager = $this->getLayout()->createBlock('page/html_pager', 'custom.pager');
        $pager->setAvailableLimit(array(5 => 5, 10 => 10, 20 => 20, 'all' => 'all'));

        $this->setChild('pager', $pager);
        parent::_prepareLayout();
    }

    function getSummaryBlock($comment) {
        $blockName = 'post_comment_summary_' . $comment->getId();
        if (!$this->getLayout()->getBlock($blockName)) {
            $post_summary = $this->getLayout()->createBlock($this->getSummaryBlockType(), $blockName)
                    ->setTemplate($this->getSummaryTemplate())
                    ->setComment($comment);
            $this->getLayout()->getBlock($blockName);
        }
        return $this->getLayout()->getBlock($blockName);
    }

}