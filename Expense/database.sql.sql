-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 23, 2026 at 12:03 PM
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
-- Database: `expense`
--

-- --------------------------------------------------------

--
-- Table structure for table `expense_info`
--

CREATE TABLE `expense_info` (
  `item_id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `item_price` int(11) NOT NULL,
  `item_date` date NOT NULL,
  `item_details` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expense_info`
--

INSERT INTO `expense_info` (`item_id`, `item_name`, `item_price`, `item_date`, `item_details`) VALUES
(6, 'Lunch', 100, '2026-04-15', 'Eating Biriyani'),
(12, 'cake', 156, '2026-04-15', 'Snacks'),
(13, 'Drink', 50, '2026-04-14', 'with friends'),
(16, 'Chips', 20, '2026-04-14', 'Ding Dong'),
(20, 'Cake', 50, '2026-04-20', 'With my friend '),
(21, 'Drink', 100, '2026-04-20', 'With my friend '),
(22, 'Biriyani', 250, '2026-04-19', 'With my Brother'),
(23, 'Cake', 123, '2026-04-20', 'With Friend');

-- --------------------------------------------------------

--
-- Table structure for table `reg_users`
--

CREATE TABLE `reg_users` (
  `reg_id` int(11) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `user_pass` varchar(255) NOT NULL,
  `user_pic` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reg_users`
--

INSERT INTO `reg_users` (`reg_id`, `user_name`, `user_pass`, `user_pic`) VALUES
(2, 'Manosh', '$2y$10$05QQ9ulZ4GeGevMd/AUbwu6osZXyqaeLmcOYVGFKsp97A5kmQG7ri', '1776624467.jpeg'),
(6, 'Shuvro', '$2y$10$wi4exncnuC7bb4RnOWLVfOvwOyY9XzDzXaS5K9LVH/PWCYqgNpA3y', '1776626921.jpeg'),
(8, 'Sojib', '$2y$10$YcpM9VyF.fiQGn2AvkwS5O8YAijgjHIv4BX2SyodJzaMcRssYdaQy', '1776659773.jpg'),
(9, 'Sayem', '$2y$10$/GUf7d5gIm7A1.jw.iuGu.O3G3BDSNz/4oQAtOnSO9gohco5bZQwi', '1776661359.webp');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `expense_info`
--
ALTER TABLE `expense_info`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `reg_users`
--
ALTER TABLE `reg_users`
  ADD PRIMARY KEY (`reg_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `expense_info`
--
ALTER TABLE `expense_info`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `reg_users`
--
ALTER TABLE `reg_users`
  MODIFY `reg_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
