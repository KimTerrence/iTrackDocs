-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               12.0.2-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for itrackdocs
CREATE DATABASE IF NOT EXISTS `itrackdocs` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci */;
USE `itrackdocs`;

-- Dumping structure for table itrackdocs.documents
CREATE TABLE IF NOT EXISTS `documents` (
  `tracking_id` varchar(50) NOT NULL,
  `creator_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `origin` varchar(100) NOT NULL,
  `target_department` varchar(100) DEFAULT NULL,
  `current_location` varchar(100) NOT NULL,
  `status` varchar(50) DEFAULT 'Routing',
  `file_path` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`tracking_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Dumping data for table itrackdocs.documents: ~12 rows (approximately)
INSERT INTO `documents` (`tracking_id`, `creator_id`, `title`, `category`, `origin`, `target_department`, `current_location`, `status`, `file_path`, `created_at`) VALUES
	('IC26060001', 1, 'From chm to cics', 'External Letter', 'CICS', 'CICS', 'CICS', 'Received', NULL, '2026-06-15 15:37:38'),
	('IC26060002', 1, 'from chm to ceo', 'External Letter', 'CICS', 'CICS', 'CICS', 'Returned', NULL, '2026-06-15 15:37:50'),
	('IC26060003', 2, 'from cics to ceo', 'External Letter', 'CICS', 'CICS', 'CICS', 'Returned', NULL, '2026-06-15 15:38:36'),
	('IC26060004', 1, 'Test', 'External Letter', 'CICS', 'CICS', 'CICS', 'Void', NULL, '2026-06-22 09:01:58'),
	('IC26060005', 1, 'Testing To CICS', 'External Letter', 'CICS', 'CICS', 'CICS', 'Received', NULL, '2026-06-22 09:58:29'),
	('IC26060006', 1, 'qw', 'External Letter', 'CICS', 'CICS', 'CICS', 'Received', NULL, '2026-06-22 10:08:48'),
	('IC26060007', 1, 'qwe', 'External Letter', 'CICS', 'CICS', 'CICS', 'Routing', NULL, '2026-06-23 22:57:19'),
	('IC26060008', 1, 'TO CICS', 'Internal Letter', 'CICS', 'CICS', 'CICS', 'Received', NULL, '2026-06-23 23:03:48'),
	('IC26060009', 3, 'To Kim', 'External Letter', 'CICS', 'CICS', 'CICS', 'Routing', NULL, '2026-06-24 13:58:21'),
	('IC26080001', 1, 'dwdw', 'External Letter', 'CICS', 'CICS', 'CICS', 'Routing', NULL, '2026-08-08 12:45:03'),
	('IC26080002', 2, 'wqwqwqwq', 'Internal Letter', 'CICS', 'CICS', '18.247401, 121.914560', 'Received', NULL, '2026-08-08 13:13:47'),
	('IC26080003', 1, 'qqwqw', 'EC', 'CICS', 'CICS', 'CICS', 'Routing', NULL, '2026-08-09 11:18:56');

-- Dumping structure for table itrackdocs.document_history
CREATE TABLE IF NOT EXISTS `document_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tracking_id` varchar(50) DEFAULT NULL,
  `location` varchar(100) NOT NULL,
  `action` varchar(255) NOT NULL,
  `recorded_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `tracking_id` (`tracking_id`),
  CONSTRAINT `document_history_ibfk_1` FOREIGN KEY (`tracking_id`) REFERENCES `documents` (`tracking_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Dumping data for table itrackdocs.document_history: ~26 rows (approximately)
INSERT INTO `document_history` (`id`, `tracking_id`, `location`, `action`, `recorded_at`) VALUES
	(57, 'IC26060001', 'CICS', 'Document created. Target destination: CICS', '2026-06-15 15:37:38'),
	(58, 'IC26060002', 'CICS', 'Document created. Target destination: CEO', '2026-06-15 15:37:50'),
	(59, 'IC26060003', 'CICS', 'Document created. Target destination: CEO', '2026-06-15 15:38:36'),
	(60, 'IC26060003', 'CICS', 'Document Returned to CEO', '2026-06-15 15:42:08'),
	(61, 'IC26060002', 'CICS', 'Document Returned to CEO', '2026-06-15 15:42:27'),
	(62, 'IC26060001', 'CICS', 'Document Returned to CICS', '2026-06-22 03:33:05'),
	(63, 'IC26060004', 'CICS', 'Document created. Target destination: CICS', '2026-06-22 09:01:58'),
	(64, 'IC26060005', 'CICS', 'Document created. Target destination: CICS', '2026-06-22 09:58:29'),
	(65, 'IC26060006', 'CICS', 'Document created. Target destination: CICS', '2026-06-22 10:08:48'),
	(66, 'IC26060007', 'CICS', 'Document created. Target destination: CHM', '2026-06-23 22:57:19'),
	(67, 'IC26060008', 'CICS', 'Document created. Target destination: CICS', '2026-06-23 23:03:48'),
	(68, 'IC26060005', 'CICS', 'Document Received at CICS', '2026-06-23 23:04:19'),
	(69, 'IC26060008', 'CICS', 'Document Received at CICS', '2026-06-23 23:04:33'),
	(70, 'IC26060008', 'CICS', 'Document Returned to CHM', '2026-06-24 13:48:52'),
	(71, 'IC26060005', 'CICS', 'Document Received at CICS', '2026-06-24 13:49:30'),
	(72, 'IC26060006', 'CICS', 'Document Received at CICS', '2026-06-24 13:49:49'),
	(73, 'IC26060001', 'CICS', 'Document Received at CICS', '2026-06-24 13:50:42'),
	(74, 'IC26060009', 'CICS', 'Document created. Target destination: CICS', '2026-06-24 13:58:21'),
	(75, 'IC26060004', 'CICS', 'Document marked as VOID', '2026-06-24 14:05:13'),
	(76, 'IC26060008', 'CICS', 'Document Received at CICS', '2026-06-24 14:06:04'),
	(77, 'IC26060004', 'CICS', 'Document Received at CICS', '2026-06-24 14:11:48'),
	(78, 'IC26060004', 'CICS', 'Document Received at CICS', '2026-06-24 14:11:53'),
	(79, 'IC26060004', 'CICS', 'Document marked as VOID', '2026-06-24 14:12:05'),
	(80, 'IC26080001', 'CICS', 'Document created. Target destination: CICS', '2026-08-08 12:45:03'),
	(81, 'IC26080002', 'CICS', 'Document created. Target destination: CICS', '2026-08-08 13:13:47'),
	(82, 'IC26080002', '18.247401, 121.914560', 'Received at 18.247401, 121.914560', '2026-08-08 13:15:26'),
	(83, 'IC26080002', '18.247401, 121.914560', 'Received at 18.247401, 121.914560', '2026-08-08 13:15:31'),
	(84, 'IC26080002', '18.247401, 121.914560', 'Received at 18.247401, 121.914560', '2026-08-08 13:15:35'),
	(85, 'IC26080003', 'CICS', 'Document created. Target destination: CICS', '2026-08-09 11:18:56');

-- Dumping structure for table itrackdocs.notifications
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'info',
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `tracking_id` varchar(20) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Dumping data for table itrackdocs.notifications: ~28 rows (approximately)
INSERT INTO `notifications` (`id`, `user_id`, `type`, `title`, `message`, `tracking_id`, `is_read`, `created_at`) VALUES
	(1, 1, 'warning', 'Document Returned', 'Your document \'TO CICS\' (IC26060008) has been returned to CHM.', 'IC26060008', 1, '2026-06-24 13:48:52'),
	(2, 1, 'success', 'Document Received', 'Your document \'Testing To CICS\' (IC26060005) has been received at CICS.', 'IC26060005', 1, '2026-06-24 13:49:30'),
	(3, 1, 'success', 'Document Received', 'Your document \'qw\' (IC26060006) has been received at CICS.', 'IC26060006', 1, '2026-06-24 13:49:49'),
	(4, 1, 'success', 'Document Received', 'Your document \'From chm to cics\' (IC26060001) has been received at CICS.', 'IC26060001', 1, '2026-06-24 13:50:42'),
	(5, 2, 'info', 'New Document Incoming', 'A new document \'To Kim\' (IC26060009) has been routed to your department from CEO.', 'IC26060009', 1, '2026-06-24 13:58:21'),
	(6, 10, 'info', 'New Document Incoming', 'A new document \'To Kim\' (IC26060009) has been routed to your department from CEO.', 'IC26060009', 0, '2026-06-24 13:58:21'),
	(7, 1, 'warning', 'Document Voided', 'Your document \'Test\' (IC26060004) has been marked as void.', 'IC26060004', 1, '2026-06-24 14:05:13'),
	(8, 1, 'success', 'Document Received', 'Your document \'TO CICS\' (IC26060008) has been received at CICS.', 'IC26060008', 1, '2026-06-24 14:06:04'),
	(9, 1, 'success', 'Document Received', 'Your document \'Test\' (IC26060004) has been received at CICS.', 'IC26060004', 1, '2026-06-24 14:11:48'),
	(10, 1, 'success', 'Document Received', 'Your document \'Test\' (IC26060004) has been received at CICS.', 'IC26060004', 1, '2026-06-24 14:11:53'),
	(11, 1, 'warning', 'Document Voided', 'Your document \'Test\' (IC26060004) has been marked as void.', 'IC26060004', 1, '2026-06-24 14:12:05'),
	(12, 2, 'info', 'New Document Incoming', 'A new document \'dwdw\' (IC26080001) has been routed to your department from CHM.', 'IC26080001', 1, '2026-08-08 12:45:03'),
	(13, 10, 'info', 'New Document Incoming', 'A new document \'dwdw\' (IC26080001) has been routed to your department from CHM.', 'IC26080001', 0, '2026-08-08 12:45:03'),
	(14, 12, 'info', 'New Document Incoming', 'A new document \'dwdw\' (IC26080001) has been routed to your department from CHM.', 'IC26080001', 0, '2026-08-08 12:45:03'),
	(15, 13, 'info', 'New Document Incoming', 'A new document \'dwdw\' (IC26080001) has been routed to your department from CHM.', 'IC26080001', 0, '2026-08-08 12:45:03'),
	(16, 1, 'info', 'New Document Incoming', 'A new document \'wqwqwqwq\' (IC26080002) has been routed to your department from CICS.', 'IC26080002', 1, '2026-08-08 13:13:47'),
	(17, 3, 'info', 'New Document Incoming', 'A new document \'wqwqwqwq\' (IC26080002) has been routed to your department from CICS.', 'IC26080002', 0, '2026-08-08 13:13:47'),
	(18, 10, 'info', 'New Document Incoming', 'A new document \'wqwqwqwq\' (IC26080002) has been routed to your department from CICS.', 'IC26080002', 0, '2026-08-08 13:13:47'),
	(19, 12, 'info', 'New Document Incoming', 'A new document \'wqwqwqwq\' (IC26080002) has been routed to your department from CICS.', 'IC26080002', 0, '2026-08-08 13:13:47'),
	(20, 13, 'info', 'New Document Incoming', 'A new document \'wqwqwqwq\' (IC26080002) has been routed to your department from CICS.', 'IC26080002', 0, '2026-08-08 13:13:47'),
	(21, 2, 'success', 'Document Received', 'Your document \'wqwqwqwq\' (IC26080002) has been received at 18.247401, 121.914560.', 'IC26080002', 1, '2026-08-08 13:15:26'),
	(22, 2, 'success', 'Document Received', 'Your document \'wqwqwqwq\' (IC26080002) has been received at 18.247401, 121.914560.', 'IC26080002', 1, '2026-08-08 13:15:31'),
	(23, 2, 'success', 'Document Received', 'Your document \'wqwqwqwq\' (IC26080002) has been received at 18.247401, 121.914560.', 'IC26080002', 1, '2026-08-08 13:15:35'),
	(24, 2, 'info', 'New Document Incoming', 'A new document \'qqwqw\' (IC26080003) has been routed to your department from CICS.', 'IC26080003', 0, '2026-08-09 11:18:56'),
	(25, 3, 'info', 'New Document Incoming', 'A new document \'qqwqw\' (IC26080003) has been routed to your department from CICS.', 'IC26080003', 0, '2026-08-09 11:18:56'),
	(26, 10, 'info', 'New Document Incoming', 'A new document \'qqwqw\' (IC26080003) has been routed to your department from CICS.', 'IC26080003', 0, '2026-08-09 11:18:56'),
	(27, 12, 'info', 'New Document Incoming', 'A new document \'qqwqw\' (IC26080003) has been routed to your department from CICS.', 'IC26080003', 0, '2026-08-09 11:18:56'),
	(28, 13, 'info', 'New Document Incoming', 'A new document \'qqwqw\' (IC26080003) has been routed to your department from CICS.', 'IC26080003', 0, '2026-08-09 11:18:56');

-- Dumping structure for table itrackdocs.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `role` varchar(20) DEFAULT 'staff',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `status` varchar(20) NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Dumping data for table itrackdocs.users: ~6 rows (approximately)
INSERT INTO `users` (`id`, `username`, `password`, `name`, `email`, `department`, `role`, `created_at`, `status`) VALUES
	(1, 'admin@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin@gmail.com', 'CICS', 'admin', '2026-05-27 02:46:07', 'active'),
	(2, 'quineskimterrence@gmail.com', '$2y$10$T8lOcnIRBuOL/A3LznXCyudSU8vslGvwowtofa9wZFb8vpLLo8tJq', 'Kim Terrence', 'quineskimterrence@gmail.com', 'CICS', 'head', '2026-05-28 14:35:19', 'active'),
	(3, 'kimquines274@gmail.com', '$2y$10$9wSFm4DK3JWcL8DmcLTSm./pZUWUDYWP5SSxHPEHtMGnV5rkzK3mm', 'Jane Collado', 'kimquines274@gmail.com', 'CICS', 'admin', '2026-06-12 11:32:05', 'active'),
	(10, 'john', '$2y$10$yYCqwvjIMbL1BuuYtIE27.FfSTUKtEY.r.ojWk3/RG.X68RGhq8qC', 'John Doe', 'john@gmail.com', 'CICS', 'staff', '2026-06-22 03:31:12', 'active'),
	(12, 'test@gmail.com', '$2y$10$.0rZVZh/PiBI1j3fAm52jOqB1wwQ.A40aZKAXrFDemKZiH.Ij40cy', 'TEst', 'test@gmail.com', 'CICS', 'staff', '2026-07-20 06:35:42', 'active'),
	(13, 'rqwe@gmail.com', '$2y$10$Avh8kOJ84ijNkOrRYNFgy.JzGNxp2ZU7oyjobbhjBQD/AObjYTJsa', 'test testest', 'rqwe@gmail.com', 'CICS', 'staff', '2026-07-20 06:37:46', 'active');

-- Dumping structure for table itrackdocs.user_activity_logs
CREATE TABLE IF NOT EXISTS `user_activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `username` varchar(255) NOT NULL,
  `action` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `action` (`action`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Dumping data for table itrackdocs.user_activity_logs: ~60 rows (approximately)
INSERT INTO `user_activity_logs` (`id`, `user_id`, `username`, `action`, `description`, `ip_address`, `created_at`) VALUES
	(1, 1, 'System Administrator', 'user_blocked', 'Set user ID #10 status to \'blocked\'.', '127.0.0.1', '2026-06-23 01:46:01'),
	(2, 1, 'System Administrator', 'login', 'User \'admin\' logged in.', '127.0.0.1', '2026-06-23 21:58:01'),
	(3, 1, 'System Administrator', 'logout', 'User \'System Administrator\' logged out.', '127.0.0.1', '2026-06-23 22:01:28'),
	(4, 1, 'System Administrator', 'login', 'User \'admin\' logged in.', '127.0.0.1', '2026-06-23 22:01:33'),
	(5, 1, 'System Administrator', 'login', 'User \'admin\' logged in.', '127.0.0.1', '2026-06-23 22:07:58'),
	(6, 1, 'System Administrator', 'user_active', 'Set user ID #10 status to \'active\'.', '127.0.0.1', '2026-06-23 22:13:47'),
	(7, 1, 'System Administrator', 'logout', 'User \'System Administrator\' logged out.', '127.0.0.1', '2026-06-23 22:29:57'),
	(8, 1, 'System Administrator', 'login', 'User \'quineskimterrence@gmail.com\' logged in.', '127.0.0.1', '2026-06-23 22:30:11'),
	(9, 1, 'System Administrator', 'user_update', 'Updated user ID #1 (\'admin@gmail.com\').', '127.0.0.1', '2026-06-23 22:39:11'),
	(10, 1, 'System Administrator', 'user_update', 'Updated user ID #3 (\'jane@gmail.com\').', '127.0.0.1', '2026-06-23 22:39:51'),
	(11, 1, 'System Administrator', 'logout', 'User \'System Administrator\' logged out.', '127.0.0.1', '2026-06-23 22:40:00'),
	(12, 1, 'System Administrator', 'login', 'User \'admin@gmail.com\' logged in.', '127.0.0.1', '2026-06-23 22:40:12'),
	(13, 1, 'System Administrator', 'document_create', 'Created document \'qwe\' (IC26060007) targeting \'CHM\'.', '127.0.0.1', '2026-06-23 22:57:19'),
	(14, 1, 'System Administrator', 'document_create', 'Created document \'TO CICS\' (IC26060008) targeting \'CICS\'.', '127.0.0.1', '2026-06-23 23:03:48'),
	(15, 1, 'System Administrator', 'logout', 'User \'System Administrator\' logged out.', '127.0.0.1', '2026-06-23 23:03:59'),
	(16, 2, 'Kim Terrence', 'login', 'User \'quineskimterrence@gmail.com\' logged in.', '127.0.0.1', '2026-06-23 23:04:10'),
	(17, 2, 'Kim Terrence', 'document_receive', 'Document IC26060005 received at \'CICS\'.', '127.0.0.1', '2026-06-23 23:04:19'),
	(18, 2, 'Kim Terrence', 'document_receive', 'Document IC26060008 received at \'CICS\'.', '127.0.0.1', '2026-06-23 23:04:33'),
	(19, 2, 'Kim Terrence', 'login', 'User \'quineskimterrence@gmail.com\' logged in.', '127.0.0.1', '2026-06-24 13:41:48'),
	(20, 2, 'Kim Terrence', 'logout', 'User \'Kim Terrence\' logged out.', '127.0.0.1', '2026-06-24 13:45:33'),
	(21, 1, 'System Administrator', 'login', 'User \'admin@gmail.com\' logged in.', '127.0.0.1', '2026-06-24 13:45:48'),
	(22, 1, 'System Administrator', 'document_return', 'Document IC26060008 returned to \'CHM\'.', '127.0.0.1', '2026-06-24 13:48:52'),
	(23, 1, 'System Administrator', 'logout', 'User \'System Administrator\' logged out.', '127.0.0.1', '2026-06-24 13:49:09'),
	(24, 2, 'Kim Terrence', 'login', 'User \'quineskimterrence@gmail.com\' logged in.', '127.0.0.1', '2026-06-24 13:49:16'),
	(25, 2, 'Kim Terrence', 'document_receive', 'Document IC26060005 received at \'CICS\'.', '127.0.0.1', '2026-06-24 13:49:30'),
	(26, 2, 'Kim Terrence', 'document_receive', 'Document IC26060006 received at \'CICS\'.', '127.0.0.1', '2026-06-24 13:49:49'),
	(27, 2, 'Kim Terrence', 'document_receive', 'Document IC26060001 received at \'CICS\'.', '127.0.0.1', '2026-06-24 13:50:42'),
	(28, 2, 'Kim Terrence', 'logout', 'User \'Kim Terrence\' logged out.', '127.0.0.1', '2026-06-24 13:51:07'),
	(29, 1, 'System Administrator', 'login', 'User \'admin@gmail.com\' logged in.', '127.0.0.1', '2026-06-24 13:51:29'),
	(30, 1, 'System Administrator', 'logout', 'User \'System Administrator\' logged out.', '127.0.0.1', '2026-06-24 13:56:41'),
	(31, 1, 'System Administrator', 'login', 'User \'admin@gmail.com\' logged in.', '127.0.0.1', '2026-06-24 13:57:11'),
	(32, 1, 'System Administrator', 'user_update', 'Updated user ID #3 (\'kimquines274@gmail.com\').', '127.0.0.1', '2026-06-24 13:57:32'),
	(33, 1, 'System Administrator', 'logout', 'User \'System Administrator\' logged out.', '127.0.0.1', '2026-06-24 13:57:38'),
	(34, 3, 'Jane Collado', 'login', 'User \'kimquines274@gmail.com\' logged in.', '127.0.0.1', '2026-06-24 13:57:52'),
	(35, 3, 'Jane Collado', 'document_create', 'Created document \'To Kim\' (IC26060009) targeting \'CICS\'.', '127.0.0.1', '2026-06-24 13:58:21'),
	(36, 3, 'Jane Collado', 'logout', 'User \'Jane Collado\' logged out.', '127.0.0.1', '2026-06-24 13:58:32'),
	(37, 2, 'Kim Terrence', 'login', 'User \'quineskimterrence@gmail.com\' logged in.', '127.0.0.1', '2026-06-24 13:58:40'),
	(38, 2, 'Kim Terrence', 'document_void', 'Document IC26060004 marked as void.', '127.0.0.1', '2026-06-24 14:05:13'),
	(39, 2, 'Kim Terrence', 'document_receive', 'Document IC26060008 received at \'CICS\'.', '127.0.0.1', '2026-06-24 14:06:04'),
	(40, 2, 'Kim Terrence', 'document_receive', 'Document IC26060004 received at \'CICS\'.', '127.0.0.1', '2026-06-24 14:11:48'),
	(41, 2, 'Kim Terrence', 'document_receive', 'Document IC26060004 received at \'CICS\'.', '127.0.0.1', '2026-06-24 14:11:53'),
	(42, 2, 'Kim Terrence', 'document_void', 'Document IC26060004 marked as void.', '127.0.0.1', '2026-06-24 14:12:05'),
	(43, 1, 'System Administrator', 'login', 'User \'admin@gmail.com\' logged in.', '127.0.0.1', '2026-07-20 06:29:10'),
	(44, 1, 'System Administrator', 'user_create', 'Created user \'test@gmail.com\' with role \'staff\' in department \'CICS\'.', '127.0.0.1', '2026-07-20 06:35:42'),
	(45, 1, 'System Administrator', 'user_create', 'Created user \'rqwe@gmail.com\' with role \'staff\' in department \'CICS\'.', '127.0.0.1', '2026-07-20 06:37:46'),
	(46, 1, 'System Administrator', 'login', 'User \'admin@gmail.com\' logged in.', '::1', '2026-08-08 12:44:04'),
	(47, 1, 'System Administrator', 'document_create', 'Created document \'dwdw\' (IC26080001) targeting \'CICS\'.', '::1', '2026-08-08 12:45:03'),
	(48, 1, 'System Administrator', 'logout', 'User \'System Administrator\' logged out.', '::1', '2026-08-08 12:45:26'),
	(49, 1, 'System Administrator', 'login', 'User \'admin@gmail.com\' logged in.', '::1', '2026-08-08 13:10:30'),
	(50, 1, 'System Administrator', 'logout', 'User \'System Administrator\' logged out.', '::1', '2026-08-08 13:11:46'),
	(51, 1, 'System Administrator', 'login', 'User \'admin@gmail.com\' logged in.', '::1', '2026-08-08 13:13:14'),
	(52, 1, 'System Administrator', 'user_update', 'Updated user ID #2 (\'quineskimterrence@gmail.com\').', '::1', '2026-08-08 13:13:31'),
	(53, 1, 'System Administrator', 'logout', 'User \'System Administrator\' logged out.', '::1', '2026-08-08 13:13:33'),
	(54, 2, 'Kim Terrence', 'login', 'User \'quineskimterrence@gmail.com\' logged in.', '::1', '2026-08-08 13:13:38'),
	(55, 2, 'Kim Terrence', 'document_create', 'Created document \'wqwqwqwq\' (IC26080002) targeting \'CICS\'.', '::1', '2026-08-08 13:13:47'),
	(56, 2, 'Kim Terrence', 'document_receive', 'Document IC26080002 received at \'18.247401, 121.914560\'.', '::1', '2026-08-08 13:15:26'),
	(57, 2, 'Kim Terrence', 'document_receive', 'Document IC26080002 received at \'18.247401, 121.914560\'.', '::1', '2026-08-08 13:15:31'),
	(58, 2, 'Kim Terrence', 'document_receive', 'Document IC26080002 received at \'18.247401, 121.914560\'.', '::1', '2026-08-08 13:15:35'),
	(59, 1, 'System Administrator', 'login', 'User \'admin@gmail.com\' logged in.', '::1', '2026-08-09 11:18:38'),
	(60, 1, 'System Administrator', 'document_create', 'Created document \'qqwqw\' (IC26080003) targeting \'CICS\'.', '::1', '2026-08-09 11:18:56');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
