-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 05, 2017 at 05:24 PM
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
-- Table structure for table `tbl_entity_place`
--

CREATE TABLE IF NOT EXISTS `tbl_entity_place` (
  `id` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `friendly_uri` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `alternate_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `description` varchar(500) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `image_id` int(11) NOT NULL DEFAULT '0',
  `enter_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `street_number` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `route` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `locality` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `sublocality` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `colloquial_area` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `postal_code` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `administrative_area_level_2` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `administrative_area_level_1` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `country` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `location_latitude` decimal(11,8) NOT NULL DEFAULT '200.00000000',
  `location_longitude` decimal(11,8) NOT NULL DEFAULT '200.00000000',
  `viewport_northeast_latitude` decimal(11,8) NOT NULL DEFAULT '200.00000000',
  `viewport_northeast_longitude` decimal(11,8) NOT NULL DEFAULT '200.00000000',
  `viewport_southwest_latitude` decimal(11,8) NOT NULL DEFAULT '200.00000000',
  `viewport_southwest_longitude` decimal(11,8) NOT NULL DEFAULT '200.00000000',
  `bounds_northeast_latitude` decimal(11,8) NOT NULL DEFAULT '200.00000000',
  `bounds_northeast_longitude` decimal(11,8) NOT NULL DEFAULT '200.00000000',
  `bounds_southwest_latitude` decimal(11,8) NOT NULL DEFAULT '200.00000000',
  `bounds_southwest_longitude` decimal(11,8) NOT NULL DEFAULT '200.00000000',
  `icon` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `formatted_address` varchar(500) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `formatted_phone_number` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `international_phone_number` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `utc_offset` int(3) NOT NULL DEFAULT '800',
  `opening_hours` text COLLATE utf8_unicode_ci,
  `permanently_closed` int(1) NOT NULL DEFAULT '0',
  `photos` text COLLATE utf8_unicode_ci,
  `rating` decimal(2,1) NOT NULL DEFAULT '0.0',
  `reviews` text COLLATE utf8_unicode_ci,
  `types` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `url` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `website` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
