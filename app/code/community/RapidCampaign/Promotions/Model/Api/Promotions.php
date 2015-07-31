<?php
/**
 * RapidCampaign
 *
 * @category    RapidCampaign
 * @package     RapidCampaign_Promotions
 * @copyright   Copyright (c) 2015 RapidCampaign (http://rapidcampaign.com)
 */

class RapidCampaign_Promotions_Model_Api_Promotions
{
    const PROMOTIONS_ENDPOINT = '/promotions';
    const MAX_ATTEMPTS = 3;

    // Cache API responses for 24h
    const API_CACHE_TIME = 86400;

    /**
     * @param string $slug
     * @return bool|string
     */
    public function getPromotions($slug = null)
    {
        /** @var RapidCampaign_Promotions_Model_Log $logger */
        $logger = Mage::getSingleton('rapidcampaign_promotions/log');

        $apiKey = Mage::getStoreConfig('rapidcampaign_general/rapidcampaign_general_group/apikey');

        if (!$apiKey) {
            $logger->log('API Key is not set', Zend_Log::DEBUG);
            return false;
        }

        $testMode = Mage::getStoreConfig('rapidcampaign_developer/rapidcampaign_developer_group/enable_test_mode');

        $apiUrl = $testMode ? Mage::getStoreConfig('rapidcampaign_promotions/api_endpoint/test')
            : Mage::getStoreConfig('rapidcampaign_promotions/api_endpoint/production');

        $endpoint = $apiUrl . self::PROMOTIONS_ENDPOINT;

        if ($slug) {
            $endpoint .= '/' . $slug;
        }

        $cache = Mage::app()->getCache()->load($endpoint);

        if ($cache) {
            return $cache;
        }

        /** @var RapidCampaign_Promotions_Model_Api_Client $client */
        $client = Mage::getSingleton('rapidcampaign_promotions/api_client');

        for ($i = 0; $i < self::MAX_ATTEMPTS; $i++) {
            try {
                $response = $client->performHTTPRequest($endpoint, $apiKey);
            } catch (Exception $e) {
                $logger->log($e->getMessage(), Zend_Log::CRIT);
                return false;
            }

            if ($response->isSuccessful()) {
                $body = $response->getBody();
                // Make sure response is valid json
                try {
                    Mage::helper('core')->jsonDecode($body);
                } catch (Exception $e) {
                    $logger->log('Response json is not valid: ' . $e->getMessage());
                    return false;
                }

                Mage::app()->getCache()->save($body, $endpoint, array('rapidcampaign_api_cache'), self::API_CACHE_TIME);
                return $body;
            } else {
                // Handle 5xx errors
                $responseType = floor($response->getStatus() / 100);
                if ($responseType == 5) {
                    continue;
                }

                return false;
            }
        }

        $logger->log('Unable to receive data from ' . $endpoint . ' after ' . self::MAX_ATTEMPTS . ' attempts');
        return false;
    }
}
