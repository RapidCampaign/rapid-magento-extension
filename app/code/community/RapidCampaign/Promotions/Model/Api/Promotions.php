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

    // Error messages
    const ERROR_APIKEY_MISSING = 'API Key is not set';
    const ERROR_APIKEY_INVALID = 'API Key is not valid';
    const ERROR_JSON_INVALID = 'Response json is not valid: %s';
    const ERROR_REQUEST = 'RapidCampaign returned the %s code';
    const ERROR_SERVER = 'Unable to receive data after %s attempts';

    /**
     * @param string $slug
     * @return array
     * @throws Exception
     */
    public function getPromotions($slug = null)
    {
        /** @var RapidCampaign_Promotions_Model_Log $logger */
        $logger = Mage::getSingleton('rapidcampaign_promotions/log');

        $apiKey = Mage::getStoreConfig('rapidcampaign_general/rapidcampaign_general_group/apikey');

        if (!$apiKey) {
            $logger->log(self::ERROR_APIKEY_MISSING);

            throw new Exception(self::ERROR_APIKEY_MISSING);
        }

        $endpoint = $this->buildUrl($slug);

        /** @var RapidCampaign_Promotions_Model_Api_Client $client */
        $client = Mage::getSingleton('rapidcampaign_promotions/api_client');

        for ($i = 0; $i < self::MAX_ATTEMPTS; $i++) {
            try {
                $response = $client->performHTTPRequest($endpoint, $apiKey);
            } catch (Exception $e) {
                $logger->log($e->getMessage(), Zend_Log::CRIT);
                // Rethrow exception
                throw $e;
            }

            if ($response->isSuccessful()) {
                $body = $response->getBody();
                // Make sure response is valid json
                try {
                    return Mage::helper('core')->jsonDecode($body);
                } catch (Exception $e) {
                    $logger->log(sprintf(self::ERROR_JSON_INVALID, $e->getMessage()));

                    throw new Exception(sprintf(self::ERROR_JSON_INVALID, $e->getMessage()));
                }
            } else {
                // Handle 5xx errors
                $responseType = floor($response->getStatus() / 100);
                if ($responseType == 5) {
                    continue;
                }

                // API Key invalid
                if ($response->getStatus() == 403) {
                    throw new Exception(self::ERROR_APIKEY_INVALID);
                }

                // Other non-success status code
                throw new Exception(sprintf(self::ERROR_REQUEST, $response->getStatus()));
            }
        }

        // Max attempts reached
        $logger->log(sprintf(self::ERROR_SERVER, self::MAX_ATTEMPTS));

        throw new Exception(sprintf(self::ERROR_SERVER, self::MAX_ATTEMPTS));
    }

    /**
     * Build Url based on whether test mode is turned on
     *
     * @param string $slug
     * @return string
     */
    protected function buildUrl($slug = null)
    {
        $testMode = Mage::getStoreConfig('rapidcampaign_developer/rapidcampaign_developer_group/enable_test_mode');

        $apiUrl = $testMode ? Mage::getStoreConfig('rapidcampaign_promotions/api_endpoint/test')
            : Mage::getStoreConfig('rapidcampaign_promotions/api_endpoint/production');

        $endpoint = $apiUrl . self::PROMOTIONS_ENDPOINT;

        if ($slug) {
            $endpoint .= '/' . $slug;
        }

        return $endpoint;
    }
}
