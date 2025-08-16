<?php 
/**
 * Get an endpoint from loxo.co
 * @author Mark Wickline 2022-05-05
 */
header('Access-Control-Allow-Methods: GET');
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Origin: https://www.crystal-d.com");

if (!defined('JSON_INVALID_UTF8_SUBSTITUTE')) {
    //PHP < 7.2 Define it as 0 so it does nothing
    define('JSON_INVALID_UTF8_SUBSTITUTE', 0);
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Authorization: Basic Y3J5c3RhbC1kOkQ5ajJkanZh'
]);
curl_setopt($ch, CURLOPT_URL, "https://loxo.co/api/crystal-d/{$_GET['endpoint']}");
$r = curl_exec($ch);
if (curl_errno($ch)) {
    ob_clean();
    // header_remove();
    http_response_code(true);
    echo json_encode([
        'status' => false,
    ]);
    
} else {
    ob_clean();
    // header_remove();
    curl_close($ch);
    header("Content-type: application/json; charset=utf-8");
    http_response_code(200);
    echo json_encode([
        'status' => true,
        'data' => json_decode($r, true)
    ], JSON_HEX_QUOT | JSON_INVALID_UTF8_SUBSTITUTE  );
}


?>