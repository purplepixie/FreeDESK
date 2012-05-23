-- FreeDESK SQL Upgrade Script -- PurplePixie Systems/David Cutting
-- 
-- SHOW TABLES
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
