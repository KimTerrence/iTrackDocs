-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 08, 2026 at 12:21 AM
-- Server version: 11.8.8-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u941439857_itrackdocs`
--

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `tracking_id` varchar(50) NOT NULL,
  `creator_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `origin` varchar(100) NOT NULL,
  `target_department` varchar(100) DEFAULT NULL,
  `current_location` varchar(100) NOT NULL,
  `status` varchar(50) DEFAULT 'Routing',
  `file_path` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`tracking_id`, `creator_id`, `title`, `category`, `origin`, `target_department`, `current_location`, `status`, `file_path`, `created_at`) VALUES
('IC26060001', 1, 'From chm to cics', NULL, 'CHM', 'CICS', 'CICS', 'Received', NULL, '2026-06-15 15:37:38'),
('IC26060002', 1, 'from chm to ceo', NULL, 'CHM', 'CEO', 'CEO', 'Returned', NULL, '2026-06-15 15:37:50'),
('IC26060003', 2, 'from cics to ceo', NULL, 'CICS', 'CEO', 'CEO', 'Returned', NULL, '2026-06-15 15:38:36'),
('IC26060004', 1, 'Test', NULL, 'CHM', 'CICS', 'CICS', 'Void', NULL, '2026-06-22 09:01:58'),
('IC26060005', 1, 'Testing To CICS', NULL, 'CHM', 'CICS', 'CICS', 'Received', NULL, '2026-06-22 09:58:29'),
('IC26060006', 1, 'qw', NULL, 'CHM', 'CICS', 'CICS', 'Received', NULL, '2026-06-22 10:08:48'),
('IC26060007', 1, 'qwe', 'Memorandum', 'CHM', 'CHM', 'CHM', 'Routing', NULL, '2026-06-23 22:57:19'),
('IC26060008', 1, 'TO CICS', 'Letter', 'CHM', 'CICS', 'CICS', 'Received', NULL, '2026-06-23 23:03:48'),
('IC26060009', 3, 'To Kim', 'Certificate', 'CEO', 'CICS', 'CEO', 'Returned', NULL, '2026-06-24 13:58:21'),
('IC26060010', 2, 'To ceo jane', 'Notice', 'CICS', 'CEO', 'CICS', 'Routing', NULL, '2026-06-24 14:53:29'),
('IC26060011', 3, 'ti cics agaiun', 'Letter', 'CEO', 'CICS', 'CEO', 'Routing', NULL, '2026-06-24 14:55:26');

-- --------------------------------------------------------

--
-- Table structure for table `document_history`
--

