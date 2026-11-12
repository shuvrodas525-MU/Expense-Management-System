-- phpMyAdmin SQL Dump (fixed)
-- Database: `expense`
--
-- Fixes applied vs the original dump:
--   1. Added the missing `expense_info` table (every expense page depends on it).
--   2. Replaced the plaintext seed passwords with real bcrypt hashes so the
--      seeded accounts can actually log in via password_verify().
--   3. Added `is_admin` to `reg_users` — only admins manage other users.
--   4. Added `user_id` to `expense_info` (with a foreign key + cascade
--      delete) so every expense belongs to exactly one user/account —
--      each person only ever sees and reports on their own expenses.

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

-- Fresh-install reset: importing this file must not preserve old/demo expenses.
DROP TABLE IF EXISTS `expense_info`;
DROP TABLE IF EXISTS `reg_users`;
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------

--
-- Table structure for table `reg_users`
--

CREATE TABLE `reg_users` (
  `reg_id` int(11) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `user_pass` varchar(255) NOT NULL,
  `user_pic` varchar(255) DEFAULT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `recovery_code` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reg_users`
-- Passwords below are bcrypt hashes of: manosh -> 1234, Shuvro -> 989898, Tanvir -> 565676
-- `manosh` is seeded as the admin account (can manage users), the rest are regular accounts.
--

INSERT INTO `reg_users` (`reg_id`, `user_name`, `user_pass`, `user_pic`, `is_admin`, `recovery_code`) VALUES
(1, 'manosh', '$2b$12$MktDMCNsd.b7M5aH9hFbdeYxjdI0pOm5FdgyXVaSlUDBZxvqe05B2', NULL, 1, 'MANOSH1234'),
(2, 'Shuvro', '$2b$12$G0s50AMXn8TOwp871KrPp.TYFj2emgGKdem3Xky.UATyz1w6DGXhy', NULL, 0, 'SHUVRO1234'),
(3, 'Tanvir', '$2b$12$hZlIGZ.zlDl69Vx4kPn1NeT7nl/nj/lK9oA/CV0YqyL5fwNMc77oC', NULL, 0, 'TANVIR1234');

-- --------------------------------------------------------

--
-- Table structure for table `expense_info`
-- (this table was missing from the original dump)
-- `user_id` ties every expense to the account that owns it.
--

CREATE TABLE `expense_info` (
  `item_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `item_price` decimal(10,2) NOT NULL,
  `item_date` date NOT NULL,
  `item_details` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Sample data so the dashboard/date/report pages have something to show
-- (split across two different users to demonstrate the personal separation)
--

-- No sample expenses are inserted. The dashboard starts at zero and reflects only expenses the logged-in user adds.

--
-- Indexes for dumped tables
--

ALTER TABLE `reg_users`
  ADD PRIMARY KEY (`reg_id`),
  ADD UNIQUE KEY `user_name` (`user_name`),
  ADD KEY `recovery_code` (`recovery_code`);

ALTER TABLE `expense_info`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

ALTER TABLE `reg_users`
  MODIFY `reg_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

ALTER TABLE `expense_info`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Foreign key: deleting a user cleans up their expenses too
--

ALTER TABLE `expense_info`
  ADD CONSTRAINT `fk_expense_user` FOREIGN KEY (`user_id`) REFERENCES `reg_users` (`reg_id`) ON DELETE CASCADE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
