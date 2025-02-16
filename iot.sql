-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 22, 2024 at 09:11 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `iot`
--

-- --------------------------------------------------------

--
-- Table structure for table `gas`
--

CREATE TABLE `gas` (
  `GasLv` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `gas`
--

INSERT INTO `gas` (`GasLv`) VALUES
(77);

-- --------------------------------------------------------

--
-- Table structure for table `limits`
--

CREATE TABLE `limits` (
  `id` int(11) NOT NULL,
  `TempLimit` int(11) NOT NULL,
  `GasLimit` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `limits`
--

INSERT INTO `limits` (`id`, `TempLimit`, `GasLimit`) VALUES
(9, 44, 1777);

-- --------------------------------------------------------

--
-- Table structure for table `line_notify_token`
--

CREATE TABLE `line_notify_token` (
  `id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `line_notify_token`
--

INSERT INTO `line_notify_token` (`id`, `token`, `created_at`) VALUES
(1, 'bZL1A1hnkTajem8aOugROsgOlXIOXTYBq1U6Iuydori', '2024-10-22 03:17:26');

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `temperature` float DEFAULT NULL,
  `gas_value` float DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`id`, `temperature`, `gas_value`, `timestamp`) VALUES
(1, 29.5, 1675, '2024-10-22 11:08:30'),
(2, 26.2, 1825, '2024-10-22 11:18:07'),
(3, 25.6, 1569, '0000-00-00 00:00:00'),
(4, 25.4, 1519, '2024-10-22 11:21:30'),
(5, 25.3, 2458, '2024-10-22 11:26:53'),
(6, 29.5, 1675, '2024-10-22 11:08:30'),
(7, 26.2, 1825, '2024-10-22 11:18:07'),
(8, 25.6, 1569, '0000-00-00 00:00:00'),
(9, 25.4, 1519, '2024-10-22 11:21:30'),
(10, 25.3, 2458, '2024-10-22 11:26:53'),
(11, 29.5, 1675, '2024-10-22 11:08:30'),
(12, 26.2, 1825, '2024-10-22 11:18:07'),
(13, 25.6, 1569, '2024-10-30 12:38:35'),
(14, 25.4, 1519, '2024-10-31 11:21:30'),
(15, 25.3, 2458, '2024-10-29 11:26:53'),
(16, 29.5, 1675, '2024-10-28 11:08:30'),
(17, 26.2, 1825, '2024-10-22 11:18:07'),
(18, 25.6, 1569, '2024-10-27 12:38:35'),
(19, 25.4, 1519, '2024-10-26 11:21:30'),
(20, 25.3, 2458, '2024-10-25 11:26:53');

-- --------------------------------------------------------

--
-- Table structure for table `temp`
--

CREATE TABLE `temp` (
  `TempLv` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `temp`
--

INSERT INTO `temp` (`TempLv`) VALUES
(50),
(50),
(50),
(69),
(69);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `limits`
--
ALTER TABLE `limits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD UNIQUE KEY `GasLimit` (`GasLimit`),
  ADD UNIQUE KEY `TempLimit` (`TempLimit`);

--
-- Indexes for table `line_notify_token`
--
ALTER TABLE `line_notify_token`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `limits`
--
ALTER TABLE `limits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `line_notify_token`
--
ALTER TABLE `line_notify_token`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