CREATE TABLE `document_history` (
  `id` int(11) NOT NULL,
  `tracking_id` varchar(50) DEFAULT NULL,
  `location` varchar(100) NOT NULL,
  `action` varchar(255) NOT NULL,
  `recorded_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `document_history`
--

INSERT INTO `document_history` (`id`, `tracking_id`, `location`, `action`, `recorded_at`) VALUES
(57, 'IC26060001', 'CHM', 'Document created. Target destination: CICS', '2026-06-15 15:37:38'),
(58, 'IC26060002', 'CHM', 'Document created. Target destination: CEO', '2026-06-15 15:37:50'),
(59, 'IC26060003', 'CICS', 'Document created. Target destination: CEO', '2026-06-15 15:38:36'),
(60, 'IC26060003', 'CEO', 'Document Returned to CEO', '2026-06-15 15:42:08'),
(61, 'IC26060002', 'CEO', 'Document Returned to CEO', '2026-06-15 15:42:27'),
(62, 'IC26060001', 'CICS', 'Document Returned to CICS', '2026-06-22 03:33:05'),
(63, 'IC26060004', 'CHM', 'Document created. Target destination: CICS', '2026-06-22 09:01:58'),
(64, 'IC26060005', 'CHM', 'Document created. Target destination: CICS', '2026-06-22 09:58:29'),
(65, 'IC26060006', 'CHM', 'Document created. Target destination: CICS', '2026-06-22 10:08:48'),
(66, 'IC26060007', 'CHM', 'Document created. Target destination: CHM', '2026-06-23 22:57:19'),
(67, 'IC26060008', 'CHM', 'Document created. Target destination: CICS', '2026-06-23 23:03:48'),
(68, 'IC26060005', 'CICS', 'Document Received at CICS', '2026-06-23 23:04:19'),
(69, 'IC26060008', 'CICS', 'Document Received at CICS', '2026-06-23 23:04:33'),
(70, 'IC26060008', 'CHM', 'Document Returned to CHM', '2026-06-24 13:48:52'),
(71, 'IC26060005', 'CICS', 'Document Received at CICS', '2026-06-24 13:49:30'),
(72, 'IC26060006', 'CICS', 'Document Received at CICS', '2026-06-24 13:49:49'),
(73, 'IC26060001', 'CICS', 'Document Received at CICS', '2026-06-24 13:50:42'),
(74, 'IC26060009', 'CEO', 'Document created. Target destination: CICS', '2026-06-24 13:58:21'),
(75, 'IC26060004', 'CICS', 'Document marked as VOID', '2026-06-24 14:05:13'),
(76, 'IC26060008', 'CICS', 'Document Received at CICS', '2026-06-24 14:06:04'),
(77, 'IC26060004', 'CICS', 'Document Received at CICS', '2026-06-24 14:11:48'),
(78, 'IC26060004', 'CICS', 'Document Received at CICS', '2026-06-24 14:11:53'),
(79, 'IC26060004', 'CICS', 'Document marked as VOID', '2026-06-24 14:12:05'),
(80, 'IC26060009', 'CICS', 'Document Received at CICS', '2026-06-24 14:51:31'),
(81, 'IC26060009', 'CEO', 'Document Returned to CEO', '2026-06-24 14:52:12'),
(82, 'IC26060008', 'CICS', 'Document Received at CICS', '2026-06-24 14:53:05'),
(83, 'IC26060010', 'CICS', 'Document created. Target destination: CEO', '2026-06-24 14:53:29'),
(84, 'IC26060011', 'CEO', 'Document created. Target destination: CICS', '2026-06-24 14:55:26');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'info',
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `tracking_id` varchar(20) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `type`, `title`, `message`, `tracking_id`, `is_read`, `created_at`) VALUES
(12, 3, 'success', 'Document Received', 'Your document \'To Kim\' (IC26060009) has been received at CICS.', 'IC26060009', 0, '2026-06-24 14:51:31'),
(13, 3, 'warning', 'Document Returned', 'Your document \'To Kim\' (IC26060009) has been returned to CEO.', 'IC26060009', 0, '2026-06-24 14:52:12'),
(14, 1, 'success', 'Document Received', 'Your document \'TO CICS\' (IC26060008) has been received at CICS.', 'IC26060008', 0, '2026-06-24 14:53:05'),
(15, 3, 'info', 'New Document Incoming', 'A new document \'To ceo jane\' (IC26060010) has been routed to your department from CICS.', 'IC26060010', 0, '2026-06-24 14:53:29'),
(16, 2, 'info', 'New Document Incoming', 'A new document \'ti cics agaiun\' (IC26060011) has been routed to your department from CEO.', 'IC26060011', 0, '2026-06-24 14:55:26'),
(17, 10, 'info', 'New Document Incoming', 'A new document \'ti cics agaiun\' (IC26060011) has been routed to your department from CEO.', 'IC26060011', 0, '2026-06-24 14:55:26');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `role` varchar(20) DEFAULT 'staff',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `status` varchar(20) NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `name`, `email`, `department`, `role`, `created_at`, `status`) VALUES
(1, 'admin@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin@gmail.com', 'CHM', 'admin', '2026-05-27 02:46:07', 'active'),
(2, 'kimterrence', '$2y$10$iB3vQcDRuXe19IvnlkZ2/uOJapptPgce1fqLG91B72d.fMvTI2lPu', 'Kim Terrence', 'quineskimterrence@gmail.com', 'CICS', 'head', '2026-05-28 14:35:19', 'active'),
(3, 'kimquines274@gmail.com', '$2y$10$9wSFm4DK3JWcL8DmcLTSm./pZUWUDYWP5SSxHPEHtMGnV5rkzK3mm', 'Jane Collado', 'kimquines274@gmail.com', 'CEO', 'admin', '2026-06-12 11:32:05', 'active'),
(10, 'john', '$2y$10$yYCqwvjIMbL1BuuYtIE27.FfSTUKtEY.r.ojWk3/RG.X68RGhq8qC', 'John Doe', 'john@gmail.com', 'CICS', 'staff', '2026-06-22 03:31:12', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `user_activity_logs`
--

CREATE TABLE `user_activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `username` varchar(255) NOT NULL,
  `action` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `user_activity_logs`
--

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
(43, 2, 'Kim Terrence', 'login', 'User \'quineskimterrence@gmail.com\' logged in.', '143.44.141.38', '2026-06-24 14:50:17'),
(44, 2, 'Kim Terrence', 'logout', 'User \'Kim Terrence\' logged out.', '143.44.141.38', '2026-06-24 14:50:36'),
(45, 3, 'Jane Collado', 'login', 'User \'kimquines274@gmail.com\' logged in.', '143.44.141.38', '2026-06-24 14:50:47'),
(46, 3, 'Jane Collado', 'logout', 'User \'Jane Collado\' logged out.', '143.44.141.38', '2026-06-24 14:51:10'),
(47, 2, 'Kim Terrence', 'login', 'User \'quineskimterrence@gmail.com\' logged in.', '143.44.141.38', '2026-06-24 14:51:26'),
(48, 2, 'Kim Terrence', 'document_receive', 'Document IC26060009 received at \'CICS\'.', '143.44.141.38', '2026-06-24 14:51:31'),
(49, 2, 'Kim Terrence', 'logout', 'User \'Kim Terrence\' logged out.', '143.44.141.38', '2026-06-24 14:51:46'),
(50, 3, 'Jane Collado', 'login', 'User \'kimquines274@gmail.com\' logged in.', '143.44.141.38', '2026-06-24 14:51:54'),
(51, 3, 'Jane Collado', 'document_return', 'Document IC26060009 returned to \'CEO\'.', '143.44.141.38', '2026-06-24 14:52:12'),
(52, 3, 'Jane Collado', 'logout', 'User \'Jane Collado\' logged out.', '143.44.141.38', '2026-06-24 14:52:20'),
(53, 2, 'Kim Terrence', 'login', 'User \'quineskimterrence@gmail.com\' logged in.', '143.44.141.38', '2026-06-24 14:52:50'),
(54, 2, 'Kim Terrence', 'document_receive', 'Document IC26060008 received at \'CICS\'.', '143.44.141.38', '2026-06-24 14:53:05'),
(55, 2, 'Kim Terrence', 'document_create', 'Created document \'To ceo jane\' (IC26060010) targeting \'CEO\'.', '143.44.141.38', '2026-06-24 14:53:29'),
(56, 2, 'Kim Terrence', 'logout', 'User \'Kim Terrence\' logged out.', '143.44.141.38', '2026-06-24 14:53:52'),
(57, 3, 'Jane Collado', 'login', 'User \'kimquines274@gmail.com\' logged in.', '143.44.141.38', '2026-06-24 14:53:57'),
(58, 3, 'Jane Collado', 'document_create', 'Created document \'ti cics agaiun\' (IC26060011) targeting \'CICS\'.', '143.44.141.38', '2026-06-24 14:55:26'),
(59, 3, 'Jane Collado', 'logout', 'User \'Jane Collado\' logged out.', '143.44.141.38', '2026-06-24 14:55:32'),
(60, 2, 'Kim Terrence', 'login', 'User \'quineskimterrence@gmail.com\' logged in.', '143.44.141.38', '2026-06-24 14:55:40'),
(61, 2, 'Kim Terrence', 'login', 'User \'quineskimterrence@gmail.com\' logged in.', '143.44.141.38', '2026-06-28 12:23:33'),
(62, 2, 'Kim Terrence', 'login', 'User \'quineskimterrence@gmail.com\' logged in.', '143.44.141.38', '2026-07-08 00:18:28'),
(63, 2, 'Kim Terrence', 'logout', 'User \'Kim Terrence\' logged out.', '143.44.141.38', '2026-07-08 00:18:46'),
(64, 1, 'System Administrator', 'login', 'User \'admin@gmail.com\' logged in.', '143.44.141.38', '2026-07-08 00:20:31');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`tracking_id`);

--
-- Indexes for table `document_history`
--
ALTER TABLE `document_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tracking_id` (`tracking_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `user_activity_logs`
--
ALTER TABLE `user_activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `action` (`action`),
  ADD KEY `created_at` (`created_at`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `document_history`
--
ALTER TABLE `document_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `user_activity_logs`
--
ALTER TABLE `user_activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `document_history`
--
ALTER TABLE `document_history`
  ADD CONSTRAINT `document_history_ibfk_1` FOREIGN KEY (`tracking_id`) REFERENCES `documents` (`tracking_id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
