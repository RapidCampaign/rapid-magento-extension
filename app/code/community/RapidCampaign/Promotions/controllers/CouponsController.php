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
        /** @var RapidCampaign_Promotions_Helper_Config $configHelper */
        $configHelper = Mage::helper('rapidcampaign_promotions/config');

        // Module disabled
        if (!$configHelper->extensionEnabled()) {
            return $this->_redirectReferer();
        }

        $couponCode = (string) $this->getRequest()->getParam('coupon');

        $maxCouponLength = 255;

        // Magento 1.7 compatibility fix
        if (defined('Mage_Checkout_Helper_Cart::COUPON_CODE_MAX_LENGTH')) {
            $maxCouponLength = Mage_Checkout_Helper_Cart::COUPON_CODE_MAX_LENGTH;
        }

        if (!strlen($couponCode) || strlen($couponCode) > $maxCouponLength) {
            return $this->_redirectReferer();
        }

        /** @var Mage_Checkout_Model_Session $sessionModel */
        $sessionModel = Mage::getSingleton('checkout/session');

        /** @var Mage_Checkout_Model_Cart $cartModel */
        $cartModel = Mage::getSingleton('checkout/cart');

        // If cart is empty
        if (!$cartModel->getItemsCount()) {
            /** @var Mage_Core_Model_Cookie $cookieModel */
            $cookieModel = Mage::getSingleton('core/cookie');

            // Set coupon cookie
            $cookieModel->set('coupon_code', $couponCode, (int)$configHelper->getCookieLifetime(), '/');

            $sessionModel->addSuccess(
                $this->__('Coupon code "%s" was applied.', Mage::helper('core')->escapeHtml($couponCode))
            );

            return $this->_redirectReferer();
        }

        try {
            $cartModel->getQuote()->getShippingAddress()->setCollectShippingRates(true);
            $cartModel->getQuote()->setCouponCode($couponCode)
                ->collectTotals()
                ->save();

            if ($couponCode == $cartModel->getQuote()->getCouponCode()) {
                $sessionModel->addSuccess(
                    $this->__('Coupon code "%s" was applied.', Mage::helper('core')->escapeHtml($couponCode))
                );
            } else {
                $sessionModel->addError(
                    $this->__('Coupon code "%s" is not valid.', Mage::helper('core')->escapeHtml($couponCode))
                );
            }

            return $this->_redirect('checkout/cart');
        } catch (Mage_Core_Exception $e) {
            $sessionModel->addError($e->getMessage());
        } catch (Exception $e) {
            $sessionModel->addError($this->__('Cannot apply the coupon code.'));
            Mage::logException($e);
        }

        return $this->_redirectReferer();
    }
}
