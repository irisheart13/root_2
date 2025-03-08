-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: college_directory
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `tbl_fileupload`
--

DROP TABLE IF EXISTS `tbl_fileupload`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_fileupload`
--

LOCK TABLES `tbl_fileupload` WRITE;
/*!40000 ALTER TABLE `tbl_fileupload` DISABLE KEYS */;
INSERT INTO `tbl_fileupload` VALUES (0,'2025-03-08 14:17:59','ACT','CITCS','ACT','asjdlkjasdlkjf','asdfkjasldkfalkjsd','','','','uploads/research_67cc5197b70f94.82433580.pdf','uploads/abstract_67cc5197b750e3.61269075.pdf','Scheduled for Research Proposal Presentation','2025-03-14',NULL,NULL);
/*!40000 ALTER TABLE `tbl_fileupload` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_user`
--

DROP TABLE IF EXISTS `tbl_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_user` (
  `id` int(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `role` varchar(50) NOT NULL,
  `pin` varchar(50) NOT NULL,
  `department` varchar(150) NOT NULL,
  `program` varchar(150) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_user`
--

LOCK TABLES `tbl_user` WRITE;
/*!40000 ALTER TABLE `tbl_user` DISABLE KEYS */;
INSERT INTO `tbl_user` VALUES (1,'ACT','student','1234','CITCS','ACT'),(2,'BSCS','student','1234','CITCS','BSCS'),(3,'BSIT','student','1234','CITCS','BSIT'),(4,'admin_BSIT','admin','0000','CITCS','BSIT'),(5,'admin_BSCS','admin','0000','CITCS','BSCS'),(6,'admin_ACT','admin','0000','CITCS','ACT');
/*!40000 ALTER TABLE `tbl_user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-03-08 23:07:22
