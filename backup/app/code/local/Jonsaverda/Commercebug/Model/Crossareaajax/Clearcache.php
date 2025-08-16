<?php
/**
* Copyright © Pulsestorm LLC: All rights reserved
*/
class Jonsaverda_Commercebug_Model_Crossareaajax_Clearcache extends Jonsaverda_Commercebug_Model_Crossareaajax
{
    public function handleRequest()
    {
        $shim = $this->getShim();
        $shim->helper('commercebug/cacheclearer')->clearCache();
        $this->endWithHtml('Cache Cleared');    
    }
}