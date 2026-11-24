-- MySQL dump 10.13  Distrib 8.0.36, for Linux (x86_64)
--
-- Host: 127.0.0.1    Database: dec45
-- ------------------------------------------------------
-- Server version	8.0.41-0ubuntu0.22.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `ex_date_time_schedule`
--

DROP TABLE IF EXISTS `ex_date_time_schedule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ex_date_time_schedule` (
  `id` int NOT NULL AUTO_INCREMENT,
  `exam_schedule_entry_relId` int NOT NULL,
  `date` int NOT NULL,
  `shift` enum('MORNING','AFTERNOON','UKNOWN') DEFAULT 'MORNING',
  `start_time` int NOT NULL,
  `end_time` int NOT NULL,
  `standbys` json DEFAULT NULL,
  `supervisors` json DEFAULT NULL,
  `created_by` int NOT NULL,
  `created_at` int NOT NULL,
  `updated_at` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `data` tinytext,
  PRIMARY KEY (`id`),
  KEY `ex_02_idx` (`updated_by`),
  KEY `ex_01_idx` (`created_by`),
  KEY `ex_06_idx` (`id`),
  KEY `09123` (`id`),
  KEY `ex_003_idx` (`exam_schedule_entry_relId`),
  CONSTRAINT `ex_003` FOREIGN KEY (`exam_schedule_entry_relId`) REFERENCES `ex_exam_schedule_entry` (`id`),
  CONSTRAINT `ex_01` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`),
  CONSTRAINT `ex_02` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ex_date_time_schedule`
--

LOCK TABLES `ex_date_time_schedule` WRITE;
/*!40000 ALTER TABLE `ex_date_time_schedule` DISABLE KEYS */;
INSERT INTO `ex_date_time_schedule` VALUES (1,1,1737925200,'MORNING',1737786600,1737798300,'\"{\\\"0\\\":\\\"3551\\\",\\\"3\\\":\\\"4081\\\",\\\"4\\\":\\\"1904\\\"}\"','\"[\\\"4082\\\"]\"',3,1737834744,1737995172,3,'65ad2a2742f97ec72b95e231ed91c37b'),(3,1,1738011600,'MORNING',1737957600,1737968400,NULL,NULL,3,1737996188,NULL,NULL,'a0189f48724092f8138f5d540aeaaabf'),(4,1,1738357200,'AFTERNOON',1738323000,1738330200,NULL,NULL,3,1738271903,1738272099,3,'9c95cba4e2f22d07f3f9aa063e3bfb59'),(5,1,1738357200,'MORNING',1738389600,1738402200,NULL,NULL,3,1738386611,NULL,NULL,'b2bed6220c94ef5e1df546bb8d0a35ee'),(6,1,1738443600,'MORNING',1738391400,1738402200,'\"[]\"','\"[\\\"1977\\\"]\"',3,1738386712,1738414707,3,'c929f8e5dae1cd3523afcf281b50e3f0'),(7,1,1738443600,'AFTERNOON',1738364400,1738376400,NULL,NULL,3,1738386888,NULL,NULL,'6026dbc2b1705770cd083fba4b225b72'),(8,1,1738530000,'MORNING',1738476000,1738488600,NULL,NULL,3,1738480209,NULL,NULL,'a49b6e3a5d970ee3321782135c258e0f'),(9,1,1738530000,'AFTERNOON',1738450800,1738463400,NULL,NULL,3,1738480257,NULL,NULL,'797dc43692b880c5cc355fe0f14b28d2'),(10,1,1738616400,'MORNING',1738476000,1738486800,NULL,NULL,3,1738480300,NULL,NULL,'bf489b5f4a029069769dc643f06491c3'),(11,1,1738616400,'AFTERNOON',1738476000,1738488600,NULL,NULL,3,1738480511,NULL,NULL,'12c8e4064f2f7db8695275a0cee23b2d');
/*!40000 ALTER TABLE `ex_date_time_schedule` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ex_exam_room_slot`
--

DROP TABLE IF EXISTS `ex_exam_room_slot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ex_exam_room_slot` (
  `id` int NOT NULL AUTO_INCREMENT,
  `date_time_slot_relId` int NOT NULL,
  `offering_detail_IDs` json DEFAULT NULL,
  `class_room_relId` int NOT NULL,
  `created_at` int NOT NULL,
  `created_by` int NOT NULL,
  `updated_at` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `data` tinytext,
  PRIMARY KEY (`id`),
  KEY `ex_06_idx` (`created_by`),
  KEY `ex_07_idx` (`updated_by`),
  KEY `ex_0192` (`date_time_slot_relId`,`class_room_relId`),
  KEY `wqq` (`class_room_relId`) /*!80000 INVISIBLE */,
  KEY `rew` (`class_room_relId`),
  CONSTRAINT `ex_06` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`),
  CONSTRAINT `ex_07` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`),
  CONSTRAINT `ex_exam_room_slot_ibfk_1` FOREIGN KEY (`date_time_slot_relId`) REFERENCES `ex_date_time_schedule` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `ex_exam_room_slot_ibfk_2` FOREIGN KEY (`class_room_relId`) REFERENCES `cs_available_class_room` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ex_exam_room_slot`
--

LOCK TABLES `ex_exam_room_slot` WRITE;
/*!40000 ALTER TABLE `ex_exam_room_slot` DISABLE KEYS */;
INSERT INTO `ex_exam_room_slot` VALUES (1,1,'\"[2173,2213,2239]\"',1,1738000088,3,1738385660,3,'dc2b121329fc29695edbbf5dee0831f0'),(2,4,'\"[2284]\"',2,1738272187,3,1738486246,3,'ed66785fda66932c37a0053abaadaa2a'),(3,3,'\"[2077,2086,2326,2324]\"',3,1738381882,3,1738386342,3,'193b0092d528319673c53a0242ca07d4'),(4,6,'\"[2100]\"',1,1738387014,3,1738387055,3,'9223168afc30e34fb7d92f4d6f89a914'),(5,6,'\"[2100]\"',2,1738387023,3,1738387038,3,'cce0dd60a4ab719c1bceaf06a03bbab9'),(6,7,'\"[2109,2314,2259]\"',1,1738387091,3,1738474729,3,'b1df854390490f290a3f23856c1d7f29'),(7,7,'\"[2147]\"',2,1738387099,3,1738440981,3,'fc6536411f18b24bf8956422bdcee5a3'),(8,7,'\"[2308,2285,2279]\"',6,1738402011,3,1738425451,3,'379d2a815b4db59556b83b5df0ae9e10'),(9,7,'\"[2248,2199]\"',5,1738402064,3,1738478063,3,'d1d414fc4da09b537c734269322d61df'),(10,11,'\"[2096]\"',1,1738480572,3,1738480628,3,'30bea8e2e833e8c106550a7e56edaffe'),(11,5,'\"[2337,2320]\"',1,1738483696,3,1738485141,3,'28ba0fae423601013e0c0ebd55f91086');
/*!40000 ALTER TABLE `ex_exam_room_slot` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ex_exam_schedule_entry`
--

