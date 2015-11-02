<?php
/**
 * RapidCampaign
 *
 * @category    RapidCampaign
 * @package     RapidCampaign_Promotions
 * @copyright   Copyright (c) 2015 RapidCampaign (http://rapidcampaign.com)
 */
class RapidCampaign_Promotions_Helper_Data extends Mage_Core_Helper_Abstract
{
    const PROMOTION_MODAL_COOKIE_NAME = 'rapidcampaign_promotion_';

    public function getPromotionModalJs($promotionUniqueId, $modalDelay, $iframeWidth = null)
    {

        // should we show modal?
        $cookieName = $this->getPromotionModalCookieName($promotionUniqueId);
        $cookie = Mage::getModel('core/cookie')->get($cookieName);
        $cookieExpires = Mage::helper('rapidcampaign_promotions/config')->getCookieLifetime();
        if (empty($cookieExpires)) {
            $cookieExpires = Mage::getModel("core/cookie")->getLifetime();
        }

        $html = <<<SCRIPT
            <!-- RapidCampaign Modal Begins -->
            <script type="text/javascript">
            //<![CDATA[
                new PromotionModal('_rc_$promotionUniqueId', '$modalDelay', '$iframeWidth',
                    '$cookieName','$cookieExpires');
            //]]></script>
            <!-- RapidCampaign Modal Ends -->
SCRIPT;

        return $html;
    }

    /**
     * @param $promotionUniqueId
     * @return string
     */
    public function getPromotionModalCookieName($promotionUniqueId) {
        return self::PROMOTION_MODAL_COOKIE_NAME . $promotionUniqueId;
    }
}