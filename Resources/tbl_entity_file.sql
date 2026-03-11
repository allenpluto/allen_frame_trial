-- phpMyAdmin SQL Dump
-- version 4.7.9
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 03, 2025 at 05:01 AM
-- Server version: 5.7.21
-- PHP Version: 5.6.35

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
    `image_width` int(11) NOT NULL DEFAULT '0',
    `image_height` int(11) NOT NULL DEFAULT '0',
    `extension` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `mime` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `creator` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `created_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
    `last_modified_by` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `modified_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
    `meta_property` text COLLATE utf8_unicode_ci NOT NULL,
    `source_file` varchar(500) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `source_root` varchar(500) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'E:\\',
    PRIMARY KEY (`id`),
    UNIQUE KEY `source_file` (`source_file`)
    ) ENGINE=InnoDB AUTO_INCREMENT=228874 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
COMMIT;
