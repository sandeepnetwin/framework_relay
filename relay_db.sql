-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jul 29, 2015 at 12:14 PM
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
-- Table structure for table `rlb_admin_users`
--

CREATE TABLE IF NOT EXISTS `rlb_admin_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(150) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `block` tinyint(4) NOT NULL DEFAULT '0',
  `user_type` enum('SA','A') DEFAULT 'SA' COMMENT 'SA: Super Admin,A: Admin',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `rlb_admin_users`
--

INSERT INTO `rlb_admin_users` (`id`, `username`, `email`, `password`, `block`, `user_type`) VALUES
(1, 'admin', 'admin@admin.com', '0192023a7bbd73250516f069df18b500', 0, 'SA');

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Table structure for table `rlb_device`
--

CREATE TABLE IF NOT EXISTS `rlb_device` (
  `device_id` int(10) NOT NULL AUTO_INCREMENT,
  `device_number` int(10) NOT NULL,
  `device_name` varchar(150) NOT NULL,
  `device_type` varchar(100) NOT NULL,
  `device_position` text,
  `device_total_time` varchar(100) NOT NULL,
  `device_start_time` varchar(100) NOT NULL,
  `device_end_time` varchar(100) NOT NULL,
  `last_updated_date` datetime NOT NULL,
  PRIMARY KEY (`device_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Table structure for table `rlb_modes`
--

CREATE TABLE IF NOT EXISTS `rlb_modes` (
  `mode_id` int(11) NOT NULL AUTO_INCREMENT,
  `mode_name` varchar(255) NOT NULL,
  `mode_status` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`mode_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `rlb_modes`
--

INSERT INTO `rlb_modes` (`mode_id`, `mode_name`, `mode_status`) VALUES
(1, 'Auto', 0),
(2, 'Manual', 1),
(3, 'Time-Out', 0);

-- --------------------------------------------------------

--
-- Table structure for table `rlb_powercenters`
--

CREATE TABLE IF NOT EXISTS `rlb_powercenters` (
  `powercenter_id` int(11) NOT NULL AUTO_INCREMENT,
  `powercenter_number` int(11) NOT NULL,
  `powercenter_name` varchar(100) NOT NULL,
  PRIMARY KEY (`powercenter_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Table structure for table `rlb_pump_device`
--

CREATE TABLE IF NOT EXISTS `rlb_pump_device` (
  `pump_id` int(10) NOT NULL AUTO_INCREMENT,
  `pump_number` int(5) NOT NULL,
  `pump_type` varchar(150) NOT NULL COMMENT '''1'' = VS Pump, ''2'' = VF Pump',
  `pump_speed` varchar(150) NOT NULL,
  `pump_flow` varchar(250) NOT NULL,
  `pump_closure` varchar(150) NOT NULL,
  `pump_modified_date` datetime NOT NULL,
  PRIMARY KEY (`pump_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


--
-- Table structure for table `rlb_relays`
--

CREATE TABLE IF NOT EXISTS `rlb_relays` (
  `relay_id` int(11) NOT NULL AUTO_INCREMENT,
  `relay_number` int(11) NOT NULL,
  `relay_name` varchar(100) NOT NULL,
  PRIMARY KEY (`relay_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Table structure for table `rlb_setting`
--

CREATE TABLE IF NOT EXISTS `rlb_setting` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(100) DEFAULT NULL,
  `port_no` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

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
