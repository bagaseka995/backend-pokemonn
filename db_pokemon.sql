-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 13, 2025 at 04:56 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_pokemon`
--

-- --------------------------------------------------------

--
-- Table structure for table `master_pokemon`
--

CREATE TABLE `master_pokemon` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `element_type` enum('Fire','Water','Grass','Electric') NOT NULL,
  `base_hp` int(11) NOT NULL,
  `base_attack` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `master_pokemon`
--

INSERT INTO `master_pokemon` (`id`, `name`, `element_type`, `base_hp`, `base_attack`, `image_path`) VALUES
(1, 'Charmander', 'Fire', 100, 30, 'charmander.png'),
(2, 'Bulbasaur', 'Grass', 110, 25, 'bulbasaur.png'),
(3, 'Squirtle', 'Water', 105, 28, 'squirtle.png'),
(4, 'Pikachu', 'Electric', 90, 35, 'pikachu.png');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `team` enum('Blanche','Candela','Spark') DEFAULT NULL,
  `level` int(11) DEFAULT 1,
  `xp` int(11) DEFAULT 0,
  `wins` int(11) DEFAULT 0,
  `losses` int(11) DEFAULT 0,
  `avatar` varchar(255) DEFAULT 'default.png',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `team`, `level`, `xp`, `wins`, `losses`, `avatar`, `created_at`) VALUES
(1, 'tes', 'tes123@gmail.com', '$2y$10$LFRo.K0Qf5CbCmSQRlB6B..a.L9VrIJFrCq8TOkxV//1itOl5bjh6', 'Blanche', 5, 4000, 7, 2, 'assets/blanche.jpg', '2025-12-13 01:21:27'),
(2, 'tes1234', 'tes1234@gmail.com', '$2y$10$l6JvAP39iP5GHuhN1k2fm.obQhtcwdPklfJ0tjvRCm9z94wRRcj76', 'Blanche', 1, 0, 0, 0, 'assets/blanche.jpg', '2025-12-13 01:22:49');

-- --------------------------------------------------------

--
-- Table structure for table `user_pokemon`
--

CREATE TABLE `user_pokemon` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `pokemon_id` int(11) NOT NULL,
  `nickname` varchar(50) DEFAULT NULL,
  `current_level` int(11) DEFAULT 1,
  `is_active` tinyint(1) DEFAULT 0,
  `obtained_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_pokemon`
--

INSERT INTO `user_pokemon` (`id`, `user_id`, `pokemon_id`, `nickname`, `current_level`, `is_active`, `obtained_at`) VALUES
(1, 2, 3, 'My Partner', 1, 1, '2025-12-13 01:27:41'),
(4, 1, 3, 'Squirtle', 1, 0, '2025-12-13 01:57:41'),
(5, 1, 3, 'Squirtle', 6, 0, '2025-12-13 02:00:09'),
(6, 1, 4, 'jamal', 3, 1, '2025-12-13 02:00:12'),
(7, 1, 2, 'Bulbasaur', 1, 0, '2025-12-13 02:51:06'),
(8, 1, 2, 'Bulbasaur', 1, 0, '2025-12-13 03:32:33'),
(9, 1, 1, 'Charmander', 1, 0, '2025-12-13 03:32:35');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `master_pokemon`
--
ALTER TABLE `master_pokemon`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `user_pokemon`
--
ALTER TABLE `user_pokemon`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `pokemon_id` (`pokemon_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `master_pokemon`
--
ALTER TABLE `master_pokemon`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_pokemon`
--
ALTER TABLE `user_pokemon`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `user_pokemon`
--
ALTER TABLE `user_pokemon`
  ADD CONSTRAINT `user_pokemon_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_pokemon_ibfk_2` FOREIGN KEY (`pokemon_id`) REFERENCES `master_pokemon` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
