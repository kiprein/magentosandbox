<?php 
/**
 * A general helper class for the new cart and checkout processes.
 * 
 * - Most of what is involved here are methods that could be used in serveral
 * places to reduce code redudancy. 
 * - Mainly called from templates.
 */
class Mgw_Modalcart_Helper_Data extends Mage_Core_Helper_Abstract{

    const XML_EXPRESS_MAX_WEIGHT = 'carriers/mgw_modalcart/express_max_weight';

    private $optionProductKeys = ['inspiration', 'battery', 'plates', 'accent', 'goal_setter', 'sphere', 'panels'];
    private $optionOtherKeys = ['assembly'];

    /**
     * Get max weight of single item for express shipping
     *
     * @return mixed
     */
    public function getExpressMaxWeight()
    {
        return Mage::getStoreConfig(self::XML_EXPRESS_MAX_WEIGHT);
    }

    /**
     * For the formal name for the custom option types
     * 
     * @param string $key
     * @return string
     */
    public function getOptionFormal($key){
        switch($key){
            case 'goal_setter':
                return "Goal Setter Option";
            case 'battery':
                return 'Battery Option';
            case 'plates':
                return 'Plate Option';
            case 'accent':
                return 'Accent Option';
            case 'sphere':
                return 'Sphere Option';
            case 'inspiration':
                return 'Inspiration Option';
            case 'panels':
                return 'Panel Option';
            default:
                return 'Option';
        }
    }

    /**
     * Get the html for a given product option
     * 
     * @param array $option
     * @return string
     */
    public function getOptionHtml($option){
        $label = $option['label'];
        $value = $option['value'];
        if(in_array($label, $this->optionOtherKeys)){
            if($value) return "<div>Pre-assembled</div>";
            return "<div>Unassembled</div>";
        }
        if(in_array($label, $this->optionProductKeys)){
            $product = Mage::getSingleton('catalog/product')->loadByAttribute('sku', $value);
            $image = $product->getMainProductImage();
            $alt = $product->getImageAlt();
            $urlKey = $product->getUrlKey();
            return "
                <div>
                    <a href='/{$urlKey}'>
                        <img class='cartReview-productDetails-optionImage'
                            src='https://image.crystal-d.com/img/u494-y/jpg/{$image}' alt='{$alt}'>
                        <span>#{$value}</span>
                        </a>
                </div>
            ";
        }
    }

    /**
     * Returns an array like
     * [
     *  'price' => 12.00 //price before any discount is calculated
     *  'discount' => -2.00 //Amount saved on EQP for the one product.
     * ]
     * This calculation does not include a % discount. This is for EQP
     * and EPP product pricing only.
     * 
     * This is legacy method... Not being used anymore. See mwCartRebuild::mwCart_calcAndDisplayTotals() 
     * a javascript function that handles all the displayed totals.
     */
    public function getProductPriceAndDiscount($product, $qty, $taxVat = null){

        if(!isset($taxVat)){
            $customerSession = Mage::getSingleton('customer/session');
            $customerId = $customerSession->getCustomer()->getId();
            $taxVat = Mage::getSingleton('customer/customer')->load($customerId)->getData('taxvat');
        }
        
        $return = [
            'price' => 0.0,
            'discount' => 0.0
        ];

        $price1 = $product->getTrophyblankprice1();
        $price2 = $product->getTrophyblankprice2();
        $price3 = $product->getTrophyblankprice3();

        if( $qty >= $product->getProductquantity3()){
            $return['price'] = $price3;
        } else if ( $qty >= $product->getProductquantity2()){
            $return['price'] = $price2;
        } else {
            $return['price'] = $price1;
        }
        //override
        if( $taxVat == 1 || $taxVat == 2 ){
            $return['discount'] = $price3 - $return['price'];
            $return['price'] = $price3;
        }
        
        return $return;
    }

    /**
     * Fetch live inventory from ERP public API by old_product_id
     *
     * @param int|string $productId
     * @return array
     */
    public function getLiveInventory($productId)
    {
        $token = 'h2kznVAK7ybA4FHoPDZBblaJDQ6jb8';
        $url = "https://genesis.crystal-d.com/api/product/{$productId}/public-inventory";

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                "X-API-KEY: {$token}",
                "Accept: application/json"
            ],
            CURLOPT_TIMEOUT => 10
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($result)) {
            return [];
        }

        return isset($result['data']) && is_array($result['data']) ? $result['data'] : $result;
    }

}
