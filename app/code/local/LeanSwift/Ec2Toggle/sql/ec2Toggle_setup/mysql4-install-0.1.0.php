<?php
/**
 * LeanSwift Ec2Toggle Extension
 *
 * NOTICE OF LICENSE
 *
 * GNU GENERAL PUBLIC LICENSE
 * Version 3, 29 June 2007

 * Copyright (C) 2007 Free Software Foundation, Inc. <http://fsf.org/>
 * Everyone is permitted to copy and distribute verbatim copies
 * of this license document, but changing it is not allowed.
 *
 * @category   Ec2Toggle
 * @package    LeanSwift
 * @author     Janagiram <janagiram@leanswift.com>
 * @version    1.0
 */
$installer = $this;
$installer->startSetup();
$installer->run("
  DROP TABLE IF EXISTS ls_ec2Toggle;
  CREATE TABLE ls_ec2Toggle (
   id int(11) NOT NULL auto_increment,
   instance_id text NOT NULL default '',
   server_name text NOT NULL default '',
   region text NOT NULL default '',
   status smallint(6) NOT NULL default '0',
   current_status text NOT NULL default '',
   update_time datetime NULL,
   PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
$installer->endSetup();
