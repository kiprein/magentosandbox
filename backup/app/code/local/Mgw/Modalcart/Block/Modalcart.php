<?php
class Mgw_Modalcart_Block_Modalcart extends Mage_Checkout_Block_Cart_Sidebar
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getCartProducts()
    {
        Mage::log("Modalcart::getCartProducts() fired", null, "modalcart-debug.log");

        $cartProducts      = [];
        $cartProductsQuote = $this->getItems();

        foreach ($cartProductsQuote as $productQuote) {
            $productBuild = new stdClass();
            $product      = Mage::getSingleton('catalog/product')
                                 ->load($productQuote->getProductId());

            $productBuild->descrip = '#' . $product->getSku() . ' ' . $product->getMetaTitle();
            $productBuild->url     = $this->getUrl($product->getUrlKey());
            $productBuild->img_url = $product->getMainProductImage();

            // Tier pricing
            if ($product->getBTrophyOk() == 1) {
                $productBuild->tr_price1 = $product->getTrophyblankprice1();
                $productBuild->tr_price2 = $product->getTrophyblankprice2();
                $productBuild->tr_price3 = $product->getTrophyblankprice3();
            } else {
                $productBuild->tr_price1 = $product->getNetprice1();
                $productBuild->tr_price2 = $product->getNetprice2();
                $productBuild->tr_price3 = $product->getNetprice3();
            }
            $productBuild->tr_qty2 = $product->getProductquantity2();
            $productBuild->tr_qty3 = $product->getProductquantity3();

            // Base quote data
            $productBuild->qty       = (int)$productQuote->getQty();
            $productBuild->itemId    = $productQuote->getItemId();
            $productBuild->productId = $productQuote->getProductId();
            $productBuild->country   = $product->getCountry();

            // Collect all availabilities (main + options)
            $availabilities = [];

            // Main product ERP availability
            $mainEpr = Mage::helper('mgw_modalcart')
                            ->getLiveInventory($product->getOldProductId());
            $mainAvail = isset($mainEpr[0]['qty']) ? (int)$mainEpr[0]['qty'] : 0;
            $availabilities[] = $mainAvail;

            // Custom options
            $options = $this->getProductOptions($productQuote);
            if (!empty($options)) {
                foreach ($options as $option) {
                    $optProduct = Mage::getSingleton('catalog/product')
                                       ->loadByAttribute('sku', $option['value']);
                    $opObject = new stdClass();
                    $opObject->label = $option['label'];
                    $opObject->value = $option['value'];
                    $opObject->url   = $optProduct ? $optProduct->getUrlKey() : '';
                    $productBuild->selectedOptions[] = $opObject;

                    // Only clamp for non-assembly options
                    if ($optProduct && $option['label'] !== 'assembly') {
                        $optEpr = Mage::helper('mgw_modalcart')
                                      ->getLiveInventory($optProduct->getOldProductId());
                        $optAvail = isset($optEpr[0]['qty']) ? (int)$optEpr[0]['qty'] : 0;
                        $availabilities[] = $optAvail;
                    }
                }
            }

            // Final availability is minimum of all collected
            $productBuild->avail = count($availabilities)
                ? min($availabilities)
                : 0;

            $cartProducts[] = $productBuild;
        }

        return $cartProducts;
    }

    public function getAjaxUpdateUrl()
    {
        return $this->getUrl(
            'checkout/cart/ajaxUpdate',
            [
                Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED =>
                    $this->helper('core/url')->getEncodedUrl(),
                '_secure' => $this->_getApp()->getStore()->isCurrentlySecure(),
            ]
        );
    }

    public function getAjaxAddUrl()
    {
        return $this->getUrl('mgw/index/addProduct');
    }

    public function getAjaxDeleteUrl()
    {
        return $this->getUrl(
            'checkout/cart/ajaxDelete',
            [
                'id' => $this->getItem()->getId(),
                Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED =>
                    $this->helper('core/url')->getEncodedUrl(),
                '_secure' => $this->_getApp()->getStore()->isCurrentlySecure(),
            ]
        );
    }

    public function getAjaxOptionUrl()
    {
        return $this->getUrl('mgw/index/formatOptionalProducts');
    }

    public function getProductOptions($item)
    {
        /* @var $helper Mage_Catalog_Helper_Product_Configuration */
        return Mage::helper('catalog/product_configuration')->getCustomOptions($item);
    }
}
