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
class NeoTheme_Blog_Adminhtml_Neotheme_Blog_CommentController extends NeoTheme_Blog_Controller_Adminhtml_Abstract {

    public function __construct($request, $response){
        $this->_model = "neotheme_blog/comment";
        return parent::__construct($request, $response);
    }

    public function indexAction() {

        //$this->_title($this->__('Blog'))->_title($this->__('Post'));
        $this->loadLayout();
        //$this->getLayout()->createBlock('neotheme_blog/adminhtml_post');
       
        $this->renderLayout();
    }

    protected function _initPost() {
        $this->_title($this->__('Blog'))
             ->_title($this->__('Manage Posts'));

        $postId = (int) $this->getRequest()->getParam('id');
        $post = Mage::getModel('neotheme_blog/post')
                ->setStoreId($this->_getStoreId());
        if ($postId){
            $post->load($postId);
        }
        Mage::register('current_post', $post);

        return $post;
    }

    protected function _getPost() {
        return Mage::registry('current_post');
    }

    public function categoriesJsonAction() {
        $this->_initPost();

        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('imagerotator/adminhtml_image_edit_tab_categories')
                        ->getCategoryChildrenJson($this->getRequest()->getParam('category'))
        );
    }

    public function editAction() {
        $this->_initPost();
        $id = $this->getRequest()->getParam('id');
        
        $model = $this->_getPost(); 
        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            $isnew = ($id == 0);
            if (!empty($data)) {
                $model->setData($data);
            }
            if ($isnew) {
                $model->setStatus(NeoTheme_Blog_Model_Post::STATUS_ACTIVE);
                $user = Mage::getSingleton('admin/session')->getUser();
                $model->setAuthor($user->getFirstname() . " " . $user->getLastname());
                $model->setPostDate(now());
            }

            $this->loadLayout();
            $this->_addBreadcrumb(Mage::helper('neotheme_blog')->__('Blog'), Mage::helper('neotheme_blog')->__('Post'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('neotheme_blog')->__('Item does not exist'));
            $this->_redirect('*/*/', array('store' => $this->getRequest()->getParam('store')));
        }
    }

    
    private function _initPostSave() {
        $blogpost = $this->_initPost();
        $blogpost->addData($this->_getBlogPostData());
        return $blogpost;
    }
    
    private function _getBlogPostData(){
        $data = array_merge($this->getRequest()->getPost('neotheme_blog'), 
                           $this->getRequest()->getPost('meta_data'),
                           $this->getRequest()->getPost('publishing_data')
                );
        $data = $this->_filterDates($data, array('post_date', 'publish_date'));
        return $data;
    }
    
    protected function _filterDates($array, $dateFields)
    {
        if (empty($dateFields)) {
            return $array;
        }
        $filterInput = new Zend_Filter_LocalizedToNormalized(array(
            'date_format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
        ));
        $filterInternal = new Zend_Filter_NormalizedToLocalized(array(
            'date_format' => Varien_Date::DATE_INTERNAL_FORMAT
        ));

        foreach ($dateFields as $dateField) {
            if (array_key_exists($dateField, $array) && !empty($dateField)) {
                $array[$dateField] = $filterInput->filter($array[$dateField]);
                $array[$dateField] = $filterInternal->filter($array[$dateField]);
            }
        }
        return $array;
    }
    
    public function saveAction() {
        $storeId = $this->getRequest()->getParam('store');
        if ($data = $this->_getBlogPostData()) {
            $model  = $this->_initPostSave();
            if($model == NULL){
                $this->_redirect('*/*/edit', array( 'id' => $this->getRequest()->getParam('id'),
                                                    'store' => $storeId));
                return;
            }
            $model->setId($this->getRequest()->getParam('id'));
            try {
                if ($model->getCreatedAt() == NULL || $model->getUpdatedAt() == NULL) {
                    $model->setCreatedAt(now())
                          ->setUpdatedAt(now());
                } else {
                    $model->setUpdatedAt(now());
                }

                $model->save();
                if (Mage::helper('neotheme_blog')->addIpToPermittedIps($storeId)){
                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('neotheme_blog')->__('Your IP Address has been added to the Draft Viewing List, you are now able see the Drafts on the Frontend'));
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('neotheme_blog')->__('Item was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array( 'id' => $model->getId(), 
                                                        'store' => $storeId));
                    return;
                }
                $this->_redirect('*/*/',  array('store' => $storeId));
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array( 'id' => $this->getRequest()->getParam('id'),
                                                    'store' => $storeId));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('neotheme_blog')->__('Unable to find item to save'));
        $this->_redirect('*/*/', array('store' => $storeId));
    }


    public function massDeleteAction() {
        $commentIds = $this->getRequest()->getParam('comment_ids');
        if (!is_array($commentIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($commentIds as $commentId) {                
                    $comment = Mage::getModel('neotheme_blog/comment');

                    $comment->setId($commentId)
                            ->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__(
                                'Total of %d comment(s) were successfully deleted', count($commentIds)
                        )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index', array('store' => $this->getRequest()->getParam('store')));
    }

    public function massStatusAction() {
        $commentIds = $this->getRequest()->getParam('comment_ids');
        if (!is_array($commentIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($commentIds as $commentId) {
                    $comment = Mage::getSingleton('neotheme_blog/comment')
                            ->load($commentId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) were successfully updated', count($commentIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index', array('store' => $this->getRequest()->getParam('store')));
    }

    public function exportCsvAction() {
        $fileName = 'imagerotator.csv';
        $content = $this->getLayout()->createBlock('imagerotator/adminhtml_imagerotator_grid')
                ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }


    public function gridAction() {
        $this->loadLayout();
        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('neotheme_blog/adminhtml_comment_grid')->toHtml()
        );
    }


}