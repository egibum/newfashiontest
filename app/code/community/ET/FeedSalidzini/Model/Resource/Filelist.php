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

if (version_compare(Mage::getVersion(), '1.6', '<')) {
    class ET_FeedSalidzini_Model_Resource_Filelist extends Mage_Core_Model_Mysql4_Abstract
    {
        /**
         * Init resource model
         *
         */
        protected function _construct()
        {
            $this->_init('feedsalidzini/filelist', 'xmlfile_id');
        }
    }
} else {
    class ET_FeedSalidzini_Model_Resource_Filelist extends Mage_Core_Model_Resource_Db_Abstract
    {
        /**
         * Init resource model
         *
         */
        protected function _construct()
        {
            $this->_init('feedsalidzini/filelist', 'xmlfile_id');
        }
    }
}