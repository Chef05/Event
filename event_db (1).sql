-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 09, 2025 at 01:04 AM
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
-- Database: `event_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `registration_id` int(11) DEFAULT NULL,
  `otp_code` varchar(6) NOT NULL,
  `checked_in` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `start_date` date NOT NULL DEFAULT curdate(),
  `end_date` date NOT NULL DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `user_id`, `title`, `description`, `created_at`, `start_date`, `end_date`) VALUES
(42, 1, 'Test1', '1', '2025-03-08 23:05:34', '2025-03-09', '2025-03-10'),
(43, 1, 'Test2', '2', '2025-03-08 23:06:09', '2025-03-11', '2025-03-12'),
(44, 1, 'Test3', '3', '2025-03-08 23:06:36', '2025-03-14', '2025-03-15'),
(45, 1, 'Test4', '4', '2025-03-08 23:47:38', '2025-03-16', '2025-03-18');

-- --------------------------------------------------------

--
-- Table structure for table `event_images`
--

CREATE TABLE `event_images` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `image_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_images`
--

INSERT INTO `event_images` (`id`, `event_id`, `image_url`) VALUES
(75, 42, 'uploads/67ccdab00db28-1255447.jpg'),
(76, 43, 'uploads/67ccdac8efa6a-1275093.png'),
(77, 43, 'uploads/67ccdac8f01d1-1277585.png'),
(78, 44, 'uploads/67ccdad77c4dc-1295097.jpg'),
(79, 44, 'uploads/67ccdad77cd28-1302684.jpg'),
(80, 44, 'uploads/67ccdad77d682-1303029.jpg'),
(81, 45, 'uploads/67ccdae55041f-1315574.jpg'),
(82, 45, 'uploads/67ccdae550b94-1312523.png'),
(83, 45, 'uploads/67ccdae551250-1315558.jpg'),
(84, 45, 'uploads/67ccdae55189e-1315573.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `event_statistics`
--

CREATE TABLE `event_statistics` (
  `event_id` int(11) DEFAULT NULL,
  `total_registrations` int(11) DEFAULT 0,
  `total_checked_in` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_statistics`
--

INSERT INTO `event_statistics` (`event_id`, `total_registrations`, `total_checked_in`, `created_at`) VALUES
(42, 1, 0, '2025-03-08 23:07:45'),
(43, 3, 0, '2025-03-08 23:07:45'),
(44, 4, 0, '2025-03-08 23:07:45'),
(42, 1, 0, '2025-03-08 23:07:50'),
(43, 3, 0, '2025-03-08 23:07:50'),
(44, 4, 0, '2025-03-08 23:07:50'),
(42, 2, 0, '2025-03-08 23:08:00'),
(43, 3, 0, '2025-03-08 23:08:00'),
(44, 4, 0, '2025-03-08 23:08:00'),
(42, 2, 0, '2025-03-08 23:08:26'),
(43, 3, 0, '2025-03-08 23:08:26'),
(44, 4, 0, '2025-03-08 23:08:26'),
(42, 2, 0, '2025-03-08 23:09:40'),
(43, 3, 0, '2025-03-08 23:09:40'),
(44, 4, 0, '2025-03-08 23:09:40'),
(42, 2, 0, '2025-03-08 23:09:41'),
(43, 3, 0, '2025-03-08 23:09:41'),
(44, 4, 0, '2025-03-08 23:09:41'),
(42, 2, 0, '2025-03-08 23:09:41'),
(43, 3, 0, '2025-03-08 23:09:41'),
(44, 4, 0, '2025-03-08 23:09:41'),
(42, 2, 0, '2025-03-08 23:09:41'),
(43, 3, 0, '2025-03-08 23:09:41'),
(44, 4, 0, '2025-03-08 23:09:41'),
(42, 2, 0, '2025-03-08 23:10:25'),
(43, 3, 0, '2025-03-08 23:10:25'),
(44, 4, 0, '2025-03-08 23:10:25'),
(42, 2, 0, '2025-03-08 23:10:25'),
(43, 3, 0, '2025-03-08 23:10:25'),
(44, 4, 0, '2025-03-08 23:10:25'),
(42, 2, 0, '2025-03-08 23:10:25'),
(43, 3, 0, '2025-03-08 23:10:25'),
(44, 4, 0, '2025-03-08 23:10:25'),
(42, 2, 0, '2025-03-08 23:10:25'),
(43, 3, 0, '2025-03-08 23:10:25'),
(44, 4, 0, '2025-03-08 23:10:25'),
(42, 2, 0, '2025-03-08 23:10:26'),
(43, 3, 0, '2025-03-08 23:10:26'),
(44, 4, 0, '2025-03-08 23:10:26'),
(42, 2, 0, '2025-03-08 23:10:26'),
(43, 3, 0, '2025-03-08 23:10:26'),
(44, 4, 0, '2025-03-08 23:10:26'),
(42, 2, 0, '2025-03-08 23:10:26'),
(43, 3, 0, '2025-03-08 23:10:26'),
(44, 4, 0, '2025-03-08 23:10:26'),
(42, 2, 0, '2025-03-08 23:11:51'),
(43, 3, 0, '2025-03-08 23:11:51'),
(44, 4, 0, '2025-03-08 23:11:51'),
(42, 2, 0, '2025-03-08 23:11:51'),
(43, 3, 0, '2025-03-08 23:11:51'),
(44, 4, 0, '2025-03-08 23:11:51'),
(42, 2, 0, '2025-03-08 23:11:52'),
(43, 3, 0, '2025-03-08 23:11:52'),
(44, 4, 0, '2025-03-08 23:11:52'),
(42, 2, 0, '2025-03-08 23:12:04'),
(43, 3, 0, '2025-03-08 23:12:04'),
(44, 4, 0, '2025-03-08 23:12:04'),
(42, 2, 0, '2025-03-08 23:12:45'),
(43, 3, 0, '2025-03-08 23:12:45'),
(44, 4, 0, '2025-03-08 23:12:45');

