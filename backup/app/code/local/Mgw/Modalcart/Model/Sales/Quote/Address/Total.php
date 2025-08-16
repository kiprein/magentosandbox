<?php
/**
 * @author Mark Wickline
 * This class will listen to the subtotal on quotes and hopefully break down the price
 * according to Crystal D rules.
 * 
 * @resources
 * 
 */
class Mgw_Modalcart_Model_Sales_Quote_Address_Total extends Mage_Sales_Model_Quote_Address_Total_Abstract{
	protected $_code = 'fee';

	public function collect(Mage_Sales_Model_Quote_Address $address)
	{
		parent::collect($address);
		$this->_setAmount(0);
		$this->_setBaseAmount(0);

		$items = $this->_getAddressItems($address);
		if (!count($items)) {
			return $this; //this makes only address type shipping to come through
        }
        
        $quote = $address->getQuote();

        //Hack, need to get final price on quote items before the quote totals are calculated.
        $discountAmount = 20;
        foreach($quote->getAllItems() as $quoteItem){
            Mage::dispatchEvent('catalog_product_get_final_price', array('product' => $quoteItem, 'qty' => $quoteItem->getQty()));
            //TODO this is where discounts and tarrifs would be calculated
            $price = (string)$quoteItem->getData('final_price');
            Mage::log("Item Price {$price}", null, 'mgwlog.log', true );
        }
        
        // foreach($items as $item){
        //     $itemID = $item->getProductId();
        //     Mage::log("Item id {$itemID}", null, 'mgwlog.log', true );
        // }
        Mage::log("address subtotal " . $address->getSubtotal(), null, 'mgwlog.log', true );
        Mage::log("address base subtotal " . $address->getBaseSubtotal(), null, 'mgwlog.log', true );
        Mage::log("address grand total " . $address->getGrandTotal(), null, 'mgwlog.log', true );
        Mage::log("address base grand total " . $address->getBaseGrandTotal(), null, 'mgwlog.log', true );
        
        $quote->setGrandTotal($quote->getBaseSubtotal() - $discountAmount)
            ->setBaseGrandTotal($quote->getBaseSubtotal() - $discountAmount)
            ->setSubtotalWithDiscount($quote->getBaseSubtotal() - $discountAmount)
            ->setBaseSubtotalWithDiscount($quote->getBaseSubtotal() - $discountAmount)
            ->save();
        

        $address->setSubtotalWithDiscount((float) $address->getSubtotalWithDiscount() - $discountAmount);
        $address->setGrandTotal((float) $address->getGrandTotal() - $discountAmount);
        $address->setBaseSubtotalWithDiscount((float) $address->getBaseSubtotalWithDiscount() - $discountAmount);
        $address->setBaseGrandTotal((float) $address->getBaseGrandTotal() - $discountAmount);
        if ($address->getDiscountDescription()) {
            $address->setDiscountAmount(-($address->getDiscountAmount() - $discountAmount));
            $address->setDiscountDescription($address->getDiscountDescription() . ', Custom Discount');
            $address->setBaseDiscountAmount(-($address->getBaseDiscountAmount() - $discountAmount));
        } else {
            $address->setDiscountAmount(-($discountAmount));
            $address->setDiscountDescription('Custom Discount');
            $address->setBaseDiscountAmount(-($discountAmount));
        }


		// if(Excellence_Fee_Model_Fee::canApply($address)){
		// 	$exist_amount = $quote->getFeeAmount();
		// 	$fee = Excellence_Fee_Model_Fee::getFee();
		// 	$balance = $fee - $exist_amount;
		// 	// 			$balance = $fee;

		// 	//$this->_setAmount($balance);
		// 	//$this->_setBaseAmount($balance);

		// 	$address->setFeeAmount($balance);
		// 	$address->setBaseFeeAmount($balance);
				
		// 	$quote->setFeeAmount($balance);

		// 	$address->setGrandTotal($address->getGrandTotal() + $address->getFeeAmount());
		// 	$address->setBaseGrandTotal($address->getBaseGrandTotal() + $address->getBaseFeeAmount());
        // }


        //checking
        Mage::log("address subtotal " . $address->getSubtotal(), null, 'mgwlog.log', true );
        Mage::log("address base subtotal " . $address->getBaseSubtotal(), null, 'mgwlog.log', true );
        Mage::log("address grand total " . $address->getGrandTotal(), null, 'mgwlog.log', true );
        Mage::log("address base grand total " . $address->getBaseGrandTotal(), null, 'mgwlog.log', true );

        //save address??
        $address->save();

	}

	// public function fetch(Mage_Sales_Model_Quote_Address $address)
	// {
	// 	$amt = $address->getFeeAmount();
	// 	$address->addTotal(array(
	// 			'code'=>$this->getCode(),
	// 			'title'=>Mage::helper('fee')->__('Fee'),
	// 			'value'=> $amt
	// 	));
	// 	return $this;
	// }
}