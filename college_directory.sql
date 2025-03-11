-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 11, 2025 at 05:48 PM
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
-- Database: `college_directory`
--

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
  `research_status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_fileupload`
--

INSERT INTO `tbl_fileupload` (`id`, `date_of_submission`, `username`, `department`, `program`, `title`, `main_author`, `co_author_1`, `co_author_2`, `others`, `file_research_paper`, `file_abstract`, `notification`, `sched_proposal`, `sched_final`, `research_status`) VALUES
(4, '2025-03-11 16:31:23', 'ACT', 'CITCS', 'ACT', 'Final Try - EDIT TRY ', 'Iris Heart Prado', 'undefined', 'undefined', '', '[Student ID] Prado, Iris Heart A.pdf', 'Abstract_CITCS-EXAM-SCHEDULE-MIDTERM-2ND-SEM-2024-2025.pdf', NULL, NULL, NULL, NULL),
(5, '2025-03-11 16:32:36', 'ACT', 'CITCS', 'ACT', 'First Try WORKED! EDIT 2nd Try', 'Iris Heart Prado', 'undefined', 'undefined', '', 'PradoIrisHeartA_WeeklyJournal_Week3.pdf', 'Abstract_[Proof of Matriculation] Prado, Iris Heart A.pdf', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user`
--

CREATE TABLE `tbl_user` (
  `id` int(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `role` varchar(50) NOT NULL,
  `pin` varchar(50) NOT NULL,
  `department` varchar(150) NOT NULL,
  `program` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_user`
--

INSERT INTO `tbl_user` (`id`, `username`, `role`, `pin`, `department`, `program`) VALUES
(1, 'ACT', 'student', '1234', 'CITCS', 'ACT'),
(2, 'BSCS', 'student', '1234', 'CITCS', 'BSCS'),
(3, 'BSIT', 'student', '1234', 'CITCS', 'BSIT'),
(4, 'admin_BSIT', 'admin', '0000', 'CITCS', 'BSIT'),
(5, 'admin_BSCS', 'admin', '0000', 'CITCS', 'BSCS'),
(6, 'admin_ACT', 'admin', '0000', 'CITCS', 'ACT');

--
-- Indexes for dumped tables
--

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
-- AUTO_INCREMENT for table `tbl_fileupload`
--
ALTER TABLE `tbl_fileupload`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
