-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 06, 2025 at 04:25 PM
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
-- Database: `snackshopdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(3, 'admin', 'admin123'),
(4, 'admin2', 'admin234');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `product_name` text NOT NULL,
  `quantity` int(30) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `order_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `customer_name`, `product_name`, `quantity`, `total`, `order_date`) VALUES
(1, 'Bukhari', 'Mr Potato - Honey Cheese', 2, 9.00, '2025-04-10'),
(2, 'Aisyah', 'Super Ring', 3, 6.00, '2025-04-13'),
(3, 'Haikal', 'Mamee Monster - Spicy', 2, 20.00, '2025-04-16'),
(4, 'Zulhaq', 'Miaow Miaow - Chicken', 6, 13.20, '2025-04-22'),
(5, 'Dahlia', 'Snek Ku - Shoyue Mi (Blackpaper)', 4, 6.00, '2025-04-23'),
(6, 'Sara', 'Bika', 1, 1.80, '2025-04-24'),
(7, 'Firdaus', 'Bika', 3, 5.40, '2025-04-27'),
(8, 'Hana', 'Mr Potato - Honey Cheese', 2, 9.00, '2025-04-27'),
(9, 'Ashraf', 'Super Ring', 1, 2.00, '2025-11-29'),
(10, 'Siti Humairah', 'Corntoz - Spicy', 15, 30.00, '2025-11-29'),
(11, 'test', 'Mamee Monster - Spicy', 1, 10.00, '2025-11-29'),
(12, 'Nurul', 'Twisties - Cheese', 25, 75.00, '2025-11-10'),
(13, 'Rina', 'Diction Potato Stick (Chili Sauce)', 2, 4.80, '2025-11-30');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `current_stock` int(11) DEFAULT NULL,
  `image_url` varchar(50) NOT NULL,
  `original_stock` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `price`, `current_stock`, `image_url`, `original_stock`) VALUES
(1, 'Mr Potato - Honey Cheese', 4.50, 26, 'mrpotato.jpg', 30),
(2, 'Super Ring', 2.00, 27, 'superring.jpg', 30),
(3, 'Mamee Monster - Spicy', 10.00, 28, 'mameemonster.jpg', 30),
(4, 'Miaow Miaow - Chicken', 2.20, 24, 'miaowmiaow.jpg', 30),
(5, 'Snek Ku - Shoyue Mi (Blackpaper)', 2.00, 26, 'snekku.jpg', 30),
(6, 'Bika', 1.80, 26, 'bika.jpg', 30),
(7, 'Corntoz - Spicy', 2.00, 15, 'corntoz.jpg', 30),
(8, 'Twisties - Cheese', 3.00, 0, 'twisties.jpg', 25),
(9, 'Diction Potato Stick (Chili Sauce)', 2.40, 28, 'diction.jpg', 30);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
