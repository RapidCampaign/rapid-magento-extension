<?php
/**
 * RapidCampaign
 *
 * @category    RapidCampaign
 * @package     RapidCampaign_Promotions
 * @copyright   Copyright (c) 2015 RapidCampaign (http://rapidcampaign.com)
 */

class RapidCampaign_Promotions_Model_Log
{
    const LOG_FILE = 'rapidcampaign_debug.log';

    public function log($message, $level = Zend_Log::DEBUG)
    {
        /** @var RapidCampaign_Promotions_Helper_Config $configHelper */
        $configHelper = Mage::helper('rapidcampaign_promotions/config');

        if ($configHelper->debugLogsEnabled()) {
            Mage::log($message, $level, self::LOG_FILE, true);
        }

        return $this;
    }
}
