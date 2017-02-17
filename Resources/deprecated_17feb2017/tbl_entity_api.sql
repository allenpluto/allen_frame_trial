-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 25, 2016 at 12:50 AM
-- Server version: 5.6.12-log
-- PHP Version: 5.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `allen_frame_trial`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_entity_api`
--

DROP TABLE IF EXISTS `tbl_entity_api`;
CREATE TABLE IF NOT EXISTS `tbl_entity_api` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `friendly_uri` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `alternate_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `description` varchar(500) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `image_id` int(11) NOT NULL DEFAULT '0',
  `enter_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `password` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=10004 ;

--
-- Dumping data for table `tbl_entity_api`
--

INSERT INTO `tbl_entity_api` (`id`, `friendly_uri`, `name`, `alternate_name`, `description`, `image_id`, `enter_time`, `update_time`, `password`) VALUES
(10001, '', 'Top4', '', '', 0, '2016-10-16 21:52:56', '2016-10-17 04:44:29', '0328d7d6cd562906ccdaf520e27f335854d41d7ca22f72396eb12573d3f04a2a'),
(10002, '', 'Haystack', '', '', 0, '2016-10-17 03:42:38', '2016-11-07 06:37:44', 'ea8387a5ed198ddc2d568fca8f61f57af200f15cbe90bc698ad2aa9fdeaedf6f'),
(10003, '', 'Crazy Domain', 'crazydomain', '', 0, '2016-11-15 05:46:15', '2016-11-25 00:24:54', '4533a2cf28454dec7f71880550120faf25a412946bc795382f845b594bc45057');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
