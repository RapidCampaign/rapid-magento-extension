<?php
/**
 * RapidCampaign
 *
 * @category    RapidCampaign
 * @package     RapidCampaign_Promotions
 * @copyright   Copyright (c) 2015 RapidCampaign (http://rapidcampaign.com)
 */

/**
 * Options for analytics select
 */
class RapidCampaign_Promotions_Model_System_Config_Source_Analytics
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 1, 'label' => Mage::helper('rapidcampaign_promotions')->__('Full (Recommended)')),
            array('value' => 0, 'label' => Mage::helper('rapidcampaign_promotions')->__('Partial')),
        );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            0 => Mage::helper('rapidcampaign_promotions')->__('Partial'),
            1 => Mage::helper('rapidcampaign_promotions')->__('Full (Recommended)'),
        );
    }
}
