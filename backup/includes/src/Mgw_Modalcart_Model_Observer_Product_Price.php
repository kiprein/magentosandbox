<?php 
/**
 * Price Observer.
 */
class Mgw_Modalcart_Model_Observer_Product_Price {
    /**
     * Listens to the catalog_product_get_final_price event
    * and adjusts the price of products according to the crystal D column breaks.
     */
    public function getColumnPrice($observer){
        $customerSession = Mage::getSingleton('customer/session');
        $customerId = $customerSession->getCustomer()->getId();
        $taxVat = Mage::getSingleton('customer/customer')->load($customerId)->getData('taxvat');
        
        $product = $observer->getEvent()->getProduct();

        // $qty = $observer->getEvent()->getQty();
        // Mage::log("Qty selected {$qty}", null, 'mgwlog.log', true );
        //Bug in magento, have to load the product again.
        $productReload =  Mage::getSingleton('catalog/product')->load($product->getId());
        if($productReload->getbTrophyOk() == 1){
            $col3_price = $productReload->getTrophyblankprice3();
            $col2_price = $productReload->getTrophyblankprice2();
            $col1_price = $productReload->getTrophyblankprice1();
        } else {
            $col3_price = $productReload->getNetprice3();
            $col2_price = $productReload->getNetprice2();
            $col1_price = $productReload->getNetprice1();
        }

        // https://crystaldit.mydonedone.com/issuetracker/projects/70721/issues/129
        // If a product is in the cart twice we need to add it to the qty discount
        // For backend change search for "catalog_product_get_final_price"
        $qty = 0;
        $data = [];
        $quote = Mage::helper('checkout/cart')->getQuote();
        foreach($quote->getAllItems() as $p){
            if($product->getId() === $p->getProductId()){
                $qty += $p->getQty();
            }
            $data[] = [
                'product_id' => $product->getId(),
                'p_id' => $p->getProductId(),
                'p_qty' => $p->getQty()
            ];
        }
        // echo json_encode($data); die();

        // Mage::log("Qty selected \$productQty {$productQty}", null, 'mgwlog.log', true );
        //Mage::log("setting final price on  {$id}", null, 'mgwlog.log', true );
        if( $qty >= $productReload->getProductquantity3() || $taxVat == 1 || $taxVat == 2 || $taxVat == 3 ){
            $price = $col3_price;
            // Mage::log("Setting column 3  {$price}", null, 'mgwlog.log', true );
            if($taxVat == 2){
                $price = $price - ( $price * 0.15); //15% discount (EPP)
                // Mage::log("Addint EQP  {$price}", null, 'mgwlog.log', true );
            }
            if($taxVat == 3){
                $price = $price - ( $price * 0.10); //10% discount (EPP)
                // Mage::log("Adding EPP  {$price}", null, 'mgwlog.log', true );
            }
        } else if ( $qty >= $productReload->getProductquantity2()){
            $price = $col2_price;
            // Mage::log("Setting column 2  {$price}", null, 'mgwlog.log', true );
        } else {
            $price = $col1_price;
            // Mage::log("Setting column 1  {$price}", null, 'mgwlog.log', true );
        }
        /*
        if( $productReload->getCountry() == 'China'){
            $price = $price + ( $price * 0.03 ); //Tariff surcharge.
            // Mage::log("Post tariff addition {$price}", null, 'mgwlog.log', true );
        }*/
        //Mage::log("product {$id} set price {$price}", null, 'mgwlog.log', true);

        // Apply tariff surcharge if product originates from China
        // $countryOfOrigin = $productReload->getCountry();
        // if (!empty($countryOfOrigin) && strtolower($countryOfOrigin) === 'china') {
        //     $tariffRate = 0.15; // 15% surcharge
        //     $price += $price * $tariffRate;
        //     Mage::log("Tariff surcharge applied for product {$product->getId()}: new price {$price}", null, 'mgwlog.log', true );
        // }


        $product->setFinalPrice($price);
        return $this;
    }
}
