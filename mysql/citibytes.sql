-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 14, 2014 at 12:50 PM
-- Server version: 5.5.31
-- PHP Version: 5.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+05:30";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `citibytes`
--
CREATE DATABASE IF NOT EXISTS `citibytes` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `citibytes`;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `analytics_employee` (
  `email_id` varchar(255) NOT NULL,
  `business_id` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `duration` int(11) NOT NULL,
  PRIMARY KEY (`email_id`,`business_id`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `analytics_pincode`
--

CREATE TABLE IF NOT EXISTS `analytics_pincode` (
  `business_id` varchar(255) NOT NULL,
  `pincode` varchar(10) NOT NULL,
  `city` varchar(255) NOT NULL,
  `create_ts` datetime NOT NULL,
  PRIMARY KEY (`business_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

--
-- Table structure for table `approved_pincode_requests`
--

CREATE TABLE IF NOT EXISTS `approved_pincode_requests` (
  `email_id` varchar(255) NOT NULL,
  `personal_number` varchar(30) NOT NULL,
  `pincode` varchar(10) NOT NULL,
  `city` varchar(255) NOT NULL,
  `request_ts` datetime NOT NULL,
  PRIMARY KEY (`email_id`,`pincode`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

--
-- Table structure for table `pending_pincode_requests`
--

CREATE TABLE IF NOT EXISTS `pending_pincode_requests` (
  `email_id` varchar(255) NOT NULL,
  `personal_number` varchar(30) NOT NULL,
  `pincode` varchar(10) NOT NULL,
  `city` varchar(255) NOT NULL,
  `request_ts` datetime NOT NULL,
  PRIMARY KEY (`email_id`,`pincode`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `pincodes`
--

CREATE TABLE IF NOT EXISTS `pincodes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pincode` varchar(10) NOT NULL,
  `area_name` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `city` (`city`),
  KEY `pincode` (`pincode`),
  KEY `city_2` (`city`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `email_id` varchar(255) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `personal_number` varchar(30) NOT NULL,
  `business_number` varchar(30) NOT NULL,
  `created_ts` datetime NOT NULL,
  PRIMARY KEY (`email_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
