-- FreeDESK SQL Upgrade Script -- PurplePixie Systems/David Cutting
-- 
-- SHOW TABLES
-- Table: permgroup
-- DESCRIBE permgroup
ALTER TABLE `permgroup` CHANGE `permgroupid` `permgroupid` bigint(20) unsigned NOT NULL auto_increment;
ALTER TABLE `permgroup` ADD `permgroupid` bigint(20) unsigned NOT NULL auto_increment;
ALTER TABLE `permgroup` ADD PRIMARY KEY( `permgroupid` );
ALTER TABLE `permgroup` CHANGE `groupname` `groupname` varchar(254) NOT NULL;
ALTER TABLE `permgroup` ADD `groupname` varchar(254) NOT NULL;
-- 
-- Table: permissions
-- DESCRIBE permissions
ALTER TABLE `permissions` CHANGE `permissionid` `permissionid` bigint(20) NOT NULL auto_increment;
ALTER TABLE `permissions` ADD `permissionid` bigint(20) NOT NULL auto_increment;
ALTER TABLE `permissions` ADD PRIMARY KEY( `permissionid` );
ALTER TABLE `permissions` CHANGE `permissiontype` `permissiontype` varchar(16) NOT NULL;
ALTER TABLE `permissions` ADD `permissiontype` varchar(16) NOT NULL;
CREATE INDEX `permissiontype` ON `permissions` ( `permissiontype` );
ALTER TABLE `permissions` CHANGE `permission` `permission` varchar(254) NOT NULL;
ALTER TABLE `permissions` ADD `permission` varchar(254) NOT NULL;
ALTER TABLE `permissions` CHANGE `usergroupid` `usergroupid` varchar(254) NOT NULL;
ALTER TABLE `permissions` ADD `usergroupid` varchar(254) NOT NULL;
ALTER TABLE `permissions` CHANGE `allowed` `allowed` tinyint(4) NOT NULL;
ALTER TABLE `permissions` ADD `allowed` tinyint(4) NOT NULL;
-- 
-- Table: request
-- DESCRIBE request
ALTER TABLE `request` CHANGE `requestid` `requestid` bigint(20) NOT NULL auto_increment;
ALTER TABLE `request` ADD `requestid` bigint(20) NOT NULL auto_increment;
ALTER TABLE `request` ADD PRIMARY KEY( `requestid` );
ALTER TABLE `request` CHANGE `customer` `customer` bigint(20) NOT NULL;
ALTER TABLE `request` ADD `customer` bigint(20) NOT NULL;
CREATE INDEX `customer` ON `request` ( `customer` );
ALTER TABLE `request` CHANGE `assignteam` `assignteam` bigint(20) NOT NULL;
ALTER TABLE `request` ADD `assignteam` bigint(20) NOT NULL;
ALTER TABLE `request` CHANGE `assignuser` `assignuser` varchar(254) NOT NULL;
ALTER TABLE `request` ADD `assignuser` varchar(254) NOT NULL;
ALTER TABLE `request` CHANGE `class` `class` int(11) NOT NULL;
ALTER TABLE `request` ADD `class` int(11) NOT NULL;
ALTER TABLE `request` CHANGE `openeddt` `openeddt` datetime NOT NULL;
ALTER TABLE `request` ADD `openeddt` datetime NOT NULL;
ALTER TABLE `request` CHANGE `status` `status` int(11) NOT NULL;
ALTER TABLE `request` ADD `status` int(11) NOT NULL;
-- 
-- Table: requestclass
-- DESCRIBE requestclass
ALTER TABLE `requestclass` CHANGE `classid` `classid` int(10) unsigned NOT NULL auto_increment;
ALTER TABLE `requestclass` ADD `classid` int(10) unsigned NOT NULL auto_increment;
ALTER TABLE `requestclass` ADD PRIMARY KEY( `classid` );
ALTER TABLE `requestclass` CHANGE `classname` `classname` varchar(254) NOT NULL;
ALTER TABLE `requestclass` ADD `classname` varchar(254) NOT NULL;
ALTER TABLE `requestclass` CHANGE `classclass` `classclass` varchar(254) NOT NULL;
ALTER TABLE `requestclass` ADD `classclass` varchar(254) NOT NULL;
-- 
-- Table: session
-- DESCRIBE session
ALTER TABLE `session` CHANGE `session_id` `session_id` varchar(254) NOT NULL;
ALTER TABLE `session` ADD `session_id` varchar(254) NOT NULL;
ALTER TABLE `session` ADD PRIMARY KEY( `session_id` );
ALTER TABLE `session` CHANGE `username` `username` varchar(254) NOT NULL;
ALTER TABLE `session` ADD `username` varchar(254) NOT NULL;
ALTER TABLE `session` CHANGE `sessiontype` `sessiontype` int(11) NOT NULL DEFAULT '-1';
ALTER TABLE `session` ADD `sessiontype` int(11) NOT NULL DEFAULT '-1';
ALTER TABLE `session` CHANGE `created_dt` `created_dt` datetime NOT NULL;
ALTER TABLE `session` ADD `created_dt` datetime NOT NULL;
ALTER TABLE `session` CHANGE `updated_dt` `updated_dt` datetime NOT NULL;
ALTER TABLE `session` ADD `updated_dt` datetime NOT NULL;
ALTER TABLE `session` CHANGE `expires_dt` `expires_dt` datetime NOT NULL;
ALTER TABLE `session` ADD `expires_dt` datetime NOT NULL;
-- 
-- Table: status
-- DESCRIBE status
ALTER TABLE `status` CHANGE `status` `status` int(11) NOT NULL;
ALTER TABLE `status` ADD `status` int(11) NOT NULL;
ALTER TABLE `status` ADD PRIMARY KEY( `status` );
ALTER TABLE `status` CHANGE `description` `description` varchar(254) NOT NULL;
ALTER TABLE `status` ADD `description` varchar(254) NOT NULL;
-- 
-- Table: sysconfig
-- DESCRIBE sysconfig
ALTER TABLE `sysconfig` CHANGE `sc_option` `sc_option` varchar(254) NOT NULL;
ALTER TABLE `sysconfig` ADD `sc_option` varchar(254) NOT NULL;
ALTER TABLE `sysconfig` ADD PRIMARY KEY( `sc_option` );
ALTER TABLE `sysconfig` CHANGE `sc_value` `sc_value` varchar(254) NOT NULL;
ALTER TABLE `sysconfig` ADD `sc_value` varchar(254) NOT NULL;
-- 
-- Table: syslog
-- DESCRIBE syslog
ALTER TABLE `syslog` CHANGE `event_id` `event_id` bigint(20) unsigned NOT NULL auto_increment;
ALTER TABLE `syslog` ADD `event_id` bigint(20) unsigned NOT NULL auto_increment;
ALTER TABLE `syslog` ADD PRIMARY KEY( `event_id` );
ALTER TABLE `syslog` CHANGE `event_dt` `event_dt` datetime NOT NULL;
ALTER TABLE `syslog` ADD `event_dt` datetime NOT NULL;
ALTER TABLE `syslog` CHANGE `event` `event` varchar(254) NOT NULL;
ALTER TABLE `syslog` ADD `event` varchar(254) NOT NULL;
ALTER TABLE `syslog` CHANGE `event_class` `event_class` varchar(128) NOT NULL;
ALTER TABLE `syslog` ADD `event_class` varchar(128) NOT NULL;
ALTER TABLE `syslog` CHANGE `event_type` `event_type` varchar(128) NOT NULL;
ALTER TABLE `syslog` ADD `event_type` varchar(128) NOT NULL;
ALTER TABLE `syslog` CHANGE `event_level` `event_level` int(11) NOT NULL;
ALTER TABLE `syslog` ADD `event_level` int(11) NOT NULL;
-- 
-- Table: team
-- DESCRIBE team
ALTER TABLE `team` CHANGE `teamid` `teamid` bigint(20) unsigned NOT NULL auto_increment;
ALTER TABLE `team` ADD `teamid` bigint(20) unsigned NOT NULL auto_increment;
ALTER TABLE `team` ADD PRIMARY KEY( `teamid` );
ALTER TABLE `team` CHANGE `teamname` `teamname` varchar(254) NOT NULL;
ALTER TABLE `team` ADD `teamname` varchar(254) NOT NULL;
-- 
-- Table: teamuserlink
-- DESCRIBE teamuserlink
ALTER TABLE `teamuserlink` CHANGE `linkid` `linkid` bigint(20) unsigned NOT NULL auto_increment;
ALTER TABLE `teamuserlink` ADD `linkid` bigint(20) unsigned NOT NULL auto_increment;
ALTER TABLE `teamuserlink` ADD PRIMARY KEY( `linkid` );
ALTER TABLE `teamuserlink` CHANGE `teamid` `teamid` bigint(20) unsigned NOT NULL;
ALTER TABLE `teamuserlink` ADD `teamid` bigint(20) unsigned NOT NULL;
CREATE INDEX `teamid` ON `teamuserlink` ( `teamid` );
ALTER TABLE `teamuserlink` CHANGE `username` `username` varchar(254) NOT NULL;
ALTER TABLE `teamuserlink` ADD `username` varchar(254) NOT NULL;
-- 
-- Table: update
-- DESCRIBE update
-- 
-- Table: user
-- DESCRIBE user
ALTER TABLE `user` CHANGE `username` `username` varchar(254) NOT NULL;
ALTER TABLE `user` ADD `username` varchar(254) NOT NULL;
ALTER TABLE `user` ADD PRIMARY KEY( `username` );
ALTER TABLE `user` CHANGE `password` `password` varchar(254) NOT NULL;
ALTER TABLE `user` ADD `password` varchar(254) NOT NULL;
ALTER TABLE `user` CHANGE `realname` `realname` varchar(254) NOT NULL;
ALTER TABLE `user` ADD `realname` varchar(254) NOT NULL;
ALTER TABLE `user` CHANGE `email` `email` varchar(254) NOT NULL;
ALTER TABLE `user` ADD `email` varchar(254) NOT NULL;
ALTER TABLE `user` CHANGE `permgroup` `permgroup` bigint(20) unsigned NOT NULL;
ALTER TABLE `user` ADD `permgroup` bigint(20) unsigned NOT NULL;
ALTER TABLE `user` CHANGE `authtype` `authtype` varchar(254) NOT NULL;
ALTER TABLE `user` ADD `authtype` varchar(254) NOT NULL;
ALTER TABLE `user` CHANGE `sparefield0` `sparefield0` varchar(254) NOT NULL;
ALTER TABLE `user` ADD `sparefield0` varchar(254) NOT NULL;
ALTER TABLE `user` CHANGE `sparefield1` `sparefield1` varchar(254) NOT NULL;
ALTER TABLE `user` ADD `sparefield1` varchar(254) NOT NULL;
ALTER TABLE `user` CHANGE `sparefield2` `sparefield2` varchar(254) NOT NULL;
ALTER TABLE `user` ADD `sparefield2` varchar(254) NOT NULL;
ALTER TABLE `user` CHANGE `sparefield3` `sparefield3` varchar(254) NOT NULL;
ALTER TABLE `user` ADD `sparefield3` varchar(254) NOT NULL;
ALTER TABLE `user` CHANGE `sparefield4` `sparefield4` varchar(254) NOT NULL;
ALTER TABLE `user` ADD `sparefield4` varchar(254) NOT NULL;
ALTER TABLE `user` CHANGE `sparefield5` `sparefield5` varchar(254) NOT NULL;
ALTER TABLE `user` ADD `sparefield5` varchar(254) NOT NULL;
ALTER TABLE `user` CHANGE `sparefield6` `sparefield6` varchar(254) NOT NULL;
ALTER TABLE `user` ADD `sparefield6` varchar(254) NOT NULL;
ALTER TABLE `user` CHANGE `sparefield7` `sparefield7` varchar(254) NOT NULL;
ALTER TABLE `user` ADD `sparefield7` varchar(254) NOT NULL;
ALTER TABLE `user` CHANGE `sparefield8` `sparefield8` varchar(254) NOT NULL;
ALTER TABLE `user` ADD `sparefield8` varchar(254) NOT NULL;
ALTER TABLE `user` CHANGE `sparefield9` `sparefield9` varchar(254) NOT NULL;
ALTER TABLE `user` ADD `sparefield9` varchar(254) NOT NULL;
-- 
