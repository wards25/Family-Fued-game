-- MySQL dump 10.13  Distrib 8.0.30, for Win64 (x86_64)
--
-- Host: localhost    Database: family_feud
-- ------------------------------------------------------
-- Server version	8.0.30

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
-- Table structure for table `answers`
--

DROP TABLE IF EXISTS `answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `answers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `question_id` int NOT NULL,
  `answer_text` text NOT NULL,
  `points` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `answers`
--

LOCK TABLES `answers` WRITE;
/*!40000 ALTER TABLE `answers` DISABLE KEYS */;
INSERT INTO `answers` VALUES (1,1,'Bite Nails',30),(2,1,'Fidget',25),(3,1,'Sweat',20),(4,1,'Talk a lot',15),(5,1,'Shake',10),(6,2,'Books',35),(7,2,'Pencils/Pens',25),(8,2,'Notebook',15),(9,2,'Calculator',10),(10,2,'Lunch/Snack',5),(11,3,'Hawaii',30),(12,3,'Florida',25),(13,3,'California',20),(14,3,'New York City',15),(15,3,'Las Vegas',10),(16,4,'Brush teeth',35),(17,4,'Set alarm',25),(18,4,'Read',15),(19,4,'Wash fase',15),(20,4,'Check phone',10);
/*!40000 ALTER TABLE `answers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pre_round_answers`
--

DROP TABLE IF EXISTS `pre_round_answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pre_round_answers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `question_id` int NOT NULL,
  `answer_text` text NOT NULL,
  `points` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pre_round_answers`
--

LOCK TABLES `pre_round_answers` WRITE;
/*!40000 ALTER TABLE `pre_round_answers` DISABLE KEYS */;
INSERT INTO `pre_round_answers` VALUES (21,6,'Magpanggap na hindi nakita',40),(22,6,'Takbo',30),(23,6,'Mag-smile at mag-wave',15),(24,6,'Maglakad ng mabilis papalayo',10),(25,6,'Tumawa para magmukhang okay ang buhay',5),(26,5,'Instant noodles',35),(27,5,'Pan de sal',25),(28,5,'Tuyo o sardinas',15),(29,5,'Leftover ulam',15),(30,5,'Prutas',10),(31,7,'Cellphone charger',30),(32,7,'Wallet / Purse',25),(33,7,'Susi',20),(34,7,'Face mask',15),(35,7,'Umbrella',10),(36,8,'Nagmumura sa loob ng sasakyan',35),(37,8,'Nag-message sa friends',25),(38,8,'Nakikinig ng music',15),(39,8,'Nagmumuni-muni / nai-stress',15),(40,8,'Nag-scroll sa social media',10);
/*!40000 ALTER TABLE `pre_round_answers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pre_round_questions`
--

DROP TABLE IF EXISTS `pre_round_questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pre_round_questions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `question_text` text NOT NULL,
  `round` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pre_round_questions`
--

LOCK TABLES `pre_round_questions` WRITE;
/*!40000 ALTER TABLE `pre_round_questions` DISABLE KEYS */;
INSERT INTO `pre_round_questions` VALUES (5,'Ano ang madalas kainin ng mga tao tuwing late sa almusal?',1),(6,'Ano ang unang ginagawa ng tao kapag nakita ang ex niya sa mall?',2),(7,'Ano ang mga bagay na lagi nating naiwan sa bahay pag-alis?',3),(8,'Ano ang karaniwang ginagawa ng tao kapag traffic sa EDSA?',4);
/*!40000 ALTER TABLE `pre_round_questions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `questions`
--

DROP TABLE IF EXISTS `questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `questions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `question_text` text NOT NULL,
  `round` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `questions`
--

LOCK TABLES `questions` WRITE;
/*!40000 ALTER TABLE `questions` DISABLE KEYS */;
INSERT INTO `questions` VALUES (1,'Mag bigay ng ginagawa ng tao pag siya ay kinakabahan?',1),(2,'Mag bigay ng bagay na nakikita sa school bag?',2),(3,'Ma gbigay ng sikat na bakasyunan sa U.S.?',3),(4,'Mag bigay ng ginagawa mo bago matulog?',4);
/*!40000 ALTER TABLE `questions` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-12 13:15:55
