<?php
/**
 * RapidCampaign Base Analytics Block
 *
 * @category    RapidCampaign
 * @package     RapidCampaign_Promotions
 * @copyright   Copyright (c) 2015 RapidCampaign (http://rapidcampaign.com)
 */
class RapidCampaign_Promotions_Block_Analytics_Base extends Mage_Core_Block_Template
{
    /**
     * Prepare HTML content
     *
     * @return string
     */
    protected function _toHtml()
    {
        /** @var RapidCampaign_Promotions_Helper_Config $configHelper */
        $configHelper = Mage::helper('rapidcampaign_promotions/config');

        // Module disabled or Merchant ID is not set
        if (!$configHelper->extensionEnabled() || !$configHelper->getMerchantId()) {
            return '';
        }

        $merchantId = Mage::helper('core')->jsonEncode($configHelper->getMerchantId());

        $html = <<<SCRIPT
<!-- RapidCampaign Analytics Begins -->
<script type="text/javascript">
//<![CDATA[
    var _naq = _naq || [];
    _naq.push(["_setAccount", {$merchantId}]);
    _naq.push(["_trackPageview"]);

    (function() {
        var na = document.createElement('script'); na.type = 'text/javascript'; na.async = true;
        na.src = 'http://analytics.rapidcampaign.com/na.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(na,s);
    })();
//]]></script>
<!-- RapidCampaign Analytics Ends -->
SCRIPT;

        return $html;
    }
}
