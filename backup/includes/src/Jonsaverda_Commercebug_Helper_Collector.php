<?php
/**
* Copyright © Pulsestorm LLC: All rights reserved
*/

class Jonsaverda_Commercebug_Helper_Collector extends Jonsaverda_Commercebug_Helper_Abstract
{
    static protected $items;
    static public function saveItem($key, $value)
    {
        self::$items[$key] = $value;
    }
}