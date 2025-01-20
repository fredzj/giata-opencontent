-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 18, 2025 at 01:24 PM
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
-- Table structure for table `vendor_giata_accommodations`
--

CREATE TABLE `vendor_giata_accommodations` (
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `id` mediumint(8) UNSIGNED NOT NULL,
  `giata_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `city_giata_id` int(10) UNSIGNED DEFAULT NULL,
  `destination_giata_id` int(10) UNSIGNED DEFAULT NULL,
  `country_code` char(2) DEFAULT NULL,
  `source` tinyint(3) UNSIGNED DEFAULT NULL,
  `rating` decimal(3,1) UNSIGNED DEFAULT NULL,
  `address_street` varchar(255) DEFAULT NULL,
  `address_streetnum` varchar(255) DEFAULT NULL,
  `address_zip` varchar(255) DEFAULT NULL,
  `address_cityname` varchar(255) DEFAULT NULL,
  `address_pobox` varchar(255) DEFAULT NULL,
  `address_federalstate_giata_id` int(10) UNSIGNED DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `geocode_accuracy` varchar(255) DEFAULT NULL,
  `geocode_latitude` varchar(255) DEFAULT NULL,
  `geocode_longitude` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `vendor_giata_accommodations`
--
ALTER TABLE `vendor_giata_accommodations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `giata_id` (`giata_id`),
  ADD KEY `name` (`name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `vendor_giata_accommodations`
--
ALTER TABLE `vendor_giata_accommodations`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
