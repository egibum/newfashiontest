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
ALTER TABLE `{$this->getTable('ecommerceteam_page_meta')}`
ADD COLUMN `cms_block` int
");
$installer->run("
ALTER TABLE `{$this->getTable('ecommerceteam_sln_attribute_data')}`
ADD COLUMN `options_limit` smallint(5)
");

$installer->endSetup();
