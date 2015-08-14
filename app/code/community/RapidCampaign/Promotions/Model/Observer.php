<?php
/**
 * RapidCampaign
 *
 * @category    RapidCampaign
 * @package     RapidCampaign_Promotions
 * @copyright   Copyright (c) 2015 RapidCampaign (http://rapidcampaign.com)
 */

class RapidCampaign_Promotions_Model_Observer
{
    /**
     * Hook addProduct to apply coupon code if cookie exist
     *
     * @param Varien_Event_Observer $observer
     */
    public function onCartProductAdded($observer)
    {
        /** @var RapidCampaign_Promotions_Helper_Config $configHelper */
        $configHelper = Mage::helper('rapidcampaign_promotions/config');

        // Module disabled
        if (!$configHelper->extensionEnabled()) {
            return;
        }

        /** @var Mage_Core_Model_Cookie $cookieModel */
        $cookieModel = Mage::getSingleton('core/cookie');

        $coupon = $cookieModel->get('coupon_code');

        if (!$coupon) {
            return;
        }

        /** @var Mage_Checkout_Model_Cart $cartModel */
        $cartModel = Mage::getSingleton('checkout/cart');

        // Apply coupon to cart
        $cartModel->getQuote()
            ->setCouponCode($coupon)
            ->collectTotals()
            ->save();

        // Remove cookie
        $cookieModel->delete('coupon_code');
    }

    /**
     * Hook build/save widget action and inject rule as parameters to post
     *
     * @param Varien_Event_Observer $observer
     */
    public function onControllerActionPredispatch($observer)
    {
        /** @var Mage_Core_Controller_Request_Http $request */
        $request    = $observer->getControllerAction()->getRequest();

        $action     = $request->getActionName();
        $controller = $request->getControllerName();

        if (($action == 'buildWidget' && $controller == 'widget') || ($action == 'save' && $controller == 'widget_instance')) {
            $rule = $request->getPost('rule', array());

            if (!empty($rule)) {
                $params = $request->getPost('parameters', array());

                // Serialize, encode and inject rule to post parameters
                $params['rule'] = base64_encode(serialize($rule));

                $request->setPost('parameters', $params);
            }
        }
    }

    /**
     * Hook block toHtml to add order variables to success page
     *
     * @param Varien_Event_Observer $observer
     * @return Varien_Event_Observer
     */
    public function onBlockToHtml($observer)
    {
        /** @var $block Mage_Core_Block_Abstract */
        $block = $observer->getBlock();

        if (!$block instanceof Mage_Checkout_Block_Onepage_Success) {
            return $observer;
        }

        if (get_class($block) !== 'Mage_Checkout_Block_Onepage_Success') {
            return $observer;
        }

        /** @var RapidCampaign_Promotions_Helper_Config $configHelper */
        $configHelper = Mage::helper('rapidcampaign_promotions/config');

        // Module disabled
        if (!$configHelper->extensionEnabled()) {
            return $observer;
        }

        /** @var Mage_Checkout_Model_Session $checkoutModel */
        $checkoutModel = Mage::getSingleton('checkout/session');

        /** @var Mage_Sales_Model_Order $orderModel */
        $orderModel = Mage::getModel('sales/order');
        $orderModel->loadByIncrementId($checkoutModel->getLastRealOrderId());

        if ($orderModel->isEmpty()) {
            return $observer;
        }

        $transport = $observer->getTransport();
        $html      = $transport->getHtml();

        $orderNumber   = Mage::helper('core')->jsonEncode($orderModel->getIncrementId());
        $orderValue    = Mage::helper('core')->jsonEncode($orderModel->getSubtotal());
        $couponCode    = Mage::helper('core')->jsonEncode($orderModel->getCouponCode());
        $customerId    = Mage::helper('core')->jsonEncode($orderModel->getCustomerId());
        $customerEmail = Mage::helper('core')->jsonEncode($orderModel->getCustomerEmail());

        $analyticsScript = $configHelper->getExternalAnalyticsScriptPath();

        // Add order variables to page
        $html .= <<<SCRIPT
<script type="text/javascript">
//<![CDATA[
    var order_number = {$orderNumber},
        order_value = {$orderValue},
        coupon_code = {$couponCode},
        customer_id = {$customerId},
        customer_email = {$customerEmail};
//]]></script>
<script src="{$analyticsScript}"></script>
SCRIPT;

        $transport->setHtml($html);
        $observer->setTransport($transport);

        return $observer;
    }

    /**
     * Cron job for promotions update
     */
    public function cron()
    {
        /** @var RapidCampaign_Promotions_Model_Storage $promotionsStorage */
        $promotionsStorage = Mage::getModel('rapidcampaign_promotions/storage');

        $promotionsStorage->updateCache();
    }
}
