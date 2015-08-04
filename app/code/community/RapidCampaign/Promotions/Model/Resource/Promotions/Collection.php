<?php
/**
 * RapidCampaign
 *
 * @category    RapidCampaign
 * @package     RapidCampaign_Promotions
 * @copyright   Copyright (c) 2015 RapidCampaign (http://rapidcampaign.com)
 */
class RapidCampaign_Promotions_Model_Resource_Promotions_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Resource collection initialization
     *
     */
    protected function _construct()
    {
        $this->_init('rapidcampaign_promotions/promotions');
    }

    /**
     * @param DateTime $dateTime
     */
    public function updateExpireTime(DateTime $dateTime)
    {
        $tableName = $this->getTable('rapidcampaign_promotions/promotions');

        /** @var Varien_Db_Adapter_Interface $dbAdapter */
        $dbAdapter = Mage::getSingleton('core/resource')->getConnection('core_write');

        $dbAdapter->update($tableName, array('expire_time' => $dateTime->format('Y-m-d H:i:s')));
    }
}
