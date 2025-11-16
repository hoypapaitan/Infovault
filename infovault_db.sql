-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for infovault_db
CREATE DATABASE IF NOT EXISTS `infovault_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `infovault_db`;

-- Dumping structure for table infovault_db.tblgraduates
CREATE TABLE IF NOT EXISTS `tblgraduates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` text,
  `address` text,
  `batch` varchar(50) DEFAULT NULL,
  `yearGraduated` varchar(50) DEFAULT NULL,
  `advisoryId` int DEFAULT NULL,
  `section` text,
  `course` text,
  `major` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table infovault_db.tblgraduates: ~5 rows (approximately)
DELETE FROM `tblgraduates`;
INSERT INTO `tblgraduates` (`id`, `name`, `address`, `batch`, `yearGraduated`, `advisoryId`, `section`, `course`, `major`, `created_at`, `updated_at`, `deleted_at`, `created_by`) VALUES
	(1, 'Carrasco, Julie Luh Gutierrez', 'Brgy. Lual, Casiguran, Aurora', '2023-2024', '2024', NULL, '', 'MASTER OF ARTS IN EDUCATION (MAEd)', 'Major in Educational Management', '2025-10-27 19:37:05', '2025-10-27 19:37:05', NULL, 1),
	(2, 'Coralde, Amie Rubio', 'Brgy. 02, Casiguran, Aurora', '2023-2024', '2024', NULL, '', 'MASTER OF ARTS IN EDUCATION (MAEd)', 'Major in Educational Management', '2025-10-27 19:37:05', '2025-10-27 19:37:05', NULL, 1),
	(3, 'Nazareno, Raymond Arimboyutan', 'Brgy. North Pob., Dipaculao, Aurora', '2023-2024', '2024', NULL, '', 'MASTER OF ARTS IN EDUCATION (MAEd)', 'Major in Educational Management', '2025-10-27 19:37:05', '2025-10-27 19:37:05', NULL, 1),
	(4, 'Panganiban, Vergenita Lamar', 'Brgy. Pingit, Baler, Aurora', '2023-2024', '2024', NULL, '', 'MASTER OF ARTS IN EDUCATION (MAEd)', 'Major in Educational Management', '2025-10-27 19:37:05', '2025-10-27 19:37:05', NULL, 1),
	(5, 'Tecson, Harold Trinidad', 'Brgy. Florida, Maria Aurora, Aurora', '2023-2024', '2024', NULL, '', 'MASTER OF ARTS IN EDUCATION (MAEd)', 'Major in Educational Management', '2025-10-27 19:37:05', '2025-10-27 19:37:05', NULL, 1);

-- Dumping structure for table infovault_db.tblusers
CREATE TABLE IF NOT EXISTS `tblusers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Student Number',
  `password` text NOT NULL,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `middleName` varchar(255) NOT NULL,
  `suffix` varchar(255) NOT NULL,
  `sex` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `contact` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `userType` int NOT NULL,
  `status` int NOT NULL DEFAULT '1',
  `isDeleted` int NOT NULL DEFAULT '0',
  `createdAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table infovault_db.tblusers: ~1 rows (approximately)
DELETE FROM `tblusers`;
INSERT INTO `tblusers` (`id`, `username`, `password`, `firstName`, `lastName`, `middleName`, `suffix`, `sex`, `email`, `contact`, `address`, `userType`, `status`, `isDeleted`, `createdAt`, `updatedAt`) VALUES
	(1, 'admin', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'Firsts', 'Users', 'Admin', ' ', 'Male', 'test@mail.com', '09876543212', 'test address', 1, 1, 0, '2024-10-12 14:35:21', '2024-10-12 14:35:21');

-- Dumping structure for table infovault_db.tblusertypes
CREATE TABLE IF NOT EXISTS `tblusertypes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `description` varchar(255) NOT NULL,
  `modules` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table infovault_db.tblusertypes: ~2 rows (approximately)
DELETE FROM `tblusertypes`;
INSERT INTO `tblusertypes` (`id`, `description`, `modules`) VALUES
	(1, 'Super Admin', '101,102'),
	(2, 'Coordinator', '201,202');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
