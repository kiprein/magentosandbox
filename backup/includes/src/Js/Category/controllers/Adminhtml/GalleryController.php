<?php
class Js_Category_Adminhtml_GalleryController extends Mage_Adminhtml_Controller_Action
{

//    protected function _initAction()
//    {
//        $this->loadLayout()
//            ->_setActiveMenu('js_banners/items')
//            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Banners Manager'), Mage::helper('adminhtml')->__('Banners Manager'));
//        return $this;
//    }
    
    public function featuredAction() {
	    $this->loadLayout();
	    $this->_addContent($this->getLayout()->createBlock('js_category/adminhtml_featured_gallery'));
	    $this->renderLayout();
    }

	public function indexAction() {
		$this->loadLayout();
		$this->_addContent($this->getLayout()->createBlock('js_category/adminhtml_featured_gallery'));
		$this->renderLayout();
	}

	public function editAction() {
		$id    = $this->getRequest()->getParam('id');
		$model = Mage::getModel('js_category/featured_gallery');

		if ($id) {
			$model->load($id);
			if (!$model->getId()) {
				$this->_getSession()->addError(
					Mage::helper('js_category')->__('This Featured Gallery no longer exists.')
				);
				$this->_redirect('*/*/');
				return;
			}
		}

		$data = $this->_getSession()->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register('current_model', $model);

		$this->loadLayout();
		$this->_addContent($this->getLayout()->createBlock('js_category/adminhtml_featured_gallery_edit'));
		$this->renderLayout();
	}

	public function newAction() {
		$this->_forward('edit');
	}

	public function saveAction() {
		$redirectBack = $this->getRequest()->getParam('back', false);
		if ($data = $this->getRequest()->getPost()) {

			$id    = $this->getRequest()->getParam('id');
			$model = Mage::getModel('js_category/featured_gallery');
			if ($id) {
				$model->load($id);
				if (!$model->getId()) {
					$this->_getSession()->addError(
						Mage::helper('js_category')->__('This Featured Gallery no longer exists.')
					);
					$this->_redirect('*/*/index');
					return;
				}
			}

			// save model
			try {
				//Need to save the image

				if ($_FILES['image']['name'] != '') {
					try {
						$uploader = new Varien_File_Uploader('image');
						$uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
						$uploader->setAllowRenameFiles(false);
						$uploader->setFilesDispersion(false);

						$path = Mage::getBaseDir('media') . DS . 'featured_gallery' . DS;

						$uploader->save($path, $_FILES['image']['name']);
					} catch (Exception $e) {
					}
					$data['image'] = $_FILES['image']['name'];
				}

				$model->addData($data);
				$this->_getSession()->setFormData($data);
				$model->save();
				$this->_getSession()->setFormData(false);
				$this->_getSession()->addSuccess(
					Mage::helper('js_category')->__('The Featured Gallery has been saved.')
				);
			} catch (Mage_Core_Exception $e) {
				$this->_getSession()->addError($e->getMessage());
				$redirectBack = true;
			} catch (Exception $e) {
				$this->_getSession()->addError(Mage::helper('js_category')->__('Unable to save the Featured Gallery.'));
				$redirectBack = true;
				Mage::logException($e);
			}

			if ($redirectBack) {
				$this->_redirect('*/*/edit', array('id' => $model->getId()));
				return;
			}
		}
		$this->_redirect('*/*/index');
	}

	public function deleteAction() {
		if ($id = $this->getRequest()->getParam('id')) {
			try {
				// init model and delete
				$model = Mage::getModel('js_category/featured_gallery');
				$model->load($id);
				if (!$model->getId()) {
					Mage::throwException(Mage::helper('js_category')->__('Unable to find a Featured Gallery to delete.'));
				}
				$model->delete();
				// display success message
				$this->_getSession()->addSuccess(
					Mage::helper('js_category')->__('The Featured Gallery has been deleted.')
				);
				// go to grid
				$this->_redirect('*/*/index');
				return;
			} catch (Mage_Core_Exception $e) {
				$this->_getSession()->addError($e->getMessage());
			} catch (Exception $e) {
				$this->_getSession()->addError(
					Mage::helper('js_category')->__('An error occurred while deleting Featured Gallery data. Please review log and try again.')
				);
				Mage::logException($e);
			}
			// redirect to edit form
			$this->_redirect('*/*/edit', array('id' => $id));
			return;
		}
// display error message
		$this->_getSession()->addError(
			Mage::helper('js_category')->__('Unable to find a Featured Gallery to delete.')
		);
// go to grid
		$this->_redirect('*/*/index');
	}
} 
