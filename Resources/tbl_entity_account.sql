-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 19, 2017 at 02:47 PM
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
-- Table structure for table `tbl_entity_account`
--

CREATE TABLE IF NOT EXISTS `tbl_entity_account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `friendly_uri` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `alternate_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `description` varchar(500) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `image_id` int(11) NOT NULL DEFAULT '0',
  `enter_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `lock_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `password` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `first_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `last_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `company` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `subpremise` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `street_address` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `street_address_alt` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `place_id` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `telephone` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `fax_number` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `website_uri` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `account_type` int(11) NOT NULL DEFAULT '0' COMMENT 'INDIVIDUAL=0,BUSINESS=1,BRAND=2',
  `signup_as` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `other_company` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `other_company_phone` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `banner_id` int(11) NOT NULL DEFAULT '0',
  `credit_points` int(11) NOT NULL DEFAULT '0',
  `number_views` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=192 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
