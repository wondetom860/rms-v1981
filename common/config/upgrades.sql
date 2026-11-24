-- MySQL dump 10.13  Distrib 5.7.17, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: dec32
-- ------------------------------------------------------
-- Server version	5.5.5-10.1.38-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `payment_redemption`
--

DROP TABLE IF EXISTS `payment_redemption`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment_redemption` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `acadYear_relId` int(11) NOT NULL,
  `sem_relId` int(11) NOT NULL,
  `std_relId` int(11) NOT NULL,
  `reason` tinytext NOT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` int(11) DEFAULT NULL,
  `used` int(1) DEFAULT NULL,
  `comments` tinytext,
  `created_at` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `data` tinytext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `acadYear_relId` (`acadYear_relId`),
  KEY `sem_relId` (`sem_relId`),
  KEY `std_relId` (`std_relId`),
  KEY `approved_by` (`approved_by`),
  KEY `created_by` (`created_by`),
  KEY `updated_by` (`updated_by`),
  CONSTRAINT `payment_redemption_ibfk_1` FOREIGN KEY (`acadYear_relId`) REFERENCES `school_year` (`id`),
  CONSTRAINT `payment_redemption_ibfk_2` FOREIGN KEY (`sem_relId`) REFERENCES `semester` (`id`),
  CONSTRAINT `payment_redemption_ibfk_3` FOREIGN KEY (`std_relId`) REFERENCES `student` (`id`),
  CONSTRAINT `payment_redemption_ibfk_4` FOREIGN KEY (`approved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `payment_redemption_ibfk_5` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`),
  CONSTRAINT `payment_redemption_ibfk_6` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-09-28 11:56:32
ALTER TABLE `user` 
ADD COLUMN `last_login` INT(11) NULL AFTER `faculty_relId`,
ADD COLUMN `_last_login` INT(11) NULL AFTER `last_login`;

ALTER TABLE `grade_scale` 
ADD COLUMN `gs_scale_status` INT(1) NULL DEFAULT 1 AFTER `data`;

ALTER TABLE `course_offering_temp` 
ADD COLUMN `cot_acProg_relId` INT(11) NULL AFTER `cot_registrarApproved_by`;

ALTER TABLE `specialization` 
ADD COLUMN `spec_code` VARCHAR(10) NULL AFTER `spec_updated_by`;

ALTER TABLE `w_group_setup` 
ADD COLUMN `gs_active_prog_relId` INT(11) NULL AFTER `gs_internship`;

ALTER TABLE `course_offering2_detail` 
ADD COLUMN `coff2det_acProg_relId` INT(11) NULL AFTER `coff2det_bypassEvaluation`;

ALTER TABLE `course_offering2` 
ADD COLUMN `coff2_acProg_relId` INT(11) NULL AFTER `coff2_group_relId`,
ADD COLUMN `coff2_cocatIds` TINYTEXT NULL AFTER `coff2_acProg_relId`,
ADD COLUMN `coff2_lock_status` INT(1) NULL DEFAULT 0 AFTER `coff2_cocatIds`,
ADD COLUMN `coff2_created_at` INT(11) NOT NULL AFTER `coff2_lock_status`,
ADD COLUMN `coff2_created_by` INT(11) NOT NULL AFTER `coff2_created_at`,
ADD COLUMN `coff2_updated_at` INT(11) NULL AFTER `coff2_created_by`,
ADD COLUMN `coff2_updated_by` INT(11) NULL AFTER `coff2_updated_at`,
ADD COLUMN `data` TINYTEXT NULL AFTER `coff2_updated_by`;


CREATE TABLE `sent_publishd_grades` (
  `id` int NOT NULL AUTO_INCREMENT,
  `semreg_relId` int NOT NULL,
  `date_published` int NOT NULL,
  `recipient_email` varchar(150) NOT NULL,
  `relation` varchar(150) NOT NULL,
  `reg_approved` smallint DEFAULT '0',
  `mail_sent` smallint DEFAULT '0',
  `date_sent` int DEFAULT NULL,
  `grade_report_path` varchar(150) DEFAULT NULL,
  `trail_count` smallint DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_pgr_semreg_idx` (`semreg_relId`),
  CONSTRAINT `fk_pgr_semreg` FOREIGN KEY (`semreg_relId`) REFERENCES `semester_registration_2` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;