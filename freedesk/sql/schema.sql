-- MySQL dump 10.13  Distrib 5.5.20, for Linux (i686)
--
-- Host: localhost    Database: freedesk
-- ------------------------------------------------------
-- Server version	5.5.20

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `session`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `session` (
  `session_id` varchar(254) NOT NULL,
  `username` varchar(254) NOT NULL,
  `sessiontype` int(11) NOT NULL DEFAULT '-1',
  `created_dt` datetime NOT NULL,
  `updated_dt` datetime NOT NULL,
  `expires_dt` datetime NOT NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sysconfig`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sysconfig` (
  `sc_option` varchar(254) NOT NULL,
  `sc_value` varchar(254) NOT NULL,
  PRIMARY KEY (`sc_option`),
  UNIQUE KEY `sc_option` (`sc_option`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `syslog`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `syslog` (
  `event_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `event_dt` datetime NOT NULL,
  `event` varchar(254) NOT NULL,
  `event_class` varchar(128) NOT NULL,
  `event_type` varchar(128) NOT NULL,
  `event_level` int(11) NOT NULL,
  PRIMARY KEY (`event_id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `username` varchar(254) NOT NULL,
  `password` varchar(254) NOT NULL,
  `realname` varchar(254) NOT NULL,
  `email` varchar(254) NOT NULL,
  `authtype` varchar(254) NOT NULL,
  `sparefield0` varchar(254) NOT NULL,
  `sparefield1` varchar(254) NOT NULL,
  `sparefield2` varchar(254) NOT NULL,
  `sparefield3` varchar(254) NOT NULL,
  `sparefield4` varchar(254) NOT NULL,
  `sparefield5` varchar(254) NOT NULL,
  `sparefield6` varchar(254) NOT NULL,
  `sparefield7` varchar(254) NOT NULL,
  `sparefield8` varchar(254) NOT NULL,
  `sparefield9` varchar(254) NOT NULL,
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-05-28 17:44:15
