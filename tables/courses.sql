-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 05, 2020 at 11:02 AM
-- Server version: 5.7.29-0ubuntu0.18.04.1
-- PHP Version: 7.2.24-0ubuntu0.18.04.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `degree_plans`
--

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `fetched` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `code` char(8) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `name` varchar(200) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `units` float NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `prereq` varchar(1000) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `coreq` varchar(1000) NOT NULL,
  `note` varchar(1000) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `exclusion` varchar(1000) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `reccomend` varchar(1000) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `oneWay` varchar(1000) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `learnHours` varchar(300) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `equivalency` varchar(500) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `website` text CHARACTER SET utf8 COLLATE utf8_bin
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`fetched`,`code`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
