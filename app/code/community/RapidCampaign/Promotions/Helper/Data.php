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

    public function getPromotionModalJs($promotionUniqueId, $modalDelay, $iframeUrl, $iframeWidth = null, $iframeHeight = null)
    {
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

            Event.observe(window, 'load', loadModalAfterDelay, false);

            function loadModalAfterDelay() {
                var cookie = '$cookie';
                if (cookie == '') {
                    setTimeout(
                    function(){
                        myLightWindow.activateWindow({
                            href: '$iframeUrl',
                            width: '$iframeWidth',
                            height: 'auto'
                            iframeEmbed: 'true',
                        });
                    }, $modalDelay * 1000);
                }
            }

            function setPromotionDismissalCookie() {
                var name = '$cookieName';
                expires = $cookieExpires;

                var cookieStr = name + "=" + escape(1) + "; ";
                if (expires) {
                    var expiresDate = new Date(new Date().getTime() + expires * 24 * 60 * 60 * 1000);
                    cookieStr += "expires=" + expiresDate.toGMTString() + "; ";
                }
                if (Mage.Cookies.path) {
                    cookieStr += "path=" + Mage.Cookies.path + "; ";
                }
                if (Mage.Cookies.domain) {
                    cookieStr += "domain=" + Mage.Cookies.domain + "; ";
                }
                if (Mage.Cookies.domain.secure) {
                    cookieStr += "secure; ";
                }
                document.cookie = cookieStr;

            }

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