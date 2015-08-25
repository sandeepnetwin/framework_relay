-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Aug 25, 2015 at 07:48 AM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `relay_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `rlb_access_permissions`
--

CREATE TABLE IF NOT EXISTS `rlb_access_permissions` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(5) NOT NULL,
  `module_id` int(5) NOT NULL,
  `access` enum('0','1','2') NOT NULL DEFAULT '0' COMMENT '0=block,1=View,2=View and change',
  `modified_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=25 ;

--
-- Dumping data for table `rlb_access_permissions`
--

INSERT INTO `rlb_access_permissions` (`id`, `user_id`, `module_id`, `access`, `modified_date`) VALUES
(1, 4, 2, '1', '2015-08-24 08:14:43'),
(2, 4, 3, '1', '2015-08-24 08:14:43'),
(3, 4, 4, '1', '2015-08-24 08:14:44'),
(4, 4, 5, '0', '2015-08-24 08:14:44'),
(5, 4, 6, '0', '2015-08-24 08:14:44'),
(6, 4, 7, '2', '2015-08-24 08:14:44'),
(7, 4, 8, '1', '2015-08-24 08:14:44'),
(8, 4, 9, '1', '2015-08-24 08:14:44'),
(9, 4, 10, '1', '2015-08-24 08:14:44'),
(10, 4, 11, '1', '2015-08-24 08:14:44'),
(11, 4, 12, '1', '2015-08-24 08:14:44'),
(12, 4, 13, '0', '2015-08-24 08:14:44'),
(13, 5, 2, '0', '2015-08-20 13:24:45'),
(14, 5, 3, '2', '2015-08-20 13:24:46'),
(15, 5, 4, '0', '2015-08-20 13:24:46'),
(16, 5, 5, '1', '2015-08-20 13:24:46'),
(17, 5, 6, '0', '2015-08-20 13:24:46'),
(18, 5, 7, '0', '2015-08-20 13:24:46'),
(19, 5, 8, '0', '2015-08-20 13:24:46'),
(20, 5, 9, '0', '2015-08-20 13:24:46'),
(21, 5, 10, '0', '2015-08-20 13:24:46'),
(22, 5, 11, '0', '2015-08-20 13:24:46'),
(23, 5, 12, '0', '2015-08-20 13:24:46'),
(24, 5, 13, '0', '2015-08-20 13:24:46');

-- --------------------------------------------------------

--
-- Table structure for table `rlb_admin_users`
--

CREATE TABLE IF NOT EXISTS `rlb_admin_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(150) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `block` tinyint(4) NOT NULL DEFAULT '0',
  `user_type` enum('SA','A') DEFAULT 'SA' COMMENT 'SA: Super Admin,A: Admin',
  `name` varchar(250) NOT NULL,
  `created_date` datetime NOT NULL,
  `parent_id` int(5) NOT NULL,
  `last_login` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `rlb_admin_users`
--

INSERT INTO `rlb_admin_users` (`id`, `username`, `email`, `password`, `block`, `user_type`, `name`, `created_date`, `parent_id`, `last_login`) VALUES
(1, 'admin', 'dhiraj.netwin@yahoo.com', 'YWRtaW4xMjM=', 0, 'SA', 'Admin', '0000-00-00 00:00:00', 0, '2015-08-24 06:43:38'),
(4, 'test', 'test@test.com', 'dGVzdDEyMw==', 0, 'A', 'Test User1', '2015-08-20 08:11:21', 1, '2015-08-24 07:11:53'),
(5, 'test1', 'test@test.com', 'dGVzdDExMQ==', 0, 'A', 'Test User2', '2015-08-20 13:23:28', 1, '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `rlb_analog_device`
--

CREATE TABLE IF NOT EXISTS `rlb_analog_device` (
  `analog_id` int(10) NOT NULL AUTO_INCREMENT,
  `analog_input` int(10) NOT NULL,
  `analog_name` varchar(150) NOT NULL,
  `analog_device` varchar(100) NOT NULL,
  `analog_device_type` varchar(100) NOT NULL,
  `device_direction` int(5) NOT NULL,
  `analog_device_modified_date` datetime NOT NULL,
  PRIMARY KEY (`analog_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `rlb_analog_device`
