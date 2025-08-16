<?php
/**
 * @author Mark Wickline 2020-02-24
 *
 * This observer fires after a successful order is place online.
 * 
 * This is a good place to hook into for automated order processing.
 */
class Mgw_ModalCart_Model_Observer_Checkout
{

    public function successAction(Varien_Event_Observer $observer)
    {
        $orderIds = $observer->getData('order_ids');
        foreach ($orderIds as $_orderId) {
            $order = Mage::getModel('sales/order')->load($_orderId);
            $customer = Mage::getModel('customer/customer')->load($order->getData('customer_id'));

            $storeId = Mage::app()->getStore()->getId();
            $paymentBlock = Mage::helper('payment')->getInfoBlock($order->getPayment())
                ->setIsSecureMode(true);
            $paymentBlock->getMethod()->setStore($storeId);
            $paymentBlockHtml = $paymentBlock->toHtml();
            $templateId = Mage::getStoreConfig('sales_email/order/template', $storeId);
            $vars = Array(
                'order'        => $order,
                'billing'      => $order->getBillingAddress(),
                'payment_html' => $paymentBlockHtml
            );
            $text = Mage::getModel('core/email_template')->loadDefault($templateId)->getProcessedTemplate($vars, true);
            $subject = "New Order # " . $order->increment_id;
            
            $dataString = json_encode([
                'userId' => 'MASTER',
                'to' => $customer->getEmail(),
                'from' => 'blanks@crystal-d.com',
                'subject' => $subject,
                'body' => $text,
                'activityCode' => 'X'
            ]);

            $headers = array(
                'x-api-key:rmxulnj77aAxji4KctmsfCVBPYiDwpIbOxx',
                'Content-Type:application/json',
                'Content-Length:' . strlen($dataString),
            );
            $http = new Varien_Http_Adapter_Curl();
            $config = array('timeout' => 10);
            //$http->setConfig($config);
            $http->write(Zend_Http_Client::POST, 'https://api.crystal-d.com/gm/linkEmail', '1.1', $headers, $dataString);
            $res = $http->read();
            //Mage::log("response" . json_encode($res), null, 'mgwlog.log', true);
            //Mage::log("response" . $res, null, 'mgwlog.log', true);
            // $code = Zend_Http_Response::extractCode($res);
            // $response = '';
            // if ($code == 200) //if success
            // {
            //     $tmp = preg_split('/^\r?$/m', $res, 2);
            //     $response = trim($tmp[1]);
            // }
            $http->close();
        }
    }
}
