<?php
/**
* Copyright © Pulsestorm LLC: All rights reserved
*/
class Jonsaverda_Commercebug_Model_Graphviz
{
    public function capture()
    {    
        $collector  = new Jonsaverda_Commercebug_Model_Collectorgraphviz; 
        $o = new stdClass();
        $o->dot = Jonsaverda_Commercebug_Model_Observer_Dot::renderGraph();
        $collector->collectInformation($o);
    }
    
    public function getShim()
    {
        $shim = Jonsaverda_Commercebug_Model_Shim::getInstance();        
        return $shim;
    }    
}