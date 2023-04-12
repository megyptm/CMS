-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 27, 2023 at 04:41 AM
-- Server version: 10.4.17-MariaDB
-- PHP Version: 8.0.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `maatify_cms`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(128) COLLATE utf8_bin NOT NULL DEFAULT '',
  `name` varchar(128) COLLATE utf8_bin NOT NULL DEFAULT '',
  `isAdmin` tinyint(1) NOT NULL DEFAULT 0,
  `isActive` tinyint(1) NOT NULL DEFAULT 1,
  `lang` varchar(8) COLLATE utf8_bin NOT NULL DEFAULT 'ar'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `name`, `isAdmin`, `isActive`, `lang`) VALUES
(1, 'Mohamed', 'Mohamed Abdulalim', 1, 1, 'en');

-- --------------------------------------------------------

--
-- Table structure for table `a_2fa`
--

CREATE TABLE `a_2fa` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL DEFAULT 0,
  `auth` varchar(512) COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT 'GoogleAuthenticator Token',
  `isAuthRequired` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `a_2fa`
--

INSERT INTO `a_2fa` (`id`, `admin_id`, `auth`, `isAuthRequired`) VALUES
(1, 1, 'cykP211OXXmHhJRW0b4hh/DNRvppeyUd3+dIBWmPw0k=', 0);

-- --------------------------------------------------------

--
-- Table structure for table `a_edits_log`
--

CREATE TABLE `a_edits_log` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL DEFAULT 0,
  `type` varchar(256) COLLATE utf8_bin NOT NULL DEFAULT '',
  `description` text COLLATE utf8_bin DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `time` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
  `ip` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `a_email`
--

CREATE TABLE `a_email` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL DEFAULT 0,
  `email` varchar(128) COLLATE utf8_bin NOT NULL DEFAULT '',
  `confirmed` tinyint(1) NOT NULL DEFAULT 0,
  `token` varchar(512) COLLATE utf8_bin NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `a_email`
--

INSERT INTO `a_email` (`id`, `admin_id`, `email`, `confirmed`, `token`) VALUES
(1, 1, 'info@maatify.dev', 1, '');

-- --------------------------------------------------------

--
-- Table structure for table `a_f_login`
--

