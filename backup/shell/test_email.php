<?php
require_once '../app/Mage.php';
umask(0);

// Initialize Magento
Mage::app();

// Enable debugging
Mage::setIsDeveloperMode(true);
ini_set('display_errors', 1);

// Create a new instance of the email library
$mail = Mage::getModel('core/email');

// Set the email parameters
$mail->setToName('Recipient Name');
$mail->setToEmail('markgw@crystal-d.com');
$mail->setBody(json_encode($_SERVER, JSON_PRETTY_PRINT));
$mail->setSubject('Test Email');
$mail->setFromEmail('blanks@crystal-d.com');
$mail->setFromName('Blanks');
// SEt enveloper sender
$mail->setSender('blanks@crystal-d.com');
$mail->setType('html'); // You can change this to 'text' if needed

// echo debug
for($x = 0; $x < 1000; $x++){
echo "Thanks! :-)";
}

try {
    // Enable SMTP debugging
    // $mail->getMail()->SMTPDebug = true;
    // var_dump($mail->getMail());
    // Send the email
    $mail->send();
} catch (Exception $e) {
    
}
