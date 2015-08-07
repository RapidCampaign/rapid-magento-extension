<?php
/**
 * RapidCampaign
 *
 * @category    RapidCampaign
 * @package     RapidCampaign_Promotions
 * @copyright   Copyright (c) 2015 RapidCampaign (http://rapidcampaign.com)
 */
class RapidCampaign_Promotions_CouponsController extends Mage_Core_Controller_Front_Action
{
    /**
     * Coupon application action
     */
    public function applyAction()
    {
        $moduleEnabled = Mage::getStoreConfig('rapidcampaign_general/rapidcampaign_general_group/enable');

        // Module disabled
        if (!$moduleEnabled) {
            return $this->_redirectReferer();
        }

        $coupon = Mage::app()->getRequest()->getParam('coupon');

        /** @var Mage_Checkout_Model_Session $sessionModel */
        $sessionModel = Mage::getSingleton('checkout/session');

        if (!$coupon) {
            return $this->_redirectReferer();
        }

        /** @var Mage_Checkout_Helper_Cart $cartHelper */
        $cartHelper = Mage::helper('checkout/cart');

        $countCartItems = $cartHelper->getItemsCount();

        // If cart not empty
        if ($countCartItems) {
            /** @var Mage_Checkout_Model_Cart $cartModel */
            $cartModel = Mage::getSingleton('checkout/cart');

            // Apply coupon to cart
            $cartModel->getQuote()
                ->setCouponCode($coupon)
                ->collectTotals()
                ->save();

            $sessionModel->addSuccess('The coupon has been successfully applied to your cart');

            return $this->_redirect('checkout/cart');
        }

        /** @var Mage_Core_Model_Cookie $cookieModel */
        $cookieModel = Mage::getSingleton('core/cookie');

        $cookieLifetime = Mage::getStoreConfig('rapidcampaign_general/rapidcampaign_general_group/cookie_lifetime');

        // Set coupon cookie
        $cookieModel->set('coupon_code', $coupon, (int)$cookieLifetime, '/');

        $sessionModel->addSuccess('The coupon code has been saved for your cart');

        return $this->_redirectReferer();
    }
}
