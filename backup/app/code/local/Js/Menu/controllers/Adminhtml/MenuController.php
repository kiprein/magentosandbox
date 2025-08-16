<?php
class Js_Menu_Adminhtml_MenuController extends Mage_Adminhtml_Controller_Action
{

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('js_menu/menu')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Menu Manager'), Mage::helper('adminhtml')->__('Menu Manager'));
        return $this;
    }
    
    public function indexAction() {
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('js_menu/adminhtml_menu'));
        $this->renderLayout();
    }

    public function importAction() {
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('js_menu/adminhtml_menu_import'));
        $this->renderLayout();
    }
    
    public function editAction()
    {
        $menuId     = $this->getRequest()->getParam('id');
        $model  = Mage::getModel('js_menu/menu')->load($menuId);
        if ($model->getId() || $menuId == 0) {
    
            Mage::register('menu_item_data', $model);
    
            $this->loadLayout();
            $this->_setActiveMenu('js_menu/items');
    
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
    
            $this->_addContent($this->getLayout()->createBlock('js_menu/adminhtml_menu_edit'))
                 ->_addLeft($this->getLayout()->createBlock('js_menu/adminhtml_menu_edit_tabs'));
    
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('js_menu')->__('Menu item does not exist.'));
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
                $menuData = $postData['menu'];
                $model = Mage::getModel('js_menu/menu');
                $model->setId($this->getRequest()->getParam('id'));

	            //Makes it easier to save each field if adding new ones
	            foreach($menuData as $key => $value) {
		            $keySave = str_replace('_', ' ', $key);
		            $keySave = 'set' . str_replace(' ', '', ucwords( $keySave ) );

		            if(is_array($value)) {
		            	$value = json_encode($value);
		            }

		            $model->$keySave( $value );
	            }

                $model->save();
        
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Menu Item was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setMenuItemData(false);
    
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setMenuItemData($this->getRequest()->getPost());
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
                $bannersModel = Mage::getModel('js_menu/banners');
    
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

    public function importMenuAction() {
		$helper = Mage::helper('js_menu');

    	$csvObject = new Varien_File_Csv();
		$fileName  = $_FILES['import_file']['tmp_name'];
		$csvData   = $csvObject->getData( $fileName );

		foreach ( $csvData as $row => $column ) {
			//Skip over the first row
			if ($row == 0) {
				continue;
			}

			if ( $column[0] ) {
				////Mage::log("Memory Usage: " . ( memory_get_peak_usage( true ) / 1024 / 1024 ) . " MiB");
				//Trim all white space from values
				$column = array_map('trim', $column);

				$id         = $column[0];
				$menuType   = $helper->getMenuType($column[1]);
				$title      = $column[2];
				$parentId   = $column[3];
				$position   = $column[4];
				$url        = $column[5];
				$target     = $helper->getTarget($column[6]);
				$class      = $column[7];
				$active     = $column[8];

				//Setup the model
				$model = Mage::getModel('js_menu/menu')->load($id);

                $model->setMenuType($menuType)
	                ->setTitle($title)
	                ->setParentId($parentId)
	                ->setPosition($position)
	                ->setUrl($url)
	                ->setPermission($permission)
	                ->setTarget($target)
	                ->setClass($class)
	                ->setActive($active);

                //Save and cleanup
				$model->save();
                $model->clearInstance();

			}
		}

		Mage::getSingleton( 'adminhtml/session' )->addSuccess( Mage::helper( 'js_menu' )->__( 'The import has successfully completed' ) );
		$this->_redirect( '*/menu/' );

		return;
    }
} 