DROP TABLE IF EXISTS `ex_exam_schedule_entry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ex_exam_schedule_entry` (
  `id` int NOT NULL AUTO_INCREMENT,
  `acad_year_relId` int NOT NULL,
  `start_date` int NOT NULL,
  `end_date` int NOT NULL,
  `header_text` tinytext NOT NULL,
  `created_at` int NOT NULL,
  `created_by` int NOT NULL,
  `updated_at` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `data` tinytext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `acad_year_relId` (`acad_year_relId`),
  KEY `created_by` (`created_by`),
  KEY `updated_by` (`updated_by`),
  CONSTRAINT `ex_exam_schedule_entry_ibfk_1` FOREIGN KEY (`acad_year_relId`) REFERENCES `ac_academic_year` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `ex_exam_schedule_entry_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `ex_exam_schedule_entry_ibfk_3` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ex_exam_schedule_entry`
--

LOCK TABLES `ex_exam_schedule_entry` WRITE;
/*!40000 ALTER TABLE `ex_exam_schedule_entry` DISABLE KEYS */;
INSERT INTO `ex_exam_schedule_entry` VALUES (1,10,1737752400,1738011600,'Final Examination Schedule for ACY 2024/25-I',1737815303,3,1737970299,3,'80d133d45f95a4f8bce572b2c6fc5c17');
/*!40000 ALTER TABLE `ex_exam_schedule_entry` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ex_examinees`
--

DROP TABLE IF EXISTS `ex_examinees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ex_examinees` (
  `id` int NOT NULL AUTO_INCREMENT,
  `exam_slot_relId` int NOT NULL,
  `std_relId` int NOT NULL,
  `section_relId` int NOT NULL,
  `offering_detail_relId` int NOT NULL,
  `class_attendance_percent` decimal(5,2) DEFAULT '0.00',
  `created_at` int NOT NULL,
  `created_by` int NOT NULL,
  `updated_at` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `data` tinytext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ex_03_idx` (`created_by`),
  KEY `ex_03_idx1` (`updated_by`),
  KEY `exam_slot_relId` (`exam_slot_relId`),
  KEY `offering_detail_relId` (`offering_detail_relId`),
  KEY `section_relId` (`section_relId`),
  KEY `std_relId` (`std_relId`),
  CONSTRAINT `ex_examinees_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `ex_examinees_ibfk_2` FOREIGN KEY (`exam_slot_relId`) REFERENCES `ex_exam_room_slot` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `ex_examinees_ibfk_3` FOREIGN KEY (`offering_detail_relId`) REFERENCES `course_offering2_detail` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `ex_examinees_ibfk_4` FOREIGN KEY (`section_relId`) REFERENCES `w_group_setup` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `ex_examinees_ibfk_5` FOREIGN KEY (`std_relId`) REFERENCES `student` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `ex_examinees_ibfk_6` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=127 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ex_examinees`
--

LOCK TABLES `ex_examinees` WRITE;
/*!40000 ALTER TABLE `ex_examinees` DISABLE KEYS */;
INSERT INTO `ex_examinees` VALUES (1,1,970,411,2239,0.00,1738352217,3,NULL,NULL,'8696b0358a4a2b191434aa9548a536a9'),(2,1,823,411,2239,0.00,1738352515,3,NULL,NULL,'efb47d30e247edeac13f028d6d47bf8f'),(3,1,1441,396,2173,0.00,1738353132,3,NULL,NULL,'3075d030ad7ecb81d64ed2f96abf7e4b'),(4,1,1313,396,2173,0.00,1738353132,3,NULL,NULL,'0ad585a49a27d6f77285744b9cbe9c42'),(5,1,1338,396,2173,0.00,1738353132,3,NULL,NULL,'fa42a13aed9f8f78bdab13e9d8466513'),(6,1,1458,396,2173,0.00,1738353132,3,NULL,NULL,'374fc7d4e0110443af7ff0e299d67b20'),(7,1,1264,396,2173,0.00,1738353132,3,NULL,NULL,'5904ab9deb463e7faec8e41edb4d949b'),(8,1,1216,396,2173,0.00,1738353132,3,NULL,NULL,'a8a48c1a128ba8e07aeb0d555e4588de'),(9,1,1381,396,2173,0.00,1738353132,3,NULL,NULL,'f86b0a51935346481ad5e1d6e198cbce'),(10,1,407,408,2213,0.00,1738353154,3,NULL,NULL,'11351f61c581000716115845179bdb62'),(11,1,439,408,2213,0.00,1738353154,3,NULL,NULL,'3ad96d3c6fdecb4bd6072c45da74f333'),(12,1,341,408,2213,0.00,1738353154,3,NULL,NULL,'3f8c2a55adeb70911c334bbd55802931'),(13,1,427,408,2213,0.00,1738353154,3,NULL,NULL,'c616d8aff467e352d5c10c5f607240f1'),(14,1,632,408,2213,0.00,1738353154,3,NULL,NULL,'1eb16c3ab2e205b975c573c5722097c7'),(15,1,601,408,2213,0.00,1738353154,3,NULL,NULL,'d78ece9f460709a9c952fd47a8178a29'),(16,1,458,408,2213,0.00,1738353154,3,NULL,NULL,'eca557b1a09a2e2d4a2d95f49535986a'),(17,1,1856,438,2387,0.00,1738353171,3,NULL,NULL,'c21ced529d0b4c27da3dc43900a3a20d'),(18,1,1835,438,2387,0.00,1738353171,3,NULL,NULL,'859c79fe4a6b8adb4b2a58ee76fee987'),(19,1,1776,438,2387,0.00,1738353171,3,NULL,NULL,'ac2b9c3bf6b63aebc6130b2a2823983f'),(20,1,1814,438,2387,0.00,1738353171,3,NULL,NULL,'0c54b59b9a376a2507b5f00fb51742b0'),(21,1,734,411,2239,0.00,1738353188,3,NULL,NULL,'f1ba068dcf514590f24f86db140a4a4c'),(22,1,755,411,2239,0.00,1738353188,3,NULL,NULL,'82339b1c51fffa8308239c77dbca49d3'),(23,1,719,411,2239,0.00,1738353188,3,NULL,NULL,'519239e439ba047cce4451858b356076'),(24,1,846,411,2239,0.00,1738353188,3,NULL,NULL,'e7ed86553e50fe66a498a6072c647c47'),(25,1,784,411,2239,0.00,1738353188,3,NULL,NULL,'16d461597415c49c5a026516bdc45ea2'),(26,1,815,411,2239,0.00,1738353188,3,NULL,NULL,'8957a4101900f59c34d24106c7d344b9'),(27,1,1093,411,2239,0.00,1738353188,3,NULL,NULL,'8c6297065f23f4a2f5d3d55bbce4ce0b'),(28,1,1353,396,2173,0.00,1738353201,3,NULL,NULL,'cb0e6a6f069f64483054e7d0b0d440aa'),(29,1,1150,396,2173,0.00,1738353201,3,NULL,NULL,'94f6aa0c9a9fb8166929ada1f705a9af'),(30,1,1192,396,2173,0.00,1738353201,3,NULL,NULL,'7bb1d17b9c7c48689c932cb2cd1857fd'),(40,3,1123,383,2086,0.00,1738386405,3,NULL,NULL,'d49bf7ea87ae8002bf64745dc2154f17'),(41,3,738,383,2086,0.00,1738386405,3,NULL,NULL,'0e137f187eda51d1ff9e452b19208357'),(42,3,778,383,2086,0.00,1738386405,3,NULL,NULL,'9a3829e35025cb2415619dd2e75e17ee'),(43,3,928,383,2086,0.00,1738386405,3,NULL,NULL,'711e82fcfc5717e5f3eb7e57d71d3ff2'),(44,3,749,383,2086,0.00,1738386405,3,NULL,NULL,'2399846ebcba6e812aee2600a1475d8c'),(45,3,697,383,2086,0.00,1738386405,3,NULL,NULL,'f373eb435afe6b169b175cb17e96ebac'),(46,3,850,383,2086,0.00,1738386405,3,NULL,NULL,'ad04188449452d7c85cfe8ca48e5f9f8'),(47,3,1095,383,2086,0.00,1738386405,3,NULL,NULL,'6f124245687814c366cfa1867699b02f'),(48,3,1105,383,2086,0.00,1738386405,3,NULL,NULL,'63b697a575da0fa732a6e0218d1db386'),(49,3,1089,383,2086,0.00,1738386405,3,NULL,NULL,'0c5552da426864f0b240acdeee7b0caf'),(50,3,1092,383,2086,0.00,1738386405,3,NULL,NULL,'0bbacf2e4b2f74bbb24045cccb4dab30'),(51,3,1059,383,2086,0.00,1738386405,3,NULL,NULL,'c45710df1b734d7231dbad7cd9ab6b10'),(67,9,1308,410,2248,0.00,1738402176,3,NULL,NULL,'54ee433e05f8c577a3093b112f7877de'),(68,9,1404,410,2248,0.00,1738402176,3,NULL,NULL,'50368fa540a44008172fb611063a89ce'),(69,9,1417,410,2248,0.00,1738402176,3,NULL,NULL,'7b283c5de9c543e0fe926a405d3f989b'),(70,9,1461,410,2248,0.00,1738402176,3,NULL,NULL,'a9e0f74924808dc2ad2576afe8c505a3'),(71,9,1364,410,2248,0.00,1738402176,3,NULL,NULL,'cb60d5a747f34f4bae49724d93517f60'),(72,9,1405,410,2248,0.00,1738402176,3,NULL,NULL,'3399aacc1e92e6f411459e7319cd33c3'),(73,9,1638,410,2248,0.00,1738402176,3,NULL,NULL,'f0d0ca96b0bde822bada3be8df797a10'),(74,9,1630,410,2248,0.00,1738402176,3,NULL,NULL,'9cf2c96b169c9189e69ae2cbae31d1a1'),(75,9,1430,410,2248,0.00,1738402176,3,NULL,NULL,'15b20ae8a9b738e06933d607ed119b31'),(76,9,1418,410,2248,0.00,1738402176,3,NULL,NULL,'8659ca8b414146894aa8c2d84a0182d5'),(77,9,1256,410,2248,0.00,1738402176,3,NULL,NULL,'78fb7be33f65d80451cbf3f64f3f948a'),(78,9,1345,410,2248,0.00,1738402176,3,NULL,NULL,'1b3e9c79fa6cc1a5fc3657eb2f3dda99'),(79,9,1255,410,2248,0.00,1738402176,3,NULL,NULL,'371a77863321bff63d8e5dc9e4fb17ef'),(80,9,1459,410,2248,0.00,1738402176,3,NULL,NULL,'f906e42dd5e0688951baa8bf50b3f025'),(81,9,1314,410,2248,0.00,1738402176,3,NULL,NULL,'0b14a0c7e361851e64f410abdbb5dc70'),(82,2,1525,418,2284,0.00,1738403070,3,NULL,NULL,'5fd2febae787b01743bebedb367a308e'),(83,2,1571,418,2284,0.00,1738403070,3,NULL,NULL,'d7917cf657b1e281bf86831441bfd6d9'),(84,2,1475,418,2284,0.00,1738403070,3,NULL,NULL,'ad97d39f28d03a8ddf9b34f1e472e76c'),(105,8,1634,417,2279,0.00,1738425475,3,NULL,NULL,'4969eaaec54065db1c882cbd726bfb0a'),(106,8,1635,417,2279,0.00,1738425475,3,NULL,NULL,'6e83d6798ec9714e51146b346bfa16f9'),(107,8,1490,417,2279,0.00,1738425475,3,NULL,NULL,'fc9418539434d94f9fa426bbda2f5056'),(108,8,1558,417,2279,0.00,1738425475,3,NULL,NULL,'f26803b09b96418c0902bab2bb67ce14'),(109,8,1511,417,2279,0.00,1738425475,3,NULL,NULL,'72e6b66882cb36296538ce0113ba4ae3'),(110,8,1555,417,2279,0.00,1738425475,3,NULL,NULL,'043862047798c7ebfdec5589ceadc368'),(111,8,44,402,2308,0.00,1738425493,3,NULL,NULL,'2ac401b955b50d79c597f1cf53a18da0'),(112,8,3,402,2308,0.00,1738425493,3,NULL,NULL,'04e3df73a369a0c1926651d8f41d2251'),(113,8,42,402,2308,0.00,1738425493,3,NULL,NULL,'2fc1c57b4821119e4d2de3a31e0dccf7'),(114,8,8,402,2308,0.00,1738425493,3,NULL,NULL,'4919d75c941495018cbc6b227d8480c6'),(115,8,68,402,2308,0.00,1738425493,3,NULL,NULL,'ede035a190be2fc0ae1285a40eb8edff'),(116,8,316,402,2308,0.00,1738425493,3,NULL,NULL,'d4acc0cbcc2600fdb2b40d66878ccd35'),(117,8,186,402,2308,0.00,1738425493,3,NULL,NULL,'5b4f9decf5630bb55e040676f984431b'),(118,8,18,402,2308,0.00,1738425493,3,NULL,NULL,'0e59937802a0f48d2aa9d1f0df4309ce'),(119,8,24,402,2308,0.00,1738425493,3,NULL,NULL,'1033054eb87b84e4474dd67ce1ac0a77'),(120,8,56,402,2308,0.00,1738425493,3,NULL,NULL,'fb16d6fecb4d0736e4ab457d1c8e1105'),(121,8,13,402,2308,0.00,1738425493,3,NULL,NULL,'2ca3f49de4fda3649bf69bb1e1a16e31'),(122,8,101,402,2308,0.00,1738425493,3,NULL,NULL,'2648f855b0554f73d00222900b41cb38'),(123,8,1265,402,2308,0.00,1738425493,3,NULL,NULL,'893d80fd84fd471bec0c9743edaceef5'),(124,8,1475,418,2285,0.00,1738425506,3,NULL,NULL,'bf1cf307c09a2968084dbd39736bc22e'),(125,8,1571,418,2285,0.00,1738425506,3,NULL,NULL,'3fb971474a3e14a170b7d3c41f07e847'),(126,8,1525,418,2285,0.00,1738425506,3,NULL,NULL,'241f35a925f283d6410823b94cc75548');
/*!40000 ALTER TABLE `ex_examinees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ex_inv_attendance_form`
--

DROP TABLE IF EXISTS `ex_inv_attendance_form`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ex_inv_attendance_form` (
  `id` int NOT NULL AUTO_INCREMENT,
  `date_time_slot_relId` int NOT NULL,
  `exam_slot_relId` int NOT NULL,
  `inviglator_relId` int NOT NULL,
  `assigned_as` tinytext NOT NULL,
  `status` int DEFAULT '0',
  `approved` int DEFAULT '0',
  `created_at` int NOT NULL,
  `created_by` int NOT NULL,
  `updated_at` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `data` tinytext,
  PRIMARY KEY (`id`),
  KEY `ex_15_idx` (`created_by`),
  KEY `ex_16_idx` (`updated_by`),
  KEY `ex_17_idx` (`inviglator_relId`),
  KEY `date_time_slot_relId` (`date_time_slot_relId`),
  KEY `exam_slot_relId` (`exam_slot_relId`),
  CONSTRAINT `ex_15` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`),
  CONSTRAINT `ex_16` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`),
  CONSTRAINT `ex_17` FOREIGN KEY (`inviglator_relId`) REFERENCES `user` (`id`),
  CONSTRAINT `ex_inv_attendance_form_ibfk_1` FOREIGN KEY (`date_time_slot_relId`) REFERENCES `ex_date_time_schedule` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `ex_inv_attendance_form_ibfk_2` FOREIGN KEY (`exam_slot_relId`) REFERENCES `ex_exam_room_slot` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ex_inv_attendance_form`
--

LOCK TABLES `ex_inv_attendance_form` WRITE;
/*!40000 ALTER TABLE `ex_inv_attendance_form` DISABLE KEYS */;
/*!40000 ALTER TABLE `ex_inv_attendance_form` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ex_invigilators`
--

DROP TABLE IF EXISTS `ex_invigilators`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ex_invigilators` (
  `id` int NOT NULL AUTO_INCREMENT,
  `exam_room_slot_relId` int NOT NULL,
  `section_relId` int DEFAULT NULL,
  `offering_detail_relId` int DEFAULT NULL,
  `staff_relId` int NOT NULL,
  `created_at` int NOT NULL,
  `created_by` int NOT NULL,
  `updated_at` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `data` tinytext,
  PRIMARY KEY (`id`),
  KEY `ex_20_idx` (`created_by`),
  KEY `ex_11_idx` (`updated_by`),
  KEY `exam_room_slot_relId` (`exam_room_slot_relId`),
  KEY `offering_detail_relId` (`offering_detail_relId`),
  KEY `section_relId` (`section_relId`),
  KEY `fk_ex_invigilators_4_idx` (`staff_relId`),
  CONSTRAINT `ex_10` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`),
  CONSTRAINT `ex_11` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`),
  CONSTRAINT `ex_invigilators_ibfk_1` FOREIGN KEY (`exam_room_slot_relId`) REFERENCES `ex_exam_room_slot` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `ex_invigilators_ibfk_2` FOREIGN KEY (`offering_detail_relId`) REFERENCES `course_offering2_detail` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `ex_invigilators_ibfk_3` FOREIGN KEY (`section_relId`) REFERENCES `w_group_setup` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_ex_invigilators_4` FOREIGN KEY (`staff_relId`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ex_invigilators`
--

LOCK TABLES `ex_invigilators` WRITE;
/*!40000 ALTER TABLE `ex_invigilators` DISABLE KEYS */;
INSERT INTO `ex_invigilators` VALUES (1,8,NULL,NULL,3545,1738422227,3,NULL,NULL,'cd16cb5b91e473f410118d07469ed88a'),(3,8,NULL,NULL,3843,1738424769,3,NULL,NULL,'7bf81aec996f8ab22157b6308e411c60'),(6,8,NULL,NULL,3533,1738425427,3,NULL,NULL,'d5398186b48eae3f5d9726a1a18a46a1'),(7,7,NULL,NULL,1920,1738427381,3,NULL,NULL,'4b02cf0cd284ef3f2052af480b125978'),(8,9,NULL,NULL,1985,1738478915,3,NULL,NULL,'f3f5375472d681f5086d086a6095f4cd'),(9,9,NULL,NULL,3564,1738478915,3,NULL,NULL,'9a6b0168044d35fad99fcff41b1cbdd7');
/*!40000 ALTER TABLE `ex_invigilators` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-02-02 12:06:26