--

INSERT INTO `rlb_analog_device` (`analog_id`, `analog_input`, `analog_name`, `analog_device`, `analog_device_type`, `device_direction`, `analog_device_modified_date`) VALUES
(1, 0, 'AP0', '0', 'R', 0, '2015-07-17 14:19:51'),
(2, 1, 'AP1', '2', 'V', 1, '2015-07-17 14:19:51'),
(3, 2, 'AP2', '2', 'P', 0, '2015-07-17 14:19:51'),
(4, 3, 'AP3', '0', 'V', 2, '2015-07-17 14:19:51');

-- --------------------------------------------------------

--
-- Table structure for table `rlb_device`
--

CREATE TABLE IF NOT EXISTS `rlb_device` (
  `device_id` int(10) NOT NULL AUTO_INCREMENT,
  `device_number` int(10) NOT NULL,
  `device_name` varchar(150) NOT NULL,
  `device_type` varchar(100) NOT NULL,
  `device_power_type` varchar(10) DEFAULT NULL COMMENT '0=24VAC,1=12VDC',
  `device_position` text,
  `device_total_time` varchar(100) NOT NULL,
  `device_start_time` varchar(100) NOT NULL,
  `device_end_time` varchar(100) NOT NULL,
  `last_updated_date` datetime NOT NULL,
  PRIMARY KEY (`device_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;

--
-- Dumping data for table `rlb_device`
--

INSERT INTO `rlb_device` (`device_id`, `device_number`, `device_name`, `device_type`, `device_power_type`, `device_position`, `device_total_time`, `device_start_time`, `device_end_time`, `last_updated_date`) VALUES
(1, 0, 'PowerCenter1', 'P', NULL, NULL, '', '', '', '2015-07-09 09:04:10'),
(2, 0, 'RelayName0', 'R', NULL, NULL, '20', '', '', '2015-08-03 10:49:52'),
(3, 1, 'PowerCenter2', 'P', NULL, NULL, '', '', '', '2015-07-09 09:04:19'),
(4, 1, 'Test Relay 2', 'R', NULL, NULL, '', '', '', '2015-07-09 09:03:34'),
(5, 3, 'Valve Name Save', 'V', '0', NULL, '', '', '', '2015-08-12 10:32:30'),
(6, 0, 'PumpName1', 'PS', '1', NULL, '', '', '', '2015-08-19 06:32:33'),
(7, 0, '', 'V', NULL, 'a:2:{s:9:"position1";s:3:"spa";s:9:"position2";s:4:"pool";}', '', '', '', '2015-07-30 07:44:23'),
(8, 2, '', 'R', NULL, NULL, '30', '', '', '2015-08-03 10:52:12'),
(9, 10, '', 'R', NULL, NULL, '', '08:13:57', '08:23:57', '2015-08-21 14:20:01'),
(10, 0, 'TempratureRelay1', 'T', NULL, NULL, '', '', '', '2015-08-17 12:52:12'),
(11, 12, '', 'R', '0', NULL, '', '', '', '2015-08-12 10:22:14'),
(12, 2, '', 'P', '1', NULL, '', '', '', '2015-08-21 13:48:27'),
(13, 1, '', 'PS', '1', NULL, '', '', '', '2015-08-19 06:32:31'),
(14, 2, '', 'PS', '0', NULL, '', '', '', '2015-08-19 06:32:37');

-- --------------------------------------------------------

--
-- Table structure for table `rlb_modes`
--

CREATE TABLE IF NOT EXISTS `rlb_modes` (
  `mode_id` int(11) NOT NULL AUTO_INCREMENT,
  `mode_name` varchar(255) NOT NULL,
  `mode_status` int(1) NOT NULL DEFAULT '0',
  `start_time` datetime NOT NULL,
  `timer_total` varchar(150) NOT NULL,
  `timer_start` varchar(150) NOT NULL,
  `timer_end` varchar(150) NOT NULL,
  PRIMARY KEY (`mode_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `rlb_modes`
--

INSERT INTO `rlb_modes` (`mode_id`, `mode_name`, `mode_status`, `start_time`, `timer_total`, `timer_start`, `timer_end`) VALUES
(1, 'Auto', 1, '2015-08-24 07:54:33', '', '', ''),
(2, 'Manual', 0, '0000-00-00 00:00:00', '10', '', ''),
(3, 'Time-Out', 0, '0000-00-00 00:00:00', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `rlb_powercenters`
--

CREATE TABLE IF NOT EXISTS `rlb_powercenters` (
  `powercenter_id` int(11) NOT NULL AUTO_INCREMENT,
  `powercenter_number` int(11) NOT NULL,
  `powercenter_name` varchar(100) NOT NULL,
  PRIMARY KEY (`powercenter_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `rlb_powercenters`
--

INSERT INTO `rlb_powercenters` (`powercenter_id`, `powercenter_number`, `powercenter_name`) VALUES
(1, 0, 'Test powercenter0'),
(2, 4, 'pc4 edit'),
(3, 2, 'Testing'),
(4, 1, 'PowerCenter1');

-- --------------------------------------------------------

--
-- Table structure for table `rlb_program`
--

CREATE TABLE IF NOT EXISTS `rlb_program` (
  `program_id` int(11) NOT NULL AUTO_INCREMENT,
  `program_name` varchar(255) NOT NULL,
  `device_number` varchar(8) NOT NULL,
  `device_type` varchar(8) NOT NULL,
  `program_type` int(2) NOT NULL COMMENT '1-Daily, 2-Weekly',
  `program_days` varchar(255) NOT NULL COMMENT '0-All, 1-Mon, 2-Tue...7-Sun',
  `start_time` varchar(255) NOT NULL,
  `end_time` varchar(255) NOT NULL,
  `program_created_date` datetime NOT NULL,
  `program_modified_date` datetime NOT NULL,
  `program_delete` int(1) NOT NULL DEFAULT '0',
  `program_active` int(1) NOT NULL DEFAULT '0',
  `program_absolute` enum('0','1') NOT NULL DEFAULT '0',
  `program_absolute_start_time` varchar(100) DEFAULT NULL,
  `program_absolute_end_time` varchar(100) DEFAULT NULL,
  `program_absolute_total_time` varchar(100) DEFAULT NULL,
  `program_absolute_run_time` varchar(100) DEFAULT NULL,
  `program_absolute_start_date` date DEFAULT NULL,
  `program_absolute_run` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`program_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `rlb_program`
--

INSERT INTO `rlb_program` (`program_id`, `program_name`, `device_number`, `device_type`, `program_type`, `program_days`, `start_time`, `end_time`, `program_created_date`, `program_modified_date`, `program_delete`, `program_active`, `program_absolute`, `program_absolute_start_time`, `program_absolute_end_time`, `program_absolute_total_time`, `program_absolute_run_time`, `program_absolute_start_date`, `program_absolute_run`) VALUES
(1, 'Test Program1', '10', 'R', 2, '2,6', '14:50:00', '14:55:00', '2015-08-05 11:27:12', '2015-08-10 10:52:48', 0, 0, '1', NULL, NULL, '00:05:00', NULL, NULL, '0'),
(2, 'Test Program2', '10', 'R', 1, '0', '13:55:00', '14:00:00', '2015-08-05 11:27:53', '2015-08-18 13:55:29', 0, 0, '0', NULL, NULL, '00:10:00', NULL, NULL, '0'),
(3, 'Test Program P1', '0', 'PS', 2, '1,2,7', '01:05:00', '01:10:00', '2015-08-05 11:33:16', '2015-08-05 11:39:06', 0, 0, '1', NULL, NULL, '00:05:00', NULL, NULL, '0'),
(4, 'Test Program11', '11', 'R', 1, '0', '01:10:00', '01:15:00', '2015-08-05 11:39:59', '0000-00-00 00:00:00', 0, 0, '0', NULL, NULL, '00:05:00', NULL, NULL, '0');

-- --------------------------------------------------------

--
-- Table structure for table `rlb_pump_device`
--

CREATE TABLE IF NOT EXISTS `rlb_pump_device` (
  `pump_id` int(10) NOT NULL AUTO_INCREMENT,
  `pump_number` int(5) NOT NULL,
  `pump_type` enum('12','24','Intellicom','Emulator') NOT NULL,
  `pump_sub_type` enum('VS','VF') NOT NULL,
  `pump_speed` varchar(150) NOT NULL,
  `pump_flow` varchar(250) NOT NULL,
  `pump_closure` varchar(150) NOT NULL,
  `relay_number` varchar(10) NOT NULL,
  `pump_modified_date` datetime NOT NULL,
  PRIMARY KEY (`pump_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `rlb_pump_device`
--

INSERT INTO `rlb_pump_device` (`pump_id`, `pump_number`, `pump_type`, `pump_sub_type`, `pump_speed`, `pump_flow`, `pump_closure`, `relay_number`, `pump_modified_date`) VALUES
(1, 0, '12', '', '', '', '1', '1', '2015-08-19 13:01:53');

-- --------------------------------------------------------

--
-- Table structure for table `rlb_relays`
--

CREATE TABLE IF NOT EXISTS `rlb_relays` (
  `relay_id` int(11) NOT NULL AUTO_INCREMENT,
  `relay_number` int(11) NOT NULL,
  `relay_name` varchar(100) NOT NULL,
  PRIMARY KEY (`relay_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `rlb_relays`
--

INSERT INTO `rlb_relays` (`relay_id`, `relay_number`, `relay_name`) VALUES
(1, 0, 'Test Realy 1'),
(2, 2, 'Test for relay 2'),
(3, 3, 'Test for relay3 editedaf'),
(4, 5, 'rl5'),
(5, 10, 'relay 10'),
(6, 1, 'Test Relay 11111');

-- --------------------------------------------------------

--
-- Table structure for table `rlb_relay_prog`
--

CREATE TABLE IF NOT EXISTS `rlb_relay_prog` (
  `relay_prog_id` int(11) NOT NULL AUTO_INCREMENT,
  `relay_prog_name` varchar(255) NOT NULL,
  `relay_number` varchar(8) NOT NULL,
  `relay_prog_type` int(2) NOT NULL COMMENT '1-Daily, 2-Weekly',
  `relay_prog_days` varchar(255) NOT NULL COMMENT '0-All, 1-Mon, 2-Tue...7-Sun',
  `relay_start_time` varchar(255) NOT NULL,
  `relay_end_time` varchar(255) NOT NULL,
  `relay_prog_created_date` datetime NOT NULL,
  `relay_prog_modified_date` datetime NOT NULL,
  `relay_prog_delete` int(1) NOT NULL DEFAULT '0',
  `relay_prog_active` int(1) NOT NULL DEFAULT '0',
  `relay_prog_absolute` enum('0','1') NOT NULL DEFAULT '0',
  `relay_prog_absolute_start_time` varchar(100) DEFAULT NULL,
  `relay_prog_absolute_end_time` varchar(100) DEFAULT NULL,
  `relay_prog_absolute_total_time` varchar(100) DEFAULT NULL,
  `relay_prog_absolute_run_time` varchar(100) DEFAULT NULL,
  `relay_prog_absolute_start_date` date DEFAULT NULL,
  `relay_prog_absolute_run` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`relay_prog_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `rlb_relay_prog`
--

INSERT INTO `rlb_relay_prog` (`relay_prog_id`, `relay_prog_name`, `relay_number`, `relay_prog_type`, `relay_prog_days`, `relay_start_time`, `relay_end_time`, `relay_prog_created_date`, `relay_prog_modified_date`, `relay_prog_delete`, `relay_prog_active`, `relay_prog_absolute`, `relay_prog_absolute_start_time`, `relay_prog_absolute_end_time`, `relay_prog_absolute_total_time`, `relay_prog_absolute_run_time`, `relay_prog_absolute_start_date`, `relay_prog_absolute_run`) VALUES
(1, 'test', '0', 1, '0', '23:00:00', '23:30:00', '2015-04-03 15:40:46', '2015-07-10 12:37:12', 0, 0, '0', NULL, NULL, NULL, NULL, NULL, '0'),
(2, 'newtest1', '0', 2, '2,3,6', '14:00:00', '20:00:00', '2015-04-07 00:00:00', '2015-04-07 00:00:00', 0, 0, '0', NULL, NULL, NULL, NULL, NULL, '0'),
(3, 'testrelay0', '0', 2, '1,4,5', '22:00:00', '23:30:00', '2015-04-07 00:00:00', '2015-04-07 00:00:00', 0, 0, '0', NULL, NULL, NULL, NULL, NULL, '0'),
(4, 'testrelay1', '1', 1, '0', '10:00:00', '13:00:00', '2015-04-08 00:00:00', '2015-04-08 00:00:00', 0, 0, '0', NULL, NULL, NULL, NULL, NULL, '0'),
(5, 'Weeklytest', '0', 2, '2,6', '01:00:00', '01:30:00', '2015-04-20 00:00:00', '2015-04-20 00:00:00', 1, 0, '0', NULL, NULL, NULL, NULL, NULL, '0'),
(6, '</>', '0', 1, '0', '</>', '</>', '2015-04-24 00:00:00', '2015-04-24 00:00:00', 1, 0, '0', NULL, NULL, NULL, NULL, NULL, '0'),
(7, 'dhiraj Test', '0', 1, '0', '02:00:00', '02:30:00', '2015-07-06 00:00:00', '2015-07-13 12:50:40', 0, 0, '1', NULL, NULL, '00:30:00', '', NULL, '0'),
(8, 'Test', '0', 2, '2,4,6', '00:00:00', '01:00:00', '2015-07-09 07:31:37', '0000-00-00 00:00:00', 1, 0, '0', NULL, NULL, NULL, NULL, NULL, '0'),
(9, 'Test Relay 1', '1', 2, '2,3,4', '00:30:00', '01:00:00', '2015-07-09 08:38:46', '2015-07-09 08:40:21', 1, 0, '0', NULL, NULL, NULL, NULL, NULL, '0'),
(10, 'Program2', '1', 2, '2,3', '02:00:00', '04:00:00', '2015-07-09 09:05:11', '2015-07-09 09:05:23', 0, 0, '0', NULL, NULL, NULL, NULL, NULL, '0'),
(11, 'Test Relay Number', '0', 2, '3', '01:00:00', '02:00:00', '2015-07-09 11:41:53', '2015-07-10 14:56:21', 0, 0, '1', NULL, NULL, '01:00:00', NULL, NULL, '0');

-- --------------------------------------------------------

--
-- Table structure for table `rlb_setting`
--

CREATE TABLE IF NOT EXISTS `rlb_setting` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(100) DEFAULT NULL,
  `port_no` varchar(100) DEFAULT NULL,
  `extra` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `rlb_setting`
--

INSERT INTO `rlb_setting` (`id`, `ip_address`, `port_no`, `extra`) VALUES
(1, '72.193.44.191', '13330', 'a:4:{s:9:"Pool_Temp";s:1:"1";s:17:"Pool_Temp_Address";s:3:"TS0";s:8:"Spa_Temp";s:1:"0";s:16:"Spa_Temp_Address";s:0:"";}');

-- --------------------------------------------------------

--
-- Table structure for table `rlb_site_modules`
--

CREATE TABLE IF NOT EXISTS `rlb_site_modules` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `module_name` varchar(250) NOT NULL,
  `module_active` enum('0','1') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

--
-- Dumping data for table `rlb_site_modules`
--

INSERT INTO `rlb_site_modules` (`id`, `module_name`, `module_active`) VALUES
(2, '24V AC Relay', '1'),
(3, '12V DC Power Center Relay', '1'),
(4, 'Modes', '1'),
(5, 'Lights', '1'),
(6, 'Spa Devices', '1'),
(7, 'Pool Devices', '1'),
(8, 'Valve', '1'),
(9, 'Pump', '1'),
(10, 'Temperature Sensors', '1'),
(11, 'Input', '1'),
(12, 'Settings', '1'),
(13, 'Status', '1');

-- --------------------------------------------------------

--
-- Table structure for table `rlb_valves`
--

CREATE TABLE IF NOT EXISTS `rlb_valves` (
  `valve_id` int(11) NOT NULL AUTO_INCREMENT,
  `valve_number` int(11) NOT NULL,
  `valve_name` varchar(100) NOT NULL,
  PRIMARY KEY (`valve_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `test`
--

CREATE TABLE IF NOT EXISTS `test` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
