-- MySQL dump 10.13  Distrib 8.0.43, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: family_feud
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL,
  `answer_text` text NOT NULL,
  `points` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `answers`
--

LOCK TABLES `answers` WRITE;
/*!40000 ALTER TABLE `answers` DISABLE KEYS */;
INSERT INTO `answers` VALUES (1,1,'Humalik',20),(2,1,'Mag Salita',50),(3,1,'Kumanta',30),(4,1,'Sumipol',12),(5,1,'Dumila',16),(6,2,'Magtakip ng Ilong ',40),(7,2,'Lumipat ng Upuan ',20),(8,2,'Mag Pabango',16),(9,2,'Iiwas ang mukha ',18),(10,2,'Matulog ',25),(11,3,'Magulang',15),(12,3,'Pera',46),(13,3,'Damit na isusuot',23),(14,3,'Simbahan',5),(15,3,'Mga bisita',10),(16,4,'Lollipop',36),(17,4,'Ice Cream',41),(18,4,'Stamp',5),(19,4,'Popsicle',10),(20,4,'Envelope',12),(21,5,'Computer',50),(22,5,'Lapis/papel',20),(23,5,'Lamesa',10),(24,5,'Upuan',5),(25,5,'Telepono',14);
/*!40000 ALTER TABLE `answers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audience_answers`
--

DROP TABLE IF EXISTS `audience_answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audience_answers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL,
  `answer` text NOT NULL,
  `points` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audience_answers`
--

LOCK TABLES `audience_answers` WRITE;
/*!40000 ALTER TABLE `audience_answers` DISABLE KEYS */;
INSERT INTO `audience_answers` VALUES (1,1,'paglalaba',45),(2,1,'pag inom',29),(3,1,'pag hugas ng plato',15),(4,1,'pag luluto',11),(5,0,'',0);
/*!40000 ALTER TABLE `audience_answers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audience_question`
--

DROP TABLE IF EXISTS `audience_question`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audience_question` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question_text` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audience_question`
--

LOCK TABLES `audience_question` WRITE;
/*!40000 ALTER TABLE `audience_question` DISABLE KEYS */;
INSERT INTO `audience_question` VALUES (1,'Bukod sa pang ligo, saan ppa ginagamit ang tubig?');
/*!40000 ALTER TABLE `audience_question` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fast_answers`
--

DROP TABLE IF EXISTS `fast_answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fast_answers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL,
  `asnwer` text NOT NULL,
  `points` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fast_answers`
--

LOCK TABLES `fast_answers` WRITE;
/*!40000 ALTER TABLE `fast_answers` DISABLE KEYS */;
INSERT INTO `fast_answers` VALUES (1,1,'KAMBING',30),(2,1,'USA',28),(3,1,'TUPA',8),(4,1,'BISON',7),(5,1,'BUFFALO',5),(6,2,'PANSIT',25),(7,2,'LUMPIA',20),(8,2,'CHICKEN CURRY',17),(9,2,'ADOBO',15),(10,2,'GULAY',10),(11,3,'KARNE',21),(12,3,'TOYO',20),(13,3,'BAWANG',19),(14,3,'PANGIHAW',8),(15,3,'ULING',5),(16,4,'JANUARY',15),(17,4,'FEBRUARY',13),(18,4,'MARCH',11),(19,4,'MAY',10),(20,4,'APRIL',4),(21,5,'PAPUTOK',35),(22,5,'LOBO',30),(23,5,'GULONG',26),(24,5,'MAKINA',13),(25,5,'KANYON',10);
/*!40000 ALTER TABLE `fast_answers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fast_player_answers`
--

DROP TABLE IF EXISTS `fast_player_answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fast_player_answers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `player` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer_text` text NOT NULL,
  `points` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fast_player_answers`
--

LOCK TABLES `fast_player_answers` WRITE;
/*!40000 ALTER TABLE `fast_player_answers` DISABLE KEYS */;
INSERT INTO `fast_player_answers` VALUES (1,1,1,'SAMPLE',0),(2,2,1,'ML',0),(3,1,2,'SAMPLE',0),(4,2,2,'SAMPLES',0),(5,1,3,'SAMPLE',0);
/*!40000 ALTER TABLE `fast_player_answers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fast_questions`
--

DROP TABLE IF EXISTS `fast_questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fast_questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question_text` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fast_questions`
--

LOCK TABLES `fast_questions` WRITE;
/*!40000 ALTER TABLE `fast_questions` DISABLE KEYS */;
INSERT INTO `fast_questions` VALUES (1,'Bukod sa baka at kalabaw, Hayop na may sungay'),(2,'Madalas na tinitinda sa ramosco canteen'),(3,'Ano ang kailngan kapag mag luluto ng barbeque'),(4,'Month na may letter A'),(5,'Bukod sa baril ano pa ang pumuputok');
/*!40000 ALTER TABLE `fast_questions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fast_total_points`
--

DROP TABLE IF EXISTS `fast_total_points`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fast_total_points` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `player_id` int(11) NOT NULL,
  `total_points` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fast_total_points`
--

LOCK TABLES `fast_total_points` WRITE;
/*!40000 ALTER TABLE `fast_total_points` DISABLE KEYS */;
/*!40000 ALTER TABLE `fast_total_points` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pre_round_answers`
--

DROP TABLE IF EXISTS `pre_round_answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pre_round_answers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL,
  `answer_text` text NOT NULL,
  `points` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pre_round_answers`
--

LOCK TABLES `pre_round_answers` WRITE;
/*!40000 ALTER TABLE `pre_round_answers` DISABLE KEYS */;
INSERT INTO `pre_round_answers` VALUES (1,1,'Noodles',35),(2,1,'Kanin',12),(3,1,'Prutas',21),(4,1,'Tinapay',17),(5,1,'Biscuit',5),(6,2,'Mag almusal ',40),(7,2,'Maligo',25),(8,2,'Mag Exercise',10),(9,2,'Maghilamos',21),(10,2,'Mag dasal',15),(11,3,'Lindol ',30),(12,3,'Pagputok ng bulkan ',25),(13,3,'Buhawi',19),(14,3,'Sunog',26),(15,3,'landslide',27),(16,4,'Cellphone',41),(17,4,'Wallet',26),(18,4,'Pabango',20),(19,4,'Lipstick',34),(20,4,'Salamin',37),(21,5,'Traffic ',50),(22,5,'Hindi na gising',34),(23,5,'Na aksidente ang sinasakyan',10),(24,5,'Walang Ma sakyan',5),(25,5,'Tinatamad',18);
/*!40000 ALTER TABLE `pre_round_answers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pre_round_questions`
--

DROP TABLE IF EXISTS `pre_round_questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pre_round_questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question_text` text NOT NULL,
  `round` int(11) NOT NULL,
  `question_status` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pre_round_questions`
--

LOCK TABLES `pre_round_questions` WRITE;
/*!40000 ALTER TABLE `pre_round_questions` DISABLE KEYS */;
INSERT INTO `pre_round_questions` VALUES (1,'Magbigay ng mga pagkain na madalas hinahanap kapag gutom ',1,1),(2,'Ano ang ginagawa ng mga tao sa umaga',2,1),(3,'Magbigay ng mga Natural Disaster maliban sa Baha ',3,1),(4,'Ano ang pinaka-hindi pwedeng mawala sa bag ng babae',4,1),(5,'Ano ang mga dahilan bakit na late ang isang tao sa trabaho',5,1);
/*!40000 ALTER TABLE `pre_round_questions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `questions`
--

DROP TABLE IF EXISTS `questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question_text` text NOT NULL,
  `round` int(11) NOT NULL,
  `question_status` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `questions`
--

LOCK TABLES `questions` WRITE;
/*!40000 ALTER TABLE `questions` DISABLE KEYS */;
INSERT INTO `questions` VALUES (1,'Ano ang pwedeng gawin gamit ang bibig',1,0),(2,'Kadalasan ginagawa kung may Body Odor ang katabi mo sa sasakyan',2,0),(3,'Ano ang madalas pag-awayan ng magkasintahang ikakasal',3,0),(4,'Mga Bagay na dinidilaan ',4,0),(5,'Magbigay ng mga bagay na nakikita sa opisina ',5,0);
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

-- Dump completed on 2025-12-11 14:52:51
