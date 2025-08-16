<?php
class Ravi_Testimonials_IndexController extends Mage_Core_Controller_Front_Action{
	
	const XML_PATH_MODULE_DISABLED = 'testimonial/status/enabled';
	
    public function IndexAction() {
		
      
	//echo ; die();  
    $output = Mage::getStoreConfig('advanced/modules_disable_output/Ravi_Testimonials');
    //echo $output;
	if(Mage::getStoreConfig(self::XML_PATH_MODULE_DISABLED) == 0 && $output == 1){
		

	$this->getResponse()->setHeader('HTTP/1.1','404 Not Found');
$this->getResponse()->setHeader('Status','404 File not found');
$pageId = Mage::getStoreConfig('web/default/cms_no_route');
if (!Mage::helper('cms/page')->renderPage($pageId)) {
    $this->_forward('noRoute');
}
		
		return;	
	}

    else if($output == 1){

        $this->getResponse()->setHeader('HTTP/1.1','404 Not Found');
        $this->getResponse()->setHeader('Status','404 File not found');
        $pageId = Mage::getStoreConfig('web/default/cms_no_route');
        if (!Mage::helper('cms/page')->renderPage($pageId)) {
            $this->_forward('noRoute');
        }

        return;


    }

    else if(Mage::getStoreConfig(self::XML_PATH_MODULE_DISABLED) == 0){

        $this->getResponse()->setHeader('HTTP/1.1','404 Not Found');
        $this->getResponse()->setHeader('Status','404 File not found');
        $pageId = Mage::getStoreConfig('web/default/cms_no_route');
        if (!Mage::helper('cms/page')->renderPage($pageId)) {
            $this->_forward('noRoute');
        }

        return;



    }

    else{
	  
	  $this->loadLayout();   
	  $this->getLayout()->getBlock("head")->setTitle($this->__("Add Testimonial"));
	        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
      $breadcrumbs->addCrumb("home", array(
                "label" => $this->__("Home Page"),
                "title" => $this->__("Home Page"),
                "link"  => Mage::getBaseUrl()
		   ));


      $breadcrumbs->addCrumb("add testimonial", array(
                "label" => $this->__("Add Testimonial"),
                "title" => $this->__("Add Testimonial")
		   ));

      $this->renderLayout();
	}
    }
}