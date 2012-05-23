-- FreeDESK SQL Upgrade Script -- PurplePixie Systems/David Cutting
-- 
-- SHOW TABLES
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
