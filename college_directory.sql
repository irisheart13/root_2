-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 12, 2025 at 09:46 AM
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
-- Database: `college_directory`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_comments`
--

CREATE TABLE `admin_comments` (
  `id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `title` text NOT NULL,
  `abstract` text NOT NULL,
  `others` text NOT NULL,
  `file_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(4, '2025-03-11 16:31:23', 'ACT', 'CITCS', 'ACT', 'Final Try - EDIT TRY ', 'Iris Heart Prado', 'undefined', 'undefined', '', '[Student ID] Prado, Iris Heart A.pdf', 'Abstract_CITCS-EXAM-SCHEDULE-MIDTERM-2ND-SEM-2024-2025.pdf', NULL, NULL, NULL, NULL, 0),
(5, '2025-03-11 16:32:36', 'ACT', 'CITCS', 'ACT', 'First Try WORKED! EDIT 2nd Try', 'Iris Heart Prado', 'undefined', 'undefined', '', '[Journal] Prado, Iris Heart A.pdf', 'Abstract_[Proof of Matriculation] Prado, Iris Heart A.pdf', NULL, NULL, NULL, NULL, 1),
(6, '2025-03-12 01:30:06', 'ACT', 'CITCS', 'ACT', '2nd Try removed the viewing of previous file - keeping the columns blank instead of putting undefined', 'Iris Heart Prado', '', '', '', 'Research_BLGF-PM-04-12-Disposal-of-Records.pdf', 'Abstract_letter for endorsement.pdf', NULL, NULL, NULL, NULL, 1),
(7, '2025-03-12 01:35:06', 'ACT', 'CITCS', 'ACT', '3rd try adding a prefix to the newly submitted files ', 'asdfkjasldkfalkjsd', '', '', '', 'Resubmitted_[Journal] Prado, Iris Heart A.pdf', 'Abstract_harrypotter.pdf', NULL, NULL, NULL, NULL, 1);

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
-- Indexes for table `admin_comments`
--
ALTER TABLE `admin_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_file` (`file_id`),
  ADD KEY `fk_admin` (`admin_id`);

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
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_comments`
--
ALTER TABLE `admin_comments`
  ADD CONSTRAINT `fk_admin` FOREIGN KEY (`admin_id`) REFERENCES `tbl_user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_file` FOREIGN KEY (`file_id`) REFERENCES `tbl_fileupload` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
