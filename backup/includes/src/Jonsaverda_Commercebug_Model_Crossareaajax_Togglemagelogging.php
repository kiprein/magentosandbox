<?php
/**
* Copyright © Pulsestorm LLC: All rights reserved
*/
class Jonsaverda_Commercebug_Model_Crossareaajax_Togglemagelogging extends Jonsaverda_Commercebug_Model_Crossareaajax
{
    public function handleRequest()
    {
        $session = $this->_getSessionObject();        
        $c = $session->getData(Jonsaverda_Commercebug_Model_Observer::MAGE_LOGGING_ON);
        $c = $c == 'on' ? 'off' : 'on';        
        $session->setData(Jonsaverda_Commercebug_Model_Observer::MAGE_LOGGING_ON, $c);        
        $this->endWithHtml('Mage Logging ' . ucwords($c) .'');        
    }
}