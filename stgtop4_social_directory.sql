-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 22, 2014 at 07:31 AM
-- Server version: 5.6.12-log
-- PHP Version: 5.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `stgtop4_social_directory`
--
CREATE DATABASE IF NOT EXISTS `stgtop4_social_directory` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `stgtop4_social_directory`;

-- --------------------------------------------------------

--
-- Table structure for table `top4_image_object`
--

DROP TABLE IF EXISTS `top4_image_object`;
CREATE TABLE IF NOT EXISTS `top4_image_object` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Universal ID',
  `friendly_url` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `alternate_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Text - An alias for the item.',
  `description` varchar(500) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Text - A short description of the item.',
  `image_id` int(11) NOT NULL DEFAULT '-1',
  `enter_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `caption` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Caption. Image title attribute.',
  `exif_data` text COLLATE utf8_unicode_ci,
  `width` int(11) NOT NULL DEFAULT '0',
  `height` int(11) NOT NULL DEFAULT '0',
  `source_data` text COLLATE utf8_unicode_ci NOT NULL,
  `start_x` int(11) NOT NULL DEFAULT '-1',
  `start_y` int(11) NOT NULL DEFAULT '-1',
  `end_x` int(11) NOT NULL DEFAULT '-1',
  `end_y` int(11) NOT NULL DEFAULT '-1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `top4_image_object`
--

