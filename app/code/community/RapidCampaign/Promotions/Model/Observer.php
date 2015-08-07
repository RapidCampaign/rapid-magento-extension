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
        $moduleEnabled = Mage::getStoreConfig('rapidcampaign_general/rapidcampaign_general_group/enable');

        // Module disabled
        if (!$moduleEnabled) {
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
}
