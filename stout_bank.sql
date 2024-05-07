-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 07, 2024 at 03:53 AM
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
-- Database: `stout_bank`
--

-- --------------------------------------------------------

--
-- Table structure for table `checking_accounts`
--

CREATE TABLE `checking_accounts` (
  `checking_account_number` int(11) NOT NULL,
  `amount` decimal(10,2) DEFAULT 0.00,
  `u_id` int(11) NOT NULL,
  `checking_account_first_name` varchar(50) NOT NULL,
  `checking_account_last_name` varchar(50) NOT NULL,
  `checking_account_email` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `checking_accounts`
--

INSERT INTO `checking_accounts` (`checking_account_number`, `amount`, `u_id`, `checking_account_first_name`, `checking_account_last_name`, `checking_account_email`) VALUES
(28, 109.00, 20, 'Aden', 'Scott', 'a@s.com');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `message` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `type`, `message`) VALUES
(1, 'welcome', 'Welcome to the Stout Bank Cybersecurity Challenge!\r\n\r\nIn this quick interactive challenge, you\'ll learn cybersecurity practices through hands-on activities.\r\n\r\nGet started by creating your first bank account.'),
(2, 'achievement', 'Congratulations on Creating Your First Account! \r\n\r\nWe recommend setting up both a checking and a savings account. '),
(3, 'achievement', 'Well Done on Setting Up Multiple Accounts!\r\n\r\nNow it\'s time to explore how to manage transfers between accounts. Navigate to the transfer tab and transfer some money.'),
(4, 'achievement', 'Congratulations on completing your money Transfer!\r\n\r\nYou can view this transaction in the Accounts tab, listed under Recent Transactions. It\'s always good practice to keep an eye on your financial activities for any discrepancies.\r\n\r\nReady to boost your balance further? Visit our Services tab and dive into our challenge - deciphering a Caeser cipher.'),
(5, 'achievement', 'Congratulations on Successfully Decoding the Cipher!\r\n\r\nThe funds have been added to your account. Visit our Help Page under the Services tab for more information about cryptographic techniques.\r\n\r\nReady for your next challenge? Head over to the Transfer tab. A secret number is hidden somewhere on that page. Use your skills to find this number by inspecting the page elements. Once you discover it, enter this number into the transfer amount box. Doing so will unlock access to a hidden page. '),
(6, 'achievement', 'Congratulations on completing the final challenge. This task was designed to mimic a basic form of an injection attack, where unexpected input can manipulate a system in unintended ways. \r\n'),
(7, 'achievement', 'You\'ve just experienced a simulation of what could happen with a phishing attack. To see more information about this attack visit the help page. Remember, always verify the authenticity of a login page to protect your information from real threats.'),
(8, 'phishing', 'Dear customer,\r\n\r\nwe have observed unusual activity on your account that appears to be an unauthorized transaction. Immediate action is required to secure your account. \r\n\r\nPlease verify your account immediately by clicking the link:'),
(9, 'achievment', 'Congratulations! You\'ve successfully completed the game!\r\n\r\nFantastic work! Your journey doesn\'t have to end here.\r\n\r\nDive deeper by visiting our Help Page under the Services tab for comprehensive insights into each step and the cybersecurity principes behind them.\r\n\r\nThank you for participating in Stout Bank!');

-- --------------------------------------------------------

--
-- Table structure for table `saving_accounts`
--

CREATE TABLE `saving_accounts` (
  `saving_account_number` int(11) NOT NULL,
  `amount` decimal(10,2) DEFAULT 0.00,
  `u_id` int(11) NOT NULL,
  `saving_account_email` varchar(50) NOT NULL,
  `saving_account_first_name` varchar(50) NOT NULL,
  `saving_account_last_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `saving_accounts`
--

INSERT INTO `saving_accounts` (`saving_account_number`, `amount`, `u_id`, `saving_account_email`, `saving_account_first_name`, `saving_account_last_name`) VALUES
(41, 200.00, 20, 'a@s.com', 'Aden', 'Scott'),
(42, 1.00, 20, 'a@s.com', 'Aden', 'Scott');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `transaction_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `transaction_type` enum('Deposit','Withdrawal','Transfer') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `source_account` int(11) NOT NULL,
  `source_account_type` varchar(50) NOT NULL,
  `destination_account` int(11) NOT NULL,
  `destination_account_type` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`transaction_id`, `user_id`, `transaction_type`, `amount`, `transaction_date`, `source_account`, `source_account_type`, `destination_account`, `destination_account_type`) VALUES
