<?php
// if(!isset($_SERVER['SHELL'])){
//     echo "Ah ah ah, you didn\'t say the magic word!";
//     exit;
// }

require_once( 'abstract.php' );
require_once( 'class_product_sync.php' );

if(session_id() == '' || !isset($_SESSION)) {
    // session isn't started
    session_start();
}


$shell = new Product_Sync();

$shell->run();
echo 'Done';
