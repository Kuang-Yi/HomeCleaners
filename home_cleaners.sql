-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 17, 2025 at 08:24 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `home_cleaners`
--
CREATE DATABASE IF NOT EXISTS `home_cleaners` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `home_cleaners`;

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
CREATE TABLE IF NOT EXISTS `bookings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `homeowner_id` int NOT NULL,
  `service_id` int NOT NULL,
  `status` enum('pending','confirmed','in_progress','completed','cancelled','rejected') DEFAULT 'pending',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `booking_datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `homeowner_id` (`homeowner_id`),
  KEY `service_id` (`service_id`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `homeowner_id`, `service_id`, `status`, `created_at`, `booking_datetime`) VALUES
(2, 4, 1, 'cancelled', '2025-05-05 11:57:55', '2025-05-06 14:00:00'),
(3, 4, 1, 'completed', '2025-05-05 12:08:04', '2025-05-07 12:00:00'),
(4, 4, 1, 'completed', '2025-05-05 12:10:57', '2025-05-09 16:00:00'),
(5, 4, 2, 'cancelled', '2025-05-05 12:14:47', '2025-05-08 13:00:00'),
(6, 4, 2, 'rejected', '2025-05-05 12:17:04', '2025-05-09 13:00:00'),
(7, 4, 2, 'cancelled', '2025-05-05 12:21:52', '2025-05-15 14:30:00'),
(8, 4, 2, 'completed', '2025-05-05 12:25:19', '2025-05-16 17:00:00'),
(9, 4, 2, 'completed', '2025-05-05 12:40:14', '2025-05-14 16:00:00'),
(19, 14, 18, 'rejected', '2025-05-13 10:41:47', '2025-06-12 18:00:00'),
(11, 4, 6, 'completed', '2025-05-05 15:45:43', '2025-05-13 16:00:00'),
(12, 6, 1, 'completed', '2025-05-05 15:47:23', '2025-05-16 18:00:00'),
(13, 7, 6, 'pending', '2025-05-05 16:28:28', '2025-05-06 12:00:00'),
(14, 4, 1, 'confirmed', '2025-05-07 23:12:49', '2025-05-08 13:00:00'),
(15, 9, 1, 'confirmed', '2025-05-08 13:43:05', '2025-05-16 16:00:00'),
(16, 9, 12, 'rejected', '2025-05-08 13:51:10', '2025-05-14 12:00:00'),
(17, 11, 15, 'pending', '2025-05-09 18:52:13', '2025-05-15 16:00:00'),
(20, 14, 19, 'completed', '2025-05-13 10:42:30', '2025-06-10 17:00:00'),
(21, 14, 18, 'confirmed', '2025-05-13 11:37:31', '2025-06-05 18:00:00'),
(22, 20, 20, 'completed', '2025-05-17 16:15:26', '2025-07-17 17:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'House'),
(3, 'Toilet'),
(4, 'Test'),
(5, 'Cars'),
(7, 'Lawn'),
(13, 'Others'),
(9, 'Window');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
CREATE TABLE IF NOT EXISTS `reviews` (
  `id` int NOT NULL AUTO_INCREMENT,
  `booking_id` int NOT NULL,
  `rating` int DEFAULT NULL,
  `comment` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `booking_id` (`booking_id`)
) ;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `booking_id`, `rating`, `comment`, `created_at`) VALUES
(1, 22, 5, 'Very Good', '2025-05-17 08:19:25');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

DROP TABLE IF EXISTS `services`;
CREATE TABLE IF NOT EXISTS `services` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cleaner_id` int NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `category_id` int NOT NULL,
  `pricing_type` enum('per_job','per_hour') NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `view_count` int DEFAULT '0',
  `shortlist_count` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `cleaner_id` (`cleaner_id`),
  KEY `category_id` (`category_id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `cleaner_id`, `title`, `description`, `price`, `category_id`, `pricing_type`, `created_at`, `view_count`, `shortlist_count`) VALUES
