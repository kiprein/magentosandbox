<?php
/**
 * Simple method for getting a simple Content-Type from a cors protected source.
 * u = url from which contents will be gotten.
 * ct = Content type header
 * o = Origin
 * 
 * example:
 * http://ot.crystal-d.com/scripts/ajax/misc/norcors.php?u=example.com&ct=text/xml&o=http://ot.crystal-d.com
 * 
 * @author Mark Wickline 2022-04-27
 */
$contents = file_get_contents($_GET['u']);
ob_clean();
header('Access-Control-Allow-Methods: GET');
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Origin: {$_GET['o']}");
header("Content-Type: {$_GET['ct']}");
flush();
echo $contents;
?>