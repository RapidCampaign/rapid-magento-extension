<?php
/**
 * RapidCampaign
 *
 * @category    RapidCampaign
 * @package     RapidCampaign_Promotions
 * @copyright   Copyright (c) 2015 RapidCampaign (http://rapidcampaign.com)
 */

class RapidCampaign_Promotions_Model_Condition_Extra extends Mage_Rule_Model_Condition_Abstract
{
    /**
     * @return RapidCampaign_Promotions_Model_Condition_Extra
     */
    public function loadAttributeOptions()
    {
        $attributes = array(
            'customer_group_condition' => Mage::helper('rapidcampaign_promotions')->__('Customer Group')
        );

        $this->setAttributeOption($attributes);

        return $this;
    }

    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);
        return $element;
    }

    /**
     * @return string
     */
    public function getInputType()
    {
        switch ($this->getAttribute()) {
            case 'customer_group_condition':
                return 'multiselect';
        }
        return 'string';
    }

    /**
     * @return string
     */
    public function getValueElementType()
    {
        switch ($this->getAttribute()) {
            case 'customer_group_condition':
                return 'multiselect';
        }

        return 'text';
    }

    public function getValue()
    {
        if (!$this->getIsValueParsed()) {
            switch ($this->getAttribute()) {
                case 'customer_group_condition':
                    $value = $this->getData('value');

                    if (!$value) {
                        break;
                    }

                    $value = reset($value);

                    if (!$value) {
                        break;
                    }

                    $this->setValue(explode(',', $value));
                    $this->setIsValueParsed(true);
                    break;
            }
        }

        return parent::getValue();
    }

    /**
     * Retrieve parsed value
     *
     * @return array|string|int|float
     */
    public function getValueParsed()
    {
        if (!$this->hasValueParsed()) {
            switch ($this->getAttribute()) {
                case 'customer_group_condition':
                    /** @var Mage_Customer_Model_Session $customerSessionModel */
                    $customerSessionModel = Mage::getSingleton('customer/session');

                    $this->setValueParsed(array($customerSessionModel->getCustomerGroupId()));
                    break;
            }
        }

        return parent::getValueParsed();
    }

    /**
     * @return array
     */
    public function getValueSelectOptions()
    {
        if (!$this->hasData('value_select_options')) {
            switch ($this->getAttribute()) {
                case 'customer_group_condition':
                    $options = Mage::getModel('customer/group')->getCollection()->toOptionArray();
                    break;

                default:
                    $options = array();
            }

            $this->setData('value_select_options', $options);
        }

        return $this->getData('value_select_options');
    }

    /**
     * @param Varien_Object $object
     * @return bool
     */
    public function validate(Varien_Object $object)
    {
        $address = $object;
        if (!$address instanceof Mage_Sales_Model_Quote_Address) {
            if ($object->getQuote()->isVirtual()) {
                $address = $object->getQuote()->getBillingAddress();
            } else {
                $address = $object->getQuote()->getShippingAddress();
            }
        }

        $address->setCustomerGroupCondition($this->getValue());

        return parent::validate($address);
    }
}
