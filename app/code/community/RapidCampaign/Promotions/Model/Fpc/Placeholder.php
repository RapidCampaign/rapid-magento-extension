<?php
/**
 * RapidCampaign
 *
 * @category    RapidCampaign
 * @package     RapidCampaign_Promotions
 * @copyright   Copyright (c) 2015 RapidCampaign (http://rapidcampaign.com)
 */

class RapidCampaign_Promotions_Model_Fpc_Placeholder extends Enterprise_PageCache_Model_Container_Abstract
{
    const CACHE_PREFIX = 'RAPIDCAMPAIGN_HOLEPUNCH_';

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
     * Get container individual cache id
     *
     * @return string
     */
    protected function _getCacheId()
    {
        return self::CACHE_PREFIX . $this->_placeholder->getAttribute('unique_id');
    }

    /**
     * Render block content from placeholder
     *
     * @return false|string
     */
    protected function _renderBlock()
    {
        $block = $this->_getPlaceHolderBlock();

        // Set block parameters
        $id = $this->_getCacheId() . '_params';

        if ($parameters = Enterprise_PageCache_Model_Cache::getCacheInstance()->load($id)) {
            $block->addData(unserialize($parameters));

            // Set environment information on block
            $block->setFullPageCacheEnvironment(true);
        }

        Mage::dispatchEvent('render_block', array('block' => $block, 'placeholder' => $this->_placeholder));

        return $block->toHtml();
    }
}
