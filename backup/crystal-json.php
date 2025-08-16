<?php
$json = file_get_contents('http://db.crystal-d.com/scripts/GoldmineAPI.php?email=sales@crystal-d.com');
$obj = json_decode($json);
echo $obj->access_token;
echo "<pre>";
print_r($obj);
?>