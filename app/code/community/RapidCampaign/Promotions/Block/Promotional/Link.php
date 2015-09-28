<?php
/**
 * RapidCampaign Link Block
 *
 * @category    RapidCampaign
 * @package     RapidCampaign_Promotions
 * @copyright   Copyright (c) 2015 RapidCampaign (http://rapidcampaign.com)
 */
class RapidCampaign_Promotions_Block_Promotional_Link extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $originalData = $element->getOriginalData();

        $this->setData('rapidcampaign_link_href', $originalData['href']);
        $this->setData('rapidcampaign_link_value', $originalData['value']);

        return $this->toHtml();
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        $html = $this->getBeforeHtml() . '<a target="_blank"'
            . ($this->getId() ? ' id="' . $this->getId() . '"':'')
            . ($this->getTitle() ? ' title="' . Mage::helper('core')->quoteEscape($this->getTitle()) . '"':'')
            . ' class="scalable ' . $this->getClass() . ($this->getDisabled() ? ' disabled' : '') . '"'
            . ' style="' . $this->getStyle() . '"'
            . ($this->getDisabled() ? ' disabled="disabled"' : '')
            . ($this->getData('rapidcampaign_link_href') ? ' href="'.$this->getData('rapidcampaign_link_href') . '"':'')
            . '>' . $this->getData('rapidcampaign_link_value') . '</a>' . $this->getAfterHtml();

        return $html;
    }
}
