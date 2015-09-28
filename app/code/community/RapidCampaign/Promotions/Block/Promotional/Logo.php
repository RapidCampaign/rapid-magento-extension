<?php
/**
 * RapidCampaign Logo Block
 *
 * @category    RapidCampaign
 * @package     RapidCampaign_Promotions
 * @copyright   Copyright (c) 2015 RapidCampaign (http://rapidcampaign.com)
 */
class RapidCampaign_Promotions_Block_Promotional_Logo extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $originalData = $element->getOriginalData();

        $logoFilename = Mage::getDesign()
            ->getFilename('images' . DS . 'rapidcampaign' . DS . 'rapidcampaign_logo.svg', array('_type' => 'skin'));

        if (file_exists($logoFilename)) {
            $this->setData(
                'rapidcampaign_img_src',
                $this->getSkinUrl('images' . DS . 'rapidcampaign' . DS . 'rapidcampaign_logo.svg')
            );
        }

        $this->setData('rapidcampaign_img_alt', $originalData['img_alt']);

        return $this->toHtml();
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        $html = $this->getBeforeHtml() . '<img '
            . ($this->getId() ? ' id="'.$this->getId() . '"':'')
            . ($this->getTitle() ? ' title="' . Mage::helper('core')->quoteEscape($this->getTitle()) . '"':'')
            . ' class="scalable ' . $this->getClass() . ($this->getDisabled() ? ' disabled' : '') . '"'
            . ' style="' . $this->getStyle() . '"'
            . ($this->getData('rapidcampaign_img_src') ? ' src="' . $this->getData('rapidcampaign_img_src') . '"':'')
            . ($this->getData('rapidcampaign_img_alt') ? ' alt="' . $this->getData('rapidcampaign_img_alt') . '"':'')
            . ' />' . $this->getAfterHtml();

        return $html;
    }
}
