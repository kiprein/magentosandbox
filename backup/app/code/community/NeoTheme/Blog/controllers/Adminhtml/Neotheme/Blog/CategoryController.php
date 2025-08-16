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
class NeoTheme_Blog_Adminhtml_Neotheme_Blog_CategoryController extends NeoTheme_Blog_Controller_Adminhtml_Abstract {

    public function __construct($request, $response){
        $this->_model = "neotheme_blog/category";
        return parent::__construct($request, $response);
    }
    public function indexAction() {
        $this->_title($this->__('Blog'))->_title($this->__('Category'));
        $this->loadLayout();
        $this->renderLayout();
    }
    
    protected function _initCategory() {
        $this->_title($this->__('Blog'))
             ->_title($this->__('Manage Category'));

        $categoryId = (int) $this->getRequest()->getParam('id');
        $category = Mage::getModel('neotheme_blog/category')
                ->setStoreId($this->_getStoreId());
        if ($categoryId){
            $category->load($categoryId);
        }
        Mage::register('current_category', $category);

        return $category;
    }
    
    protected function _getCategory() {
        return Mage::registry('current_category');
    }

    public function newAction() {
        $this->_forward('edit');
    }
    
    public function editAction() {
        $this->_initCategory();
        $id = $this->getRequest()->getParam('id');
        
        $model = $this->_getCategory(); 
        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            $isnew = ($id == 0);
            if (!empty($data)) {
                $model->setData($data);
            }
            if ($isnew) {
                $model->setStatus(true);
            }
            //Mage::register('current_post', $model);

            $this->loadLayout();
      //      if (!Mage::app()->isSingleStoreMode() && ($switchBlock = $this->getLayout()->getBlock('store_switcher'))) {
       //         $switchBlock->setDefaultStoreName($this->__('Default Values'))
       //             ->setSwitchUrl(
       //                 $this->getUrl('*/*/*', array('_current'=>true, 'active_tab'=>null, 'tab' => null, 'store'=>null))
       //             );
       //     }
            $this->_addBreadcrumb(Mage::helper('neotheme_blog')->__('Blog'), Mage::helper('neotheme_blog')->__('Category'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('neotheme_blog')->__('Item does not exist'));
            $this->_redirect('*/*/', array('store' => $this->getRequest()->getParam('store')));
        }
    }

    protected $_postData;
    protected function _getPostData(){
        if ($this->_postData == null){
            $this->_postData = array_merge( $this->getRequest()->getPost('category'),
                                            $this->getRequest()->getPost('design_data'),
                                            $this->getRequest()->getPost('meta_data')

            );
            if (count($this->_postData) == 0){
                $this->_postData = null;
            }
        }
        return $this->_postData;
    }
    
    protected function _initCategorySave() {
        $category = $this->_initCategory();
        $data = $this->_getPostData();
        
        $category->addData($data);
        return $category;
    }

     public function saveAction() {
        $storeId = $this->getRequest()->getParam('store');

        if ($data = $this->_getPostData()) {
            $model  = $this->_initCategorySave();
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
                if (Mage::helper('neotheme_blog')->addIpToPermittedIps()){
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
}