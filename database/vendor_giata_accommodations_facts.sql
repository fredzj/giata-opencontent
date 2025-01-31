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
-- Table structure for table `vendor_giata_accommodations_facts`
--

CREATE TABLE `vendor_giata_accommodations_facts` (
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `id` mediumint(5) UNSIGNED NOT NULL,
  `giataId` int(10) UNSIGNED NOT NULL,
  `factDefId` smallint(5) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `vendor_giata_accommodations_facts`
--
ALTER TABLE `vendor_giata_accommodations_facts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `giataId` (`giataId`),
  ADD KEY `factDefId` (`factDefId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `vendor_giata_accommodations_facts`
--
ALTER TABLE `vendor_giata_accommodations_facts`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
