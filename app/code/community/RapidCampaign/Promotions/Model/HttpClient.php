<?php
/**
 * RapidCampaign
 *
 * @category    RapidCampaign
 * @package     RapidCampaign_Promotions
 * @copyright   Copyright (c) 2015 RapidCampaign (http://rapidcampaign.com)
 */

/**
 * Extend Varien_Http_Client to be able to get request body and headers
 */
class RapidCampaign_Promotions_Model_HttpClient extends Varien_Http_Client
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return string of raw body
     */
    public function getBody()
    {
        return $this->_prepareBody();
    }

    /**
     * @return string of headers with status line
     */
    public function getHeadersAsString()
    {
        $uri = $this->getUri();

        return $this->method . ' ' . $uri->getScheme() . '://' . $uri->getHost() . $uri->getPath() .
            ' HTTP/' . $this->config['httpversion']
            . "\n" . join("\n", $this->_prepareHeaders());
    }
}
