<?php
class CrysD_Carousel_Adminhtml_CarouselController extends Mage_Adminhtml_Controller_Action
{
  protected function _initCarousel()
  {
    $id = $this->getRequest()->getParam('id');
    $model = Mage::getModel('carousel/carousel');
    if ($id) {
      $model->load($id);
      if (!$model->getId()) {
        Mage::getSingleton('adminhtml/session')->addError('This carousel no longer exists.');
        $this->_redirect('*/*/');
        return false;
      }
    }
    Mage::register('current_carousel', $model);
    return $model;
  }
  
  public function indexAction()
  {
    Mage::log(__METHOD__ . ' was hit', null, 'carousel.log');
    
    $this->_title('Carousels');
    $this->loadLayout()
    ->_setActiveMenu('carousel/manage')
    ->_addContent($this->getLayout()->createBlock('carousel/adminhtml_carousel'))
    ->renderLayout();
  }
  
  public function newAction()
  {
    $this->_forward('edit');
  }
  
  public function editAction()
  {
    $model = $this->_initCarousel();
    if ($model === false) return;
    
    $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
    if (!empty($data)) {
      $model->setData($data);
    }
    
    $this->_title($model->getId() ? $model->getHeadline() : 'New Carousel');
    $this->loadLayout()
    ->_setActiveMenu('carousel/manage')
    ->_addBreadcrumb('Carousels', 'Carousels')
    ->_addBreadcrumb($model->getId() ? 'Edit Carousel' : 'New Carousel', '')
    ->_addContent($this->getLayout()->createBlock('carousel/adminhtml_carousel_edit'))
    ->renderLayout();
  }
  
  public function saveAction()
  {
    if ($data = $this->getRequest()->getPost()) {
      try {
        $model = $this->_initCarousel();
        $model->addData($data);
        $model->save();
        
        // save slides
        $slidesData = isset($data['slides']) ? $data['slides'] : [];
        Mage::getModel('carousel/slide')->getResource()->saveSlides($model, $slidesData);
        
        Mage::getSingleton('adminhtml/session')->addSuccess('Carousel saved.');
        if ($this->getRequest()->getParam('back')) {
          return $this->_redirect('*/*/edit', ['id' => $model->getId()]);
        }
      } catch (Exception $e) {
        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        Mage::getSingleton('adminhtml/session')->setFormData($data);
        return $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
      }
    }
    $this->_redirect('*/*/');
  }
  
  public function deleteAction()
  {
    if ($id = $this->getRequest()->getParam('id')) {
      try {
        Mage::getModel('carousel/carousel')->load($id)->delete();
        Mage::getSingleton('adminhtml/session')->addSuccess('Carousel deleted.');
      } catch (Exception $e) {
        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
      }
    }
    $this->_redirect('*/*/');
  }
  
  protected function _isAllowed()
  {
    return Mage::getSingleton('admin/session')->isAllowed('carousel/manage');
  }
}
