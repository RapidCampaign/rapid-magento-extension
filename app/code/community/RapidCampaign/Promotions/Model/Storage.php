<?php
/**
 * RapidCampaign
 *
 * @category    RapidCampaign
 * @package     RapidCampaign_Promotions
 * @copyright   Copyright (c) 2015 RapidCampaign (http://rapidcampaign.com)
 */

class RapidCampaign_Promotions_Model_Storage
{
    // Cache API responses for 24h
    const API_CACHE_TIME = 86400;

    /**
     * @return RapidCampaign_Promotions_Model_Promotions
     * @throws Exception
     */
    public function getPromotionsModel()
    {
        if ($this->cacheExpired()) {
            $this->updateCache();
        }

        return Mage::getModel('rapidcampaign_promotions/promotions');
    }

    /**
     * @return RapidCampaign_Promotions_Model_Promotions
     */
    public function getCachedPromotionsModel()
    {
        return Mage::getModel('rapidcampaign_promotions/promotions');
    }

    /**
     * Check if cache expired
     *
     * @return bool
     */
    protected function cacheExpired()
    {
        /** @var RapidCampaign_Promotions_Model_Promotions $cachedItem */
        $cachedItem = Mage::getModel('rapidcampaign_promotions/promotions')->getCollection()
            ->setOrder('expire_time', 'ASC')->getFirstItem();

        if ($cachedItem->isEmpty()) {
            return true;
        }

        $now        = new DateTime();
        $expireTime = new DateTime($cachedItem->getExpireTime());

        return $now >= $expireTime;
    }

    /**
     * Update promotions cache
     *
     * @return bool
     * @throws Exception
     */
    public function updateCache()
    {
        /** @var RapidCampaign_Promotions_Model_Api_Promotions $promotionsClient */
        $promotionsClient = Mage::getSingleton('rapidcampaign_promotions/api_promotions');

        try {
            $promotions = $promotionsClient->getPromotions();
        } catch (Exception $e) {
            throw $e;
        }

        /** @var RapidCampaign_Promotions_Model_Resource_Promotions_Collection $collection */
        $collection = Mage::getModel('rapidcampaign_promotions/promotions')->getCollection();

        // Delete promotions which not present in response

        /** @var RapidCampaign_Promotions_Model_Promotions $item */
        foreach ($collection as $item) {
            // Get response promotion by slug
            $promotion = array_filter($promotions, function ($promotion) use ($item) {
                return $promotion['slug'] == $item->getId();
            });

            if (!empty($promotion)) {
                continue;
            }

            try {
                $item->delete();
            } catch (Exception $e) {
                continue;
            }
        }

        // Insert and update rest promotions
        foreach ($promotions as $promotion) {
            $promotion['campaign_template_name'] = '';

            if (is_array($promotion['campaign_template']) && isset($promotion['campaign_template']['name'])) {
                $promotion['campaign_template_name'] = $promotion['campaign_template']['name'];
            }

            $model = $collection->getItemById($promotion['slug']);

            if ($model) {
                $modelData = $model->getData();

                // Setup default values for comparing
                $promotion['expire_time'] = $modelData['expire_time'];
                $promotion['width'] = isset($promotion['width']) ? $promotion['width'] : 0;
                $promotion['height'] = isset($promotion['height']) ? $promotion['height'] : 0;

                $diff = array_diff_assoc($modelData, $promotion);

                // Continue if no difference
                if (empty($diff)) {
                    continue;
                }
            }

            // Save new promotion or update only if changed

            /** @var RapidCampaign_Promotions_Model_Promotions $model */
            $model = Mage::getModel('rapidcampaign_promotions/promotions');
            $model->setData($promotion);

            try {
                $model->save();
            } catch (Exception $e) {
                continue;
            }
        }

        $dateTime = new DateTime();
        $dateTime->modify('+' . self::API_CACHE_TIME . 'seconds');

        // Update expiry time of whole collection
        $collection->updateExpireTime($dateTime);

        return true;
    }
}
