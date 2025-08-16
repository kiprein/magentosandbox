<?php
/**
 * Controller class for Mark Wickline Modalcart module
 * 
 * @author Mark Wickline
 */
class Mgw_Modalcart_IndexController extends Mage_Core_Controller_Front_Action {  

    /**
     * Ajax method for adding a product to the cart with custom options.
     * 
     */
    public function addProductAction(){
        $id = (int)$this->getRequest()->getParam('id');
        $requestedQty = (int)$this->getRequest()->getParam('qty') ?: 1;
        $options = json_decode($this->getRequest()->getParam('options'));
        $index = (int)$this->getRequest()->getParam('index'); //Index of products position in cartProducts.

        $result = [];
        if ($id) {
            try {
                $product = Mage::getModel('catalog/product')->load($id);
                // Fetch ERP inventory and clamp
                $oldId = $product->getOldProductId();
                $erpInventory = Mage::helper('mgw_modalcart')->getLiveInventory($oldId);
                $available = isset($erpInventory[0]['qty']) ? (int)$erpInventory[0]['qty'] : 0;
                $qty = min($requestedQty, $available);

                // If there’s no inventory, don’t add—just return an out-of-stock error
                if ($available <= 0) {
                    $result = [
                        'success' => 0,
                        'error'   => $this->__('Sorry, this item is out of stock and cannot be added.')
                    ];
                    $this->getResponse()
                        ->setHeader('Content-Type','application/json')
                        ->setBody(Mage::helper('core')->jsonEncode($result));
                    return;
                }

                $result['avail'] = $available;

                $cart = Mage::getModel('checkout/cart');
                $quote = Mage::getSingleton('checkout/session')->getQuote();
                $quoteItem = $quote->addProduct($product, $qty);

                if (!empty($options)) {
                    $optionArray = [];
                    foreach ($options as $option) {
                        $optionArray[] = [
                            'label' => $option->label,
                            'value' => $option->value
                        ];
                    }
                    $quoteItem->addOption(new Varien_Object([
                        'product' => $quoteItem->getProduct(),
                        'code'    => 'additional_options',
                        'value'   => serialize($optionArray)
                    ]));
                }

                $quote->collectTotals()->save();
                $cart->save();

                $result['itemId'] = $quoteItem->getId();
                $result['index']  = $index;
                $result['success'] = 1;
                $result['message'] = $this->__('Item was added successfully.');

                if ($requestedQty > $available) {
                    Mage::getSingleton('checkout/session')->addNotice(
                        $this->__(
                            'Requested quantity (%s) for SKU %s exceeds available stock (%s). Adjusted to %s.',
                            $requestedQty,
                            $product->getSku(),
                            $available,
                            $available
                        )
                    );
                }
            } catch (Exception $e) {
                $result['success'] = 0;
                $result['error']   = $e->getMessage();
            }
        }

        $this->getResponse()
             ->setHeader('Content-type', 'application/json')
             ->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /**
     * Ajax method that will make a call to Webster API and return account credentials
     * for a given 3rd party shipping account number.
     */
    public function thirdPartyAccAction(){
        $accNumber = $this->getRequest()->getParam('accNum');
        $url = 'https://api.crystal-d.com/freight/3rdparty?acc=' . $accNumber;
        try {
            $http = new Varien_Http_Adapter_Curl();
            $config = array('timeout' => 10);
			$headers = array('x-api-key:rmxulnj77aAxji4KctmsfCVBPYiDwpIbOxx');
            $http->setConfig($config);
            $http->write(Zend_Http_Client::GET, $url, '1.1', $headers);
            $res = $http->read();

            $code = Zend_Http_Response::extractCode($res);
            $response = '';
            if ($code == 200) //if success
            {
                $tmp = preg_split('/^\r?$/m', $res, 2);
                $response = trim($tmp[1]);
            }
            $http->close();
            $this->getResponse()->setHeader('Content-type', 'application/json');
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
        } catch (Exception $e) {
            Mage::log($e->getCode() . ' ' . $e->getMessage(), null, 'mgwlog.log', true);
        }
    }

    /**
     * Ajax method that will take a | delimited list of s_item_nums
     * and load HTML for an option modal
     * 
     * @param string $list
     * 
     * [{
     *  list: 'sku|sku|sku',
     *  label: 'label'
     * }]
     */
    public function formatOptionalProductsAction(){
        $input = json_decode($this->getRequest()->getParam('list'));

        if(!isset($input)){
            $this->getResponse()->setHeader('Content-Type', 'text/html', true)->setBody('');
            return;
        }

        $htmlBuild = '';

        foreach($input as $category){
  
            $list = explode('|', $category->list);
            $title = $this->_getOptionTitle($category->label);
            /**
             * Column break for bootstrap. If there are more than 12 options this will break.
             */
            $col = round( 12 / count($list) );
            $html = "
                <style>
                    div{ vertical-align: top;}
                </style>
                <div style='text-align: left; margin-bottom: 19px;'>
                    <div style='display: inline-flex; flex-direction: column; justify-content: center; height: 35px;'>Quantity:</div>
                    <div style='display: inline-block;'>
                        <button class='mwCart-dec-btn' 
                            onclick='mwCart_optionModal_changeQty(\"mwCart_optionModal_mainQty\", mwCart_tempProduct.avail, \"-\")'>-</button>
                        <input class='mwCart-qty-input' 
                            id='mwCart_optionModal_mainQty' 
                            type='text' name='mwCart_optionModal_mainQty' 
                            onchange='mwCart_optionModal_changeQty(\"mwCart_optionModal_mainQty\", mwCart_tempProduct.avail)'
                            value=0>
                        <button class='mwCart-inc-btn' 
                            onclick='mwCart_optionModal_changeQty(\"mwCart_optionModal_mainQty\", mwCart_tempProduct.avail, \"+\")'>+</button>
                    </div>
                </div>
                <div id='mwCart_optionSelectWrapper' style='display: none;'>
                <h4 style='text-align: center;' class='text-gray'>{$title}</h4>
                <div
                    class='container'
                    style='display: flex; flex-wrap: wrap; justify-content: space-between;'
                    id='mwCart_optionSelect_{$category->label}'>
            ";
            foreach($list as $index => $sku){

                $product = Mage::getSingleton('catalog/product')->loadByAttribute('sku', $sku);
                $stock = Mage::getSingleton('cataloginventory/stock_item')->loadByProduct($product);

                $metaTitle = $product->getMetaTitle();

                $productInfo = new stdClass();
                $productInfo->productId = $product->getId();
                $productInfo->descrip = $sku . " " . $product->getMetaTitle();
                $productInfo->tr_price1 = (float)$product->getTrophyblankprice1();
                $productInfo->tr_price2 = (float)$product->getTrophyblankprice2();
                $productInfo->tr_price3 = (float)$product->getTrophyblankprice3();
                $productInfo->tr_qty2 = (float)$product->getProductquantity2();
                $productInfo->tr_qty3 = (float)$product->getProductquantity2();
                //$productInfo->needs_assembly = (int)$product->getAssemblyRequired();
                //$productInfo->needs_trophy_assembly = (int)$product->getTrophyAssemblyRequired();
                $productInfo->img_url = $product->getMainProductImage();
                $productInfo->url = $product->getUrlKey();
                $productInfo->avail = (int)$stock->getQty();
                $productInfo->sku = $sku;
                //$productInfo->category = $category->label; //For selecting the proper display with JS

                //build a JSON string to pass as value in option tag
                $optionValue = htmlspecialchars(json_encode($productInfo), ENT_COMPAT);

                $checked = $index == 0 ? 'checked' : '';

                //Prepopulated display with first option
                $optionHtml = "
                    <!--<div class='col-sm-{$col} text-center'>-->
                    <div class='text-center' style='padding: 0 4px; display: inline-block;'>
                        <p class='mwCart_optionModal_productTitle'
                            id='mwCart_optionModal_productTitle_{$category->label}'>
                            {$productInfo->title}
                        </p>
                        <a class='mwCart_optionModal_productLink'
                            id='mwCart_optionModal_productLink_{$category->label}' href='{$productInfo->url}'>
                            <img src='https://image.crystal-d.com/img/u494-y/jpg/{$productInfo->url}'
                                id='mwCart_optionModal_productImage_{$category->label}'
                                class='mwCart_optionModal_productImage' alt='{$metaTitle}'
                                style='max-height: 100px;'>
                        </a>
                        <p class='mwCart_optionModal_productQty'>
                            <span id='mwCart_optionModal_productQty_{$category->label}'>{$productInfo->avail}</span> Available
                        </p>
                        <div style='display: inline-block;'>";

                if($category->label == 'goal_setter'){
                    $optionHtml .= "
                        <button class='mwCart-dec-btn' 
                            onclick='mwCart_optionModal_changeQty(\"mwCart_optionModal_option_{$sku}\", {$productInfo->avail}, \"-\")'>-</button>
                        <input class='mwCart_optionModal_option mwCart-qty-input' 
                            id='mwCart_optionModal_option_{$sku}' 
                            type='text' name='mwCart_optionModal_option_{$sku}' 
                            onchange='mwCart_optionModal_changeQty(\"mwCart_optionModal_option_{$sku}\", {$productInfo->avail})'
                            value=0>
                        <button class='mwCart-inc-btn' 
                            onclick='mwCart_optionModal_changeQty(\"mwCart_optionModal_option_{$sku}\", {$productInfo->avail}, \"+\")'>+</button>
                    ";
                } else {
                    $optionHtml .= "
                        <input 
                            id='mwCart_optionModal_option_{$sku}' 
                            type='radio' name='mwCart_optionModal_option'>
                    ";
                }
                $optionHtml .= "       
                            </div>
                        <input type='hidden' id='mwCart_optionModal_option_{$sku}_data' value='{$optionValue}'>
                    </div>
                ";
                $html .= $optionHtml;
            }
            $html .= "
                            </div>
                        <a class='btn checkoutStep-btn' style='margin: 6px 0;' onclick='mwCart_addProduct()'>Add Product</a>
                    </div>";
            $htmlBuild .= $html;
        }
        
        $this->getResponse()->setHeader('Content-Type', 'text/html', true)->setBody($htmlBuild);
    }

    /**
     * Take two parameters to determine if product requires assembly.
     * 3 options available.
     *  - Assembly required by TR
     *  - Assembly by us or TR
     *  - No assembly required
     * 
     * NOT USING, HANDLED BY JAVASCRIPT INSTEAD
     */
    public function formatAssemblyOptionsAction(){
        //This product requires assembly to be a completed product
        $needsAssembly = $this->getRequest()->getParam('needs_assembly');
        //This product needs to be assembled by the Trophy Retailer, no exceptions
        $needsTrophyAssembly = $this->getRequest()->getParam('needs_trophy_assembly');
        $html = '';
        if(!$needsTrophyAssembly && $needsAssembly){
            $html .= "
                <hr>
                <h2>Select Assembly Option</h2>
                <input id='mwCart_assembled' name='mwCart_assemblyOption' type='radio' checked/>
                <label for='mwCart_assembled'>Pre Assembled</label>
                <input id='mwCart_notAssembled' name='mwCart_assemblyOption' type='radio'/>
                <label for='mwCart_notAssembled'>Not Assembled</label>
            ";
        }
    }

    /**
     * Proccess the form on the order review page.
     * 
     * crystal-d.com/checkout/cart was subsequently turned into a sort
     * of review page. A form was added to handle additional notes for the
     * checkout proccess. This action was added to handle that form and
     * then redirect to the onepage checkout.
     */
    public function saveOrderReviewAction(){
        $notes = $this->getRequest()->getParam('notes');
        Mage::getModel('mgw_modalcart/checkout_onepage')->setAdditionalNotes($notes);
        $this->_redirect("checkout/onepage");
    }
	

    /**
     * Private helper for setting labels on option select tags
     * 
     * @param string $label
     */
    private function _getOptionTitle($label){
        switch($label){
            case 'inspiration':
                return 'Your award includes one inspiration card. Please select your card.';
            case 'battery':
                return 'Select Included Battery Pack';
            case 'plates':
                return 'Your plaque includes one plate. Please select your plate style.';
            case 'accent':
                return 'Your award includes one accent. Please select your accent style.';
            case 'goal_setter':
                return 'Each award includes one block in the price of the award. Please select your block color(s) below. If you choose more blocks than the number of awards you have selected, additional blocks will be added to your cart.';
            case 'sphere':
                return 'Your award includes one sphere. Please select your color.';
		case 'panels':
			return 'Your award includes one panel. Please select a color.';
            default:
                return '';
        }
    }
}