CREATE TABLE `a_f_login` (
  `id` int(11) NOT NULL,
  `isSuccess` tinyint(1) NOT NULL DEFAULT 0,
  `ip` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `username` varchar(256) COLLATE utf8_bin NOT NULL DEFAULT '',
  `page` varchar(256) COLLATE utf8_bin NOT NULL DEFAULT '',
  `time` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
  `admin_id` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `a_f_login`
--

INSERT INTO `a_f_login` (`id`, `isSuccess`, `ip`, `username`, `page`, `time`, `admin_id`) VALUES
(13, 0, '127.0.0.1', 'Mohamed', 'account/Login', '2023-03-24 02:33:24', 0);

-- --------------------------------------------------------

--
-- Table structure for table `a_info`
--

CREATE TABLE `a_info` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL DEFAULT 0,
  `reg_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
  `reg_by` varchar(128) COLLATE utf8_bin NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `a_info`
--

INSERT INTO `a_info` (`id`, `admin_id`, `reg_date`, `reg_by`) VALUES
(1, 1, '2023-03-24 00:00:00', '1');

-- --------------------------------------------------------

--
-- Table structure for table `a_logs`
--

CREATE TABLE `a_logs` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL DEFAULT 0,
  `description` text COLLATE utf8_bin DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `time` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
  `ip` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `a_logs`
--

INSERT INTO `a_logs` (`id`, `admin_id`, `description`, `user_id`, `time`, `ip`) VALUES
(19, 0, 'success Login', 0, '2023-03-27 04:03:18', '197.121.24.121');

-- --------------------------------------------------------

--
-- Table structure for table `a_pass`
--

CREATE TABLE `a_pass` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL DEFAULT 0,
  `password` varchar(1024) COLLATE utf8_bin NOT NULL DEFAULT '',
  `is_temp` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `a_pass`
--

INSERT INTO `a_pass` (`id`, `admin_id`, `password`, `is_temp`) VALUES
(1, 1, 'empty-need-to-set', 0);

-- --------------------------------------------------------

--
-- Table structure for table `a_roles`
--

CREATE TABLE `a_roles` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL DEFAULT 0,
  `role_id` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `a_token`
--

CREATE TABLE `a_token` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL DEFAULT 0,
  `token` varchar(256) COLLATE utf8_bin NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `a_token`
--

INSERT INTO `a_token` (`id`, `admin_id`, `token`) VALUES
(1, 1, '');

-- --------------------------------------------------------

--
-- Table structure for table `cron_email`
--

CREATE TABLE `cron_email` (
  `id` int(11) NOT NULL,
  `type` int(11) NOT NULL DEFAULT 1 COMMENT '1=messge; 2=confirm; 3=promotion',
  `ct_id` int(11) NOT NULL DEFAULT 0,
  `name` varchar(128) COLLATE utf8_bin NOT NULL DEFAULT '',
  `email` varchar(128) COLLATE utf8_bin NOT NULL DEFAULT '',
  `message` text COLLATE utf8_bin DEFAULT NULL,
  `subject` varchar(128) COLLATE utf8_bin NOT NULL DEFAULT '',
  `record_time` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
  `is_sent` tinyint(1) NOT NULL DEFAULT 0,
  `sent_time` datetime NOT NULL DEFAULT '1900-01-01 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `cron_phone`
--

CREATE TABLE `cron_phone` (
  `id` int(11) NOT NULL,
  `type` int(11) NOT NULL DEFAULT 1 COMMENT '1=message; 2=confirm',
  `ct_id` int(11) NOT NULL DEFAULT 0,
  `phone` varchar(128) COLLATE utf8_bin NOT NULL DEFAULT '',
  `message` text COLLATE utf8_bin DEFAULT NULL,
  `record_time` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
  `is_sent` tinyint(1) NOT NULL DEFAULT 0,
  `sent_time` datetime NOT NULL DEFAULT '1900-01-01 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `privileges`
--

CREATE TABLE `privileges` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL DEFAULT 0,
  `method_id` int(11) NOT NULL DEFAULT 0,
  `granted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Page Privileges';

-- --------------------------------------------------------

--
-- Table structure for table `privilege_methods`
--

CREATE TABLE `privilege_methods` (
  `id` int(11) NOT NULL,
  `method` varchar(128) COLLATE utf8_bin NOT NULL DEFAULT '',
  `sort` int(11) NOT NULL DEFAULT 0,
  `name_ar` varchar(128) COLLATE utf8_bin NOT NULL DEFAULT '',
  `name_en` varchar(128) COLLATE utf8_bin NOT NULL DEFAULT '',
  `comment` varchar(128) COLLATE utf8_bin NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `privilege_roles`
--

CREATE TABLE `privilege_roles` (
  `id` int(11) NOT NULL,
  `name_ar` varchar(256) COLLATE utf8_bin NOT NULL DEFAULT '',
  `name_en` varchar(256) COLLATE utf8_bin NOT NULL DEFAULT '',
  `comment` varchar(256) COLLATE utf8_bin NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `queue`
--

CREATE TABLE `queue` (
  `id` int(11) NOT NULL,
  `name` varchar(128) COLLATE utf8_bin NOT NULL DEFAULT '',
  `timestamp` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `queue`
--

INSERT INTO `queue` (`id`, `name`, `timestamp`) VALUES
(1, 'emails', ''),
(2, 'sms', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `a_2fa`
--
ALTER TABLE `a_2fa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admin_id` (`admin_id`);

--
-- Indexes for table `a_edits_log`
--
ALTER TABLE `a_edits_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `a_email`
--
ALTER TABLE `a_email`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admin_id` (`admin_id`);

--
-- Indexes for table `a_f_login`
--
ALTER TABLE `a_f_login`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `a_info`
--
ALTER TABLE `a_info`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admin_id` (`admin_id`);

--
-- Indexes for table `a_logs`
--
ALTER TABLE `a_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `a_pass`
--
ALTER TABLE `a_pass`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admin_id` (`admin_id`);

--
-- Indexes for table `a_roles`
--
ALTER TABLE `a_roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admin_id` (`admin_id`,`role_id`);

--
-- Indexes for table `a_token`
--
ALTER TABLE `a_token`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admin_id` (`admin_id`);

--
-- Indexes for table `cron_email`
--
ALTER TABLE `cron_email`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cron_phone`
--
ALTER TABLE `cron_phone`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `privileges`
--
ALTER TABLE `privileges`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `group_id` (`role_id`,`method_id`);

--
-- Indexes for table `privilege_methods`
--
ALTER TABLE `privilege_methods`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `page` (`method`);

--
-- Indexes for table `privilege_roles`
--
ALTER TABLE `privilege_roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `queue`
--
ALTER TABLE `queue`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `a_2fa`
--
ALTER TABLE `a_2fa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `a_edits_log`
--
ALTER TABLE `a_edits_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `a_email`
--
ALTER TABLE `a_email`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `a_f_login`
--
ALTER TABLE `a_f_login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `a_info`
--
ALTER TABLE `a_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `a_logs`
--
ALTER TABLE `a_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `a_pass`
--
ALTER TABLE `a_pass`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `a_roles`
--
ALTER TABLE `a_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `a_token`
--
ALTER TABLE `a_token`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `cron_email`
--
ALTER TABLE `cron_email`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cron_phone`
--
ALTER TABLE `cron_phone`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `privileges`
--
ALTER TABLE `privileges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `privilege_methods`
--
ALTER TABLE `privilege_methods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=188;

--
-- AUTO_INCREMENT for table `privilege_roles`
--
ALTER TABLE `privilege_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `queue`
--
ALTER TABLE `queue`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
