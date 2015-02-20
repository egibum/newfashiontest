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

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();


$installer->run("
-- DROP TABLE IF EXISTS `{$this->getTable('et_feedsalidzini')}`;
CREATE TABLE IF NOT EXISTS `{$this->getTable('et_feedsalidzini')}` (
  `xmlfile_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Xml file Id',
  `xmlfile_filename` varchar(32) DEFAULT NULL COMMENT 'Xml Filename',
  `xmlfile_path` varchar(255) DEFAULT NULL COMMENT 'Xml file Path',
  `xmlfile_time` timestamp NULL DEFAULT NULL COMMENT 'Xml file Time',
  `store_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Store id',
  PRIMARY KEY  (`xmlfile_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Xml files data. ET Feed Salidzini';

ALTER TABLE {$this->getTable('et_feedsalidzini')}
    ADD CONSTRAINT `FK_FEEDSALIDZINI_STORE` FOREIGN KEY (`store_id`)
    REFERENCES {$this->getTable('core_store')} (`store_id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE;
");

$installer->endSetup();
