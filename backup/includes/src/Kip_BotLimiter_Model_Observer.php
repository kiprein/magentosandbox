<?php
class Kip_BotLimiter_Model_Observer {
    public function blockAbusiveBots(Varien_Event_Observer $observer) {

        if (php_sapi_name() === 'cli') {
            return;
        }

        $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
        $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

        $cacheKey = 'bot_ip_' . $ip;
        $cache = Mage::app()->getCache();
        $hits = (int)$cache->load($cacheKey);
        $hits++;
        $cache->save((string)$hits, $cacheKey, array(), 60); // <-- (string) cast here

        if ($hits > 50000) {
            header("HTTP/1.1 429 Too Many Requests");
            echo "Too many requests.";
            exit;
        }

        $denyPattern  = '/(crawl|slurp|spider|python|curl|wget|fetch|libwww|scanner)/i';
        $allowPattern = '/^(Googlebot|Bingbot|DuckDuckBot|Yahoo! Slurp)/i';

        if (empty($userAgent) || (preg_match($denyPattern, $userAgent) && !preg_match($allowPattern, $userAgent))) {
            header("HTTP/1.1 403 Forbidden");
            echo "Access denied.";
            exit;
        }
    }
}
?>
