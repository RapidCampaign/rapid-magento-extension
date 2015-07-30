<?php
/**
 * RapidCampaign Promotion Widget Block
 *
 * @category    RapidCampaign
 * @package     RapidCampaign_Promotions
 * @copyright   Copyright (c) 2015 RapidCampaign (http://rapidcampaign.com)
 */
class RapidCampaign_Promotions_Block_Widget_Promotion extends Mage_Core_Block_Template implements Mage_Widget_Block_Interface
{
    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        // There will be iframe output

        // But now just test output
        return (new DateTime())->format('Y-m-d H:i:s');
    }
}
