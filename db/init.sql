-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 26, 2025 at 12:01 PM
-- Server version: 10.11.13-MariaDB-cll-lve
-- PHP Version: 8.1.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ihrbdtop_shahedsir_ecom`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `image` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `image`, `link`, `description`) VALUES
(1, 'Cricket Gear', 'category/cricket_gear.jpg', 'jerseys.php', '“Everything you need to play like a pro! From bats, balls, and protective gear to gloves, pads, and helmets, find high-quality cricket equipment for practice, matches, and tournaments.”'),
(2, 'Football & Fan Merchandise', 'category/football_gear.jpg', 'football.php', '“Score big with top-quality footballs, boots, jerseys, and training equipment. Perfect for amateurs and professional players alike.”'),
(3, 'Gym & Fitness', 'category/gym_equipment.png', 'cricket-bat.php', '“Stay fit and strong with our range of dumbbells, resistance bands, yoga mats, and home gym essentials.”'),
(4, 'Outdoor & Adventure', 'category/adventure-equipment-for-outdoor.png', 'sports.php', '“Gear up for every adventure! Camping, hiking, cycling, fishing, climbing, water sports, and outdoor essentials all in one place.”');

-- --------------------------------------------------------

--
-- Table structure for table `clubs`
--

CREATE TABLE `clubs` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `logo` varchar(255) NOT NULL,
  `alt_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `clubs`
--

INSERT INTO `clubs` (`id`, `name`, `logo`, `alt_name`) VALUES
(1, 'Tottenham', 'clubs_logo/1.webp', 'Tottenham'),
(2, 'Al Nassr', 'clubs_logo/2.webp', 'Al Nassr'),
(3, 'Chelsea', 'clubs_logo/3.webp', 'Chelsea'),
(4, 'Liverpool', 'clubs_logo/4.webp', 'Liverpool'),
(5, 'Man United', 'clubs_logo/5.webp', 'Manchester United'),
(6, 'Man City', 'clubs_logo/6.webp', 'Manchester City'),
(7, 'Barcelona', 'clubs_logo/7.webp', 'Barcelona'),
(8, 'Real Madrid', 'clubs_logo/8.webp', 'Real Madrid'),
(9, 'PSG', 'clubs_logo/9.webp', 'PSG'),
(10, 'Newcastle', 'clubs_logo/10.webp', 'Newcastle'),
(11, 'Juventus', 'clubs_logo/11.webp', 'Juventus');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` mediumtext DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `description`, `price`, `status`, `created_at`) VALUES
(1, 1, 'Cricket Bat SS', '<h5>About SS Cricket Bat</h5>\nSelected Kashmir Willow Grade 4\nDesigned as per specifications of world Top Players\nThe latest shape with massive concave edges enable high impact with optimum performance\nEmbossed Retro Sticker with Superb Grip.\nPremium/Portable SS bat Cover\nBuy a cricket bat from the best sports shop in Bangladesh. Quality and service guaranteed.', 5040.00, 'active', '2025-08-16 09:21:43'),
(2, 1, 'Cricket Bat SF', '<h5>About SF Cricket Bat</h5>\r\nSelected Kashmir Willow Grade 4\r\nDesigned as per specifications of world Top Players\r\nThe latest shape with massive concave edges enable high impact with optimum performance\r\nEmbossed Retro Sticker with Superb Grip.\r\nPremium/Portable SF bat Cover\r\nBuy a cricket bat from the best sports shop in Bangladesh. Quality and service guaranteed.', 7040.00, 'active', '2025-08-16 09:21:43'),
(3, 1, 'ক্রিকেট প্লাস্টিক স্ট্যাম্প', '<h5>ক্রিকেট প্লাস্টিক স্ট্যাম্প</h5>\r\nপ্লাস্টিক স্ট্যাম্প সাধারণত টেকসই এবং হালকা হয়ে থাকে, যা বাচ্চাদের খেলার জন্য উপযুক্ত। কিছু স্ট্যাম্প সেটে তিনটি স্ট্যাম্প এবং দুটি বেইল থাকে। আপনি আপনার পছন্দ ও প্রয়োজন অনুযায়ী বিভিন্ন রঙের স্ট্যাম্পও খুঁজে নিতে পারেন। ', 400.00, 'active', '2025-08-16 09:21:43'),
(4, 1, 'প্র্যাকটিস কোণ', '<h5>প্র্যাকটিস কোণ</h5>\r\nপ্লাস্টিক স্ট্যাম্প সাধারণত টেকসই এবং হালকা হয়ে থাকে, যা বাচ্চাদের খেলার জন্য উপযুক্ত। কিছু স্ট্যাম্প সেটে তিনটি স্ট্যাম্প এবং দুটি বেইল থাকে। আপনি আপনার পছন্দ ও প্রয়োজন অনুযায়ী বিভিন্ন রঙের স্ট্যাম্পও খুঁজে নিতে পারেন। ', 450.00, 'active', '2025-08-16 09:21:43'),
(5, 2, 'ফুটবল পাম্পার', '<h5>ফুটবল পাম্পার</h5>\r\nফুটবল পাম্পার হলো ফুটবল, বাস্কেটবল, বেলুন বা সাইকেলের টায়ারের মতো জিনিসকে ফোলানোর জন্য ব্যবহৃত একটি যন্ত্র। এটি সাধারণত একটি হ্যান্ড পাম্প বা ফুট পাম্প হিসেবে পাওয়া যায়, যা একটি পাম্প এবং একটি বা একাধিক পিন (নিপল) দিয়ে আসে, যা বলের মধ্যে বাতাস ভরতে সাহায্য করে। ', 400.00, 'active', '2025-08-24 09:21:43'),
(6, 2, 'ফুটবল Select প্রিমিয়াম', '<h5>ফুটবল Select প্রিমিয়াম</h5>\r\nওজন ৪১০-৪৫০ গ্রাম\r\nপানিরোধী তাই ভেজা আবহাওয়ায় খেলা যাবে এবং সহজেই নষ্ট হবে না\r\nবলের বাইরের অংশটি নরম এবং মসৃণ চামড়া বা সিন্থেটিক উপাদান দিয়ে তৈরি', 1400.00, 'active', '2025-08-24 09:21:43');

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `image_url`, `is_primary`) VALUES
(1, 1, 'products/product3.jpg', 1),
(2, 1, 'products/product3_side.jpg', 0),
(3, 1, 'products/product3_back.jpg', 0),
(4, 2, 'products/product2.jpg', 1),
(5, 2, 'products/product2_side.jpg', 0),
(6, 2, 'products/product2_back.jpg', 0),
(7, 3, 'products/cfs.jpg', 1),
(8, 3, 'products/cfs_side.jpg', 0),
(9, 3, 'products/cfs_fullset.jpg', 0),
(10, 4, 'products/pc4.jpg', 1),
(11, 4, 'products/pc_yellow4.jpg', 0),
(12, 5, 'products/885.jpg', 1),
(13, 5, 'products/504.jpg', 0),
(14, 5, 'products/706.jpg', 0),
(15, 6, 'products/629.jpg', 1),
(16, 6, 'products/301.jpg', 0),
(17, 6, 'products/443.jpg', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `clubs`
--
ALTER TABLE `clubs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `clubs`
--
ALTER TABLE `clubs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
