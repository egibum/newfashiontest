<?php
/**
 * "Seo Layered Navigation" extension for "Magento Commerce" by "EcommerceTeam (www.ecommerce-team.com)"
 *
 * @category     Extension
 * @copyright    Copyright (c) 2014 EcommerceTeam (http://www.ecommerce-team.com)
 * @author       EcommerceTeam
 * @version      4.0.0
 */

$installer = $this;
$installer->startSetup();
$installer->run("
CREATE TABLE IF NOT EXISTS `{$this->getTable('ecommerceteam_page_meta')}` (
  `page_id` int(11) NOT NULL auto_increment,
  `url` varchar(255) NOT NULL,
  `meta_title` varchar(255) NOT NULL,
  `meta_keywords` text,
  `meta_description` text,
  `page_title` varchar(255) NOT NULL,
  `robots` varchar(255) NOT NULL,
  PRIMARY KEY (`page_id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8
");
$installer->endSetup();
