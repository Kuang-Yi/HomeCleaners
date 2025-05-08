-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 08, 2025 at 05:53 AM
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
DROP DATABASE IF EXISTS `home_cleaners`;
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
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
(10, 4, 3, 'confirmed', '2025-05-05 12:44:42', '2025-05-21 18:00:00'),
(11, 4, 6, 'completed', '2025-05-05 15:45:43', '2025-05-13 16:00:00'),
(12, 6, 1, 'completed', '2025-05-05 15:47:23', '2025-05-16 18:00:00'),
(13, 7, 6, 'pending', '2025-05-05 16:28:28', '2025-05-06 12:00:00'),
(14, 4, 1, 'confirmed', '2025-05-07 23:12:49', '2025-05-08 13:00:00'),
(15, 9, 1, 'confirmed', '2025-05-08 13:43:05', '2025-05-16 16:00:00'),
(16, 9, 12, 'rejected', '2025-05-08 13:51:10', '2025-05-14 12:00:00');

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
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'House'),
(3, 'Toilet'),
(4, 'Carpet'),
(5, 'Car'),
(7, 'Lawn');

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
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `cleaner_id`, `title`, `description`, `price`, `category_id`, `pricing_type`, `created_at`, `view_count`, `shortlist_count`) VALUES
(1, 1, 'Full House Cleaning', 'Clean the whole house', 100.00, 1, 'per_job', '2025-05-04 00:34:10', 4, 2),
(2, 1, 'Toilet Cleaning', 'Clean the whole toilet', 50.00, 3, 'per_job', '2025-05-05 12:14:20', 3, 2),
(6, 5, 'Carpet Cleaning', 'Clean any carpet', 35.00, 4, 'per_job', '2025-05-05 15:45:10', 1, 1),
(4, 5, 'House Deep Cleaning', 'Deep cleaning for entire house yay', 30.00, 1, 'per_hour', '2025-05-05 12:43:43', 1, 1),
(13, 8, 'Washing of Carpet', 'Deep cleaning of carpet', 50.00, 4, 'per_job', '2025-05-08 13:32:37', 0, 0),
(10, 8, 'Lawn Mowing', 'Cutting of Grass, removing of weeds', 30.00, 7, 'per_hour', '2025-05-08 13:26:29', 0, 0),
(12, 8, 'Full Cleaning of house', 'Vacuuming, Mopping', 40.00, 1, 'per_hour', '2025-05-08 13:32:11', 0, 1);

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
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
(13, 4, 4, '2025-05-05 19:14:08'),
(14, 4, 7, '2025-05-05 19:14:09'),
(16, 9, 12, '2025-05-08 13:42:14');

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
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `user_type`, `created_at`) VALUES
(1, 'cleaner@gmail.com', '$2y$10$W26PcTPSpuvWIvA7HedLheaW/PHFNomqf.5wV.PXJqHxDuOF0IGsK', 'C', '2025-05-04 00:07:50'),
(2, 'admin@gmail.com', '$2y$10$W26PcTPSpuvWIvA7HedLheaW/PHFNomqf.5wV.PXJqHxDuOF0IGsK', 'A', '2025-05-04 00:07:51'),
(3, 'platform@gmail.com', '$2y$10$W26PcTPSpuvWIvA7HedLheaW/PHFNomqf.5wV.PXJqHxDuOF0IGsK', 'P', '2025-05-04 00:07:52'),
(4, 'homeowner@gmail.com', '$2y$10$5qtGmE9QiI4DD3nCNFdy3OBsgalvRkDlsbBcLJkPTuItDl/sLqPE.', 'H', '2025-05-04 00:47:40'),
(5, 'cleaner2@gmail.com', '$2y$10$59HWF.KBWTy6vb.5eS2iMOqg92uFPpmh4s63Zi40XaP0Km1k6pQpq', 'C', '2025-05-05 12:42:30'),
(6, 'homeowner2@gmail.com', '$2y$10$ConnCVhXKMEac60WGF0xT.poiHIOMOfcrPMealKx0uNchD5oQIQOO', 'H', '2025-05-05 15:46:40'),
(8, 'cleaner3@gmail.com', '$2y$10$9SOuBjFLCheUIcWgTJCAOuhqusJqSUWj6e3Oo60xIgZwqmIMnWHfS', 'C', '2025-05-08 13:25:33'),
(9, 'homeowner3@gmail.com', '$2y$10$.z0fNpXoA06IrR4X3FqAFufPd91/eJ4IZ7gvZwNSLArEIsGNovRU2', 'H', '2025-05-08 13:25:43');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
