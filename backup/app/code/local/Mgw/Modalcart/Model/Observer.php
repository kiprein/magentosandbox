<?php
/**
 * Observer for ERP stock-clamping after cart add/update
 */
class Mgw_Modalcart_Model_Observer
{
    /**
     * Restrict quote item quantity to ERP-available stock
     * 
     * @param Varien_Event_Observer $observer
     */
    public function restrictQuoteItemQty(Varien_Event_Observer $observer)
    {
        /** @var Mage_Sales_Model_Quote_Item $item */
        $item    = $observer->getEvent()->getQuoteItem();
        $product = $item->getProduct();
        // Fetch old ERP product ID
        $oldId   = $product->getOldProductId();
        // Get live inventory
        $data    = Mage::helper('mgw_modalcart')->getLiveInventory($oldId);
        $avail   = isset($data[0]['qty']) ? (int)$data[0]['qty'] : 0;

        // If current quote quantity exceeds ERP stock, clamp it
        if ($item->getQty() > $avail) {
            $original = $item->getQty();
            $item->setQty($avail);

            // Add a notice to the session
            Mage::getSingleton('checkout/session')->addNotice(
                Mage::helper('mgw_modalcart')->__(
                    'Quantity for SKU %s limited from %d to %d by live inventory.',
                    $product->getSku(),
                    $original,
                    $avail
                )
            );

            // Recollect totals and save the quote
            $quote = $item->getQuote();
            $quote->collectTotals()->save();
        }
    }
}