(1, 1, 'Full House Cleaning', 'Clean the whole house', 100.00, 1, 'per_job', '2025-05-04 00:34:10', 4, 3),
(2, 1, 'Toilet Cleaning', 'Clean the whole toilet', 50.00, 3, 'per_job', '2025-05-05 12:14:20', 3, 3),
(6, 5, 'Carpet Cleaning', 'Clean any carpet', 35.00, 4, 'per_job', '2025-05-05 15:45:10', 1, 2),
(4, 5, 'House Deep Cleaning', 'Deep cleaning for entire house yay', 30.00, 1, 'per_hour', '2025-05-05 12:43:43', 1, 1),
(13, 8, 'Washing of Carpet', 'Deep cleaning of carpet', 50.00, 4, 'per_job', '2025-05-08 13:32:37', 0, 0),
(10, 8, 'Lawn Mowing', 'Cutting of Grass, removing of weeds', 30.00, 7, 'per_hour', '2025-05-08 13:26:29', 0, 0),
(12, 8, 'Full Cleaning of house', 'Vacuuming, Mopping', 40.00, 1, 'per_hour', '2025-05-08 13:32:11', 0, 1),
(14, 10, 'Window Polishing', 'Polish all the windows in ur house', 200.00, 9, 'per_job', '2025-05-09 18:49:24', 0, 0),
(15, 10, 'Washing Toilet', 'Wash the whole toilet', 50.00, 3, 'per_job', '2025-05-09 18:50:16', 1, 0),
(19, 13, 'Cut Grass', 'cut ur grass for u :D', 303.00, 7, 'per_job', '2025-05-13 10:40:54', 1, 1),
(18, 13, 'Wipe Window', 'wipe ur window for u', 123.00, 9, 'per_job', '2025-05-13 10:39:50', 0, 1),
(20, 19, 'Car Clean', 'Car cleaning for u', 200.00, 5, 'per_job', '2025-05-17 01:56:05', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `shortlists`
--

DROP TABLE IF EXISTS `shortlists`;
CREATE TABLE IF NOT EXISTS `shortlists` (
  `id` int NOT NULL AUTO_INCREMENT,
  `homeowner_id` int NOT NULL,
  `service_id` int NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `homeowner_id` (`homeowner_id`,`service_id`),
  KEY `service_id` (`service_id`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `shortlists`
--

INSERT INTO `shortlists` (`id`, `homeowner_id`, `service_id`, `created_at`) VALUES
(11, 4, 2, '2025-05-05 19:14:07'),
(4, 4, 3, '2025-05-05 12:44:27'),
(12, 4, 6, '2025-05-05 19:14:08'),
(6, 6, 1, '2025-05-05 15:46:58'),
(7, 6, 2, '2025-05-05 15:47:07'),
(10, 4, 1, '2025-05-05 19:14:07'),
(14, 4, 7, '2025-05-05 19:14:09'),
(17, 11, 16, '2025-05-09 18:52:03'),
(16, 9, 12, '2025-05-08 13:42:14'),
(18, 14, 18, '2025-05-13 10:41:25'),
(23, 20, 20, '2025-05-17 16:15:17'),
(21, 14, 1, '2025-05-13 11:20:09'),
(22, 14, 2, '2025-05-13 11:20:17');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` enum('C','H','A','P') NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `account_status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `user_type`, `created_at`, `account_status`) VALUES
(1, 'cleaner@gmail.com', '$2y$10$W26PcTPSpuvWIvA7HedLheaW/PHFNomqf.5wV.PXJqHxDuOF0IGsK', 'C', '2025-05-04 00:07:50', 1),
(2, 'admin@gmail.com', '$2y$10$W26PcTPSpuvWIvA7HedLheaW/PHFNomqf.5wV.PXJqHxDuOF0IGsK', 'A', '2025-05-04 00:07:51', 1),
(3, 'platform@gmail.com', '$2y$10$W26PcTPSpuvWIvA7HedLheaW/PHFNomqf.5wV.PXJqHxDuOF0IGsK', 'P', '2025-05-04 00:07:52', 1),
(4, 'homeowner@gmail.com', '$2y$10$5qtGmE9QiI4DD3nCNFdy3OBsgalvRkDlsbBcLJkPTuItDl/sLqPE.', 'H', '2025-05-04 00:47:40', 1),
(5, 'cleaner2@gmail.com', '$2y$10$59HWF.KBWTy6vb.5eS2iMOqg92uFPpmh4s63Zi40XaP0Km1k6pQpq', 'C', '2025-05-05 12:42:30', 1),
(6, 'homeowner2@gmail.com', '$2y$10$ConnCVhXKMEac60WGF0xT.poiHIOMOfcrPMealKx0uNchD5oQIQOO', 'H', '2025-05-05 15:46:40', 1),
(8, 'cleaner3@gmail.com', '$2y$10$9SOuBjFLCheUIcWgTJCAOuhqusJqSUWj6e3Oo60xIgZwqmIMnWHfS', 'C', '2025-05-08 13:25:33', 1),
(16, 'cleaner6@gmail.com', '$2y$10$2.7M7M8FMSxatGa5BJ4xV.JqHGtFTiC5QmpsCOfNdJHDZGnZ6Z.hC', 'C', '2025-05-15 21:05:00', 1),
(10, 'cleaner4@gmail.com', '$2y$10$TYEDlXHmiNvl4k4rC1VyaexjCDlhfNfRKQCJ86a8/NtALypy42i/O', 'C', '2025-05-09 16:21:59', 1),
(11, 'homeowner4@gmail.com', '$2y$10$LNfsQNolI84C8g8lW.uJlO5l.Rwfxdm8p77BA2t2EYdxFMnpv8vb.', 'H', '2025-05-09 16:22:28', 1),
(13, 'cleaner5@gmail.com', '$2y$10$62wiE7ERMpawIh/VIL2Cg.nCcOG.hwKUBS6Ya7dKAAP59Vve69fNG', 'C', '2025-05-13 10:37:32', 1),
(14, 'homeowner5@gmail.com', '$2y$10$66BNETh2GMBOM21pCdfMSeET.q79sxB6CdMQMDVfTM8Q3bT6XrQSe', 'H', '2025-05-13 10:37:45', 1),
(20, 'homeowner7@gmail.com', '$2y$10$7CQGHuJP5Ga97OijEH6PBOMw1dpZOT.iZomaJ29LHSEDA2eY3D4bG', 'H', '2025-05-17 16:00:26', 1),
(19, 'cleaner7@gmail.com', '$2y$10$e.CsB0WS.Ll5r0gPw9sdT./9bEiJRL2KOxtSQB/N1EAkz6iJoNOci', 'C', '2025-05-17 01:44:47', 1);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
