<?php
/**
 * RapidCampaign
 *
 * @category    RapidCampaign
 * @package     RapidCampaign_Promotions
 * @copyright   Copyright (c) 2015 RapidCampaign (http://rapidcampaign.com)
 */
class RapidCampaign_Promotions_Model_Resource_Promotions extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource initialization
     *
     */
    protected function _construct()
    {
        $this->_init('rapidcampaign_promotions/promotions', 'slug');
        $this->_isPkAutoIncrement = false;
    }
}
