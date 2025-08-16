<?php
/**
 * Written by Mark Wickline 7/24/19
 *
 * An observer to listen to Magento event `catalog_product_load_after` and add custom attributes to cart items
 * This will hold Crystal D's optional items, such as which goal setter block to use.
 *
 * @see https://stackoverflow.com/questions/9412074/magento-quote-order-product-item-attribute-based-on-user-input/9496266#9496266
 * @see http://excellencemagentoblog.com/blog/2011/10/06/magento-add-custom-fields-checkout-page/
 * @see https://github.com/manishiitg/excellence_magento_blog/tree/master/Custom%20Checkout%20Module
 */
class Mgw_Modalcart_Model_Observer_Sales
{
    /**
     * This function is called just before $quote object get stored to database.
     * Here, from POST data, we capture our custom field and put it in the quote object
     * @param unknown_type $evt
     */
    public function saveQuoteBefore($evt){
        $quote = $evt->getQuote();
        $post = Mage::app()->getFrontController()->getRequest()->getPost();
        if(isset($post['custom']['3rdpartyAccountNumber']) && $post['custom']['3rdpartyAccountNumber']){
            $var['acc'] = $post['custom']['3rdpartyAccountNumber'];
            $var['carrier'] = trim($post['custom']['3rdPartyCarrier']) ?: '';
            $var['preferred'] = isset($post['custom']['3rdPartyPreferredService'])? $post['custom']['3rdPartyPreferredService'] : '';
            $var['company'] = isset($post['custom']['3rdPartyCompany'])? $post['custom']['3rdPartyCompany'] : '';
            $var['add1'] = isset($post['custom']['3rdPartyAddress1'])? $post['custom']['3rdPartyAddress1'] : '';
            $var['add2'] = isset($post['custom']['3rdPartyAddress2'])? $post['custom']['3rdPartyAddress2'] : '';
            $var['city'] = isset($post['custom']['3rdPartyCity'])? $post['custom']['3rdPartyCity'] : '';
            $var['state'] = isset($post['custom']['3rdPartyState'])? $post['custom']['3rdPartyState'] : '';
            $var['zip'] = isset($post['custom']['3rdPartyZip'])? $post['custom']['3rdPartyZip'] : '';
            $var['country'] = isset($post['custom']['3rdPartyCountry'])? $post['custom']['3rdPartyCountry'] : '';
            $quote->setData('3rdpartyFedex', serialize($var) );
        }
        if(isset($post['custom']['ponumber']) && $post['custom']['ponumber']){
            $quote->setData('ponumber', $post['custom']['ponumber'] );
        }
        //Coming from final order review only.
        if(isset($post['custom']['comments']) && $post['custom']['comments']){
            $quote->setData('comments', $post['custom']['comments'] );
        }
    }

      /**
     * This function is called, just after $quote object get saved to database.
     * Here, after the quote object gets saved in database
     * we save our custom field in the our table created i.e sales_quote_custom
     * @param unknown_type $evt
     */
    public function saveQuoteAfter($evt){
        //List of items to store in the database.
        $keys = [
            '3rdpartyFedex',
            'ponumber',
            'comments'
        ];
        $quote = $evt->getQuote();

        $setData = function( $key ) use ($quote){
            if($quote->getData($key)){
                $var = $quote->getData($key);
                if(!empty($var)){
                    $model = Mage::getModel('custom/custom_quote');
                    $model->deteleByQuote($quote->getId(), $key);
                    $model->setQuoteId($quote->getId());
                    $model->setKey($key);
                    $model->setValue($var);
                    $model->save();
                }
            }
        };
        
        foreach( $keys as $key ){
            $setData( $key );
        }
    }
    /**
     *
     * When load() function is called on the quote object,
     * we read our custom fields value from database and put them back in quote object.
     * @param unknown_type $evt
     */
    public function loadQuoteAfter($evt){
        $quote = $evt->getQuote();
        $model = Mage::getSingleton('custom/custom_quote');
        $data = $model->getByQuote($quote->getId());
        foreach($data as $key => $value){
            $quote->setData($key, trim($value));
        }
    }
    /**
     *
     * This function is called after order gets saved to database.
     * Here we transfer our custom fields from quote table to order table i.e sales_order_custom
     * @param $evt
     */
    public function saveOrderAfter($evt){
        $order = $evt->getOrder();
        $orderId = $order->getId();
        $quoteId = $evt->getQuote()->getId();
        $quoteModel = Mage::getSingleton('custom/custom_quote');
        
        //Data was not loading from quote properly, will need to load it from 
        //the database like in `loadQuoteAfter()`
        if( count($quoteModel->getByQuote( $quoteId )) > 0 ){
            $result = $quoteModel->getByQuote( $quoteId );
            
            foreach($result as $key => $value){
                $orderModel = Mage::getModel('custom/custom_order');
                $orderModel->deleteByOrder($orderId, $key );
                $orderModel->setOrderId( $orderId );
                $orderModel->setKey( $key );
                $orderModel->setValue( $value );
                $orderModel->save();
                $order->setData( $key, $value );
            }
        }
    }
    /**
     *
     * This function is called when $order->load() is done.
     * Here we read our custom fields value from database and set it in order object.
     * @param unknown_type $evt
     */
    public function loadOrderAfter($evt){
        $order = $evt->getOrder();
        $model = Mage::getModel('custom/custom_order');
        $data = $model->getByOrder($order->getId());
        foreach($data as $key => $value){
            $order->setData($key,$value);
        }
    }

    /**
     * https://stackoverflow.com/questions/19736273/magento-quote-items-options-do-not-persist-into-final-order
     * 
     * Will take the custom attributes like feature options and assembly instuctions and transfer them
     * to the order items
     */
    public function salesConvertQuoteItemToOrderItem(Varien_Event_Observer $observer)
    {
        $quoteItem = $observer->getItem();
        if ($additionalOptions = $quoteItem->getOptionByCode('additional_options')) {
            $orderItem = $observer->getOrderItem();
            $options = $orderItem->getProductOptions();
            $options['additional_options'] = unserialize($additionalOptions->getValue());
            $orderItem->setProductOptions($options);
        }
    }
}
