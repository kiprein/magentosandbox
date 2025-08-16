<?php

/**
 * Crystal D shipping carrier service.
 * 
 * Makes a call to the Webster API and returns our current company shipping rates.
 * 
 * @author Mark Wickline
 * @see https://inchoo.net/magento/custom-shipping-method-in-magento/
 */
class Mgw_Modalcart_Model_Shipping_Carrier extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface
{
    /**
     * Carrier's code, as defined in parent class
     *
     * @var string
     */
    protected $_code = 'mgw_modalcart';

    /**
     * Key value pair for services. These are taken from the methCode 
     * value returned from the freight class.
     */
    protected $shippingMethods = array(
        '06' => 'FedEx Ground',
        '01' => 'FedEx 3 Day Express Saver',
        '03' => 'FedEx 2nd Day',
        '07' => 'FedEx 2 Day AM',
        '02' => 'FedEx Standard Overnight',
        '04' => 'FedEx Priority Overnight',
        '05' => 'FedEx First Day AM Overnight',
        'fedex_3rdparty' => 'Use your own Fedex or UPS account'
    );
    /**
     * Returns available shipping rates for Inchoo Shipping carrier
     *
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return Mage_Shipping_Model_Rate_Result
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        /** @var Mage_Shipping_Model_Rate_Result $result */
        $result = Mage::getModel('shipping/rate_result');

        $products = array(); //products that will be sent to the CrystalD API
        $grandTotal = 0;     //Dollar amount of all the products to calculate TR free.

        foreach ($request->getAllItems() as $item) {
            $buildItem = new stdClass();
            $productReload = Mage::getModel('catalog/product')->load($item->getProductId());
            $qty = $item->getQty();
            // Mage::log("qty form carrier.php " . $qty, null, 'mgwlog.log', true);
            $finalPrice = $productReload->getFinalPrice($qty, $productReload);
            $total = ($finalPrice * $qty);
            // Mage::log("\$total pre tariff " . $total, null, 'mgwlog.log', true);
            //Need to pull the tariff surcharge from the total to calculate TR free #tariffSurcharge.
            /*
            if( $productReload->getCountry() == 'China'){
                $total = $total - ( $total * 0.03 );
            }*/
            // Mage::log("\$total post " . $total, null, 'mgwlog.log', true);


            $grandTotal += $total;

            // if ($productReload->getCountry() == 'China') {
            //     $total = $total + ($total * 0.15); // Apply 15% tariff
            // }

            $buildItem->item = $item->getSku(); //s_item_num
            $buildItem->w = floatval($productReload->getGiftBoxWidth()); //n_gift_box_width
            $buildItem->h = floatval($productReload->getGiftBoxHeight()); //n_gift_box_height
            $buildItem->d = floatval($productReload->getGiftBoxLength()); //n_gift_box_length
            $buildItem->wg = floatval($productReload->getGiftboxShipWeight()); //n_ship_weight
            $buildItem->q = $qty;
            $buildItem->b_ground_shipping_anywhere = intval($productReload->getShippingAnywhere()); //b_ground_shipping_anywhere
            $buildItem->b_ground_shipping_1_day = $productReload->getShipping1Day() ? 0 : 1; //b_ground_shipping_1_day
            $products[] = $buildItem;
        }
        // Mage::log("\$grandTotal " . $grandTotal, null, 'mgwlog.log', true);

        //Get rates from Webster... api.crystal-d.com/freight
        $ratesResponse = json_decode($this->getCrstalDRates($products, $grandTotal >= 400));

        if ($ratesResponse) {
            $ratesResponse->data = (array)$ratesResponse->data;
            foreach ($ratesResponse->data as $method => $rates) {
                if (!isset($this->shippingMethods[$rates->methCode]))
                    continue;
                $result->append($this->_getRate(trim($rates->methCode), floatval($rates->calculated)));
            }
        }
        //Append 3rd party shipping option
        $result->append($this->_getRate('fedex_3rdparty', 0, true));
        return $result;
    }

    /**
     * Returns Allowed shipping methods
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        return $this->shippingMethods;
    }
    /**
     * Get Standard rate object
     * 
     * @param string $method
     * @param float $price
     * @param bool $useKey Detetermines if the first paramater is a key or value, value is default
     * @return Mage_Shipping_Model_Rate_Result_Method
     */
    protected function _getRate($method, $price, $isKey = false)
    {
        /** @var Mage_Shipping_Model_Rate_Result_Method $rate */
        $rate = Mage::getModel('shipping/rate_result_method');
        $rate->setCarrier($this->_code);
        $rate->setCarrierTitle($this->getConfigData('title'));
        $rate->setMethod($method);
        $rate->setMethodTitle($this->shippingMethods[$method]);
        $rate->setPrice($price); //Customer cost
        $rate->setCost(0); //Merchant cost
        return $rate;
    }

    /**
     * Send curtl to Webster API to get the rates from freight quote script
     * @param array
     * @return object
     */
    protected function getCrstalDRates($products, $trFree)
    {
        $url = 'https://api.crystal-d.com/freight/estimate';
        $param = new stdClass();
        $param->zipcode = Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress()->getData()['postcode'];
        $param->trFree = $trFree ? 1 : 0;
        $param->products = $products;

        try {
            $http = new Varien_Http_Adapter_Curl();
            $config = array('timeout' => 10);
            $dataString = json_encode($param);
            //$http->setConfig($config);

            $headers = array(
                'x-api-key:rmxulnj77aAxji4KctmsfCVBPYiDwpIbOxx',
                'Content-Type:application/json',
                'Content-Length:' . strlen($dataString),
            );
            // disable ssl verification
            $http->addOption(CURLOPT_SSL_VERIFYHOST, false);
            $http->addOption(CURLOPT_SSL_VERIFYPEER, false);
            $http->write(Zend_Http_Client::POST, $url, '1.1', $headers, $dataString);

            $res = $http->read();
            // $this->log("ship response" . json_encode($res));
            $code = Zend_Http_Response::extractCode($res);
            $response = '';
            if ($code == 200) //if success
            {
                $tmp = preg_split('/^\r?$/m', $res, 2);
                $response = trim($tmp[1]);
            }
            $http->close();
            return $response;
        } catch (Exception $e) {
            $this->log($e->getCode() . ' ' . $e->getMessage());
        }
    }

    /**
     * Custom log function since the log function in Magento is not working
     */
    protected function log($message)
    {
        // Specify the log file path (you can change this to your desired location and file name).
        $logFile = '/var/www/html/var/log/mgwlog.log';

        // Create or open the log file in append mode.
        $fileHandle = fopen($logFile, 'a');

        if ($fileHandle) {
            // Get the current timestamp in your desired format (you can change the date format as needed).
            $timestamp = date('Y-m-d H:i:s');

            // Format the log message with the timestamp.
            $logMessage = "[$timestamp] $message" . PHP_EOL;

            // Write the log message to the file.
            fwrite($fileHandle, $logMessage);

            // Close the file handle.
            fclose($fileHandle);
        } else {
            // Failed to open the log file. You might want to handle this error in a specific way.
            // For example, you can trigger an error log or use another method to notify you about the issue.
            // For simplicity, we'll just print an error message here.
            echo "Error: Unable to open the log file.";
        }
    }
}
