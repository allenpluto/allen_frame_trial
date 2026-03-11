-- phpMyAdmin SQL Dump
-- version 4.7.9
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 10, 2024 at 10:45 PM
-- Server version: 5.7.21
-- PHP Version: 7.0.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `allen_frame`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_entity_file`
--

DROP TABLE IF EXISTS `tbl_entity_file`;
CREATE TABLE IF NOT EXISTS `tbl_entity_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `alternate_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `description` varchar(500) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `image_id` int(11) NOT NULL DEFAULT '0',
  `enter_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `source_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_dir` tinyint(1) NOT NULL DEFAULT '0',
  `file_scanned` tinyint(4) NOT NULL DEFAULT '0',
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `file_size` bigint(20) NOT NULL DEFAULT '0',
  `extension` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `mime` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `source_file` varchar(500) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
COMMIT;