INSERT INTO `top4_image_object` (`id`, `friendly_url`, `name`, `alternate_name`, `description`, `image_id`, `enter_time`, `update_time`, `caption`, `exif_data`, `width`, `height`, `source_data`, `start_x`, `start_y`, `end_x`, `end_y`) VALUES
(1, 'some-friendly-url-1', 'Image 1', 'Photo', 'some test image', -1, '0000-00-00 00:00:00', '2014-12-17 03:52:10', 'Test Photo 1', NULL, 280, 233, 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/2wBDAA0JCgsKCA0LCgsODg0PEyAVExISEyccHhcgLikxMC4pLSwzOko+MzZGNywtQFdBRkxOUlNSMj5aYVpQYEpRUk//2wBDAQ4ODhMREyYVFSZPNS01T09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT0//wAARCADpARgDASIAAhEBAxEB/8QAHwAAAQUBAQEBAQEAAAAAAAAAAAECAwQFBgcICQoL/8QAtRAAAgEDAwIEAwUFBAQAAAF9AQIDAAQRBRIhMUEGE1FhByJxFDKBkaEII0KxwRVS0fAkM2JyggkKFhcYGRolJicoKSo0NTY3ODk6Q0RFRkdISUpTVFVWV1hZWmNkZWZnaGlqc3R1dnd4eXqDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uHi4+Tl5ufo6erx8vP09fb3+Pn6/8QAHwEAAwEBAQEBAQEBAQAAAAAAAAECAwQFBgcICQoL/8QAtREAAgECBAQDBAcFBAQAAQJ3AAECAxEEBSExBhJBUQdhcRMiMoEIFEKRobHBCSMzUvAVYnLRChYkNOEl8RcYGRomJygpKjU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6goOEhYaHiImKkpOUlZaXmJmaoqOkpaanqKmqsrO0tba3uLm6wsPExcbHyMnK0tPU1dbX2Nna4uPk5ebn6Onq8vP09fb3+Pn6/9oADAMBAAIRAxEAPwD06iiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKiuXMdvI46hSRSbsBFcX9tbHEsnPoOabDqdpM21ZMH/AGhisKyt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fWnyI6NhxzTdPbyIQp+8eT9avl0lXDDNeDiFCrNuI9GUAMkAVtxTSrNFb/3Vyx9ayHiaNsryBzVuC+G7Mq4Y8bhWNF8js9Bx0NnfTHlCqSTwKqfaU27twxVOSSa9JSIEIoyTXZKrbYpyJ4c6hfj/AJ5R8/WtysLQhi4fHTFbtVhneHM92OG1woooroLCiiigAooooAKKKKAOb8R6VNJMt7aAl1+8B1+tZUl5qNyqxyQMXHG7Zg13NFa+0TjyyVyHC5xUejajMm94yB6EipI7KS3PMLE+uM12NFY1eaorJ2XkL2aOXRpiceU/5VYis7yXgJ5anua6CiudYddWCp+Zn2+lxRgmb96x9elcXrWnPpV+VAPkSHKH09q9EqpqVhDqNo0Ew6/dPdT612UJKk9NhygmrHn0cmKtxy1TvrO40y6Nvcrx/A/ZhSJJivR0kro5dYuzNZJferCTe9ZCS1Os1Q4lqRqrL708S+9ZizVIJveo5SuY0PN96aZao+d70hm96OUOYsZRXLqAGPU0x5feqzTVC83HWqURXHNw5d2ye3tWloGn/brj7VOv7mP7oP8AEapaXp82q3AwCtsp+d/X2FdvBDHbwrFEu1FGAKyrVLLlRdOPVmZdaKrktbttP909KzpLO7gPMZI9RzXUUV5ksNB6rQt00zlQ044MT/8AfJpwtp5z8kDZ/KuooqPqqe7F7PzObk0y7RM7MjuAaFF6U8hEZVPBAHX8a6Sij6pFbNofIinptn9kh+b77cmrlFFdMYqKsikrBRRRVDCiiigAooooAKKKKACiiigAooooAKKKKACiiigCrf2FvqFuYblAy9j3BriNU0C901y8StPb9mUcr9a9BpCARgjIrSnVlDYmUFLc8sjmDfdNTLJXbaj4c0++yxj8qQ/xpwawLjwjfREm2uElXsG4Ndca8Jb6GDpNbGYJqcJvenyaLq8Od9oT/usDUf8AZupf8+clac0H1I5ZDvOpDN71NFoWrzfdtQo9WYVo2vhG4cg3lyqr3VBzUupBdSlCTMQz5YKuWY9AOprZ0rw7cXbLLfgxQ9fL7t9a6Ow0axsB+5hBfu7ck1oVzzxF9ImsaSW5HBBHbxLFCgRFGABUlFFcxqFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAf/2Q==', 0, 0, 280, 233),
(2, 'some-friendly-url-2', 'Image 2', 'Photo', 'some test image', -1, '0000-00-00 00:00:00', '2014-12-17 03:52:10', 'Test Photo 2', NULL, 280, 233, 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/2wBDAA0JCgsKCA0LCgsODg0PEyAVExISEyccHhcgLikxMC4pLSwzOko+MzZGNywtQFdBRkxOUlNSMj5aYVpQYEpRUk//2wBDAQ4ODhMREyYVFSZPNS01T09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT0//wAARCADpARgDASIAAhEBAxEB/8QAHwAAAQUBAQEBAQEAAAAAAAAAAAECAwQFBgcICQoL/8QAtRAAAgEDAwIEAwUFBAQAAAF9AQIDAAQRBRIhMUEGE1FhByJxFDKBkaEII0KxwRVS0fAkM2JyggkKFhcYGRolJicoKSo0NTY3ODk6Q0RFRkdISUpTVFVWV1hZWmNkZWZnaGlqc3R1dnd4eXqDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uHi4+Tl5ufo6erx8vP09fb3+Pn6/8QAHwEAAwEBAQEBAQEBAQAAAAAAAAECAwQFBgcICQoL/8QAtREAAgECBAQDBAcFBAQAAQJ3AAECAxEEBSExBhJBUQdhcRMiMoEIFEKRobHBCSMzUvAVYnLRChYkNOEl8RcYGRomJygpKjU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6goOEhYaHiImKkpOUlZaXmJmaoqOkpaanqKmqsrO0tba3uLm6wsPExcbHyMnK0tPU1dbX2Nna4uPk5ebn6Onq8vP09fb3+Pn6/9oADAMBAAIRAxEAPwD06iiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKiuXMdvI46hSRSbsBFcX9tbHEsnPoOabDqdpM21ZMH/AGhisKyt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fWnyI6NhxzTdPbyIQp+8eT9avl0lXDDNeDiFCrNuI9GUAMkAVtxTSrNFb/3Vyx9ayHiaNsryBzVuC+G7Mq4Y8bhWNF8js9Bx0NnfTHlCqSTwKqfaU27twxVOSSa9JSIEIoyTXZKrbYpyJ4c6hfj/AJ5R8/WtysLQhi4fHTFbtVhneHM92OG1woooroLCiiigAooooAKKKKAOb8R6VNJMt7aAl1+8B1+tZUl5qNyqxyQMXHG7Zg13NFa+0TjyyVyHC5xUejajMm94yB6EipI7KS3PMLE+uM12NFY1eaorJ2XkL2aOXRpiceU/5VYis7yXgJ5anua6CiudYddWCp+Zn2+lxRgmb96x9elcXrWnPpV+VAPkSHKH09q9EqpqVhDqNo0Ew6/dPdT612UJKk9NhygmrHn0cmKtxy1TvrO40y6Nvcrx/A/ZhSJJivR0kro5dYuzNZJferCTe9ZCS1Os1Q4lqRqrL708S+9ZizVIJveo5SuY0PN96aZao+d70hm96OUOYsZRXLqAGPU0x5feqzTVC83HWqURXHNw5d2ye3tWloGn/brj7VOv7mP7oP8AEapaXp82q3AwCtsp+d/X2FdvBDHbwrFEu1FGAKyrVLLlRdOPVmZdaKrktbttP909KzpLO7gPMZI9RzXUUV5ksNB6rQt00zlQ044MT/8AfJpwtp5z8kDZ/KuooqPqqe7F7PzObk0y7RM7MjuAaFF6U8hEZVPBAHX8a6Sij6pFbNofIinptn9kh+b77cmrlFFdMYqKsikrBRRRVDCiiigAooooAKKKKACiiigAooooAKKKKACiiigCrf2FvqFuYblAy9j3BriNU0C901y8StPb9mUcr9a9BpCARgjIrSnVlDYmUFLc8sjmDfdNTLJXbaj4c0++yxj8qQ/xpwawLjwjfREm2uElXsG4Ndca8Jb6GDpNbGYJqcJvenyaLq8Od9oT/usDUf8AZupf8+clac0H1I5ZDvOpDN71NFoWrzfdtQo9WYVo2vhG4cg3lyqr3VBzUupBdSlCTMQz5YKuWY9AOprZ0rw7cXbLLfgxQ9fL7t9a6Ow0axsB+5hBfu7ck1oVzzxF9ImsaSW5HBBHbxLFCgRFGABUlFFcxqFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAf/2Q==', 0, 0, 280, 233),
(3, 'some-friendly-url-3', 'Image 3', 'Photo', 'some test image', -1, '0000-00-00 00:00:00', '2014-12-17 03:52:10', 'Test Photo 3', NULL, 280, 233, 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/2wBDAA0JCgsKCA0LCgsODg0PEyAVExISEyccHhcgLikxMC4pLSwzOko+MzZGNywtQFdBRkxOUlNSMj5aYVpQYEpRUk//2wBDAQ4ODhMREyYVFSZPNS01T09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT0//wAARCADpARgDASIAAhEBAxEB/8QAHwAAAQUBAQEBAQEAAAAAAAAAAAECAwQFBgcICQoL/8QAtRAAAgEDAwIEAwUFBAQAAAF9AQIDAAQRBRIhMUEGE1FhByJxFDKBkaEII0KxwRVS0fAkM2JyggkKFhcYGRolJicoKSo0NTY3ODk6Q0RFRkdISUpTVFVWV1hZWmNkZWZnaGlqc3R1dnd4eXqDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uHi4+Tl5ufo6erx8vP09fb3+Pn6/8QAHwEAAwEBAQEBAQEBAQAAAAAAAAECAwQFBgcICQoL/8QAtREAAgECBAQDBAcFBAQAAQJ3AAECAxEEBSExBhJBUQdhcRMiMoEIFEKRobHBCSMzUvAVYnLRChYkNOEl8RcYGRomJygpKjU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6goOEhYaHiImKkpOUlZaXmJmaoqOkpaanqKmqsrO0tba3uLm6wsPExcbHyMnK0tPU1dbX2Nna4uPk5ebn6Onq8vP09fb3+Pn6/9oADAMBAAIRAxEAPwD06iiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKiuXMdvI46hSRSbsBFcX9tbHEsnPoOabDqdpM21ZMH/AGhisKyt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fWnyI6NhxzTdPbyIQp+8eT9avl0lXDDNeDiFCrNuI9GUAMkAVtxTSrNFb/3Vyx9ayHiaNsryBzVuC+G7Mq4Y8bhWNF8js9Bx0NnfTHlCqSTwKqfaU27twxVOSSa9JSIEIoyTXZKrbYpyJ4c6hfj/AJ5R8/WtysLQhi4fHTFbtVhneHM92OG1woooroLCiiigAooooAKKKKAOb8R6VNJMt7aAl1+8B1+tZUl5qNyqxyQMXHG7Zg13NFa+0TjyyVyHC5xUejajMm94yB6EipI7KS3PMLE+uM12NFY1eaorJ2XkL2aOXRpiceU/5VYis7yXgJ5anua6CiudYddWCp+Zn2+lxRgmb96x9elcXrWnPpV+VAPkSHKH09q9EqpqVhDqNo0Ew6/dPdT612UJKk9NhygmrHn0cmKtxy1TvrO40y6Nvcrx/A/ZhSJJivR0kro5dYuzNZJferCTe9ZCS1Os1Q4lqRqrL708S+9ZizVIJveo5SuY0PN96aZao+d70hm96OUOYsZRXLqAGPU0x5feqzTVC83HWqURXHNw5d2ye3tWloGn/brj7VOv7mP7oP8AEapaXp82q3AwCtsp+d/X2FdvBDHbwrFEu1FGAKyrVLLlRdOPVmZdaKrktbttP909KzpLO7gPMZI9RzXUUV5ksNB6rQt00zlQ044MT/8AfJpwtp5z8kDZ/KuooqPqqe7F7PzObk0y7RM7MjuAaFF6U8hEZVPBAHX8a6Sij6pFbNofIinptn9kh+b77cmrlFFdMYqKsikrBRRRVDCiiigAooooAKKKKACiiigAooooAKKKKACiiigCrf2FvqFuYblAy9j3BriNU0C901y8StPb9mUcr9a9BpCARgjIrSnVlDYmUFLc8sjmDfdNTLJXbaj4c0++yxj8qQ/xpwawLjwjfREm2uElXsG4Ndca8Jb6GDpNbGYJqcJvenyaLq8Od9oT/usDUf8AZupf8+clac0H1I5ZDvOpDN71NFoWrzfdtQo9WYVo2vhG4cg3lyqr3VBzUupBdSlCTMQz5YKuWY9AOprZ0rw7cXbLLfgxQ9fL7t9a6Ow0axsB+5hBfu7ck1oVzzxF9ImsaSW5HBBHbxLFCgRFGABUlFFcxqFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAf/2Q==', 0, 0, 280, 233);

-- --------------------------------------------------------

--
-- Table structure for table `top4_image_variations`
--

DROP TABLE IF EXISTS `top4_image_variations`;
CREATE TABLE IF NOT EXISTS `top4_image_variations` (
  `id` int(11) NOT NULL,
  `image_id` int(11) NOT NULL,
  `type` varchar(100) NOT NULL,
  `start_x` double NOT NULL DEFAULT '0',
  `start_y` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `top4_person`
--

DROP TABLE IF EXISTS `top4_person`;
CREATE TABLE IF NOT EXISTS `top4_person` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `friendly_url` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `alternate_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `description` varchar(500) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `image_id` int(11) NOT NULL DEFAULT '-1',
  `enter_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `address_id` int(11) NOT NULL DEFAULT '-1',
  `birth_date` date DEFAULT NULL,
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `family_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Given name, first name',
  `additional_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'An additional name for a Person, can be used for a middle name.',
  `given_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `gender` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Unspecified' COMMENT 'Male;Female',
  PRIMARY KEY (`id`),
  KEY `address` (`address_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `top4_person`
--

INSERT INTO `top4_person` (`id`, `friendly_url`, `name`, `alternate_name`, `description`, `image_id`, `enter_time`, `update_time`, `address_id`, `birth_date`, `email`, `family_name`, `additional_name`, `given_name`, `gender`) VALUES
(1, 'allen-woo-1', 'Allen Woo 1', 'Allen Alt', 'some test', -1, '2014-12-09 23:04:32', '2014-12-10 01:16:02', -1, '1980-12-01', 'allen@twmg.com.au', 'Wu', '', 'Daixi', 'Male'),
(2, 'allen-woo-2', 'Allen Woo 2', 'Allen Alt', 'some test', -1, '2014-12-09 06:03:01', '2014-12-10 01:16:02', -1, '1980-12-02', 'allen@twmg.com.au', 'Wu', '', 'Daixi', 'Male'),
(3, 'allen-woo-3', 'Allen Woo 3', 'Allen Alt', 'some test', -1, '2014-12-09 23:04:32', '2014-12-10 01:16:02', -1, '1980-12-03', 'allen@twmg.com.au', 'Wu', '', 'Daixi', 'Male');

-- --------------------------------------------------------

--
-- Table structure for table `top4_thing`
--

DROP TABLE IF EXISTS `top4_thing`;
CREATE TABLE IF NOT EXISTS `top4_thing` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Universal ID',
  `friendly_rul` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `alternate_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Text - An alias for the item.',
  `description` varchar(500) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Text - A short description of the item.',
  `image_id` int(11) NOT NULL DEFAULT '-1' COMMENT 'ImageObject - An image of the item',
  `enter_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;