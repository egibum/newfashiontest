<?php
/**
 * NOTICE OF LICENSE
 *
 * You may not give, sell, distribute, sub-license, rent, lease or lend
 * any portion of the Software or Documentation to anyone.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade to newer
 * versions in the future.
 *
 * @category   ET
 * @package    ET_FeedSalidzini
 * @copyright  Copyright (c) 2012 ET Web Solutions (http://etwebsolutions.com)
 * @contacts   support@etwebsolutions.com
 * @license    http://shop.etwebsolutions.com/etws-license-commercial-v1/   ETWS Commercial License (ECL1)
 */

class ET_FeedSalidzini_Model_Observer
{
    public function generateFeedsByCron()
    {
        $errors = array();

        // check if scheduled generation enabled
        if (!Mage::getStoreConfigFlag("feedsalidzini/generate/active")) {
            return;
        }

        $collection = Mage::getModel('feedsalidzini/feedsalidzini')->getCollection();
        foreach ($collection as $sitemap) {
            /* @var $sitemap ET_FeedSalidzini_Model_Feedsalidzini */

            try {
                $sitemap->generateXml();
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        if ($errors && Mage::getStoreConfig("feedsalidzini/generate/error_email")) {
            $translate = Mage::getSingleton('core/translate');
            /* @var $translate Mage_Core_Model_Translate */
            $translate->setTranslateInline(false);

            $emailTemplate = Mage::getModel('core/email_template');
            /* @var $emailTemplate Mage_Core_Model_Email_Template */
            $emailTemplate->setDesignConfig(array('area' => 'backend'))
                ->sendTransactional(
                    Mage::getStoreConfig("feedsalidzini/generate/template"),
                    Mage::getStoreConfig("feedsalidzini/generate/error_email_identity"),
                    Mage::getStoreConfig("feedsalidzini/generate/error_email"),
                    null,
                    array('warnings' => join("\n", $errors))
                );

            $translate->setTranslateInline(true);
        }
    }

    /**
     * Event:  default
     * @param $observer Varien_Event_Observer
     */
    public function scheduleFeedGeneration(Varien_Event_Observer $observer)
    {
        /* @var $helper ET_FeedSalidzini_Helper_Data */
        $helper = Mage::helper('feedsalidzini');

        $isAutoGenerationEnabled = $helper->isAutoXmlGenerationEnabled();

        if ($isAutoGenerationEnabled) {
            $modelNode = 'feedsalidzini/observer::generateFeedsByCron';

            //Setting cron job
            $xmlGenerationTime = $helper->getParsedXmlGenerationTime();

            if (is_array($xmlGenerationTime)) {
                $cronStr = (int)$xmlGenerationTime[1] . ' ' . (int)$xmlGenerationTime[0] . ' * * *';

                Mage::getConfig()->setNode('crontab/jobs/et_feedsalidzini_generate_feeds/schedule/cron_expr',
                    $cronStr);
                Mage::getConfig()->setNode('crontab/jobs/et_feedsalidzini_generate_feeds/run/model',
                    $modelNode);
            }

            //Setting additional cron jobs
            $timeAdditional = $helper->getParsedXmlGenerationTimeAdditional();

            if (is_array($timeAdditional) && count($timeAdditional) > 0) {
                $i = 1;
                foreach ($timeAdditional as $time) {
                    $cronStr = (int)$time[1] . ' ' . (int)$time[0] . ' * * *'; // minutes - hour - * * *

                    Mage::getConfig()->setNode('crontab/jobs/et_feedsalidzini_generate_feeds_' . $i
                        . '/schedule/cron_expr',
                        $cronStr);
                    Mage::getConfig()->setNode('crontab/jobs/et_feedsalidzini_generate_feeds_' . $i . '/run/model',
                        $modelNode);
                    $i++;
                }
            }
        }
    }
}