<?php
/**
 * RapidCampaign
 *
 * @category    RapidCampaign
 * @package     RapidCampaign_Promotions
 * @copyright   Copyright (c) 2015 RapidCampaign (http://rapidcampaign.com)
 */

class RapidCampaign_Promotions_Model_Api_Client
{
    /**
     * Perform API request
     *
     * @param string $endpoint
     * @param string $apiKey
     * @return Zend_Http_Response
     * @throws Exception
     * @throws Zend_Http_Client_Exception
     */
    public function performHTTPRequest($endpoint, $apiKey)
    {
        $client = new RapidCampaign_Promotions_Model_HttpClient();

        $client->setUri($endpoint)
            ->setMethod(Varien_Http_Client::GET)
            ->setHeaders('Accept', 'application/json')
            ->setHeaders('x-api-key', $apiKey);

        $response = $client->request();

        /** @var RapidCampaign_Promotions_Model_Log $logger */
        $logger = Mage::getSingleton('rapidcampaign_promotions/log');

        // Log API communication
        $logger->log('Request: ' . $endpoint . "\n\n" . $client->getHeadersAsString() . "\n" . $client->getBody() .
            "\n" . $response->getHeadersAsString() . "\n" . $response->getBody() . "\n");

        return $response;
    }
}
