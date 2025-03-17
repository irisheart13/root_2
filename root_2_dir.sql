-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 17, 2025 at 05:57 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `root_2_dir`
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
(1, 'jdncsdnc', 'alsknclkec', 'v jvn dlsdknv sjk', 1, 6),
(2, 'try-coor-comment', 'try-coor-comment', 'try-coor-comment', 2, 6);

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

INSERT INTO `tbl_fileupload` (`id`, `date_of_submission`, `username`, `department`, `program`, `title`, `main_author`, `co_author_1`, `co_author_2`, `others`, `file_research_paper`, `file_abstract`, `notification`, `sched_proposal`, `sched_final`, `research_status`, `edit_access`) VALUES
(1, '2025-03-17 02:06:11', 'ACT', 'CITCS', 'ACT', 'Try - Comment', 'Iris Heart Prado', '', '', '', 'Research_[Journal] Prado, Iris Heart A.pdf', 'Abstract_BLGF-PM-04-12-Disposal-of-Records.pdf', NULL, NULL, NULL, NULL, 1),
(2, '2025-03-17 13:35:59', 'user1_act@plmun.edu.ph', 'CITCS', 'ACT', 'try-user-edit button', 'try-user', '', '', '', 'Resubmitted_Resume_Prado Iris Heart A.pdf', 'Abstract_[PESO Training] Prado Iris Heart A.pdf', 'For Revision', NULL, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user`
--

CREATE TABLE `tbl_user` (
  `id` int(50) NOT NULL,
  `fullname` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `role` varchar(50) NOT NULL,
  `pin` varchar(50) NOT NULL,
  `department` varchar(150) NOT NULL,
  `program` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_user`
--

INSERT INTO `tbl_user` (`id`, `fullname`, `username`, `role`, `pin`, `department`, `program`) VALUES
(1, '', 'prog_head_ACT', 'prog_head', '1234', 'CITCS', 'ACT'),
(2, '', 'prog_head_BSCS', 'prog_head', '1234', 'CITCS', 'BSCS'),
(3, '', 'prog__head_BSIT', 'prog_head', '1234', 'CITCS', 'BSIT'),
(4, '', 'coor_BSIT', 'coor', '0000', 'CITCS', 'BSIT'),
(5, '', 'coor_BSCS', 'coor', '0000', 'CITCS', 'BSCS'),
(6, '', 'coor_ACT', 'coor', '0000', 'CITCS', 'ACT'),
(7, '', 'user1_act@plmun.edu.ph', 'user', '1234', 'CITCS', 'ACT'),
(8, '', 'user2_bscs@plmun.edu.ph', 'user', '1234', 'CITCS', 'BSCS'),
(9, '', 'user3_bsit@plmun.edu.ph', 'user', '1234', 'CITCS', 'BSIT'),
(10, 'Prado, Iris Heart A.', 'pradoirisheart_bscs@plmun.edu.ph', 'user', '1234', 'CITCS', 'BSCS'),
(11, '', 'dean_CITCS', 'dean', '0000', 'CITCS', ''),
(12, '', 'dean_CBA', 'dean', '0000', 'CBA', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_comments`
--
ALTER TABLE `admin_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `file_id` (`file_id`),
  ADD KEY `admin_id` (`coor_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_fileupload`
--
ALTER TABLE `tbl_fileupload`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_user`
--
ALTER TABLE `tbl_user`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_comments`
--
ALTER TABLE `admin_comments`
  ADD CONSTRAINT `admin_comments_ibfk_1` FOREIGN KEY (`file_id`) REFERENCES `tbl_fileupload` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `admin_comments_ibfk_2` FOREIGN KEY (`coor_id`) REFERENCES `tbl_user` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
