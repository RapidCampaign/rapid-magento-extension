<?php
/**
 * RapidCampaign
 *
 * @category    RapidCampaign
 * @package     RapidCampaign_Promotions
 * @copyright   Copyright (c) 2015 RapidCampaign (http://rapidcampaign.com)
 */

class RapidCampaign_Promotions_Test_Model_Api_Promotions extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     * @loadFixture
     * @dataProvider dataProvider
     */
    public function getPromotions($id, $slug, $httpCode, $body)
    {
        $this->registerApiClientStub($httpCode, $body)
            ->registerLoggerStub();

        $promotionsClient = Mage::getSingleton('rapidcampaign_promotions/api_promotions');

        $expected = $this->expected('%s-%s', $id, $httpCode);

        if ($expected->getThrow()) {
            $this->setExpectedException($expected->getThrow());
        }

        $this->assertEquals(
            Mage::helper('core')->jsonDecode($expected->getResult()),
            $promotionsClient->getPromotions($slug)
        );
    }

    /**
     * Create api client mock and replace `performHTTPRequest` method with callback
     *
     * @return $this
     * @throws PHPUnit_Framework_Exception
     */
    protected function registerApiClientStub($httpCode, $body)
    {
        $apiClientModelMock = $this->getModelMock('rapidcampaign_promotions/api_client', array('performHTTPRequest'));
        $apiClientModelMock->expects($this->any())
            ->method('performHTTPRequest')
            ->will($this->returnValue(new Zend_Http_Response($httpCode, array(), $body)));

        if ($httpCode == 0) {
            $apiClientModelMock->method('performHTTPRequest')
                ->will($this->throwException(
                    new Zend_Http_Client_Exception('Unable to read response, or response is empty')
                ));
        }

        $this->replaceByMock('model', 'rapidcampaign_promotions/api_client', $apiClientModelMock);

        return $this;
    }

    /**
     * Create logger mock
     *
     * @return $this
     * @throws PHPUnit_Framework_Exception
     */
    protected function registerLoggerStub()
    {
        $loggerModelMock = $this->getModelMock('rapidcampaign_promotions/log', array('log'));
        $loggerModelMock->expects($this->any())
            ->method('log');

        $this->replaceByMock('model', 'rapidcampaign_promotions/log', $loggerModelMock);

        return $this;
    }
}
