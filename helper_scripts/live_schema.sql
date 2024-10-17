-- MySQL dump 10.13  Distrib 8.0.39, for Linux (x86_64)
--
-- Host: localhost    Database: story_db
-- ------------------------------------------------------
-- Server version	8.0.39-0ubuntu0.22.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `Choices`
--

DROP TABLE IF EXISTS `Choices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Choices` (
  `ChoiceID` int NOT NULL AUTO_INCREMENT,
  `PassageID` int NOT NULL,
  `ChoiceText` varchar(255) NOT NULL,
  `NextPassageID` int DEFAULT NULL,
  `IsFinal` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`ChoiceID`),
  KEY `PassageID` (`PassageID`),
  KEY `NextPassageID` (`NextPassageID`),
  CONSTRAINT `Choices_ibfk_1` FOREIGN KEY (`PassageID`) REFERENCES `Passages` (`PassageID`) ON DELETE CASCADE,
  CONSTRAINT `Choices_ibfk_2` FOREIGN KEY (`NextPassageID`) REFERENCES `Passages` (`PassageID`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Passages`
--

DROP TABLE IF EXISTS `Passages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Passages` (
  `PassageID` int NOT NULL AUTO_INCREMENT,
  `StoryID` int NOT NULL,
  `Text` text NOT NULL,
  PRIMARY KEY (`PassageID`),
  KEY `StoryID` (`StoryID`),
  CONSTRAINT `Passages_ibfk_1` FOREIGN KEY (`StoryID`) REFERENCES `Stories` (`ID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Stories`
--

DROP TABLE IF EXISTS `Stories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Stories` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `Story_Name` varchar(100) NOT NULL,
  `UserID` int NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `fk_user_id` (`UserID`),
  CONSTRAINT `fk_user_id` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `StoriesPlayed`
--

DROP TABLE IF EXISTS `StoriesPlayed`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `StoriesPlayed` (
  `UserID` int NOT NULL,
  `StoryID` int NOT NULL,
  `CurrentPassageID` int DEFAULT NULL,
  PRIMARY KEY (`UserID`,`StoryID`),
  KEY `StoryID` (`StoryID`),
  KEY `CurrentPassageID` (`CurrentPassageID`),
  CONSTRAINT `StoriesPlayed_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE,
  CONSTRAINT `StoriesPlayed_ibfk_2` FOREIGN KEY (`StoryID`) REFERENCES `Stories` (`ID`) ON DELETE CASCADE,
  CONSTRAINT `StoriesPlayed_ibfk_3` FOREIGN KEY (`CurrentPassageID`) REFERENCES `Passages` (`PassageID`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `StoryTags`
--

DROP TABLE IF EXISTS `StoryTags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `StoryTags` (
  `StoryID` int NOT NULL,
  `TagID` int NOT NULL,
  PRIMARY KEY (`StoryID`,`TagID`),
  KEY `TagID` (`TagID`),
  CONSTRAINT `StoryTags_ibfk_1` FOREIGN KEY (`StoryID`) REFERENCES `Stories` (`ID`) ON DELETE CASCADE,
  CONSTRAINT `StoryTags_ibfk_2` FOREIGN KEY (`TagID`) REFERENCES `Tags` (`TagID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Tags`
--

DROP TABLE IF EXISTS `Tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Tags` (
  `TagID` int NOT NULL AUTO_INCREMENT,
  `TagName` varchar(50) NOT NULL,
  PRIMARY KEY (`TagID`),
  UNIQUE KEY `TagName` (`TagName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Users`
--

DROP TABLE IF EXISTS `Users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Users` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(50) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `EULA_Agreement` tinyint(1) DEFAULT '0',
  `Data_Collection_Agreement` tinyint(1) DEFAULT '0',
  `Preferences` json DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Name` (`Name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-10-17 12:32:20
