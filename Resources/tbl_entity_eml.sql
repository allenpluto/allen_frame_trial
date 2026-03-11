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

DROP TABLE IF EXISTS `tbl_entity_eml`;
CREATE TABLE IF NOT EXISTS `tbl_entity_eml` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `alternate_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `description` varchar(500) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `image_id` int(11) NOT NULL DEFAULT '0',
    `enter_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `from` varchar(500) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `to` varchar(500) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `reply_to` varchar(500) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `cc` varchar(500) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `bcc` varchar(500) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `message_id` varchar(500) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `subject` varchar(1000) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `thread_index` varchar(500) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `thread_topic` varchar(1000) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `header` text COLLATE utf8_unicode_ci NULL,
    `attachments` text COLLATE utf8_unicode_ci NULL,
    `content_html` text COLLATE utf8_unicode_ci NULL,
    `content_text` text COLLATE utf8_unicode_ci NULL,
    `has_html` tinyint(1) NOT NULL DEFAULT '0',
    `has_attachment` int(11) NOT NULL DEFAULT '0',
    `source_file` varchar(500) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `source_root` varchar(500) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'E:\\',
    `target_file` varchar(500) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY (`id`),
    UNIQUE KEY `source` (`source_file`,`source_root`)
    ) ENGINE=InnoDB AUTO_INCREMENT=228874 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
COMMIT;
