<?php
class Ravi_Testimonials_AddController extends Mage_Core_Controller_Front_Action{

    public function newAction(){

/*	$session1 = Mage::getSingleton('customer/session');		
	if ( !$session1->isLoggedIn() ){
            $this->_redirect('customer/account/login');
			//Mage::throwException($this->__('Please login to write testimonials.'));
			$session1->addError($this->__('Please login to write Testimonial'));
            return;
      }
*/	
	$session1 = Mage::getSingleton('customer/session');	
//	echo $session1->getId(); die();	
  
    $session            = Mage::getSingleton('core/session');
    $customerSession    = Mage::getSingleton('customer/session');

	$post_data	=	$this->getRequest()->getPost();
	$post_data['customer_id'] = $session1->getId();
	//echo $post_data['customer_id']; die();
	$post_data['status'] = "1";


	
	
	$email  = (string) $this->getRequest()->getPost('email');
	$security_code = (string) $this->getRequest()->getPost('security_code');
	
    try {
           if (!Zend_Validate::is($email, 'EmailAddress')) {
           Mage::throwException($this->__('Please enter a valid email address.'));
	        }
			

				 //save image
		try{

if((bool)$post_data['photo']['delete']==1) {

	        $post_data['photo']='';

}
else {

	unset($post_data['photo']);

	if (isset($_FILES)){

		if ($_FILES['photo']['name']) {

			if($this->getRequest()->getParam("id")){
				$model = Mage::getModel("testimonials/testimonial")->load($this->getRequest()->getParam("id"));
				if($model->getData('photo')){
						$io = new Varien_Io_File();
						$io->rm(Mage::getBaseDir('media').DS.implode(DS,explode('/',$model->getData('photo'))));	
				}
			}
						$path = Mage::getBaseDir('media') . DS . 'testimonials' . DS .'testimonial'.DS;
						$uploader = new Varien_File_Uploader('photo');
						$uploader->setAllowedExtensions(array('jpg','png','gif'));
						$uploader->setAllowRenameFiles(false);
						$uploader->setFilesDispersion(false);
						$destFile = $path.$_FILES['photo']['name'];
						$filename = $uploader->getNewFileName($destFile);
						$uploader->save($path, $filename);

						$post_data['photo']=$filename;
		}
    }
}

        } catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
        }
//save image
		
		$model = Mage::getModel("testimonials/testimonial")
				->addData($post_data)
				//->setCustomer_id($session1->getId())
				->setId($this->getRequest()->getParam("id"))
				->save();
	 	 $session->addSuccess(Mage::helper('core')->__('Testimonial added Successfully. It will display on website after admin approves'));	
		 	
			
	     }
         catch (Mage_Core_Exception $e) {
                $session->addException($e, $this->__('There was a problem with adding testimonial: %s', $e->getMessage()));
         }
         catch (Exception $e) {
                $session->addException($e, $this->__('There was a problem with adding testimonial.'));
         }
        $this->_redirectReferer();

	}
	
	
	
}