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

    /**
     * Create a CustomBox JS modal call for a specific promotion.
     *
     * @param $promotionUniqueId
     * @param $modalDelay
     * @param null $iframeWidth
     * @return string
     */
    public function getPromotionModalJs($promotionUniqueId, $modalDelay, $iframeWidth = null)
    {
        // obtain cookie name and expiry time
        $cookieName = $this->getPromotionModalCookieName($promotionUniqueId);
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
     * Get a unique cookie name from a promotion unique id.
     *
     * @param $promotionUniqueId
     * @return string
     */
    public function getPromotionModalCookieName($promotionUniqueId) {
        return self::PROMOTION_MODAL_COOKIE_NAME . $promotionUniqueId;
    }
}