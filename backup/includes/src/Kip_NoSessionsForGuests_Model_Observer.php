<?php
class Kip_NoSessionForGuests_Model_Observer
{
    public function preventSessionStart(Varien_Event_Observer $observer)
    {
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            if (session_id()) {
                session_write_close(); // If session was already started, close it immediately
            }
            ini_set('session.use_trans_sid', 0);
            ini_set('session.use_cookies', 0);
            ini_set('session.use_only_cookies', 1);
            Mage::unregister('_singleton/core/session'); // FORCE Magento to stop using core/session singleton
        }
    }
}
?>
