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

            // Ensure we have a slides array to work with
            if (!isset($data['slides']) || !is_array($data['slides'])) {
                $data['slides'] = [];
            }

            // Handle slide image uploads (nested in $_FILES['slides'])
            if (!empty($_FILES['slides']['name']) && is_array($_FILES['slides']['name'])) {
                foreach ($_FILES['slides']['name'] as $i => $fileFields) {
                    // raw filename (e.g. 'foo.jpg')
                    $rawName = isset($fileFields['slide_image']) ? $fileFields['slide_image'] : '';
                    if ($rawName) {
                        // reassemble a single-file array for Varien_File_Uploader
                        $file = [
                            'name'     => $_FILES['slides']['name'][$i]['slide_image'],
                            'type'     => $_FILES['slides']['type'][$i]['slide_image'],
                            'tmp_name' => $_FILES['slides']['tmp_name'][$i]['slide_image'],
                            'error'    => $_FILES['slides']['error'][$i]['slide_image'],
                            'size'     => $_FILES['slides']['size'][$i]['slide_image'],
                        ];
                        $uploader = new Varien_File_Uploader($file);
                        $uploader->setAllowedExtensions(['jpg','jpeg','gif','png']);
                        $uploader->setAllowRenameFiles(true);
                        $uploader->setFilesDispersion(false);

                        $path = Mage::getBaseDir('media') . DS . 'carousel';
                        if (!is_dir($path)) {
                            mkdir($path, 0755, true);
                        }
                        $result = $uploader->save($path);

                        // store the resulting filename back into the form data
                        $data['slides'][$i]['slide_image'] = $result['file'];
                    }
                }
            }

            // handle mobile‐image uploads
if (!empty($_FILES['slides']['name']) && is_array($_FILES['slides']['name'])) {
    foreach ($_FILES['slides']['name'] as $i => $fields) {
        if (!empty($fields['slide_image_mobile'])) {
            // build $fileMobile same as $file above but for slide_image_mobile…
            $fileMobile = [
                'name'     => $_FILES['slides']['name'][$i]['slide_image_mobile'],
                'type'     => $_FILES['slides']['type'][$i]['slide_image_mobile'],
                'tmp_name' => $_FILES['slides']['tmp_name'][$i]['slide_image_mobile'],
                'error'    => $_FILES['slides']['error'][$i]['slide_image_mobile'],
                'size'     => $_FILES['slides']['size'][$i]['slide_image_mobile'],
            ];
            $uploader = new Varien_File_Uploader($fileMobile);
            // same config…
            $uploader->setAllowedExtensions(['jpg','jpeg','gif','png'])
                     ->setAllowRenameFiles(true)
                     ->setFilesDispersion(false);
            $path = Mage::getBaseDir('media').DS.'carousel';
            if (!is_dir($path)) mkdir($path,0755,true);
            $result = $uploader->save($path);
            $data['slides'][$i]['slide_image_mobile'] = $result['file'];
        }
    }
}


            // Save carousel itself
            $model->addData($data);
            $model->save();

            // Save slides (including the slide_image field you just set)
            $slidesData = isset($data['slides']) ? $data['slides'] : [];
            if (!empty($slidesData)) {
    // Load existing slides for this carousel in one query
    $existing = Mage::getModel('carousel/slide')->getCollection()
        ->addFieldToFilter('carousel_id', $model->getId());

    $existingById = array();
    foreach ($existing as $s) {
        $existingById[(int)$s->getId()] = array(
            'slide_image'        => $s->getSlideImage(),
            'slide_image_mobile' => $s->getSlideImageMobile(),
        );
    }

    // Merge old filenames into posted rows when those keys are missing/empty
    foreach ($slidesData as $idx => $row) {
        if (!empty($row['slide_id'])) {
            $sid = (int)$row['slide_id'];
            if (isset($existingById[$sid])) {
                if (!isset($row['slide_image']) || $row['slide_image'] === '') {
                    $slidesData[$idx]['slide_image'] = $existingById[$sid]['slide_image'];
                }
                if (!isset($row['slide_image_mobile']) || $row['slide_image_mobile'] === '') {
                    $slidesData[$idx]['slide_image_mobile'] = $existingById[$sid]['slide_image_mobile'];
                }
            }
        }
    }
}
Mage::getModel('carousel/slide')->getResource()->saveSlides($model, $slidesData);

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
