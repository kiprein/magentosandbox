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
abstract class NeoTheme_Blog_Controller_Adminhtml_Abstract
extends Mage_Adminhtml_Controller_Action  {
    public function newAction() {
        $this->_forward('edit');
    }

    protected function _getStoreId() {
        return (int) $this->getRequest()->getParam('store');
    }

    protected $_store;

    protected function _getStore() {
        if ($this->_store == NULL) {
            $this->_store = Mage::app()->getStore($this->_getStoreId());
        }
        return $this->_store;
    }
    
    protected $_model;
    
    public function deleteAction() {
        if (!$this->_model){
            Mage::throwException("Model not defined");
        }
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel($this->_model);

                $model->setId($this->getRequest()->getParam('id'))
                        ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id'), 'store' => $this->getRequest()->getParam('store')));
            }
        }
        $this->_redirect('*/*/', array('store' => $this->getRequest()->getParam('store')));
    }
}