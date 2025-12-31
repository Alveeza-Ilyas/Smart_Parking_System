-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 21, 2025 at 11:34 AM
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
-- Database: `smart_parking`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `slot_id` varchar(10) NOT NULL,
  `duration` int(11) NOT NULL,
  `booked_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `slot_id`, `duration`, `booked_at`) VALUES
(1, 2, 'A10', 1, '2025-12-20 19:01:10'),
(2, 2, 'B16', 5, '2025-12-20 19:16:08'),
(3, 2, 'A18', 1, '2025-12-21 09:23:20'),
(4, 2, 'B24', 1, '2025-12-21 09:24:58'),
(5, 2, 'D25', 3, '2025-12-21 09:43:31');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `subject` varchar(100) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `user_id`, `subject`, `message`, `sent_at`) VALUES
(1, 2, 'feedback', 'good', '2025-12-20 19:01:58'),
(2, 2, 'feedback', 'good', '2025-12-20 19:05:13'),
(3, 2, 'feedback', 'good', '2025-12-20 19:05:19');

-- --------------------------------------------------------

--
-- Table structure for table `parking_slots`
--

CREATE TABLE `parking_slots` (
  `id` int(11) NOT NULL,
  `slot_id` varchar(10) NOT NULL,
  `floor` char(1) NOT NULL,
  `status` enum('available','booked') DEFAULT 'available',
  `booked_by` int(11) DEFAULT NULL,
  `booked_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parking_slots`
--

INSERT INTO `parking_slots` (`id`, `slot_id`, `floor`, `status`, `booked_by`, `booked_at`) VALUES
(1, 'A1', 'A', 'available', NULL, NULL),
(2, 'A2', 'A', 'available', NULL, NULL),
(3, 'A3', 'A', 'available', NULL, NULL),
(4, 'A4', 'A', 'available', NULL, NULL),
(5, 'A5', 'A', 'available', NULL, NULL),
(6, 'A6', 'A', 'available', NULL, NULL),
(7, 'A7', 'A', 'available', NULL, NULL),
(8, 'A8', 'A', 'available', NULL, NULL),
(9, 'A9', 'A', 'available', NULL, NULL),
(10, 'A10', 'A', 'booked', 2, '2025-12-20 19:01:10'),
(11, 'A11', 'A', 'available', NULL, NULL),
(12, 'A12', 'A', 'available', NULL, NULL),
(13, 'A13', 'A', 'available', NULL, NULL),
(14, 'A14', 'A', 'available', NULL, NULL),
(15, 'A15', 'A', 'available', NULL, NULL),
(16, 'A16', 'A', 'available', NULL, NULL),
(17, 'A17', 'A', 'available', NULL, NULL),
(18, 'A18', 'A', 'booked', 2, '2025-12-21 09:23:20'),
(19, 'A19', 'A', 'available', NULL, NULL),
(20, 'A20', 'A', 'available', NULL, NULL),
(21, 'A21', 'A', 'available', NULL, NULL),
(22, 'A22', 'A', 'available', NULL, NULL),
(23, 'A23', 'A', 'available', NULL, NULL),
(24, 'A24', 'A', 'available', NULL, NULL),
(25, 'A25', 'A', 'available', NULL, NULL),
(26, 'B1', 'B', 'available', NULL, NULL),
(27, 'B2', 'B', 'available', NULL, NULL),
(28, 'B3', 'B', 'available', NULL, NULL),
(29, 'B4', 'B', 'available', NULL, NULL),
(30, 'B5', 'B', 'available', NULL, NULL),
(31, 'B6', 'B', 'available', NULL, NULL),
(32, 'B7', 'B', 'available', NULL, NULL),
(33, 'B8', 'B', 'available', NULL, NULL),
(34, 'B9', 'B', 'available', NULL, NULL),
(35, 'B10', 'B', 'available', NULL, NULL),
(36, 'B11', 'B', 'available', NULL, NULL),
(37, 'B12', 'B', 'available', NULL, NULL),
(38, 'B13', 'B', 'available', NULL, NULL),
(39, 'B14', 'B', 'available', NULL, NULL),
(40, 'B15', 'B', 'available', NULL, NULL),
(41, 'B16', 'B', 'booked', 2, '2025-12-20 19:16:08'),
(42, 'B17', 'B', 'available', NULL, NULL),
(43, 'B18', 'B', 'available', NULL, NULL),
(44, 'B19', 'B', 'available', NULL, NULL),
(45, 'B20', 'B', 'available', NULL, NULL),
(46, 'B21', 'B', 'available', NULL, NULL),
(47, 'B22', 'B', 'available', NULL, NULL),
(48, 'B23', 'B', 'available', NULL, NULL),
(49, 'B24', 'B', 'booked', 2, '2025-12-21 09:24:58'),
(50, 'B25', 'B', 'available', NULL, NULL),
(51, 'C1', 'C', 'available', NULL, NULL),
(52, 'C2', 'C', 'available', NULL, NULL),
(53, 'C3', 'C', 'available', NULL, NULL),
(54, 'C4', 'C', 'available', NULL, NULL),
(55, 'C5', 'C', 'available', NULL, NULL),
(56, 'C6', 'C', 'available', NULL, NULL),
(57, 'C7', 'C', 'available', NULL, NULL),
(58, 'C8', 'C', 'available', NULL, NULL),
(59, 'C9', 'C', 'available', NULL, NULL),
(60, 'C10', 'C', 'available', NULL, NULL),
(61, 'C11', 'C', 'available', NULL, NULL),
(62, 'C12', 'C', 'available', NULL, NULL),
(63, 'C13', 'C', 'available', NULL, NULL),
(64, 'C14', 'C', 'available', NULL, NULL),
(65, 'C15', 'C', 'available', NULL, NULL),
(66, 'C16', 'C', 'available', NULL, NULL),
(67, 'C17', 'C', 'available', NULL, NULL),
(68, 'C18', 'C', 'available', NULL, NULL),
(69, 'C19', 'C', 'available', NULL, NULL),
(70, 'C20', 'C', 'available', NULL, NULL),
(71, 'C21', 'C', 'available', NULL, NULL),
(72, 'C22', 'C', 'available', NULL, NULL),
(73, 'C23', 'C', 'available', NULL, NULL),
(74, 'C24', 'C', 'available', NULL, NULL),
(75, 'C25', 'C', 'available', NULL, NULL),
(76, 'D1', 'D', 'available', NULL, NULL),
(77, 'D2', 'D', 'available', NULL, NULL),
(78, 'D3', 'D', 'available', NULL, NULL),
(79, 'D4', 'D', 'available', NULL, NULL),
(80, 'D5', 'D', 'available', NULL, NULL),
(81, 'D6', 'D', 'available', NULL, NULL),
(82, 'D7', 'D', 'available', NULL, NULL),
(83, 'D8', 'D', 'available', NULL, NULL),
(84, 'D9', 'D', 'available', NULL, NULL),
(85, 'D10', 'D', 'available', NULL, NULL),
(86, 'D11', 'D', 'available', NULL, NULL),
(87, 'D12', 'D', 'available', NULL, NULL),
(88, 'D13', 'D', 'available', NULL, NULL),
(89, 'D14', 'D', 'available', NULL, NULL),
(90, 'D15', 'D', 'available', NULL, NULL),
(91, 'D16', 'D', 'available', NULL, NULL),
(92, 'D17', 'D', 'available', NULL, NULL),
(93, 'D18', 'D', 'available', NULL, NULL),
(94, 'D19', 'D', 'available', NULL, NULL),
(95, 'D20', 'D', 'available', NULL, NULL),
(96, 'D21', 'D', 'available', NULL, NULL),
(97, 'D22', 'D', 'available', NULL, NULL),
(98, 'D23', 'D', 'available', NULL, NULL),
(99, 'D24', 'D', 'available', NULL, NULL),
(100, 'D25', 'D', 'booked', 2, '2025-12-21 09:43:31');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `vehicle` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `phone`, `vehicle`, `password`, `created_at`) VALUES
(1, 'Test User', 'test@example.com', '1234567890', 'ABC-123', 'password123', '2025-12-20 15:34:10'),
(2, 'Alveeza', 'alveeza01@gmail.com', '03467567845', 'ASD-456', '$2y$10$f2lGqwSh.Ub14m.vJqtg..NSZkx9ZOdAeXTvF9AO44tdk3SEGTmCO', '2025-12-20 17:31:07'),
(3, 'Alveeza Ilyas', 'alveeza0109@gmail.com', '03291792350', 'AA-670', '$2y$10$5cf0QBl5pPKs.FjclxTVLOp8URP3IYKSzR6WOYb8LWhqRZxCuguMi', '2025-12-21 09:36:43');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `parking_slots`
--
ALTER TABLE `parking_slots`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booked_by` (`booked_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `parking_slots`
--
ALTER TABLE `parking_slots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD CONSTRAINT `contact_messages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `parking_slots`
--
ALTER TABLE `parking_slots`
  ADD CONSTRAINT `parking_slots_ibfk_1` FOREIGN KEY (`booked_by`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
