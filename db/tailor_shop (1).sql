-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 14, 2025 at 06:29 AM
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
-- Database: `tailor_shop`
--

-- --------------------------------------------------------

--
-- Table structure for table `cloth_subtypes`
--

CREATE TABLE `cloth_subtypes` (
  `id` int(11) NOT NULL,
  `cloth_type_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cloth_subtypes`
--

INSERT INTO `cloth_subtypes` (`id`, `cloth_type_id`, `title`, `image`, `note`) VALUES
(8, 8, 'Stretchable Suits', '1762864125_Stretchable Suits.webp', ''),
(9, 8, 'Single-Breasted Suits', '1762864148_Single-Breasted Suits.webp', ''),
(10, 8, 'dfsdfd', '1763095608_Double-Breasted Suits.webp', '');

-- --------------------------------------------------------

--
-- Table structure for table `cloth_types`
--

CREATE TABLE `cloth_types` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `gender` enum('Male','Female','Unisex') DEFAULT 'Unisex',
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cloth_types`
--

INSERT INTO `cloth_types` (`id`, `title`, `gender`, `note`) VALUES
(6, 'paint', 'Male', 'paint for male'),
(8, 'suit', 'Male', 'only for male');

-- --------------------------------------------------------

--
-- Table structure for table `cloth_type_measurements`
--

CREATE TABLE `cloth_type_measurements` (
  `id` int(11) NOT NULL,
  `cloth_type_id` int(11) NOT NULL,
  `measurement_title` varchar(100) NOT NULL,
  `unit` varchar(20) DEFAULT 'inch',
  `sequence_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `mobile` varchar(15) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `mobile`, `address`, `created_at`) VALUES
(18, 'aman', '8521478956', 'gulab nagar bareilly', '2025-11-10 12:06:40'),
(19, 'Rohit kumar', '1234567891', 'bareilly', '2025-11-10 13:23:52'),
(20, 'rajit', '4561231235', 'bareilly', '2025-11-11 11:24:26');

-- --------------------------------------------------------

--
-- Table structure for table `customer_payments`
--

CREATE TABLE `customer_payments` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `pay_mode` enum('Cash','UPI','Card','Bank') DEFAULT 'Cash',
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `karigars`
--

CREATE TABLE `karigars` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `mobile` varchar(15) DEFAULT NULL,
  `specialization` varchar(50) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `karigars`
--

INSERT INTO `karigars` (`id`, `name`, `mobile`, `specialization`, `remarks`, `created_at`) VALUES
(2, 'Vijay', '9898989898', 'Pant Maker', NULL, '2025-11-04 16:57:58'),
(7, 'ramesh karigar', '123456789u', '', NULL, '2025-11-07 12:58:29'),
(9, 'kareem', '4561234235', 'paint', NULL, '2025-11-11 11:25:03');

-- --------------------------------------------------------

--
-- Table structure for table `karigar_payments`
--

CREATE TABLE `karigar_payments` (
  `id` int(11) NOT NULL,
  `karigar_id` int(11) NOT NULL,
  `payment_date` date NOT NULL,
  `total_work` decimal(10,2) DEFAULT 0.00,
  `paid_amount` decimal(10,2) NOT NULL,
  `balance` decimal(10,2) GENERATED ALWAYS AS (`total_work` - `paid_amount`) STORED,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `measurements`
--

CREATE TABLE `measurements` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `garment_type_id` int(11) NOT NULL,
  `subtype_id` int(11) NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data`)),
  `date_taken` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `measurements`
--

INSERT INTO `measurements` (`id`, `customer_id`, `garment_type_id`, `subtype_id`, `data`, `date_taken`) VALUES
(11, 19, 8, 8, '{\"length\":\"4\",\"width\":\"5\",\"sadasd\":\"5\"}', '2025-11-12');

-- --------------------------------------------------------

--
-- Table structure for table `measurement_master`
--

CREATE TABLE `measurement_master` (
  `id` int(11) NOT NULL,
  `cloth_subtype_id` int(11) NOT NULL,
  `measurement_title` varchar(100) NOT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `sequence_order` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `measurement_master`
--

INSERT INTO `measurement_master` (`id`, `cloth_subtype_id`, `measurement_title`, `unit`, `sequence_order`) VALUES
(10, 9, 'length', 'inch', 1),
(11, 9, 'width', 'inch', 2),
(12, 9, 'jjj', 'inch', 3),
(13, 9, 'sadsad', 'inch', 4),
(14, 8, 'length', 'inch', 1),
(15, 8, 'width', 'inch', 2),
(16, 10, 'length', 'inch', 1),
(17, 10, 'width', 'inch', 2),
(18, 10, 'nack', 'inch', 3);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_no` varchar(20) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `karigar_id` int(11) DEFAULT NULL,
  `order_date` date DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `trial_date` date DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT 0.00,
  `advance_paid` decimal(10,2) DEFAULT 0.00,
  `status` enum('Pending','Completed','Delivered') DEFAULT 'Pending',
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_no`, `customer_id`, `karigar_id`, `order_date`, `delivery_date`, `trial_date`, `total_amount`, `advance_paid`, `status`, `remarks`) VALUES
(28, 'ORD1762956698', 18, 2, '2025-11-12', '2025-11-12', '2025-11-13', 1400.00, 0.00, 'Pending', NULL),
(29, 'ORD1762956705', 18, 2, '2025-11-12', '2025-11-12', '2025-11-13', 1400.00, 0.00, 'Pending', NULL),
(30, 'ORD1762956743', 18, 2, '2025-11-12', '2025-11-12', '2025-11-13', 1400.00, 0.00, 'Pending', NULL),
(31, 'ORD1763096120', 18, 7, '2025-11-14', '2025-11-16', '2025-11-15', 100.00, 0.00, 'Pending', NULL),
(32, 'ORD1763096729', 18, 9, '2025-11-14', '2025-11-20', '2025-11-25', 1400.00, 0.00, 'Pending', NULL),
(33, 'ORD1763096735', 18, 9, '2025-11-14', '2025-11-20', '2025-11-25', 1400.00, 0.00, 'Pending', NULL),
(34, 'ORD1763096736', 18, 9, '2025-11-14', '2025-11-20', '2025-11-25', 1400.00, 0.00, 'Pending', NULL),
(35, 'ORD1763096737', 19, 7, '2025-11-14', '2025-11-21', '2025-11-29', 3600.00, 0.00, 'Pending', NULL),
(36, 'ORD1763096738', 18, 2, '2025-11-14', '2025-11-16', '2025-11-28', 4000.00, 500.00, 'Pending', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `garment_type` varchar(50) DEFAULT NULL,
  `sub_item_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `rate` decimal(10,2) DEFAULT NULL,
  `karigar_charge` decimal(10,2) DEFAULT 0.00,
  `delivery_status` enum('Pending','Completed') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `garment_type`, `sub_item_id`, `quantity`, `rate`, `karigar_charge`, `delivery_status`) VALUES
