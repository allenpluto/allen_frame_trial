-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 05, 2017 at 05:39 PM
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
-- Table structure for table `listingfeatured`
--

CREATE TABLE IF NOT EXISTS `listingfeatured` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_start` datetime NOT NULL,
  `date_end` datetime NOT NULL,
  `listing_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Listing featured period' AUTO_INCREMENT=28 ;

--
-- Dumping data for table `listingfeatured`
--

INSERT INTO `listingfeatured` (`id`, `date_start`, `date_end`, `listing_id`) VALUES
(16, '2015-08-05 00:00:00', '2017-12-05 00:00:00', 111869),
(17, '2015-08-05 00:00:00', '2017-12-05 00:00:00', 72033),
(18, '2015-08-05 00:00:00', '2017-12-05 00:00:00', 11760),
(19, '2015-08-05 00:00:00', '2017-12-05 00:00:00', 124078),
(20, '2015-08-05 00:00:00', '2017-12-05 00:00:00', 104987),
(21, '2015-08-05 00:00:00', '2015-12-05 00:00:00', 65100),
(22, '2015-08-05 00:00:00', '2017-12-05 00:00:00', 65217),
(23, '2015-08-05 00:00:00', '2017-12-05 00:00:00', 73052),
(24, '2016-02-02 00:00:00', '2015-12-05 00:00:00', 89791),
(25, '2016-02-02 00:00:00', '2015-12-05 00:00:00', 104165),
(26, '2016-02-02 00:00:00', '2017-12-05 00:00:00', 65602),
(27, '2016-02-03 00:00:00', '2015-12-05 00:00:00', 104201);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
