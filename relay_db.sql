-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2+deb7u1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 29, 2015 at 11:34 PM
-- Server version: 5.5.46
-- PHP Version: 5.4.45-0+deb7u1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
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
-- Table structure for table `ci_sessions`
--

CREATE TABLE IF NOT EXISTS `ci_sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(45) NOT NULL DEFAULT '0',
  `user_agent` varchar(120) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `last_activity_idx` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
(1, 'admin', 'dhiraj.netwin@yahoo.com', 'YWRtaW4xMjM=', 0, 'SA', 'Admin', '0000-00-00 00:00:00', 0, '2015-10-29 22:56:22');

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
  `is_pool_or_spa` enum('0','1','2') NOT NULL DEFAULT '0' COMMENT '0=Other,1=Spa,2=Pool',
  `valve_relay_number` varchar(150) NOT NULL,
  `light_relay_number` text NOT NULL,
  PRIMARY KEY (`device_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
(1, 'Auto', 0, '0000-00-00 00:00:00', '', '', ''),
(2, 'Manual', 1, '2015-10-12 14:45:43', '10', '14:45:43', '14:55:43'),
(3, 'Time-Out', 0, '0000-00-00 00:00:00', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `rlb_mode_questions`
--

CREATE TABLE IF NOT EXISTS `rlb_mode_questions` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `general` text NOT NULL,
  `device` text NOT NULL,
  `heater` text NOT NULL,
  `more` text NOT NULL,
  `added_date` datetime NOT NULL,
  `last_modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `rlb_pool_spa_current`
--

CREATE TABLE IF NOT EXISTS `rlb_pool_spa_current` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `mode_id` int(5) NOT NULL,
  `current_on_device` varchar(255) NOT NULL,
  `device_type` varchar(10) NOT NULL,
  `device_number` varchar(10) NOT NULL,
  `current_on_time` datetime NOT NULL,
  `current_off_time` datetime NOT NULL,
  `current_device_complete` enum('0','1') NOT NULL,
  `current_unique_id` varchar(100) NOT NULL,
  `current_sequence` int(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `rlb_pool_spa_log`
--

CREATE TABLE IF NOT EXISTS `rlb_pool_spa_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `mode_id` int(5) NOT NULL,
  `unique_id` varchar(100) NOT NULL,
  `device` varchar(150) NOT NULL,
  `device_type` varchar(10) NOT NULL,
  `device_number` varchar(10) NOT NULL,
  `device_start` datetime NOT NULL,
  `device_stop` datetime NOT NULL,
  `device_complete_run` enum('0','1','2') NOT NULL DEFAULT '0',
  `current_sequence` int(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `rlb_pool_spa_mode`
--

CREATE TABLE IF NOT EXISTS `rlb_pool_spa_mode` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `mode_name` varchar(150) NOT NULL,
  `mode_status` enum('0','1') NOT NULL DEFAULT '0',
  `total_run_time` varchar(100) NOT NULL,
  `last_start_date` datetime NOT NULL,
  `last_end_date` datetime NOT NULL,
  `unique_id` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `rlb_pool_spa_mode`
--

INSERT INTO `rlb_pool_spa_mode` (`id`, `mode_name`, `mode_status`, `total_run_time`, `last_start_date`, `last_end_date`, `unique_id`) VALUES
(1, 'Pool', '0', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', ''),
(2, 'Spa', '0', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', ''),
(3, 'Both', '0', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '');

-- --------------------------------------------------------

--
-- Table structure for table `rlb_powercenters`
--

CREATE TABLE IF NOT EXISTS `rlb_powercenters` (
  `powercenter_id` int(11) NOT NULL AUTO_INCREMENT,
  `powercenter_number` int(11) NOT NULL,
  `powercenter_name` varchar(100) NOT NULL,
  PRIMARY KEY (`powercenter_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `rlb_pump_device`
--

CREATE TABLE IF NOT EXISTS `rlb_pump_device` (
  `pump_id` int(10) NOT NULL AUTO_INCREMENT,
  `pump_number` int(5) NOT NULL,
  `pump_type` enum('12','24','Intellicom','Emulator','Intellicom12','Intellicom24','Emulator12','Emulator24','2Speed') NOT NULL,
  `pump_sub_type` enum('VS','VF','12','24') NOT NULL,
  `pump_speed` varchar(150) NOT NULL,
  `pump_flow` varchar(250) NOT NULL,
  `pump_closure` varchar(150) NOT NULL,
  `relay_number` varchar(10) NOT NULL,
  `pump_address` varchar(50) NOT NULL,
  `pump_modified_date` datetime NOT NULL,
  `relay_number_1` varchar(10) NOT NULL,
  `status` enum('0','1','2') NOT NULL DEFAULT '0',
  PRIMARY KEY (`pump_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `rlb_pump_response`
--

CREATE TABLE IF NOT EXISTS `rlb_pump_response` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `pump_number` int(5) NOT NULL,
  `pump_response_time` datetime NOT NULL,
  `pump_response` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `rlb_relays`
--

CREATE TABLE IF NOT EXISTS `rlb_relays` (
  `relay_id` int(11) NOT NULL AUTO_INCREMENT,
  `relay_number` int(11) NOT NULL,
  `relay_name` varchar(100) NOT NULL,
  PRIMARY KEY (`relay_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `rlb_setting`
--

CREATE TABLE IF NOT EXISTS `rlb_setting` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(100) DEFAULT NULL,
  `port_no` varchar(100) DEFAULT NULL,
  `extra` text NOT NULL,
  `ip_external` varchar(150) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `rlb_setting`
--

INSERT INTO `rlb_setting` (`id`, `ip_address`, `port_no`, `extra`, `ip_external`) VALUES
(1, '192.168.0.103', '13330', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `rlb_site_modules`
--

CREATE TABLE IF NOT EXISTS `rlb_site_modules` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `module_name` varchar(250) NOT NULL,
  `module_active` enum('0','1') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=20 ;

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
(13, 'Status', '1'),
(15, 'Log', '1'),
(16, 'Light', '1'),
(17, 'Heater', '1'),
(18, 'Pool and Spa', '1'),
(19, 'Blower', '1');

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

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
