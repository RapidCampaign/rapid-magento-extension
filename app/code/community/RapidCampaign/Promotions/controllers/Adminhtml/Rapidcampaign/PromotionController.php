<?php
/**
 * RapidCampaign
 *
 * @category    RapidCampaign
 * @package     RapidCampaign_Promotions
 * @copyright   Copyright (c) 2015 RapidCampaign (http://rapidcampaign.com)
 */
class RapidCampaign_Promotions_Adminhtml_Rapidcampaign_PromotionController extends Mage_Adminhtml_Controller_Action
{
    const PROMOTIONS_FETCH_SUCCESS_MESSAGE = 'Promotions have been successfully updated.';
    const PROMOTIONS_FETCH_FAIL_MESSAGE = 'Promotions have not been updated.';

    /**
     * Fetch and update promotions
     */
    public function updateAction()
    {
        /** @var RapidCampaign_Promotions_Model_Storage $promotionsStorage */
        $promotionsStorage = Mage::getModel('rapidcampaign_promotions/storage');

        /** @var Mage_Core_Model_Session $sessionModel */
        $sessionModel = Mage::getSingleton('core/session');

        try {
            // Update cache
            if ($promotionsStorage->updateCache()) {
                $sessionModel->addSuccess(
                    $this->__(self::PROMOTIONS_FETCH_SUCCESS_MESSAGE)
                );
            } else {
                $sessionModel->addWarning(
                    $this->__(self::PROMOTIONS_FETCH_FAIL_MESSAGE)
                );
            }
        } catch (Exception $e) {
            $sessionModel->addError(
                $this->__($e->getMessage())
            );
        }

        // Redirect back to Rapidcampaign extension configuration
        Mage::app()->getResponse()->setRedirect(
            Mage::helper('adminhtml')->getUrl('adminhtml/system_config/edit/section/rapidcampaign_promotions')
        );
    }
}
