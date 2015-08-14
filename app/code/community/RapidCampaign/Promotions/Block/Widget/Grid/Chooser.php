<?php
/**
 * RapidCampaign Widget chooser
 *
 * @category    RapidCampaign
 * @package     RapidCampaign_Promotions
 * @copyright   Copyright (c) 2015 RapidCampaign (http://rapidcampaign.com)
 */
class RapidCampaign_Promotions_Block_Widget_Grid_Chooser extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Block construction, prepare grid params
     *
     * @param array $arguments Object data
     */
    public function __construct($arguments = array())
    {
        parent::__construct($arguments);
        $this->setUseAjax(true);
    }

    /**
     * Prepare chooser element HTML
     *
     * @param Varien_Data_Form_Element_Abstract $element Form Element
     * @return Varien_Data_Form_Element_Abstract
     */
    public function prepareElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $uniqId    = Mage::helper('core')->uniqHash($element->getId());
        $sourceUrl = $this->getUrl('rapidcampaign/adminhtml_chooser/chooser', array('uniq_id' => $uniqId));

        $chooser = $this->getLayout()->createBlock('widget/adminhtml_widget_chooser')
            ->setElement($element)
            ->setTranslationHelper($this->getTranslationHelper())
            ->setConfig($this->getConfig())
            ->setFieldsetId($this->getFieldsetId())
            ->setSourceUrl($sourceUrl)
            ->setUniqId($uniqId);

        if ($element->getValue()) {
            /** @var RapidCampaign_Promotions_Model_Cache $promotionsCache */
            $promotionsCache = Mage::getModel('rapidcampaign_promotions/cache');
            $promotion = $promotionsCache->getPromotionsModel()->load($element->getValue());

            if ($promotion->getId()) {
                $chooser->setLabel($promotion->getName());
            }
        }

        $element->setData('after_element_html', $chooser->toHtml());

        return $element;
    }

    /**
     * Grid Row JS Callback
     *
     * @return string
     */
    public function getRowClickCallback()
    {
        $chooserJsObject = $this->getId();
        $js = '
            function (grid, event) {
                var trElement = Event.findElement(event, "tr");
                var promotionName = trElement.down("td").next().innerHTML;
                var promotionSlug = trElement.down("td").innerHTML.replace(/^\s+|\s+$/g,"");
                '.$chooserJsObject.'.setElementValue(promotionSlug);
                '.$chooserJsObject.'.setElementLabel(promotionName);
                '.$chooserJsObject.'.close();
            }
        ';
        return $js;
    }

    /**
     * Prepare promotions collection
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        /** @var RapidCampaign_Promotions_Model_Cache $promotionsCache */
        $promotionsCache = Mage::getModel('rapidcampaign_promotions/cache');

        /** @var RapidCampaign_Promotions_Model_Resource_Promotions_Collection $collection */
        $collection = $promotionsCache->getPromotionsModel()->getCollection();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare columns for promotions grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('slug', array(
            'header'    => Mage::helper('rapidcampaign_promotions')->__('Promotion Slug'),
            'align'     => 'right',
            'index'     => 'slug',
            'width'     => 50
        ));

        $this->addColumn('name', array(
            'header'    => Mage::helper('rapidcampaign_promotions')->__('Promotion Name'),
            'align'     => 'left',
            'index'     => 'name',
        ));

        $this->addColumn('locale', array(
            'header'    => Mage::helper('rapidcampaign_promotions')->__('Promotion Locale'),
            'align'     => 'left',
            'index'     => 'locale'
        ));

        $this->addColumn('campaign_template_name', array(
            'header'    => Mage::helper('rapidcampaign_promotions')->__('Campaign Template Name'),
            'align'     => 'left',
            'index'     => 'campaign_template_name'
        ));

        $this->addColumn('promotion_category', array(
            'header'    => Mage::helper('rapidcampaign_promotions')->__('Promotion Category'),
            'align'     => 'left',
            'index'     => 'promotion_category'
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('rapidcampaign/adminhtml_chooser/chooser', array('_current' => true));
    }
}