-- --------------------------------------------------------

--
-- Table structure for table `registrations`
--

CREATE TABLE `registrations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `event_id` int(11) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `checked_in` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registrations`
--

INSERT INTO `registrations` (`id`, `user_id`, `event_id`, `status`, `created_at`, `checked_in`) VALUES
(28, 2, 42, 'approved', '2025-03-08 23:06:57', 1),
(29, 2, 43, 'approved', '2025-03-08 23:22:04', 1),
(30, 2, 44, 'pending', '2025-03-08 23:22:16', 0),
(31, 3, 43, 'approved', '2025-03-08 23:22:38', 1),
(32, 3, 44, 'pending', '2025-03-08 23:22:42', 0),
(33, 5, 44, 'pending', '2025-03-08 23:22:55', 0),
(35, 6, 43, 'rejected', '2025-03-08 23:33:34', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `gender` enum('male','female','other') NOT NULL,
  `role` enum('admin','volunteer') NOT NULL DEFAULT 'volunteer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `first_name`, `last_name`, `gender`, `role`, `created_at`) VALUES
(1, 'alice.johnson@example.com', '$2y$10$NbZPDI8zrOMz0venNJPvn.i6DXDp1nEEU/YGZku6PTTEvvUSqi2/6', 'Alice', 'Johnson', 'female', 'admin', '2025-03-01 21:49:42'),
(2, 'bob.smith@example.com', '$2y$10$.Yh.8y7VKxB5FZRASnJqYOBqFvqGcm3579yKWN7r8h9cig3WYahvW', 'Bob', 'Smith', 'male', 'volunteer', '2025-03-01 21:57:35'),
(3, 'henry.moore@example.com', '$2y$10$98ZrkC60ferj3YwmfFJdyeLaK2jx7hMDjQEUtneZrPIEETVR5K2sG', 'Henry', 'Moore', 'male', 'volunteer', '2025-03-01 22:38:29'),
(4, 'jack.anderson@example.com', '$2y$10$g6MnRkOqAv2iKvrvCj.YPe.vCKpP08.5OBkMMpV8mHbuxWWSf.hvW', 'Jack', 'anderson', 'male', 'volunteer', '2025-03-01 22:48:35'),
(5, 'frank.miller@example.com', '$2y$10$MPouC5l/gCRcMsNjU2t1a.EdbrN/dhy7s5Z5Xg2m8HXceuDB/puOC', 'Frank', 'Miller', 'male', 'volunteer', '2025-03-05 20:52:05'),
(6, 'demo123@gmail.com', '$2y$10$FIOu36153LQEwb2YkIc.a.9GuGmWBwzSSgycx6EudxE1flPmqrP7.', 'Demo', '01', 'other', 'admin', '2025-03-08 23:31:55');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `registration_id` (`registration_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `event_images`
--
ALTER TABLE `event_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `event_statistics`
--
ALTER TABLE `event_statistics`
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `registrations`
--
ALTER TABLE `registrations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `event_id` (`event_id`);

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
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `event_images`
--
ALTER TABLE `event_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `registrations`
--
ALTER TABLE `registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`registration_id`) REFERENCES `registrations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `event_images`
--
ALTER TABLE `event_images`
  ADD CONSTRAINT `event_images_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `event_statistics`
--
ALTER TABLE `event_statistics`
  ADD CONSTRAINT `event_statistics_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `registrations`
--
ALTER TABLE `registrations`
  ADD CONSTRAINT `registrations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `registrations_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
