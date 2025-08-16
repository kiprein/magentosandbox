<?php
class Ravi_Testimonials_Helper_Data extends Mage_Core_Helper_Abstract
{
    const COUPON_CODE = 'testimonial/couponcode/addcoupon';  
    const COUPON_SENDERNAME = 'testimonial/couponcode/sendername';  
    const COUPON_SENDEREMAIL = 'testimonial/couponcode/senderemail';  
    const COUPON_EMAILSUBJECT = 'testimonial/couponcode/emailsubject';  
    public function sendCustomerNotificationEmail($tesId)
    {

        
            $senderemail = Mage::getStoreConfig(self::COUPON_SENDEREMAIL);
            $sendername = Mage::getStoreConfig(self::COUPON_SENDERNAME);
            $subject = Mage::getStoreConfig(self::COUPON_EMAILSUBJECT);
            
            $model = Mage::getModel("testimonials/testimonial")->load($tesId);
            
            
            $emailTemplate  = Mage::getModel('core/email_template')
                        ->loadDefault('testimonial_couponcode_template1');

$emailTemplate->setSenderName($sendername);
$emailTemplate->setSenderEmail($senderemail);
$emailTemplate->setTemplateSubject($subject);

//Create an array of variables to assign to template
$emailTemplateVariables = array();
$emailTemplateVariables['firstname'] = $model->getFirstname();
$emailTemplateVariables['lastname'] = $model->getLastname();
$emailTemplateVariables['couponcode'] = Mage::getStoreConfig(self::COUPON_CODE);
//$emailTemplateVariables['comment'] = $model->getComments();

$to =  $model->getEmail();

/**
 * The best part <img src="http://inchoo.net/wp-includes/images/smilies/icon_smile.gif" alt=":)" class="wp-smiley">
 * Opens the activecodeline_custom_email1.html, throws in the variable array
 * and returns the 'parsed' content that you can use as body of email
 */
$processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
/*
 * Or you can send the email directly,
 * note getProcessedTemplate is called inside send()
 */
$emailTemplate->send($to,'John Doe', $emailTemplateVariables);
            
            
        
    }



}
     