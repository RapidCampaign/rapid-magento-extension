<?php
/**
 * RapidCampaign Button Block
 *
 * @category    RapidCampaign
 * @package     RapidCampaign_Promotions
 * @copyright   Copyright (c) 2015 RapidCampaign (http://rapidcampaign.com)
 */
class RapidCampaign_Promotions_Block_Promotional_Button extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $elementData = $element->getData();

        $this->setElement($element);

        $url = Mage::helper("adminhtml")->getUrl('adminhtml/rapidcampaign_promotion/update');

        $html = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setLabel($elementData['original_data']['button_label'])
            ->setOnClick("setLocation('$url'); return false;")
            ->toHtml();

        return $html;
    }
}
