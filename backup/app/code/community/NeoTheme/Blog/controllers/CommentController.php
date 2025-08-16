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
class NeoTheme_Blog_CommentController extends NeoTheme_Blog_Controller_Abstract {

    protected function _initComment(){
        
        $id = $this->getRequest()->getParam('comment_id', 0);
        $comment = Mage::getModel('neotheme_blog/comment');
        if ($id){
            $comment->load($id);
        }
        Mage::register('current_comment', $comment);
        return $comment;
    }
    
    protected function getComment(){
        $comment = Mage::registy('current_comment');
        if (!$comment){
            $this->_initComment();
        }
        return Mage::registy('current_comment');
    }
    
    public function saveAction(){
        if (!Mage::getSingleton('customer/session')->authenticate($this)){
            $session = Mage::getSingleton('core/session');
            $session->addError('Commenting is only available to registered users');
            $url = urldecode($this->getRequest()->getParam('return_url'));
            $this->_redirectUrl($url);
            return;
        }

        $comment = $this->_initComment();
        $comment->setData($this->getRequest()->getParams());
        $comment->setUpdatedAt(now());
        $session = Mage::getSingleton('core/session');
        try{
            $comment->save();
            $session->addSuccess('Comment Added');
            
        }
        catch(Exception $ex){
            $session->addError('Failed to Save, please notify support');
        }
        $url = urldecode($this->getRequest()->getParam('return_url'));
        $this->_redirectUrl($url);
    }
    
    

}
