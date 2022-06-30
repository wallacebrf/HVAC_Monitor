-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 30, 2022 at 11:07 AM
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
-- Database: `home_temp`
--

-- --------------------------------------------------------

--
-- Table structure for table `equipmentmail`
--

CREATE TABLE `equipmentmail` (
  `datetime` datetime NOT NULL DEFAULT current_timestamp(),
  `data` int(1) DEFAULT NULL,
  `row_num` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `firstfloormail`
--

CREATE TABLE `firstfloormail` (
  `datetime` datetime NOT NULL DEFAULT current_timestamp(),
  `data` int(1) DEFAULT NULL,
  `row_num` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `first_floor`
--

CREATE TABLE `first_floor` (
  `timeStamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `temp` float UNSIGNED DEFAULT NULL,
  `hum` float UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `waterheatermail`
--

CREATE TABLE `waterheatermail` (
  `datetime` datetime NOT NULL DEFAULT current_timestamp(),
  `data` int(1) DEFAULT NULL,
  `row_num` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `waterheatermail`
--

INSERT INTO `waterheatermail` (`datetime`, `data`, `row_num`) VALUES
('2022-06-16 18:34:55', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `waterheatermonitor`
--

CREATE TABLE `waterheatermonitor` (
  `id` tinyint(4) NOT NULL DEFAULT 1,
  `datetime` datetime NOT NULL DEFAULT current_timestamp(),
  `data` bit(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `waterheatermonitor`
--

INSERT INTO `waterheatermonitor` (`id`, `datetime`, `data`) VALUES
(1, '2022-06-30 11:07:41', b'1');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `first_floor`
--
ALTER TABLE `first_floor`
  ADD PRIMARY KEY (`timeStamp`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
