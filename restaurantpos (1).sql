-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 10, 2025 at 11:51 AM
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
-- Database: `restaurantpos`
--

-- --------------------------------------------------------

--
-- Table structure for table `cashiers`
--

CREATE TABLE `cashiers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `date_hired` date NOT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cashiers`
--

INSERT INTO `cashiers` (`id`, `name`, `username`, `password`, `contact`, `address`, `date_hired`, `status`) VALUES
(1, 'Administrator', 'admin', '$2y$10$TxTgkAMp7MUtIylkvNIrueoq5MpumUY0jAz.CHQkYRfLrVgI3jL1S', 'N/A', 'N/A', '2025-05-07', 'Active'),
(3, 'Rob Cuering', 'rob', '$2y$10$BZsYv3MOydzutxeEZ86N7eg6Q.WQCBbHeniypXUBPo7YZkAnXPhgi', '09123456789', 'dasdsa', '2025-05-07', 'Active'),
(4, 'kyle', 'kyky', '$2y$10$d2MoRO9GSVfsWgAWv5XN.umUUB1CtIsgagbrV7TAJFiHCkdyn9XFu', '09989898989', 'tinago', '2025-05-07', 'Active'),
(5, 'Jefferson Canamo', 'jefferson', '$2y$10$0U2Xv6wzUJnHFMgXdsnrpeSZ3roydgfNa0kZeBEMsujr09BiuKMD2', '09123456789', 'Ozamiz City', '2025-05-09', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_items`
--

CREATE TABLE `inventory_items` (
  `id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `current_stock` decimal(10,2) DEFAULT 0.00,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory_items`
--

INSERT INTO `inventory_items` (`id`, `item_name`, `description`, `current_stock`, `created_at`) VALUES
(14, 'Chicken', 'chicken', 390.00, '2025-05-08 13:45:30'),
(15, 'Chicken feet', 'chicken feet', 230.00, '2025-05-08 13:54:21'),
(16, 'Sili', 'Sili', 30.00, '2025-05-09 11:43:15'),
(17, 'Gloves', 'gloves', 340.00, '2025-05-10 17:29:26');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_purchases`
--

CREATE TABLE `inventory_purchases` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity_purchased` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `date_purchased` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory_purchases`
--

INSERT INTO `inventory_purchases` (`id`, `item_id`, `quantity_purchased`, `total_price`, `date_purchased`) VALUES
(15, 14, 100, 2500.00, '2025-05-08'),
(19, 15, 100, 10000.00, '2025-05-08'),
(20, 15, 200, 10000.00, '2025-05-08'),
(21, 15, 20, 2000.00, '2025-05-08'),
(22, 16, 50, 500.00, '2025-05-09'),
(23, 17, 100, 100.00, '2025-05-10'),
(24, 17, 250, 250.00, '2025-05-10');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_usage`
--

CREATE TABLE `inventory_usage` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity_used` int(11) NOT NULL,
  `date_used` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory_usage`
--

INSERT INTO `inventory_usage` (`id`, `item_id`, `quantity_used`, `date_used`) VALUES
(2, 14, 10, '2025-05-08'),
(3, 15, 90, '2025-05-08'),
(4, 16, 20, '2025-05-09'),
(5, 17, 10, '2025-05-10');

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL,
  `menu_name` varchar(255) NOT NULL,
  `approximate_cost` decimal(10,2) NOT NULL,
  `number_of_servings` int(11) NOT NULL,
  `price_per_serve` decimal(10,2) NOT NULL,
  `expected_sales` decimal(10,2) NOT NULL,
  `servings_sold` int(11) DEFAULT 0,
  `date_added` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `menu_name`, `approximate_cost`, `number_of_servings`, `price_per_serve`, `expected_sales`, `servings_sold`, `date_added`) VALUES
(13, 'Chicken Adobo', 500.00, 10, 70.00, 700.00, 6, '2025-05-08'),
(14, 'Pork Sinigang', 600.00, 8, 85.00, 680.00, 0, '2025-05-08'),
(15, 'Beef Caldereta', 750.00, 10, 95.00, 950.00, -3, '2025-05-08'),
(16, 'Spaghetti', 400.00, 12, 50.00, 600.00, 0, '2025-05-08'),
(17, 'Fried Bangus', 350.00, 6, 65.00, 390.00, 0, '2025-05-08'),
(18, 'Chicken Curry', 550.00, 9, 75.00, 675.00, 0, '2025-05-08'),
(19, 'Lumpiang Shanghai', 300.00, 15, 30.00, 450.00, 0, '2025-05-08'),
(20, 'Pancit Canton', 450.00, 12, 55.00, 660.00, 0, '2025-05-08'),
(21, 'Ginataang Gulay', 280.00, 8, 40.00, 320.00, 0, '2025-05-08'),
(22, 'Fish Fillet w/ Sauce', 500.00, 10, 60.00, 600.00, 0, '2025-05-08'),
(23, 'Monggos', 500.00, 20, 30.00, 600.00, 0, '2025-05-08'),
(26, 'Chicken Feet Adobo', 5000.00, 83, 150.00, 12450.00, 83, '2025-05-08'),
(27, 'Bulad', 500.00, 45, 25.00, 1125.00, 0, '2025-05-09'),
(28, 'Kylah Ostia', 100.00, 50, 20.00, 1000.00, 0, '2025-05-09'),
(29, 'eskatbitsi', 1000.00, 25, 50.00, 1250.00, 0, '2025-05-09'),
(30, 'Saren J', 5000.00, 300, 30.00, 9000.00, 0, '2025-05-10');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
  `transaction_date` datetime NOT NULL,
  `cashier_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` enum('Cash','Card','Other') DEFAULT 'Cash'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales_items`
--

CREATE TABLE `sales_items` (
  `id` int(11) NOT NULL,
  `sale_id` int(11) NOT NULL,
  `menu_item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `transaction_date` datetime DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `change_amount` decimal(10,2) NOT NULL,
  `status` varchar(20) DEFAULT 'completed',
  `void_processed` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `transaction_date`, `total_amount`, `amount_paid`, `change_amount`, `status`, `void_processed`) VALUES
(16, '2025-05-08 13:51:08', 350.00, 350.00, 0.00, 'completed', 0),
(17, '2025-05-08 13:58:38', 7500.00, 8000.00, 500.00, 'completed', 0),
(18, '2025-05-08 14:28:57', 70.00, 75.00, 5.00, 'completed', 0),
(19, '2025-05-08 14:37:47', 4950.00, 5000.00, 50.00, 'completed', 1),
(20, '2025-05-08 14:41:26', 95.00, 100.00, 5.00, 'completed', 1),
(21, '2025-05-09 11:52:25', 400.00, 500.00, 100.00, 'voided', 1),
(22, '2025-05-09 12:00:53', 190.00, 200.00, 10.00, 'voided', 1),
(23, '2025-05-09 12:01:33', 190.00, 200.00, 10.00, 'voided', 1),
(24, '2025-05-10 17:44:48', 1175.00, 2000.00, 825.00, 'voided', 1);

-- --------------------------------------------------------

--
-- Table structure for table `transaction_items`
--

CREATE TABLE `transaction_items` (
  `id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `menu_item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_per_item` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaction_items`
--

INSERT INTO `transaction_items` (`id`, `transaction_id`, `menu_item_id`, `quantity`, `price_per_item`, `subtotal`) VALUES
(8, 16, 13, 5, 70.00, 350.00),
(9, 17, 26, 50, 150.00, 7500.00),
(10, 18, 13, 1, 70.00, 70.00),
(11, 19, 26, 33, 150.00, 4950.00),
(12, 20, 15, 1, 95.00, 95.00),
(13, 21, 29, 8, 50.00, 400.00),
(14, 22, 15, 2, 95.00, 190.00),
(15, 23, 15, 2, 95.00, 190.00),
(16, 24, 15, 10, 95.00, 950.00),
(17, 24, 27, 9, 25.00, 225.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` enum('admin','cashier') NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `user_type`, `last_login`, `created_at`) VALUES
(1, 'admin', '$2y$10$FzJbhR0uVSHZ0CX70XzVkOo96/LbBbcaUseaKHXQ/lESBIjcxyIEC', 'admin', '2025-05-10 17:45:29', '2025-05-07 06:11:05'),
(2, 'cashier', '$2y$10$rDa3LFtMMDqnzidKDu/8nOh9A/QYqJzi3vgsyURzlsqNW8VFTjuzK', 'cashier', '2025-05-10 17:46:00', '2025-05-07 06:11:05');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cashiers`
--
ALTER TABLE `cashiers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `inventory_items`
--
ALTER TABLE `inventory_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory_purchases`
--
ALTER TABLE `inventory_purchases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `inventory_usage`
--
ALTER TABLE `inventory_usage`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cashier_id` (`cashier_id`);

--
-- Indexes for table `sales_items`
--
ALTER TABLE `sales_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_id` (`sale_id`),
  ADD KEY `menu_item_id` (`menu_item_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaction_items`
--
ALTER TABLE `transaction_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menu_item_id` (`menu_item_id`),
  ADD KEY `idx_transaction_id` (`transaction_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cashiers`
--
ALTER TABLE `cashiers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `inventory_items`
--
ALTER TABLE `inventory_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `inventory_purchases`
--
ALTER TABLE `inventory_purchases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `inventory_usage`
--
ALTER TABLE `inventory_usage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales_items`
--
ALTER TABLE `sales_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `transaction_items`
--
ALTER TABLE `transaction_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `inventory_purchases`
--
ALTER TABLE `inventory_purchases`
  ADD CONSTRAINT `inventory_purchases_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `inventory_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inventory_usage`
--
ALTER TABLE `inventory_usage`
  ADD CONSTRAINT `inventory_usage_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `inventory_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`cashier_id`) REFERENCES `cashiers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `sales_items`
--
ALTER TABLE `sales_items`
  ADD CONSTRAINT `sales_items_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sales_items_ibfk_2` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transaction_items`
--
ALTER TABLE `transaction_items`
  ADD CONSTRAINT `transaction_items_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaction_items_ibfk_2` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
