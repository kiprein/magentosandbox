<?php
/**
* Copyright © Pulsestorm LLC: All rights reserved
*/
class Jonsaverda_Commercebug_Model_Crossareaajax_Togglehints extends Jonsaverda_Commercebug_Model_Crossareaajax
{
    public function handleRequest()
    {
        $session = $this->_getSessionObject();
        $c = $session->getData(Jonsaverda_Commercebug_Model_Observer::TEMPLATE_HINTS_ON);
        $c = $c == 'on' ? 'off' : 'on';        
        $session->setData(Jonsaverda_Commercebug_Model_Observer::TEMPLATE_HINTS_ON,$c);
        $this->endWithHtml('Template Hints ' . ucwords($c) .' -- Refresh to see Changes.');
    }    
}