<?php
/**
 * RapidCampaign Condition Block
 *
 * @category    RapidCampaign
 * @package     RapidCampaign_Promotions
 * @copyright   Copyright (c) 2015 RapidCampaign (http://rapidcampaign.com)
 */
class RapidCampaign_Promotions_Block_Widget_Condition extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        /** @var Mage_SalesRUle_Model_Rule $salesRuleModel */
        $salesRuleModel = Mage::getModel('salesrule/rule');

        /** @var Mage_Widget_Block_Adminhtml_Widget_Options $widgetOptions */
        $widgetOptions = $element->getForm()->getParent();

        $widgetValues = $widgetOptions->getWidgetValues();

        if ($widgetValues && isset($widgetValues['rule'])) {
            $rules = unserialize(base64_decode($widgetValues['rule']));

            $salesRuleModel->loadPost($rules);
            $salesRuleModel->getConditions()->setJsFormObject('rule_conditions_fieldset');
            $salesRuleModel->getActions()->setJsFormObject('rule_actions_fieldset');
        }

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('rule_');

        /** @var Mage_Adminhtml_Block_Widget_Form_Renderer_Fieldset $renderer */
        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
            ->setTemplate('rapidcampaign/fieldset.phtml')
            ->setNewChildUrl($this->getUrl('*/promo_quote/newConditionHtml/form/rule_conditions_fieldset'));

        $fieldset = $form->addFieldset('conditions_fieldset', array())->setRenderer($renderer);

        $fieldset->addField('conditions', 'text', array(
            'name'     => 'conditions',
            'label'    => Mage::helper('salesrule')->__('Conditions'),
            'title'    => Mage::helper('salesrule')->__('Conditions'),
            'required' => true,
        ))
            ->setRule($salesRuleModel)
            ->setRenderer(Mage::getBlockSingleton('rule/conditions'));

        $fieldset->addClass('fieldset-nowrap');

        $form->setValues($salesRuleModel->getData());

        Mage::register('rapidcampaign/widget/created', true, true);

        $html = $form->toHtml();

        if ($element->getNote()) {
            $html.= '<p class="note"><span>' . $element->getNote() . '</span></p>';
        }

        return $html;
    }
}
