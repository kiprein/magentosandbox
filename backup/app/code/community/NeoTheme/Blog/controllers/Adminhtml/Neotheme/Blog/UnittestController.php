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
class NeoTheme_Blog_Adminhtml_Neotheme_Blog_UnittestController extends NeoTheme_Blog_Controller_Adminhtml_Abstract {



    public function indexAction() {
        $this->_title($this->__('Blog'))->_title($this->__('Post'));
        $this->loadLayout();
        //$this->getLayout()->createBlock('neotheme_blog/adminhtml_post');
       
        $this->renderLayout();
    }

    
    //public function postnewsaveAction(){
    //    $postdata = array(
    //            'neotheme_blog' => array(
    //                  'title'           => 'Unit Test',
    //                  'status'          => NeoTheme_Blog_Model_Post::STATUS_DRAFT,
    //                  'summary'         => '<p>Unit Testing Content Summary</p>',
    //                  'content_html'    => '<p>Unit Testing Content Content</p>',
    //                  'store_ids'       => '1,2',
    //                  'category_ids'    => '1,2',
    //                  'tag_ids'         => '1,2',
    //            ), 
    //            'meta_data' => array(
    //                  'meta_description'=> 'Blog Unit Test desc',
    //                  'meta_title'      => 'Blog Unit Test Title',
    //                  'meta_keywords'   => 'Blog Unit Test Keywords',
    //            )
    //        );
    //    $this->_redirect('*/neotheme_blog_post/save', $postdata);
    //}
    

}