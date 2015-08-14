<?php
/**
 * RapidCampaign
 *
 * @category    RapidCampaign
 * @package     RapidCampaign_Promotions
 * @copyright   Copyright (c) 2015 RapidCampaign (http://rapidcampaign.com)
 */
class RapidCampaign_Promotions_Helper_Config extends Mage_Core_Helper_Abstract
{
    // General settings
    const XPATH_ENABLE = 'rapidcampaign_general/rapidcampaign_general_group/enable';
    const XPATH_APIKEY = 'rapidcampaign_general/rapidcampaign_general_group/apikey';
    const XPATH_ENABLE_ENCRYPTION = 'rapidcampaign_general/rapidcampaign_general_group/enable_encryption';
    const XPATH_ENCRYPTION_KEY = 'rapidcampaign_general/rapidcampaign_general_group/encryption_key';
    const XPATH_ENABLE_ANALYTICS = 'rapidcampaign_general/rapidcampaign_general_group/enable_analytics';
    const XPATH_COOKIE_LIFETIME = 'rapidcampaign_general/rapidcampaign_general_group/cookie_lifetime';

    // Developer settings
    const XPATH_ENABLE_TEST_MODE = 'rapidcampaign_developer/rapidcampaign_developer_group/enable_test_mode';
    const XPATH_ENABLE_DEBUG_LOGS = 'rapidcampaign_developer/rapidcampaign_developer_group/enable_debug_logs';

    // Custom config
    const XPATH_API_PRODUCTION = 'rapidcampaign_promotions/api_endpoint/production';
    const XPATH_API_TEST = 'rapidcampaign_promotions/api_endpoint/test';
    const XPATH_ANALYTICS_SCRIPT = 'rapidcampaign_promotions/analytics_script/path';

    /**
     * Current scope (store Id)
     *
     * @var int
     */
    protected $storeId;

    /**
     * Return current store id
     *
     * @return int
     */
    public function getStoreId()
    {
        if (is_null($this->storeId)) {
            $this->storeId = Mage::app()->getStore()->getId();
        }

        return $this->storeId;
    }

    /**
     * @return bool
     */
    public function extensionEnabled()
    {
        return Mage::getStoreConfigFlag(self::XPATH_ENABLE, $this->getStoreId());
    }

    /**
     * @return string
     */
    public function getApikey()
    {
        return Mage::getStoreConfig(self::XPATH_APIKEY, $this->getStoreId());
    }

    /**
     * @return bool
     */
    public function encryptionEnabled()
    {
        return Mage::getStoreConfigFlag(self::XPATH_ENABLE_ENCRYPTION, $this->getStoreId());
    }

    /**
     * @return string
     */
    public function getEncryptionKey()
    {
        return Mage::getStoreConfig(self::XPATH_ENCRYPTION_KEY, $this->getStoreId());
    }

    /**
     * @return bool
     */
    public function isFullAnalytics()
    {
        return Mage::getStoreConfigFlag(self::XPATH_ENABLE_ANALYTICS, $this->getStoreId());
    }

    /**
     * @return string
     */
    public function getCookieLifetime()
    {
        return Mage::getStoreConfig(self::XPATH_COOKIE_LIFETIME, $this->getStoreId());
    }

    /**
     * @return bool
     */
    public function testModeEnabled()
    {
        return Mage::getStoreConfigFlag(self::XPATH_ENABLE_TEST_MODE, $this->getStoreId());
    }

    /**
     * @return bool
     */
    public function debugLogsEnabled()
    {
        return Mage::getStoreConfigFlag(self::XPATH_ENABLE_DEBUG_LOGS, $this->getStoreId());
    }

    /**
     * @return string
     */
    public function getProductionEndpoint()
    {
        return Mage::getStoreConfig(self::XPATH_API_PRODUCTION, $this->getStoreId());
    }

    /**
     * @return string
     */
    public function getTestEndpoint()
    {
        return Mage::getStoreConfig(self::XPATH_API_TEST, $this->getStoreId());
    }

    /**
     * @return string
     */
    public function getExternalAnalyticsScriptPath()
    {
        return Mage::getStoreConfig(self::XPATH_ANALYTICS_SCRIPT, $this->getStoreId());
    }
}
