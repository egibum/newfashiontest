<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('brandlogo')};
CREATE TABLE {$this->getTable('brandlogo')} (
  `brandlogo_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `filename` varchar(255) NOT NULL default '',
  `url` varchar(255) NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  PRIMARY KEY (`brandlogo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 
