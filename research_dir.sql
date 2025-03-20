-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 20, 2025 at 09:15 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `research_dir`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_comments`
--

CREATE TABLE `admin_comments` (
  `id` int(11) NOT NULL,
  `title` text DEFAULT NULL,
  `abstract` text DEFAULT NULL,
  `others` text DEFAULT NULL,
  `file_id` int(11) DEFAULT NULL,
  `coor_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_comments`
--

INSERT INTO `admin_comments` (`id`, `title`, `abstract`, `others`, `file_id`, `coor_id`) VALUES
(1, 'try comment on id 1', 's,mdnf,mdsfSDF', 'ADFGDFGA', 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_fileupload`
--

CREATE TABLE `tbl_fileupload` (
  `id` int(50) NOT NULL,
  `date_of_submission` timestamp NOT NULL DEFAULT current_timestamp(),
  `username` varchar(50) NOT NULL,
  `department` varchar(50) NOT NULL,
  `program` varchar(50) NOT NULL,
  `remarks` varchar(50) DEFAULT NULL,
  `title` varchar(300) NOT NULL,
  `main_author` varchar(50) NOT NULL,
  `co_author_1` varchar(50) NOT NULL,
  `co_author_2` varchar(50) NOT NULL,
  `others` varchar(150) NOT NULL,
  `file_research_paper` varchar(300) NOT NULL,
  `file_abstract` varchar(300) NOT NULL,
  `notification` varchar(50) DEFAULT NULL,
  `sched_proposal` date DEFAULT NULL,
  `sched_final` date DEFAULT NULL,
  `research_status` varchar(50) DEFAULT NULL,
  `edit_access` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_fileupload`
--

INSERT INTO `tbl_fileupload` (`id`, `date_of_submission`, `username`, `department`, `program`, `remarks`, `title`, `main_author`, `co_author_1`, `co_author_2`, `others`, `file_research_paper`, `file_abstract`, `notification`, `sched_proposal`, `sched_final`, `research_status`, `edit_access`) VALUES
(1, '2025-03-20 06:35:06', 'pradoirisheart_bscs@plmun.edu.ph', 'CITCS', 'BSCS', NULL, 'First Try - new db -try edit (main author) - reupload file', 'Choi Yoon Jeonghan ', '', '', '', '[Journal] Prado, Iris Heart A.pdf', '[Week_6_Journal] Prado, Iris Heart A.pdf', NULL, NULL, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user`
--

CREATE TABLE `tbl_user` (
  `id` int(50) NOT NULL,
  `last_name` text NOT NULL,
  `first_name` text NOT NULL,
  `middle_initial` char(1) NOT NULL,
  `username` varchar(50) NOT NULL,
  `pin` int(4) NOT NULL,
  `department` varchar(50) NOT NULL,
  `program` varchar(50) NOT NULL,
  `role` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_user`
--

INSERT INTO `tbl_user` (`id`, `last_name`, `first_name`, `middle_initial`, `username`, `pin`, `department`, `program`, `role`) VALUES
(1, 'Dela Cruz', 'Juan', 'M', 'coor_ACT', 0, 'CITCS', 'ACT', 'coor'),
(2, 'Santos', 'Maria', 'L', 'coor_BSCS', 0, 'CITCS', 'BSCS', 'coor'),
(3, 'Ramos', 'Pedro', 'B', 'coor_BSIT', 0, 'CITCS', 'BSIT', 'coor'),
(4, 'Reyes', 'Ana', 'D', 'prog_head_ACT', 0, 'CITCS', 'ACT', 'prog_head'),
(5, 'Garcia', 'Mark', 'V', 'prog_head_BSCS', 0, 'CITCS', 'BSCS', 'prog_head'),
(6, 'Fernandez', 'Luisa', 'C', 'prog_head_BSIT', 0, 'CITCS', 'BSIT', 'prog_head'),
(7, 'Mendoza', 'Roberto', 'S', 'dean_CITCS', 0, 'CITCS', '', 'dean'),
(8, 'Prado', 'Iris Heart', 'A', 'pradoirisheart_bscs@plmun.edu.ph', 1234, 'CITCS', 'BSCS', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_comments`
--
ALTER TABLE `admin_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_file_id` (`file_id`),
  ADD KEY `fk_coor_id` (`coor_id`);

--
-- Indexes for table `tbl_fileupload`
--
ALTER TABLE `tbl_fileupload`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_user`
--
ALTER TABLE `tbl_user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_comments`
--
ALTER TABLE `admin_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_fileupload`
--
ALTER TABLE `tbl_fileupload`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_user`
--
ALTER TABLE `tbl_user`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_comments`
--
ALTER TABLE `admin_comments`
  ADD CONSTRAINT `fk_coor_id` FOREIGN KEY (`coor_id`) REFERENCES `tbl_user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_file_id` FOREIGN KEY (`file_id`) REFERENCES `tbl_fileupload` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
