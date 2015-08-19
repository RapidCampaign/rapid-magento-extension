<?php
/**
 * RapidCampaign Base Analytics Block
 *
 * @category    RapidCampaign
 * @package     RapidCampaign_Promotions
 * @copyright   Copyright (c) 2015 RapidCampaign (http://rapidcampaign.com)
 */
class RapidCampaign_Promotions_Block_Analytics_Cart extends Mage_Core_Block_Template
{
    /**
     * Prepare HTML content
     *
     * @return string
     */
    protected function _toHtml()
    {
        /** @var RapidCampaign_Promotions_Helper_Config $configHelper */
        $configHelper = Mage::helper('rapidcampaign_promotions/config');

        // Module disabled or Merchant ID is not set
        if (!$configHelper->extensionEnabled() || !$configHelper->getMerchantId()) {
            return '';
        }

        /** @var Mage_Checkout_Model_Cart $cartModel */
        $cartModel = Mage::getSingleton('checkout/cart');

        $quote = $cartModel->getQuote()->collectTotals();

        // Cart is empty
        if (!$quote->getItemsCount()) {
            return '';
        }

        $pushArray = array();

        // Push currency code
        $pushArray[] = array(
            '_set',
            'currencyCode',
            (string)Mage::app()->getStore()->getCurrentCurrencyCode()
        );

        // Push basket
        $pushArray[] = array(
            '_addBasket',
            (string)$quote->getEntityId(),
            (string)$quote->getGrandTotal()
        );

        $cartItems = $quote->getAllItems();

        /** @var Mage_Sales_Model_Quote_Item $cartItem */
        foreach ($cartItems as $cartItem) {
            // Push cart items
            $pushArray[] = array(
                '_addBasketItem',
                (string)$quote->getEntityId(),
                (string)$cartItem->getSku(),
                (string)$cartItem->getName(),
                (string)$cartItem->getPrice(),
                (string)$cartItem->getQty()
            );
        }

        // Encode array to json and wrap with _naq.push();
        $pushArrayWrapped = array_map(function ($push) {
            return '_naq.push(' . Mage::helper('core')->jsonEncode($push) . ');';
        }, $pushArray);

        // Join elements with newline
        $pushHtml = join("\n", $pushArrayWrapped);

        $html = <<<SCRIPT
<!-- RapidCampaign Analytics Begins -->
<script type="text/javascript">
//<![CDATA[
var _naq = _naq || [];
{$pushHtml}
_naq.push(["_trackBasket"]);
//]]></script>
<!-- RapidCampaign Analytics Ends -->
SCRIPT;

        return $html;
    }
}
