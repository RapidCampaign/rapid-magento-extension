<?php
/**
 * RapidCampaign Promotion Widget Block
 *
 * @category    RapidCampaign
 * @package     RapidCampaign_Promotions
 * @copyright   Copyright (c) 2015 RapidCampaign (http://rapidcampaign.com)
 */
class RapidCampaign_Promotions_Block_Widget_Promotion extends Mage_Core_Block_Template implements Mage_Widget_Block_Interface
{
    // Default dimension
    const IFRAME_WIDTH = 300;
    const IFRAME_HEIGHT = 200;

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        /** @var RapidCampaign_Promotions_Helper_Config $configHelper */
        $configHelper = Mage::helper('rapidcampaign_promotions/config');

        // Module disabled
        if (!$configHelper->extensionEnabled()) {
            return '';
        }

        // The rules not valid
        if (!$this->validateRules()) {
            return '';
        }

        /** @var RapidCampaign_Promotions_Model_Cache $promotionsCache */
        $promotionsCache = Mage::getModel('rapidcampaign_promotions/cache');

        try {
            $promotionModel = $promotionsCache->getPromotionsModel();
        } catch (Exception $e) {
            return '';
        }

        $promotion = $promotionModel->load($this->getData('promotion'));

        // Promotion does not exist
        if ($promotion->isEmpty()) {
            return '';
        }

        $promotionData = $promotion->getData();

        /** @var Mage_Customer_Model_Session $sessionModel */
        $sessionModel = Mage::getSingleton('customer/session');

        $urlParams = array(
            'promo_id'       => $promotionData['slug'],
            'customer_group' => $sessionModel->getCustomerGroupId(),
            'cart_value'     => $this->getCartTotal()
        );

        if ($sessionModel->isLoggedIn()) {
            $customer = $sessionModel->getCustomer();

            if ($customer->getFirstname()) {
                $urlParams['first_name'] = $customer->getFirstname();
            }

            if ($customer->getLastname()) {
                $urlParams['last_name'] = $customer->getLastname();
            }
        }

        // Parameter encryption enabled
        if ($configHelper->encryptionEnabled()) {
            $urlParams = $this->encryptParameters($urlParams);
        }

        $iframeUrl    = $promotionData['embed_url'] . '?' . http_build_query($urlParams);
        $iframeWidth  = $promotionData['width'] ? : self::IFRAME_WIDTH;
        $iframeHeight = $promotionData['height'] ? : self::IFRAME_HEIGHT;

        return '<iframe width="' . $iframeWidth . '" height="' . $iframeHeight . '" src="' . $iframeUrl . '"></iframe>';
    }

    /**
     * Validate widget rules
     *
     * @return bool
     */
    protected function validateRules()
    {
        /** @var Mage_SalesRUle_Model_Rule $salesRuleModel */
        $salesRuleModel = Mage::getModel('salesrule/rule');

        $rulesData = $this->getData('rule');

        if (!$rulesData) {
            return true;
        }

        $rules = unserialize(base64_decode($rulesData));
        $salesRuleModel->loadPost($rules);

        /** @var Mage_Checkout_Model_Cart $cartModel */
        $cartModel = Mage::getSingleton('checkout/cart');

        $address = $cartModel->getQuote()->collectTotals()->getShippingAddress();

        // Validate rules
        if (!$salesRuleModel->validate($address)) {
            return false;
        }

        return true;
    }

    /**
     * Get Total cart value in base currency
     *
     * @return float
     */
    protected function getCartTotal()
    {
        /** @var Mage_Checkout_Model_Cart $cartModel */
        $cartModel = Mage::getSingleton('checkout/cart');

        $grandTotal = $cartModel->getQuote()->getGrandTotal();

        /** @var Mage_Directory_Model_Currency $currentCurrency */
        $currentCurrency = Mage::app()->getStore()->getCurrentCurrency();
        $baseCurrency    = Mage::app()->getStore()->getBaseCurrency();

        if ($currentCurrency->getCurrencyCode() == $baseCurrency->getCurrencyCode()) {
            return $grandTotal;
        }

        $currencyRate = $currentCurrency->getRate($baseCurrency);

        // If exist current-base currency rate
        if ($currencyRate) {
            return $currentCurrency->convert($grandTotal, $baseCurrency);
        }

        $currencyRate = $baseCurrency->getRate($currentCurrency);

        // If exist base-current currency rate
        if ($currencyRate) {
            return round($grandTotal / $currencyRate, 2);
        }

        return $grandTotal;
    }

    /**
     * Encrypt url parameters
     *
     * @param array $parameters
     * @return array
     */
    protected function encryptParameters($parameters)
    {
        /** @var RapidCampaign_Promotions_Helper_Encrypter $encrypter */
        $encrypter = Mage::helper('rapidcampaign_promotions/encrypter');

        /** @var RapidCampaign_Promotions_Helper_Config $configHelper */
        $configHelper = Mage::helper('rapidcampaign_promotions/config');

        $encrypter->setKey($configHelper->getEncryptionKey());

        try {
            array_walk($parameters, function (&$item, $key) use ($encrypter) {
                $item = $encrypter->encrypt($item);
            });
        } catch (Exception $e) {
            return $parameters;
        }

        return $parameters;
    }
}