(1, 30, '8', 0, 1, 600.00, 0.00, 'Pending'),
(2, 30, '8', 0, 1, 800.00, 0.00, 'Pending'),
(3, 31, 'shirt', 0, 1, 100.00, 0.00, 'Pending'),
(4, 34, '8', 8, 2, 700.00, 0.00, 'Pending'),
(5, 35, '8', 9, 1, 800.00, 0.00, 'Pending'),
(6, 35, '8', 8, 2, 900.00, 0.00, 'Pending'),
(7, 35, '8', 10, 1, 1000.00, 0.00, 'Pending'),
(8, 36, '8', 9, 2, 2000.00, 0.00, 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `method` enum('Cash','Card','UPI','Bank Transfer') DEFAULT 'Cash',
  `status` enum('Pending','Completed','Cancelled') DEFAULT 'Pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(150) DEFAULT NULL,
  `mobile` varchar(15) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `mobile`, `created_at`) VALUES
(1, 'admin', '12345', 'aman ', '9856232152', '2025-11-07 13:19:21');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cloth_subtypes`
--
ALTER TABLE `cloth_subtypes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cloth_type_id` (`cloth_type_id`);

--
-- Indexes for table `cloth_types`
--
ALTER TABLE `cloth_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cloth_type_measurements`
--
ALTER TABLE `cloth_type_measurements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cloth_type_id` (`cloth_type_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer_payments`
--
ALTER TABLE `customer_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `karigars`
--
ALTER TABLE `karigars`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `karigar_payments`
--
ALTER TABLE `karigar_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `karigar_id` (`karigar_id`);

--
-- Indexes for table `measurements`
--
ALTER TABLE `measurements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `garment_type_id` (`garment_type_id`);

--
-- Indexes for table `measurement_master`
--
ALTER TABLE `measurement_master`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_measurement_subtype` (`cloth_subtype_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_no` (`order_no`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `karigar_id` (`karigar_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `cloth_subtypes`
--
ALTER TABLE `cloth_subtypes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `cloth_types`
--
ALTER TABLE `cloth_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `cloth_type_measurements`
--
ALTER TABLE `cloth_type_measurements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `customer_payments`
--
ALTER TABLE `customer_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `karigars`
--
ALTER TABLE `karigars`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `karigar_payments`
--
ALTER TABLE `karigar_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `measurements`
--
ALTER TABLE `measurements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `measurement_master`
--
ALTER TABLE `measurement_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cloth_subtypes`
--
ALTER TABLE `cloth_subtypes`
  ADD CONSTRAINT `cloth_subtypes_ibfk_1` FOREIGN KEY (`cloth_type_id`) REFERENCES `cloth_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cloth_type_measurements`
--
ALTER TABLE `cloth_type_measurements`
  ADD CONSTRAINT `cloth_type_measurements_ibfk_1` FOREIGN KEY (`cloth_type_id`) REFERENCES `cloth_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `customer_payments`
--
ALTER TABLE `customer_payments`
  ADD CONSTRAINT `customer_payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `karigar_payments`
--
ALTER TABLE `karigar_payments`
  ADD CONSTRAINT `karigar_payments_ibfk_1` FOREIGN KEY (`karigar_id`) REFERENCES `karigars` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `measurements`
--
ALTER TABLE `measurements`
  ADD CONSTRAINT `measurements_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `measurements_ibfk_2` FOREIGN KEY (`garment_type_id`) REFERENCES `cloth_types` (`id`);

--
-- Constraints for table `measurement_master`
--
ALTER TABLE `measurement_master`
  ADD CONSTRAINT `fk_measurement_subtype` FOREIGN KEY (`cloth_subtype_id`) REFERENCES `cloth_subtypes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`karigar_id`) REFERENCES `karigars` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
