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
     * @return Varien_Object
     * @throws Exception
     * @throws Zend_Http_Client_Exception
     */
    public function performHTTPRequest($endpoint, $apiKey)
    {
        /** @var RapidCampaign_Promotions_Model_HttpClient $client */
        $client = Mage::getSingleton('rapidcampaign_promotions/httpClient');

        $client->setUri($endpoint)
            ->setMethod(Varien_Http_Client::GET)
            ->setHeaders('Accept', 'application/json')
            ->setHeaders('x-api-key', $apiKey);

        $response = $client->request();

        $responseObject = new Varien_Object();
        $responseObject->setResponse($response);

        // Magento 1.7 compatibility fix: chuncked body is already decoded, but header is not deleted
        if (strtolower($response->getHeader('transfer-encoding')) === 'chunked') {
            try {
                $body = $response->getBody();
            } catch (Zend_Http_Exception $e) {
                $body = $response->getRawBody();
            }
        } else {
            $body = $response->getBody();
        }

        $responseObject->setBody($body);

        /** @var RapidCampaign_Promotions_Model_Log $logger */
        $logger = Mage::getSingleton('rapidcampaign_promotions/log');

        // Log API communication
        $logger->log('Request: ' . $endpoint . "\n\n" . $client->getHeadersAsString() . "\n" . $client->getBody() .
            "\n" . $response->getHeadersAsString() . "\n" . $responseObject->getBody() . "\n");

        return $responseObject;
    }
}
