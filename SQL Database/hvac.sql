-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 30, 2022 at 10:25 AM
-- Server version: 10.3.32-MariaDB
-- PHP Version: 7.4.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hvac`
--

-- --------------------------------------------------------

--
-- Table structure for table `dehumidifier_log`
--

CREATE TABLE `dehumidifier_log` (
  `timeStamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `duration` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dehumidifier_log`
--

INSERT INTO `dehumidifier_log` (`timeStamp`, `duration`) VALUES
('2019-03-24 23:46:35', 1200311),
('2019-03-30 18:14:12', 959177),


-- --------------------------------------------------------

--
-- Table structure for table `estopmail`
--

CREATE TABLE `estopmail` (
  `datetime` datetime NOT NULL DEFAULT current_timestamp(),
  `data` int(1) DEFAULT NULL,
  `row_num` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `estopmail`
--

INSERT INTO `estopmail` (`datetime`, `data`, `row_num`) VALUES
('2022-02-18 12:59:39', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `fan_log`
--

CREATE TABLE `fan_log` (
  `timeStamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `duration` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `fan_log`
--

INSERT INTO `fan_log` (`timeStamp`, `duration`) VALUES
('2015-01-01 00:17:03', 7126560),
('2015-01-01 00:46:53', 918942),


-- --------------------------------------------------------

--
-- Table structure for table `filtermail`
--

CREATE TABLE `filtermail` (
  `datetime` datetime NOT NULL DEFAULT current_timestamp(),
  `data` int(1) DEFAULT NULL,
  `row_num` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `filtermail`
--

INSERT INTO `filtermail` (`datetime`, `data`, `row_num`) VALUES
('2022-02-18 12:55:47', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `high_cool_log`
--

CREATE TABLE `high_cool_log` (
  `timeStamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `duration` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `high_cool_log`
--

INSERT INTO `high_cool_log` (`timeStamp`, `duration`) VALUES
('2019-05-22 23:02:01', 780853),
('2019-05-22 23:12:53', 68623),


-- --------------------------------------------------------

--
-- Table structure for table `low_cool_log`
--

CREATE TABLE `low_cool_log` (
  `timeStamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `duration` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `low_cool_log`
--

INSERT INTO `low_cool_log` (`timeStamp`, `duration`) VALUES
('2015-01-01 00:16:34', 7097500),
('2015-01-01 00:46:23', 889900),


-- --------------------------------------------------------

--
-- Table structure for table `low_heat_log`
--

CREATE TABLE `low_heat_log` (
  `timeStamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `duration` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `low_heat_log`
--

INSERT INTO `low_heat_log` (`timeStamp`, `duration`) VALUES
('2019-02-27 02:48:12', 158512),
('2019-03-22 17:53:35', 417354),


-- --------------------------------------------------------

--
-- Table structure for table `status`
--

CREATE TABLE `status` (
  `row` tinyint(4) NOT NULL,
  `fan` tinyint(4) NOT NULL,
  `low_cool` tinyint(4) NOT NULL,
  `high_cool` tinyint(4) NOT NULL,
  `low_heat` tinyint(4) NOT NULL,
  `high_heat` tinyint(4) NOT NULL,
  `humidifier` tinyint(4) NOT NULL,
  `dehumidifier` tinyint(4) NOT NULL,
  `filter` tinyint(4) NOT NULL,
  `estop` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `status`
--

INSERT INTO `status` (`row`, `fan`, `low_cool`, `high_cool`, `low_heat`, `high_heat`, `humidifier`, `dehumidifier`, `filter`, `estop`) VALUES
(2, 1, 1, 0, 0, 0, 0, 1, 1, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dehumidifier_log`
--
ALTER TABLE `dehumidifier_log`
  ADD PRIMARY KEY (`timeStamp`);

--
-- Indexes for table `fan_log`
--
ALTER TABLE `fan_log`
  ADD PRIMARY KEY (`timeStamp`);

--
-- Indexes for table `high_cool_log`
--
ALTER TABLE `high_cool_log`
  ADD PRIMARY KEY (`timeStamp`);

--
-- Indexes for table `high_heat_log`
--
ALTER TABLE `high_heat_log`
  ADD PRIMARY KEY (`timeStamp`);

--
-- Indexes for table `humidifier_log`
--
ALTER TABLE `humidifier_log`
  ADD PRIMARY KEY (`timeStamp`);

--
-- Indexes for table `low_cool_log`
--
ALTER TABLE `low_cool_log`
  ADD PRIMARY KEY (`timeStamp`);

--
-- Indexes for table `low_heat_log`
--
ALTER TABLE `low_heat_log`
  ADD PRIMARY KEY (`timeStamp`);

--
-- Indexes for table `status`
--
ALTER TABLE `status`
  ADD KEY `row` (`row`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
