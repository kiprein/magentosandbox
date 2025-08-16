<?php
class Js_Banners_Adminhtml_BannersController extends Mage_Adminhtml_Controller_Action
{

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('js_banners/items')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Banners Manager'), Mage::helper('adminhtml')->__('Banners Manager'));
        return $this;
    }
    
    public function indexAction() {
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('js_banners/adminhtml_banners'));
        $this->renderLayout();
    }
    
    public function editAction()
    {
        $bannersId     = $this->getRequest()->getParam('id');
        $bannersModel  = Mage::getModel('js_banners/banners')->load($bannersId);
        if ($bannersModel->getId() || $bannersId == 0) {
    
            Mage::register('banners_data', $bannersModel);
    
            $this->loadLayout();
            $this->_setActiveMenu('js_banners/items');
    
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
    
            $this->_addContent($this->getLayout()->createBlock('js_banners/adminhtml_banners_edit'))
                 ->_addLeft($this->getLayout()->createBlock('js_banners/adminhtml_banners_edit_tabs'));
    
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('js_banners')->__('Item does not exist.'));
            $this->_redirect('*/*/');
        }
    }
    
    public function newAction()
    {
        $this->_forward('edit');
    }
    
    public function saveAction()
    {
        if ( $this->getRequest()->getPost() ) {
            try {
                $postData = $this->getRequest()->getPost();
                $bannerInfo = $postData['banner'];

				$img = '';
	            if ( $_FILES['image']['name'] != '' ) {
		            try {
			            $uploader = new Varien_File_Uploader( 'image' );
			            $uploader->setAllowedExtensions( array( 'jpg', 'jpeg', 'gif', 'png' ) );
			            $uploader->setAllowRenameFiles( false );
			            $uploader->setFilesDispersion( false );

			            $path = Mage::getBaseDir('media') . DS  . 'banners' . DS;

			            $uploader->save( $path, $_FILES['image']['name'] );
		            } catch ( Exception $e ) {
		            }
		            $img = $_FILES['image']['name'];
	            }
    
                $bannersModel = Mage::getModel('js_banners/banners');
                $bannersModel->setId($this->getRequest()->getParam('id'));

	            //Makes it easier to save each field if adding new ones
	            foreach($bannerInfo as $key => $value) {
		            $keySave = str_replace('_', ' ', $key);
		            $keySave = 'set' . str_replace(' ', '', ucwords( $keySave ) );

		            $bannersModel->$keySave( $value );
	            }

                if($img != '') {
                    $bannersModel->setImage($img);
                }
                $bannersModel->save();
        
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Banner was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setbannersData(false);
    
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setbannersData($this->getRequest()->getPost());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }
    
    public function deleteAction()
    {
        if( $this->getRequest()->getParam('id') > 0 ) {
            try {
                $bannersModel = Mage::getModel('js_banners/banners');
    
                $bannersModel->setId($this->getRequest()->getParam('id'))
                    ->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Banner was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }
} 
