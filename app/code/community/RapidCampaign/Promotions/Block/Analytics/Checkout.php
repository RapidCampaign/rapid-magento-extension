<?php
/**
 * RapidCampaign Checkout Analytics Block
 *
 * @category    RapidCampaign
 * @package     RapidCampaign_Promotions
 * @copyright   Copyright (c) 2015 RapidCampaign (http://rapidcampaign.com)
 */
class RapidCampaign_Promotions_Block_Analytics_Checkout extends Mage_Core_Block_Template
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

        if (!$configHelper->isFullAnalytics()) {
            /** @var Mage_Core_Model_Cookie $cookieModel */
            $cookieModel = Mage::getSingleton('core/cookie');

            if (!$cookieModel->get('rc_coupon_applied')) {
                return '';
            }

            $cookieModel->delete('rc_coupon_applied');
        }

        /** @var Mage_Checkout_Model_Session $checkoutModel */
        $checkoutModel = Mage::getSingleton('checkout/session');

        /** @var Mage_Sales_Model_Order $orderModel */
        $orderModel = Mage::getModel('sales/order');
        $orderModel->loadByIncrementId($checkoutModel->getLastRealOrderId());

        if ($orderModel->isEmpty()) {
            return '';
        }

        $pushArray = array();

        // Push currency code
        $pushArray[] = array(
            '_set',
            'currencyCode',
            (string)Mage::app()->getStore()->getCurrentCurrencyCode()
        );

        $transaction = array(
            '_addTrans',
            (string)$orderModel->getEntityId(),
            (string)$orderModel->getGrandTotal()
        );

        if ($orderModel->getCouponCode()) {
            $transaction[] = $orderModel->getCouponCode();
        }

        // Push Transaction
        $pushArray[] = $transaction;

        $orderItems = $orderModel->getAllVisibleItems();

        /** @var Mage_Sales_Model_Order_Item $orderItem */
        foreach ($orderItems as $orderItem) {
            // Push order items
            $pushArray[] = array(
                '_addTransItem',
                (string)$orderModel->getEntityId(),
                (string)$orderItem->getSku(),
                (string)$orderItem->getName(),
                (string)$orderItem->getPrice(),
                (string)intval($orderItem->getQtyOrdered())
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
_naq.push(['_trackTrans']);
//]]></script>
<!-- RapidCampaign Analytics Ends -->

SCRIPT;

        return $html;
    }
}
