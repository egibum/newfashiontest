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

class ET_FeedSalidzini_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * @return int
     */
    public function isAutoXmlGenerationEnabled()
    {
        return (int)Mage::getStoreConfig('feedsalidzini/generate/active');
    }

    /**
     * @return array
     */
    public function getParsedXmlGenerationTime()
    {
        $time = $this->getXmlGenerationTime();
        $time = explode(',', $time);

        return $time;
    }

    /**
     * @return string
     */
    public function getXmlGenerationTime()
    {
        return Mage::getStoreConfig('feedsalidzini/generate/time');
    }

    /**
     * @return array
     */
    public function getParsedXmlGenerationTimeAdditional()
    {
        $timeAdditional = unserialize($this->getXmlGenerationTimeAdditional());
        $parsedTime = array();

        for ($i = 1; $i < count($timeAdditional['hour']); $i++) {
            $parsedTime[] = array(
                $timeAdditional['hour'][$i],
                $timeAdditional['min'][$i],
                $timeAdditional['sec'][$i],
            );
        }

        return $parsedTime;
    }

    /**
     * @return string
     */
    public function getXmlGenerationTimeAdditional()
    {
        return Mage::getStoreConfig('feedsalidzini/generate/timeadditional');
    }
}