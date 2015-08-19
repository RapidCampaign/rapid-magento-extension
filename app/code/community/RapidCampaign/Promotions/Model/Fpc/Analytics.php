<?php
/**
 * RapidCampaign
 *
 * @category    RapidCampaign
 * @package     RapidCampaign_Promotions
 * @copyright   Copyright (c) 2015 RapidCampaign (http://rapidcampaign.com)
 */

class RapidCampaign_Promotions_Model_Fpc_Analytics extends Enterprise_PageCache_Model_Container_Abstract
{
    /**
     * Never get cached block
     *
     * @param string $content
     * @return bool
     */
    public function applyWithoutApp(&$content)
    {
        return false;
    }

    /**
     * Render block content from placeholder
     *
     * @return false|string
     */
    protected function _renderBlock()
    {
        $block = $this->_getPlaceHolderBlock();

        Mage::dispatchEvent('render_block', array('block' => $block, 'placeholder' => $this->_placeholder));

        return $block->toHtml();
    }
}
