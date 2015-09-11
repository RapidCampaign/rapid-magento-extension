<?php
/**
 * RapidCampaign
 *
 * @category    RapidCampaign
 * @package     RapidCampaign_Promotions
 * @copyright   Copyright (c) 2015 RapidCampaign (http://rapidcampaign.com)
 */

class RapidCampaign_Promotions_Model_Observer extends Mage_Core_Model_Abstract
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

        /** @var Mage_Checkout_Model_Session $session */
        $session = Mage::getSingleton('checkout/session');

        try {
            // Apply coupon to cart
            $cartModel->getQuote()
                ->setCouponCode($coupon)
                ->collectTotals()
                ->save();

            if (!$coupon != $cartModel->getQuote()->getCouponCode()) {
                $session->addError(
                    Mage::helper('core')->__('Coupon code "%s" is not valid.', Mage::helper('core')->escapeHtml($coupon))
                );
            }
        } catch (Mage_Core_Exception $e) {
            $session->addError($e->getMessage());
        } catch (Exception $e) {
            $session->addError(Mage::helper('core')->__('Cannot apply the coupon code.'));
            Mage::logException($e);
        }

        // Remove cookie
        $cookieModel->delete('coupon_code');

        if (!$configHelper->isFullAnalytics() && $coupon == $cartModel->getQuote()->getCouponCode()) {
            // Set session cookie for rapidcampaign assisted order
            $cookieModel->set('rc_coupon_applied', true, 0, '/');
        }
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
     * Hook SalesRule condition combine and add extra conditions
     *
     * @param Varien_Event_Observer $observer
     * @return Varien_Event_Observer
     */
    public function onConditionCombine($observer)
    {
        /** @var RapidCampaign_Promotions_Helper_Config $configHelper */
        $configHelper = Mage::helper('rapidcampaign_promotions/config');

        // Module disabled
        if (!$configHelper->extensionEnabled()) {
            return $observer;
        }

        if (!Mage::registry('rapidcampaign/widget/created')) {
            return $observer;
        }

        /** @var RapidCampaign_Promotions_Model_Condition_Extra $extraCondition */
        $extraCondition  = Mage::getModel('rapidcampaign_promotions/condition_extra');
        $extraAttributes = $extraCondition->loadAttributeOptions()->getAttributeOption();

        $attributes = array();
        foreach ($extraAttributes as $code => $label) {
            $attributes[] = array('value' => 'rapidcampaign_promotions/condition_extra|' . $code, 'label' => $label);
        }

        $additional = $observer->getAdditional();
        $conditions = (array) $additional->getConditions();

        $conditions = array_merge_recursive($conditions, $attributes);

        $additional->setConditions($conditions);
        $observer->setAdditional($additional);

        Mage::unregister('rapidcampaign/widget/created');

        return $observer;
    }
}
