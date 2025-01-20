-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 18, 2025 at 01:28 PM
-- Server version: 10.6.18-MariaDB-cll-lve
-- PHP Version: 8.1.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u10919p285003_see`
--

-- --------------------------------------------------------

--
-- Table structure for table `vendor_giata_roomtypes`
--

CREATE TABLE `vendor_giata_roomtypes` (
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `id` mediumint(8) UNSIGNED NOT NULL,
  `variantId` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `view` varchar(255) DEFAULT NULL,
  `category_attribute_id` tinyint(3) UNSIGNED DEFAULT NULL,
  `category_attribute_name` varchar(255) DEFAULT NULL,
  `type_attribute_id` tinyint(3) UNSIGNED DEFAULT NULL,
  `type_attribute_name` varchar(255) DEFAULT NULL,
  `view_attribute_id` tinyint(3) UNSIGNED DEFAULT NULL,
  `view_attribute_name` varchar(255) DEFAULT NULL,
  `image_relations` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `vendor_giata_roomtypes`
--
ALTER TABLE `vendor_giata_roomtypes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `variantId` (`variantId`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `vendor_giata_roomtypes`
--
ALTER TABLE `vendor_giata_roomtypes`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