(27, 20, 'Transfer', 200.00, '2024-05-03 23:31:59', 28, 'checking', 41, 'saving'),
(28, 20, 'Transfer', 1.00, '2024-05-04 12:39:31', 28, 'checking', 42, 'saving');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `routing_number` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `birthdate` date NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email_address` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`routing_number`, `first_name`, `last_name`, `birthdate`, `username`, `password`, `email_address`) VALUES
(20, 'Aden', 'Scott', '2024-03-07', 'aden.scott', 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3', ''),
(21, 'Test', 'Texst', '2024-03-08', 'test', 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3', ''),
(22, 'Stout', 'Bank', '2001-05-29', 'StoutBank', 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3', ''),
(23, 'John', 'Snow', '2024-04-13', 'john.snow', 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3', ''),
(24, 'User', 'Capstone', '2024-04-13', 'capstone', '03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4', ''),
(25, 'Marsh', 'Camp', '2000-10-19', 'mcamp', '3db538dbd36f292a71133b4328a84bac8cf77295a82f2fc425a5b05fba4c4554', ''),
(26, 'Aden', 'Scott', '2001-05-29', 'ado', '3db538dbd36f292a71133b4328a84bac8cf77295a82f2fc425a5b05fba4c4554', ''),
(27, 'Aden', 'Scott', '2001-05-29', 'scoot', '3db538dbd36f292a71133b4328a84bac8cf77295a82f2fc425a5b05fba4c4554', '');

-- --------------------------------------------------------

--
-- Table structure for table `user_notifications`
--

CREATE TABLE `user_notifications` (
  `routing_number` int(11) NOT NULL,
  `notification_id` int(11) NOT NULL,
  `date_earned` datetime DEFAULT current_timestamp(),
  `seen` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_notifications`
--

INSERT INTO `user_notifications` (`routing_number`, `notification_id`, `date_earned`, `seen`) VALUES
(20, 1, '2024-05-06 00:02:05', 1),
(20, 2, '2024-05-03 09:31:25', 1),
(20, 3, '2024-05-03 09:31:42', 1),
(20, 4, '2024-05-03 09:31:59', 1),
(20, 5, '2024-05-03 09:33:37', 1),
(20, 6, '2024-05-03 09:35:14', 1),
(20, 7, '2024-05-02 12:47:12', 1),
(20, 8, '2024-05-02 12:47:06', 1),
(20, 9, '2024-05-03 09:35:47', 1),
(21, 1, '2024-05-06 12:19:18', 1),
(21, 7, '2024-05-06 16:00:13', 1),
(21, 8, '2024-05-06 15:57:36', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `checking_accounts`
--
ALTER TABLE `checking_accounts`
  ADD PRIMARY KEY (`checking_account_number`),
  ADD KEY `fk_checking_accounts_users` (`u_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`);

--
-- Indexes for table `saving_accounts`
--
ALTER TABLE `saving_accounts`
  ADD PRIMARY KEY (`saving_account_number`),
  ADD KEY `fk_saving_accounts_users` (`u_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `account_number` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`routing_number`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `user_notifications`
--
ALTER TABLE `user_notifications`
  ADD PRIMARY KEY (`routing_number`,`notification_id`),
  ADD KEY `notification_id` (`notification_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `checking_accounts`
--
ALTER TABLE `checking_accounts`
  MODIFY `checking_account_number` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `saving_accounts`
--
ALTER TABLE `saving_accounts`
  MODIFY `saving_account_number` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `routing_number` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `checking_accounts`
--
ALTER TABLE `checking_accounts`
  ADD CONSTRAINT `checking_accounts_ibfk_1` FOREIGN KEY (`u_id`) REFERENCES `users` (`routing_number`),
  ADD CONSTRAINT `fk_checking_accounts_users` FOREIGN KEY (`u_id`) REFERENCES `users` (`routing_number`);

--
-- Constraints for table `saving_accounts`
--
ALTER TABLE `saving_accounts`
  ADD CONSTRAINT `fk_saving_accounts_users` FOREIGN KEY (`u_id`) REFERENCES `users` (`routing_number`),
  ADD CONSTRAINT `saving_accounts_ibfk_1` FOREIGN KEY (`u_id`) REFERENCES `users` (`routing_number`);

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`routing_number`);

--
-- Constraints for table `user_notifications`
--
ALTER TABLE `user_notifications`
  ADD CONSTRAINT `user_notifications_ibfk_1` FOREIGN KEY (`routing_number`) REFERENCES `users` (`routing_number`),
  ADD CONSTRAINT `user_notifications_ibfk_2` FOREIGN KEY (`notification_id`) REFERENCES `notifications` (`notification_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
