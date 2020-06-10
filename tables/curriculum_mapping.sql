-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 05, 2020 at 08:16 PM
-- Server version: 5.7.29-0ubuntu0.18.04.1
-- PHP Version: 7.2.24-0ubuntu0.18.04.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `curriculum_mapping`
--

-- --------------------------------------------------------

--
-- Table structure for table `cllo`
--

CREATE TABLE `cllo` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `number` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `text` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `type` enum('Core','Detail','None') CHARACTER SET utf8 NOT NULL,
  `ioa` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `notes` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `parent_id` mediumint(8) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cllo_and_course`
--

CREATE TABLE `cllo_and_course` (
  `cllo_id` mediumint(8) UNSIGNED NOT NULL,
  `course_id` mediumint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cllo_and_ilo`
--

CREATE TABLE `cllo_and_ilo` (
  `cllo_id` mediumint(8) UNSIGNED NOT NULL,
  `ilo_id` mediumint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cllo_and_pllo`
--

CREATE TABLE `cllo_and_pllo` (
  `cllo_id` mediumint(8) UNSIGNED NOT NULL,
  `pllo_id` mediumint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

CREATE TABLE `course` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `subject` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `number` varchar(10) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `courselist`
--

CREATE TABLE `courselist` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `class` enum('Option','Subject','Relationship') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Relationship',
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `notes` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `courselist_and_course`
--

CREATE TABLE `courselist_and_course` (
  `courselist_id` mediumint(8) UNSIGNED NOT NULL,
  `course_id` mediumint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `courselist_and_courselist`
--

CREATE TABLE `courselist_and_courselist` (
  `parent_id` mediumint(8) UNSIGNED NOT NULL,
  `child_id` mediumint(8) UNSIGNED NOT NULL,
  `level` enum('P','100','200','300','400','500','None') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'None',
  `or_above` tinyint(1) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `courselist_and_relationship`
--

CREATE TABLE `courselist_and_relationship` (
  `courselist_id` mediumint(8) UNSIGNED NOT NULL,
  `relationship` enum('and','or','any') COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `courselist_and_subject`
--

CREATE TABLE `courselist_and_subject` (
  `courselist_id` mediumint(8) UNSIGNED NOT NULL,
  `subject` varchar(10) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cpr`
--

CREATE TABLE `cpr` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `name` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `units` decimal(3,1) UNSIGNED NOT NULL,
  `connector` enum('from','in') COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `class` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `text` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `notes` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cpr_and_courselist`
--

CREATE TABLE `cpr_and_courselist` (
  `cpr_id` mediumint(8) UNSIGNED NOT NULL,
  `courselist_id` mediumint(8) UNSIGNED NOT NULL,
  `level` enum('P','100','200','300','400','500','None') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'None',
  `or_above` tinyint(1) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `degree`
--

CREATE TABLE `degree` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `name` varchar(75) COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `type` enum('Arts','Science','Computing','Music Theatre','Physical Education') COLLATE utf8_unicode_ci DEFAULT NULL,
  `honours` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `degree_and_faculty`
--

CREATE TABLE `degree_and_faculty` (
  `degree_id` mediumint(8) UNSIGNED NOT NULL,
  `faculty_id` mediumint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `name` varchar(150) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `department_and_faculty`
--

CREATE TABLE `department_and_faculty` (
  `department_id` mediumint(8) UNSIGNED NOT NULL,
  `faculty_id` mediumint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `department_and_plan`
--

CREATE TABLE `department_and_plan` (
  `department_id` mediumint(8) UNSIGNED NOT NULL,
  `plan_id` mediumint(8) UNSIGNED NOT NULL,
  `role` enum('Administrator','Partner') COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `department_and_subject`
--

CREATE TABLE `department_and_subject` (
  `department_id` mediumint(8) UNSIGNED NOT NULL,
  `subject` varchar(10) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dle`
--

CREATE TABLE `dle` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `number` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `text` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `notes` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `parent_id` mediumint(8) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faculty`
--

CREATE TABLE `faculty` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ilo`
--

CREATE TABLE `ilo` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `number` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `text` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `notes` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `parent_id` mediumint(8) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plan`
--

CREATE TABLE `plan` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `name` varchar(75) COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `type` enum('Major','Minor','Specialization','Medial','General','Sub-Plan') COLLATE utf8_unicode_ci NOT NULL,
  `internship` tinyint(1) NOT NULL DEFAULT '0',
  `prior_to` date DEFAULT NULL,
  `text` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `notes` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plan_and_cpr`
--

CREATE TABLE `plan_and_cpr` (
  `plan_id` mediumint(8) UNSIGNED NOT NULL,
  `cpr_id` mediumint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plan_and_plan`
--

CREATE TABLE `plan_and_plan` (
  `parent_id` mediumint(8) UNSIGNED NOT NULL,
  `child_id` mediumint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plan_and_pllo`
--

CREATE TABLE `plan_and_pllo` (
  `plan_id` mediumint(8) UNSIGNED NOT NULL,
  `pllo_id` mediumint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plan_and_tpr`
--

CREATE TABLE `plan_and_tpr` (
  `plan_id` mediumint(8) UNSIGNED NOT NULL,
  `tpr_id` mediumint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pllo`
--

CREATE TABLE `pllo` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `number` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `text` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  `notes` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `parent_id` mediumint(8) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pllo_and_dle`
--

CREATE TABLE `pllo_and_dle` (
  `pllo_id` mediumint(8) UNSIGNED NOT NULL,
  `dle_id` mediumint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pllo_and_ilo`
--

CREATE TABLE `pllo_and_ilo` (
  `pllo_id` mediumint(8) UNSIGNED NOT NULL,
  `ilo_id` mediumint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `program`
--

CREATE TABLE `program` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `name` varchar(75) COLLATE utf8_unicode_ci DEFAULT NULL,
  `code` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `text` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `notes` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `program_and_degree`
--

CREATE TABLE `program_and_degree` (
  `program_id` mediumint(8) UNSIGNED NOT NULL,
  `degree_id` mediumint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `program_and_plan`
--

CREATE TABLE `program_and_plan` (
  `program_id` mediumint(8) UNSIGNED NOT NULL,
  `plan_id` mediumint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `revision`
--

CREATE TABLE `revision` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `user_id` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `rev_table` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `rev_column` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `key_columns` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `key_values` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `action` enum('added','edited','deleted') COLLATE utf8_unicode_ci NOT NULL,
  `prior_value` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_and_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tpr`
--

CREATE TABLE `tpr` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `name` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `type` enum('Additional','Substitutions','Notes') COLLATE utf8_unicode_ci NOT NULL,
  `text` varchar(750) COLLATE utf8_unicode_ci NOT NULL,
  `notes` varchar(500) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `first_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `role` enum('Undergraduate Assistant','Undergraduate Chair','Curriculum Coordinator','Developer','Associate Dean') COLLATE utf8_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_access`
--

CREATE TABLE `user_access` (
  `user_id` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `logged_in` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cllo`
--
ALTER TABLE `cllo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cllo_and_course`
--
ALTER TABLE `cllo_and_course`
  ADD PRIMARY KEY (`cllo_id`,`course_id`);

--
-- Indexes for table `cllo_and_ilo`
--
ALTER TABLE `cllo_and_ilo`
  ADD PRIMARY KEY (`cllo_id`,`ilo_id`);

--
-- Indexes for table `cllo_and_pllo`
--
ALTER TABLE `cllo_and_pllo`
  ADD PRIMARY KEY (`cllo_id`,`pllo_id`);

--
-- Indexes for table `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`subject`,`number`);

--
-- Indexes for table `courselist`
--
ALTER TABLE `courselist`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `courselist_and_course`
--
ALTER TABLE `courselist_and_course`
  ADD PRIMARY KEY (`courselist_id`,`course_id`);

--
-- Indexes for table `courselist_and_courselist`
--
ALTER TABLE `courselist_and_courselist`
  ADD PRIMARY KEY (`parent_id`,`child_id`,`level`);

--
-- Indexes for table `courselist_and_relationship`
--
ALTER TABLE `courselist_and_relationship`
  ADD PRIMARY KEY (`courselist_id`);

--
-- Indexes for table `courselist_and_subject`
--
ALTER TABLE `courselist_and_subject`
  ADD PRIMARY KEY (`courselist_id`);

--
-- Indexes for table `cpr`
--
ALTER TABLE `cpr`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cpr_and_courselist`
--
ALTER TABLE `cpr_and_courselist`
  ADD PRIMARY KEY (`cpr_id`,`courselist_id`,`level`);

--
-- Indexes for table `degree`
--
ALTER TABLE `degree`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `degree_and_faculty`
--
ALTER TABLE `degree_and_faculty`
  ADD PRIMARY KEY (`degree_id`,`faculty_id`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `department_and_faculty`
--
ALTER TABLE `department_and_faculty`
  ADD PRIMARY KEY (`department_id`,`faculty_id`);

--
-- Indexes for table `department_and_plan`
--
ALTER TABLE `department_and_plan`
  ADD PRIMARY KEY (`department_id`,`plan_id`);

--
-- Indexes for table `department_and_subject`
--
ALTER TABLE `department_and_subject`
  ADD PRIMARY KEY (`department_id`,`subject`);

--
-- Indexes for table `dle`
--
ALTER TABLE `dle`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faculty`
--
ALTER TABLE `faculty`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ilo`
--
ALTER TABLE `ilo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `plan`
--
ALTER TABLE `plan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `plan_and_cpr`
--
ALTER TABLE `plan_and_cpr`
  ADD PRIMARY KEY (`plan_id`,`cpr_id`);

--
-- Indexes for table `plan_and_plan`
--
ALTER TABLE `plan_and_plan`
  ADD PRIMARY KEY (`parent_id`,`child_id`);

--
-- Indexes for table `plan_and_pllo`
--
ALTER TABLE `plan_and_pllo`
  ADD PRIMARY KEY (`plan_id`,`pllo_id`);

--
-- Indexes for table `pllo`
--
ALTER TABLE `pllo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pllo_and_dle`
--
ALTER TABLE `pllo_and_dle`
  ADD PRIMARY KEY (`pllo_id`,`dle_id`);

--
-- Indexes for table `pllo_and_ilo`
--
ALTER TABLE `pllo_and_ilo`
  ADD PRIMARY KEY (`pllo_id`,`ilo_id`);

--
-- Indexes for table `program`
--
ALTER TABLE `program`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `program_and_degree`
--
ALTER TABLE `program_and_degree`
  ADD PRIMARY KEY (`program_id`,`degree_id`);

--
-- Indexes for table `program_and_plan`
--
ALTER TABLE `program_and_plan`
  ADD PRIMARY KEY (`program_id`,`plan_id`);

--
-- Indexes for table `revision`
--
ALTER TABLE `revision`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tpr`
--
ALTER TABLE `tpr`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_access`
--
ALTER TABLE `user_access`
  ADD PRIMARY KEY (`user_id`,`logged_in`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cllo`
--
ALTER TABLE `cllo`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `courselist`
--
ALTER TABLE `courselist`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `cpr`
--
ALTER TABLE `cpr`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `degree`
--
ALTER TABLE `degree`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `dle`
--
ALTER TABLE `dle`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `faculty`
--
ALTER TABLE `faculty`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ilo`
--
ALTER TABLE `ilo`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `plan`
--
ALTER TABLE `plan`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `pllo`
--
ALTER TABLE `pllo`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `program`
--
ALTER TABLE `program`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `revision`
--
ALTER TABLE `revision`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tpr`
--
ALTER TABLE `tpr`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
