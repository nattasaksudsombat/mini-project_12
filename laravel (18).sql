-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Feb 10, 2026 at 09:27 AM
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
-- Database: `laravel`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL COMMENT 'ชื่อ-นามสกุล',
  `email` varchar(100) DEFAULT NULL,
  `role` enum('admin','manager','staff') DEFAULT 'staff',
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password`, `name`, `email`, `role`, `is_active`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ผู้ดูแลระบบ', NULL, 'admin', 1, NULL, '2025-11-02 03:21:09', '2025-11-02 03:21:09');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL COMMENT 'ชื่อหมวดหมู่'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `category_name`) VALUES
(1, 'เสื้อผ้า'),
(4, 'เครื่องประดับ'),
(8, 'เครื่องประดับ2'),
(15, 'ลูกอม'),
(16, 'เสื้อ'),
(17, 'ครีมม'),
(18, 'ห'),
(19, 'เดรสยาว'),
(20, 'กระโปรง'),
(21, 'กกกกห');

-- --------------------------------------------------------

--
-- Table structure for table `colors`
--

CREATE TABLE `colors` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `hex_code` varchar(7) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `colors`
--

INSERT INTO `colors` (`id`, `name`, `hex_code`) VALUES
(1, 'แดง', '#ff3838'),
(2, 'น้ำเงิน', '#0000FF'),
(3, 'ดำ', '#000000'),
(5, 'เขียว', '#00FF00'),
(7, 'เหลือง', '#fff700'),
(8, 'เทา', '#787878'),
(12, 'เขียวหกห', '#3cb4a0'),
(13, 'กอล์ฟ', '#000000'),
(14, 'green', '#06d028');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'ชื่อลูกค้า',
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `payment_method` enum('bank_transfer','cash_on_delivery','credit_card','e_wallet','cash') NOT NULL,
  `purchase_channel` enum('facebook','line','website','shopee','lazada','offline') NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `phone`, `email`, `payment_method`, `purchase_channel`, `notes`, `created_at`, `updated_at`) VALUES
(2, 'กอล์ฟ', '0963874971', 'sdfsdfsdfsdfsdf@ggfg.com', 'bank_transfer', 'facebook', '1010', '2026-01-06 06:47:29', '2026-01-06 06:48:24'),
(3, 'TGZ- Evening TV', '0963874972', 'goft12345678@gmail.com', 'bank_transfer', 'facebook', '10', '2026-01-06 07:05:52', '2026-01-06 07:05:52'),
(5, '50', '50', 'goft12345678@gmail.com', 'bank_transfer', 'facebook', NULL, '2026-01-14 21:13:41', '2026-01-14 21:13:41'),
(6, 'ฟหกฟหก', 'ฟหกฟหก', 'goft12345678@gmail.com', 'cash_on_delivery', 'facebook', NULL, '2026-01-15 06:58:23', '2026-01-15 06:58:23'),
(7, 'ฟหก', '0954', 'goft12345678@gmail.com', 'cash_on_delivery', 'line', NULL, '2026-01-18 23:07:31', '2026-01-18 23:07:31'),
(11, '123', 'ฟหกฟเ', 'a@aaaa.c', 'credit_card', 'line', NULL, '2026-01-18 23:27:11', '2026-01-18 23:27:11'),
(13, 'หก', 'หก', 'qq@sd.com', 'bank_transfer', 'facebook', NULL, '2026-01-19 05:23:51', '2026-01-19 05:23:51'),
(14, 'gg', '1234567890', 'goft12345678@gmail.com', 'bank_transfer', 'facebook', NULL, '2026-01-21 06:38:04', '2026-01-21 06:38:04'),
(15, 'ลูกค้าหน้าร้าน', '0000000000', NULL, 'cash', 'offline', NULL, '2026-01-21 07:00:52', '2026-01-21 07:00:52'),
(17, 'llll', '0963874521', '10f@ggfg.com', 'cash', 'facebook', NULL, '2026-01-25 07:49:59', '2026-01-25 07:49:59'),
(18, 'ๆฟ', '0999874971', 'iuikkiikik@gmail.com', 'bank_transfer', 'lazada', NULL, '2026-02-04 08:38:16', '2026-02-04 08:38:34');

-- --------------------------------------------------------

--
-- Table structure for table `customer_addresses`
--

CREATE TABLE `customer_addresses` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL COMMENT 'ชื่อที่อยู่ เช่น บ้าน / ที่ทำงาน',
  `address` text DEFAULT NULL COMMENT 'บ้านเลขที่ / หมู่ / อาคาร / ชั้น / ห้อง',
  `soi` varchar(100) DEFAULT NULL COMMENT 'ซอย',
  `road` varchar(100) DEFAULT NULL COMMENT 'ถนน',
  `subdistrict` varchar(100) DEFAULT NULL COMMENT 'ตำบล/แขวง',
  `district` varchar(100) DEFAULT NULL COMMENT 'อำเภอ/เขต',
  `province` varchar(100) DEFAULT NULL COMMENT 'จังหวัด',
  `postal_code` varchar(10) DEFAULT NULL COMMENT 'รหัสไปรษณีย์',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer_addresses`
--

INSERT INTO `customer_addresses` (`id`, `customer_id`, `name`, `address`, `soi`, `road`, `subdistrict`, `district`, `province`, `postal_code`, `created_at`, `updated_at`) VALUES
(1, 2, 'บ้าน', '10', '10', '10', '10', '10', '10', '10', '2026-01-06 06:47:29', '2026-01-06 06:48:24'),
(2, 3, 'บ้าน', '9999', '99999', '99999', '9999', '99999', '99999', '9999', '2026-01-06 07:05:52', '2026-01-06 07:05:52'),
(4, 5, '50', '50', NULL, NULL, '50', '50', '50', '50', '2026-01-14 21:13:41', '2026-01-14 21:13:41'),
(5, 6, 'ฟหก', 'ฟหก', NULL, NULL, 'ฟหก', 'ฟหก', 'ฟหก', 'ฟหก', '2026-01-15 06:58:23', '2026-01-15 06:58:23'),
(6, 2, 'ๆไฟห', 'ฟห', NULL, NULL, 'ฟห', 'ฟห', 'ฟห', 'ฟห', '2026-01-18 09:24:31', '2026-01-18 09:24:31'),
(7, 7, 'เ้', 'เ้', NULL, NULL, 'เเ้', 'เ้', 'เ้', 'เ้', '2026-01-18 23:07:31', '2026-01-18 23:07:31'),
(11, 11, 'ที่อยู่จัดส่ง', '123 ม.4', NULL, NULL, 'ฟหกฟ', 'ฟกหฟ', 'หฟก', '12354', '2026-01-18 23:27:11', '2026-01-18 23:27:11'),
(13, 13, '50', 'หก', NULL, NULL, 'หก', 'หก', 'หก', 'หก', '2026-01-19 05:23:51', '2026-01-19 05:23:51'),
(14, 14, 'บ้าน', '123/50', NULL, NULL, 'คลองด่าน', 'บางบ่อ', 'สมุทรปราการ', '10550', '2026-01-21 06:38:04', '2026-01-21 06:38:04'),
(15, 15, 'หน้าร้าน', '-', NULL, NULL, '-', '-', '-', '00000', '2026-01-21 07:00:52', '2026-01-21 07:00:52'),
(17, 17, 'บ้าน', '31124562525854/65', NULL, NULL, 'คลองด่าน', 'บางบ่อ', 'สมุทรปราการ', '10550', '2026-01-25 07:49:59', '2026-01-25 07:49:59'),
(18, 18, '10', '10', NULL, NULL, '10', '10', '10', '10', '2026-02-04 08:38:16', '2026-02-04 08:38:16');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `description` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `date` date NOT NULL,
  `category` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `description`, `amount`, `date`, `category`, `created_at`, `updated_at`, `user_id`) VALUES
(53, 'กิน', 200.00, '2025-03-30', 'อาหารและเครื่องดื่ม', '2025-03-29 22:59:40', '2025-03-29 22:59:40', NULL),
(54, 'เติมมัน', 500.00, '2025-03-30', 'การเดินทาง', '2025-03-29 23:00:37', '2025-03-29 23:00:37', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `incomes`
--

CREATE TABLE `incomes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `description` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `date` date NOT NULL,
  `category` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `incomes`
--

INSERT INTO `incomes` (`id`, `description`, `amount`, `date`, `category`, `created_at`, `updated_at`, `user_id`) VALUES
(54, 'ทำงาน', 240.00, '2025-03-30', 'รายได้เสริม', '2025-03-29 23:00:12', '2025-03-29 23:00:12', NULL),
(55, 'we', 10.00, '2025-06-08', 'อื่นๆ', '2025-06-08 02:00:48', '2025-06-08 02:00:48', NULL),
(56, 'ๆๆๆๆๆๆๆๆๆๆๆๆๆๆๆๆๆๆๆๆๆๆๆๆๆๆๆๆๆๆๆๆๆๆๆๆๆ', 10.00, '2025-12-22', 'โบนัส', '2025-12-22 01:24:51', '2025-12-22 01:24:51', NULL),
(57, '100', 10.00, '2026-01-06', 'โบนัส', '2026-01-06 08:09:31', '2026-01-27 11:23:43', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2025_03_15_061356_create_expenses_table', 1),
(2, '2025_03_15_061356_create_incomes_table', 1),
(3, '2025_03_15_082331_add_user_id_to_incomes_table', 2),
(4, '2025_03_15_082332_add_user_id_to_expenses_table', 3);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `customer_address_id` int(11) NOT NULL,
  `status` varchar(50) NOT NULL,
  `payment_status` varchar(50) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `shipping_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `notes` text DEFAULT NULL,
  `tracking_number` varchar(100) DEFAULT NULL,
  `stock_reserved_at` timestamp NULL DEFAULT NULL,
  `stock_released_at` timestamp NULL DEFAULT NULL,
  `stock_consumed_at` timestamp NULL DEFAULT NULL,
  `slip_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_number`, `customer_id`, `customer_address_id`, `status`, `payment_status`, `subtotal`, `shipping_fee`, `discount`, `total_amount`, `total_price`, `notes`, `tracking_number`, `stock_reserved_at`, `stock_released_at`, `stock_consumed_at`, `slip_image`, `created_at`, `updated_at`) VALUES
(1, 'ORD0001', 2, 1, 'pending', 'pending', 10.00, 10.00, 10.00, 10.00, 10.00, '1', NULL, '2026-01-06 08:01:46', NULL, NULL, NULL, '2026-01-06 08:01:46', '2026-01-06 08:01:46'),
(5, 'ORD-20260107-0001', 2, 1, 'pending', 'pending', 10.00, 10.00, 10.00, 10.00, 10.00, 'กด', NULL, NULL, NULL, NULL, NULL, '2026-01-07 09:35:51', '2026-01-07 09:35:51'),
(7, 'ORD-20260114-0001', 2, 1, 'pending', 'pending', 100.00, 10.00, 10.00, 100.00, 100.00, '10', NULL, NULL, NULL, NULL, NULL, '2026-01-13 23:44:10', '2026-01-13 23:44:10'),
(8, 'ORD-20260114-0002', 2, 1, 'pending', 'pending', 100.00, 0.00, 0.00, 100.00, 100.00, '10', NULL, NULL, NULL, NULL, NULL, '2026-01-14 00:01:31', '2026-01-14 00:01:31'),
(9, 'ORD-20260114-0003', 2, 1, 'shipped', 'paid', 80.00, 10.00, 10.00, 80.00, 80.00, '100', '1111111111111111111', NULL, NULL, NULL, 'slips/IzoavnmwDquig337EzL5c6ROo0GkwOWj8YDjhES1.jpg', '2026-01-14 08:44:48', '2026-01-18 22:40:06'),
(10, 'ORD-20260115-0001', 5, 4, 'pending', 'pending', 10.00, 50.00, 50.00, 10.00, 10.00, '50', NULL, NULL, NULL, NULL, NULL, '2026-01-14 21:13:41', '2026-01-14 21:13:41'),
(11, 'ORD-20260115-0002', 6, 5, 'pending', 'pending', 10.00, 0.00, 0.00, 10.00, 10.00, 'ฟหก', NULL, NULL, NULL, NULL, NULL, '2026-01-15 06:58:23', '2026-01-15 06:58:23'),
(13, 'ORD-20260118-0001', 2, 6, 'pending', 'pending', 40.00, 0.00, 0.00, 40.00, 40.00, '10', NULL, NULL, NULL, NULL, NULL, '2026-01-18 09:37:43', '2026-01-18 22:48:49'),
(14, 'ORD-20260119-0001', 2, 6, 'pending', 'pending', 10.00, 0.00, 0.00, 10.00, 10.00, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-18 23:06:09', '2026-01-18 23:06:09'),
(15, 'ORD-20260119-0002', 7, 7, 'pending', 'pending', 20.00, 0.00, 0.00, 20.00, 20.00, 'เ้', NULL, NULL, NULL, NULL, NULL, '2026-01-18 23:07:31', '2026-01-18 23:07:31'),
(19, 'ORD-20260119-0003', 11, 11, 'pending', 'pending', 1000.00, 0.00, 0.00, 1000.00, 1000.00, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-18 23:27:11', '2026-01-18 23:29:27'),
(21, 'ORD-20260119-0004', 13, 13, 'pending', 'pending', 20.00, 0.00, 0.00, 20.00, 20.00, 'ฟหก', NULL, NULL, NULL, NULL, NULL, '2026-01-19 05:23:51', '2026-01-19 05:23:51'),
(22, 'ORD-20260119-0005', 2, 6, 'pending', 'pending', 200.00, 0.00, 0.00, 200.00, 200.00, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-19 08:20:52', '2026-01-19 08:20:52'),
(25, 'ORD-20260121-0001', 2, 1, 'pending', 'pending', 240.00, 10.00, 10.00, 240.00, 240.00, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-21 06:25:09', '2026-01-21 06:25:09'),
(26, 'ORD-20260121-0002', 14, 14, 'pending', 'pending', 20.00, 10.00, 10.00, 20.00, 20.00, 'ำพ', NULL, NULL, NULL, NULL, NULL, '2026-01-21 06:38:04', '2026-01-21 06:38:05'),
(27, 'ORD-20260121-0003', 15, 15, 'pending', 'pending', 20.00, 0.00, 0.00, 20.00, 20.00, 'ฟ', NULL, NULL, NULL, NULL, NULL, '2026-01-21 07:00:52', '2026-01-21 07:00:52'),
(29, 'ORD-20260121-0004', 15, 15, 'pending', 'pending', 40.00, 50.00, 20.00, 70.00, 70.00, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-21 07:15:13', '2026-01-21 07:27:19'),
(30, 'ORD-20260125-0001', 2, 1, 'pending', 'pending', 210.00, 0.00, 0.00, 210.00, 210.00, 'w', NULL, NULL, NULL, NULL, NULL, '2026-01-25 07:16:22', '2026-01-25 07:16:38'),
(31, 'ORD-20260125-0002', 17, 17, 'shipped', 'paid', 5.00, 0.00, 0.00, 5.00, 5.00, NULL, '1', NULL, NULL, NULL, 'slips/rPcfByt72FWpMbZHjB8EvzmsVzTCW9d4NlSWsh1a.jpg', '2026-01-25 07:49:59', '2026-01-28 23:58:11'),
(32, 'ORD-20260125-0003', 17, 17, 'shipped', 'paid', 65.00, 0.00, 0.00, 65.00, 65.00, 'sx', '0', NULL, NULL, NULL, 'slips/j3PYV4URX44UyWPWdQ1ShMcYcsQmEVFpWEbfGrV7.jpg', '2026-01-25 07:53:07', '2026-01-28 23:57:19'),
(33, 'ORD-20260127-0001', 2, 6, 'pending', 'pending', 10.00, 10.00, 10.00, 10.00, 10.00, '10', NULL, NULL, NULL, NULL, NULL, '2026-01-27 11:25:02', '2026-01-27 11:25:02'),
(34, 'ORD-20260127-0002', 15, 15, 'shipped', 'paid', 10.00, 0.00, 0.00, 10.00, 10.00, NULL, '101010', NULL, NULL, NULL, 'slips/fBP5NdHpgja4XNUWXzmE0Coxd9LrkZYeaYr9bpUB.jpg', '2026-01-27 11:42:08', '2026-01-27 12:49:27'),
(35, 'ORD-20260202-0001', 2, 1, 'pending', 'pending', 30.00, 0.00, 0.00, 30.00, 30.00, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-02 00:31:26', '2026-02-02 00:31:27'),
(36, 'ORD-20260204-0001', 2, 1, 'pending', 'paid', 50.00, 100.00, 5.00, 145.00, 145.00, 'กำ', NULL, NULL, NULL, NULL, 'slips/OsP4BYCcgWEWdDTYuxUCVvnPY1X1JJzP2v8NXQqv.jpg', '2026-02-04 07:40:43', '2026-02-05 00:24:38'),
(37, 'ORD-20260205-0001', 15, 15, 'shipped', 'paid', 31.00, 100.00, 50.00, 81.00, 81.00, NULL, '9', NULL, NULL, NULL, 'slips/RFNGyK6t16ycYF3DoHZh5Cc1NVf6vefVHYXoQWU7.jpg', '2026-02-05 00:22:07', '2026-02-05 00:23:51');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL COMMENT 'รหัสออเดอร์',
  `product_id` int(20) NOT NULL COMMENT 'รหัสสินค้า',
  `product_name` varchar(300) NOT NULL COMMENT 'ชื่อสินค้าตอนสั่ง',
  `variant_name` varchar(255) DEFAULT NULL COMMENT 'สี-ไซส์ตอนสั่ง เช่น ดำ - XL',
  `quantity` int(11) NOT NULL COMMENT 'จำนวน',
  `unit_price` decimal(10,2) NOT NULL COMMENT 'ราคาต่อหน่วยตอนสั่ง',
  `total_price` decimal(10,2) NOT NULL COMMENT 'ราคารวม (quantity × unit_price)',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `color_id` int(11) DEFAULT NULL COMMENT 'รหัสสีที่เลือก',
  `size_id` int(11) DEFAULT NULL COMMENT 'รหัสไซส์ที่เลือก'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `variant_name`, `quantity`, `unit_price`, `total_price`, `created_at`, `updated_at`, `color_id`, `size_id`) VALUES
(264, 5, 91, 'asasฟ', 'ดำ 10', 1, 10.00, 10.00, '2026-01-07 09:35:51', '2026-01-07 09:35:51', 3, 10),
(265, 1, 9, 'ครีม.', 'แดง S', 1, 10.00, 10.00, '2026-01-07 09:46:02', '2026-01-07 09:46:02', 1, 1),
(267, 7, 93, 'dfsd', 'ดำ - 12', 10, 10.00, 100.00, '2026-01-13 23:44:10', '2026-01-13 23:44:10', 3, 11),
(268, 8, 93, 'dfsd', 'ดำ - 12', 10, 10.00, 100.00, '2026-01-14 00:01:31', '2026-01-14 00:01:31', 3, 11),
(276, 9, 95, 'qqqqqqq', 'ชมพูอ่อน - 14', 4, 20.00, 80.00, '2026-01-14 10:44:42', '2026-01-14 10:44:42', 10, 12),
(277, 10, 93, 'dfsd', 'ดำ - 12', 1, 10.00, 10.00, '2026-01-14 21:13:41', '2026-01-14 21:13:41', 3, 11),
(278, 11, 93, 'dfsd', 'ดำ - 12', 1, 10.00, 10.00, '2026-01-15 06:58:23', '2026-01-15 06:58:23', 3, 11),
(283, 13, 95, 'qqqqqqq', 'น้ำเงิน - 14', 2, 20.00, 40.00, '2026-01-18 22:48:49', '2026-01-18 22:48:49', 2, 12),
(284, 14, 9, 'ครีม.', 'แดง - M', 1, 10.00, 10.00, '2026-01-18 23:06:09', '2026-01-18 23:06:09', 1, 2),
(285, 15, 95, 'qqqqqqq', 'น้ำเงิน - 14', 1, 20.00, 20.00, '2026-01-18 23:07:31', '2026-01-18 23:07:31', 2, 12),
(290, 19, 93, 'dfsd', 'ดำ - 12', 100, 10.00, 1000.00, '2026-01-18 23:29:27', '2026-01-18 23:29:27', 3, 11),
(291, 21, 95, 'qqqqqqq', 'น้ำเงิน - 14', 1, 20.00, 20.00, '2026-01-19 05:23:51', '2026-01-19 05:23:51', 2, 12),
(292, 22, 95, 'qqqqqqq', 'ชมพูอ่อน - 10', 10, 20.00, 200.00, '2026-01-19 08:20:52', '2026-01-19 08:20:52', 10, 10),
(293, 25, 95, 'qqqqqqq', 'น้ำเงิน - 14', 6, 20.00, 120.00, '2026-01-21 06:25:09', '2026-01-21 06:25:09', 2, 12),
(294, 25, 95, 'qqqqqqq', 'ชมพูอ่อน - 10', 6, 20.00, 120.00, '2026-01-21 06:25:09', '2026-01-21 06:25:09', 10, 10),
(295, 26, 95, 'qqqqqqq', 'ชมพูอ่อน - 10', 1, 20.00, 20.00, '2026-01-21 06:38:05', '2026-01-21 06:38:05', 10, 10),
(296, 27, 95, 'qqqqqqq', 'ชมพูอ่อน - 10', 1, 20.00, 20.00, '2026-01-21 07:00:52', '2026-01-21 07:00:52', 10, 10),
(300, 29, 95, 'qqqqqqq', 'ชมพูอ่อน - 10', 2, 20.00, 40.00, '2026-01-21 07:27:19', '2026-01-21 07:27:19', 10, 10),
(303, 30, 95, 'qqqqqqq', ' - 10', 10, 20.00, 200.00, '2026-01-25 07:16:38', '2026-01-25 07:16:38', 10, 10),
(304, 30, 93, 'dfsd', 'ดำ - 12', 1, 10.00, 10.00, '2026-01-25 07:16:38', '2026-01-25 07:16:38', 3, 11),
(305, 31, 96, 'w0001', 'กอล์ฟ - 10', 5, 1.00, 5.00, '2026-01-25 07:49:59', '2026-01-25 07:49:59', 13, 10),
(315, 32, 96, 'w0001', 'กอล์ฟ - 10', 5, 1.00, 5.00, '2026-01-27 11:24:24', '2026-01-27 11:24:24', 13, 10),
(316, 32, 95, 'qqqqqqq', ' - 14', 2, 20.00, 40.00, '2026-01-27 11:24:24', '2026-01-27 11:24:24', 10, 12),
(317, 32, 95, 'qqqqqqq', ' - 10', 1, 20.00, 20.00, '2026-01-27 11:24:24', '2026-01-27 11:24:24', 10, 10),
(318, 33, 93, 'dfsd', 'ดำ - 12', 1, 10.00, 10.00, '2026-01-27 11:25:02', '2026-01-27 11:25:02', 3, 11),
(327, 34, 93, 'dfsd', 'ดำ - 12', 1, 10.00, 10.00, '2026-01-27 12:49:27', '2026-01-27 12:49:27', 3, 11),
(330, 35, 95, 'qqqqqqq', ' - 10', 1, 20.00, 20.00, '2026-02-02 14:22:11', '2026-02-02 14:22:11', 10, 10),
(331, 35, 93, 'dfsd', 'ดำ - 12', 1, 10.00, 10.00, '2026-02-02 14:22:11', '2026-02-02 14:22:11', 3, 11),
(332, 36, 95, 'qqqqqqq', ' - 10', 1, 20.00, 20.00, '2026-02-04 07:40:43', '2026-02-04 07:40:43', 10, 10),
(333, 36, 93, 'dfsd', 'ดำ - 12', 1, 10.00, 10.00, '2026-02-04 07:40:43', '2026-02-04 07:40:43', 3, 11),
(334, 36, 95, 'qqqqqqq', ' - 14', 1, 20.00, 20.00, '2026-02-04 07:40:43', '2026-02-04 07:40:43', 10, 12),
(337, 37, 95, 'qqqqqqq', ' - 10', 1, 20.00, 20.00, '2026-02-05 00:23:19', '2026-02-05 00:23:19', 10, 10),
(338, 37, 93, 'dfsd', 'ดำ - 12', 1, 10.00, 10.00, '2026-02-05 00:23:19', '2026-02-05 00:23:19', 3, 11),
(339, 37, 96, 'w0001', 'กอล์ฟ - 12', 1, 1.00, 1.00, '2026-02-05 00:23:19', '2026-02-05 00:23:19', 13, 11);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(20) NOT NULL COMMENT 'รหัสสินค้า',
  `category_id` int(11) NOT NULL COMMENT 'หมวดหมู่สินค้า',
  `id_stock` varchar(5) NOT NULL,
  `name` varchar(300) NOT NULL COMMENT 'ชื่อสินค้า',
  `description` text DEFAULT NULL COMMENT 'คำอธิบายสินค้า',
  `price` decimal(10,2) NOT NULL COMMENT 'ราคาสินค้า',
  `cost` decimal(10,2) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `id_stock`, `name`, `description`, `price`, `cost`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 17, 'a0001', 'เสื้อยืดลายการ์ตูน', 'เสื้อยืดผ้าฝ้าย 100%\r\n\';ll;ไำพasdasd\r\n่าม่าม\r\nรส', 2999.00, 19.00, 0, '2025-03-30 08:08:04', '2025-06-15 21:42:17'),
(2, 1, 'a0002', 'เสื้อเชิ้ตแขนยาว', 'เสื้อเชิ้ตลายเรียบหรูพดเ', 499.00, 10.00, 1, '2025-03-30 08:08:04', '2025-06-09 10:49:15'),
(5, 1, 'R0001', 'เสื้อยืดลายการ์ตูน', 'เสื้อยืดผ้าฝ้าย 100%', 299.00, 10.00, 0, '2025-03-30 08:08:29', '2025-04-06 15:09:51'),
(6, 1, 'Y0002', 'เสื้อเชิ้ตแขนยาว', 'เสื้อเชิ้ตลายเรียบหรู', 499.00, 50.00, 0, '2025-03-30 08:08:29', '2025-04-06 15:09:56'),
(9, 17, 'a0003', 'ครีม.', NULL, 10.00, 10.00, 1, '2025-06-08 02:43:47', '2025-06-08 02:44:26'),
(10, 1, 'P0001', 'น้ำดื่ม 500ml', 'น้ำดื่มสะอาด 500 มล.', 10.00, 5.00, 1, '2025-06-13 16:24:10', '2025-06-13 16:24:10'),
(11, 2, 'P0002', 'ข้าวสาร 5 กก.', 'ข้าวหอมมะลิอย่างดี', 120.00, 70.00, 1, '2025-06-13 16:24:10', '2025-06-13 16:24:10'),
(12, 1, 'P0003', 'น้ำส้มคั้น', 'น้ำส้มแท้ 100%', 25.00, 12.00, 0, '2025-06-13 16:24:10', '2025-06-13 16:24:10'),
(13, 19, 'D0001', 'เดรสยาว', 'เดรสยาว', 350.00, 100.00, 1, '2025-06-21 01:39:17', '2025-06-21 01:39:17'),
(36, 5, 'DR223', 'เสื้อกันฝน', 'พับเก็บได้ พกพาง่าย', 390.00, 180.00, 1, '2025-08-02 02:47:53', '2025-08-06 19:29:15'),
(37, 5, 'DR221', 'เสื้อคลุมตัวยาว', 'สไตล์เกาหลี ใส่ทับเสื้อผ้าได้หมด', 720.00, 350.00, 1, '2025-08-02 02:47:53', '2025-08-06 19:29:11'),
(38, 5, 'DR220', 'เสื้อโค้ทยาว', 'ใส่หน้าหนาวในเมืองหนาวได้เลย', 1590.00, 800.00, 1, '2025-08-02 02:47:53', '2025-08-06 19:29:09'),
(39, 1, 'DR218', 'เดรสคอวีลายจุด', 'ใส่แล้วดูสูงโปร่ง', 890.00, 440.00, 1, '2025-08-02 02:47:53', '2025-08-06 19:28:57'),
(40, 1, 'DR219', 'เดรสลูกไม้สีครีม', 'หวานๆ สำหรับงานแต่ง', 1290.00, 700.00, 1, '2025-08-02 02:47:53', '2025-08-06 19:28:59'),
(41, 1, 'DR217', 'เดรสเชิ้ตลินิน', 'เรียบหรู ใส่ได้ทุกวัน', 980.00, 490.00, 1, '2025-08-02 02:47:53', '2025-08-06 19:28:53'),
(42, 1, 'DR216', 'เดรสเปิดหลัง', 'สำหรับงานกลางคืน', 1150.00, 600.00, 1, '2025-08-02 02:47:53', '2025-08-06 19:28:50'),
(43, 1, 'DR215', 'เดรสสั้นสายเดี่ยว', 'ใส่ไปทะเลหรือเที่ยวกลางคืน', 720.00, 350.00, 1, '2025-08-02 02:47:53', '2025-08-06 19:28:47'),
(44, 2, 'DR214', 'เสื้อครอปแขนตุ๊กตา', 'สไตล์หวานๆ', 390.00, 180.00, 1, '2025-08-02 02:47:53', '2025-08-06 19:28:44'),
(45, 2, 'DR213', 'เสื้อโปโลลายทาง', 'ใส่เที่ยวได้ ใส่เรียนได้', 420.00, 200.00, 1, '2025-08-02 02:47:53', '2025-08-06 19:28:41'),
(46, 2, 'DR212', 'เสื้อแขนยาวโอเวอร์ไซส์', 'เท่ ดูดี', 530.00, 270.00, 1, '2025-08-02 02:47:53', '2025-08-06 19:28:37'),
(47, 2, 'DR211', 'เสื้อเปิดไหล่ลายดอก', 'เหมาะกับหน้าร้อน', 450.00, 220.00, 1, '2025-08-02 02:47:53', '2025-08-06 19:28:34'),
(48, 2, 'DR210', 'เสื้อเชิ้ตลายเส้น', 'ดูดีแบบเรียบง่าย', 470.00, 240.00, 1, '2025-08-02 02:47:53', '2025-08-06 19:28:31'),
(49, 3, 'DR209', 'กางเกงทรงขากว้าง', 'อินเทรนด์มาก', 650.00, 300.00, 1, '2025-08-02 02:47:53', '2025-08-06 19:28:26'),
(50, 3, 'DR208', 'กางเกงขาสั้นยีนส์', 'ใส่เที่ยวสบายๆ', 370.00, 180.00, 1, '2025-08-02 02:47:53', '2025-08-06 19:28:22'),
(51, 3, 'DR207', 'กางเกงรัดรูปออกกำลังกาย', 'ผ้ายืดหยุ่นดี', 520.00, 250.00, 1, '2025-08-02 02:47:53', '2025-08-06 19:28:19'),
(52, 3, 'DR206', 'กางเกงผ้าฝ้าย', 'ใส่สบายมาก', 400.00, 180.00, 1, '2025-08-02 02:47:53', '2025-08-06 19:28:14'),
(53, 3, 'DR205', 'กางเกงทำงานทรงตรง', 'สุภาพและดูดี', 580.00, 290.00, 1, '2025-08-02 02:47:53', '2025-08-06 19:28:10'),
(54, 4, 'DR204', 'กระโปรงยาวจีบ', 'พริ้วๆ สไตล์ญี่ปุ่น', 570.00, 280.00, 1, '2025-08-02 02:47:53', '2025-08-06 19:28:07'),
(55, 18, 'L0001', 'กระโปรงหนังสั้น', 'แฟชั่นแนวสาวเท่', 640.00, 330.00, 1, '2025-08-02 02:47:53', '2025-08-01 22:06:36'),
(56, 4, 'DR201', 'กระโปรงลายทางยาว', 'เหมาะกับวันทำงาน', 590.00, 280.00, 1, '2025-08-02 02:47:53', '2025-08-06 19:27:50'),
(57, 4, 'DR202', 'กระโปรงพลีทกลางเข่า', 'แฟชั่นสาวหวาน', 530.00, 260.00, 1, '2025-08-02 02:47:53', '2025-08-06 19:27:56'),
(58, 4, 'DR203', 'กระโปรงยีนส์คลุมเข่า', 'ดูดีแบบเรียบง่าย', 610.00, 290.00, 1, '2025-08-02 02:47:53', '2025-08-06 19:28:03'),
(59, 1, 'DR101', 'เดรสพีชเจ้าหญิง', 'เดรสโทนพีชพาสเทลน่ารักมากๆ เนื้อผ้านุ่มใส่สบาย', 590.00, 320.00, 1, '2025-08-02 03:55:35', '2025-08-02 03:55:35'),
(60, 18, 'DR102', 'เดรสลายเชอร์รี่หวาน', 'เดรสลายผลไม้สไตล์ญี่ปุ่น สีชมพูสดใส', 620.00, 350.00, 0, '2025-08-02 03:55:35', '2026-02-05 07:48:29'),
(62, 1, 'DR104', 'เดรสลายหัวใจจิ๋ว', 'เดรสคอกลมแขนตุ๊กตา ลายหัวใจสีแดง', 599.00, 340.00, 1, '2025-08-02 03:55:35', '2025-08-02 03:55:35'),
(63, 1, 'DR105', 'เดรสลูกไม้หวานละมุน', 'ลุคคุณหนูด้วยลูกไม้ขาวฟูฟ่อง ใส่ออกงานได้', 750.00, 420.00, 0, '2025-08-02 03:55:35', '2026-02-05 07:49:12'),
(64, 2, 'TS201', 'เสื้อยืดลายกระต่ายขี้อ้อน', 'เสื้อยืดสีชมพูลายการ์ตูนกระต่ายยิ้มแป้น', 320.00, 180.00, 1, '2025-08-02 03:55:35', '2025-08-02 03:55:35'),
(65, 2, 'TS202', 'เสื้อยืดลายบลูเบอรี่', 'สีฟ้าน่ารัก ลายผลไม้เล็กๆ สไตล์ญี่ปุ่น', 299.00, 160.00, 1, '2025-08-02 03:55:35', '2025-08-02 03:55:35'),
(66, 2, 'TS203', 'เสื้อยืดส้มบุนี่', 'เสื้อสีส้มลายกระต่ายแครอท คอปกน่ารักมาก', 350.00, 190.00, 1, '2025-08-02 03:55:35', '2025-08-02 03:55:35'),
(67, 2, 'TS204', 'เสื้อยืดโบว์ติดคอ', 'เสื้อยืดลุคหวาน มีโบว์สีครีมติดคอเสื้อ', 370.00, 200.00, 1, '2025-08-02 03:55:35', '2025-08-02 03:55:35'),
(68, 2, 'TS205', 'เสื้อยืดลายเมฆน้อย', 'ลายเมฆฟูฟ่อง สีขาวครีม ใส่ได้ทุกวัน', 310.00, 170.00, 1, '2025-08-02 03:55:35', '2025-08-02 03:55:35'),
(69, 3, 'SK301', 'กระโปรงจีบพีช', 'กระโปรงสั้นจีบพีช ทรงน่ารัก เหมาะกับสาวหวาน', 420.00, 240.00, 1, '2025-08-02 03:55:35', '2025-08-02 03:55:35'),
(70, 3, 'SK302', 'กระโปรงลายสก็อตหวานชมพู', 'ลายสก็อตแบบญี่ปุ่น สีหวานมาก', 450.00, 260.00, 1, '2025-08-02 03:55:35', '2025-08-02 03:55:35'),
(71, 3, 'SK303', 'กระโปรงทรงเอสีครีม', 'เรียบหรู สวมใส่ง่ายทุกโอกาส', 390.00, 220.00, 1, '2025-08-02 03:55:35', '2025-08-02 03:55:35'),
(72, 3, 'SK304', 'กระโปรงระบายสามชั้น', 'ทรงระบายพลิ้วๆ ดูน่ารักและมีมิติ', 470.00, 270.00, 1, '2025-08-02 03:55:35', '2025-08-02 03:55:35'),
(73, 3, 'SK305', 'กระโปรงทรงดินสอหวาน', 'กระโปรงเข้ารูป สีชมพูละมุน', 490.00, 280.00, 1, '2025-08-02 03:55:35', '2025-08-02 03:55:35'),
(74, 4, 'PN401', 'กางเกงขาสั้นลายดอก', 'ลายดอกไม้เล็กๆ สีสันสดใส', 330.00, 190.00, 1, '2025-08-02 03:55:35', '2025-08-02 03:55:35'),
(75, 4, 'PN402', 'กางเกงผ้าร่มโทนพาสเทล', 'เบาสบาย ใส่เดินเล่นได้ทุกวัน', 310.00, 170.00, 1, '2025-08-02 03:55:35', '2025-08-02 03:55:35'),
(76, 4, 'PN403', 'กางเกงยีนส์น่ารักเอวสูง', 'ทรงสวย แต่งลายหัวใจเล็กๆ', 550.00, 300.00, 1, '2025-08-02 03:55:35', '2025-08-02 03:55:35'),
(77, 4, 'PN404', 'กางเกงทรงบอลลูนชมพู', 'ทรงบานๆ แนวญี่ปุ่นมาก', 480.00, 260.00, 1, '2025-08-02 03:55:35', '2025-08-02 03:55:35'),
(78, 4, 'PN405', 'กางเกงผูกโบว์น่อง', 'มีโบว์เล็กตรงปลายน่อง ใส่กับเสื้อยืดคือใช่เลย', 370.00, 200.00, 1, '2025-08-02 03:55:35', '2025-08-02 03:55:35'),
(79, 5, 'JK501', 'เสื้อคลุมไหมพรมชมพู', 'เสื้อไหมพรมเนื้อดี อบอุ่นและนุ่มมาก', 690.00, 400.00, 1, '2025-08-02 03:55:35', '2025-08-02 03:55:35'),
(80, 5, 'JK502', 'แจ็คเก็ตลายหมีน้อย', 'แจ็คเก็ตลายหมีการ์ตูนน่ารัก ใส่แล้วละลาย', 720.00, 420.00, 1, '2025-08-02 03:55:35', '2025-08-02 03:55:35'),
(81, 5, 'JK503', 'เสื้อคลุมชีฟองครีม', 'เสื้อบางๆ พลิ้วเบา ลุคสาวหวาน', 670.00, 380.00, 1, '2025-08-02 03:55:35', '2025-08-02 03:55:35'),
(82, 5, 'JK504', 'แจ็คเก็ตขนเฟอร์พีช', 'เฟอร์เบาๆ เหมาะกับอากาศเย็น ถ่ายรูปปัง', 790.00, 450.00, 1, '2025-08-02 03:55:35', '2025-08-02 03:55:35'),
(83, 5, 'JK505', 'เสื้อคลุมผ้าห่มน้องหมี', 'สไตล์เสื้อผ้าห่ม นุ่มอบอุ่นเหมือนกอดหมี', 850.00, 490.00, 1, '2025-08-02 03:55:35', '2025-08-02 03:55:35'),
(84, 1, 'DR106', 'เดรสสตรอว์เบอร์รี่น่ารัก', 'ลายสตรอว์เบอร์รี่ ใส่แล้วดูสดใส', 620.00, 340.00, 1, '2025-08-02 03:55:35', '2025-08-02 03:55:35'),
(85, 1, 'DR107', 'เดรสโบว์แครอท', 'เดรสมีโบว์สีแครอทตรงเอว น่ารักสุดๆ', 590.00, 330.00, 1, '2025-08-02 03:55:35', '2025-08-02 03:55:35'),
(86, 2, 'TS206', 'เสื้อยืดแมวเหมียวชมพู', 'ลายแมวน่ารักมาก ใส่สบายทุกวัน', 310.00, 170.00, 1, '2025-08-02 03:55:35', '2025-08-02 03:55:35'),
(87, 3, 'SK306', 'กระโปรงลายซากุระ', 'ลายซากุระหวานๆ ลุคญี่ปุ่น', 460.00, 260.00, 1, '2025-08-02 03:55:35', '2025-08-02 03:55:35'),
(88, 4, 'PN406', 'กางเกงลายกาแล็กซี่', 'สีม่วง-ฟ้าสวยเหมือนอวกาศ', 390.00, 220.00, 1, '2025-08-02 03:55:35', '2025-08-02 03:55:35'),
(89, 5, 'JK506', 'แจ็คเก็ตหูกระต่าย', 'มีกระเป๋า+หมวกหูกระต่ายมุ้งมิ้งสุด', 880.00, 500.00, 1, '2025-08-02 03:55:35', '2025-08-02 03:55:35'),
(90, 1, 'Q0001', 'เสื้อครอป', 'เสื้อไง', 150.00, 20.00, 1, '2025-11-01 21:44:06', '2025-11-01 21:44:06'),
(91, 20, 'R2345', 'asasฟ', '10ดเดเดเ', 10.00, 10.00, 1, '2026-01-06 09:02:51', '2026-01-07 04:51:53'),
(92, 17, 'a9990', 'กอล์ฟ', '10', 10.00, 10.00, 1, '2026-01-07 08:55:25', '2026-01-07 08:55:25'),
(93, 19, 'qwert', 'dfsd', '10', 10.00, 10.00, 1, '2026-01-07 20:53:37', '2026-02-01 23:04:03'),
(94, 19, 'a1111', '1', '10ewe', 10.00, 10.00, 1, '2026-01-09 05:05:39', '2026-01-09 05:06:00'),
(95, 20, 'z0001', 'qqqqqqq', '10ดเดกเดกเ', 20.00, 20.00, 1, '2026-01-14 08:02:57', '2026-01-14 08:43:20'),
(96, 21, 'w0001', 'w0001', 'w0001', 1.00, 1.00, 1, '2026-01-21 08:06:29', '2026-02-01 03:53:36'),
(97, 8, 'L9999', 'dddd', '10', 110.00, 10.00, 0, '2026-02-03 08:19:39', '2026-02-05 00:34:40'),
(98, 19, 'f1111', 'fdfdfd', NULL, 50.00, 10.00, 1, '2026-02-05 23:46:31', '2026-02-05 23:48:12'),
(99, 15, 'www11', 'gfggg', NULL, 10.00, 5.00, 1, '2026-02-05 23:49:51', '2026-02-05 23:49:51');

-- --------------------------------------------------------

--
-- Table structure for table `product_colors`
--

CREATE TABLE `product_colors` (
  `product_id` int(11) NOT NULL,
  `color_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_colors`
--

INSERT INTO `product_colors` (`product_id`, `color_id`) VALUES
(1, 1),
(1, 3),
(2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `product_color_size`
--

CREATE TABLE `product_color_size` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `color_id` int(11) NOT NULL,
  `size_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_color_size`
--

INSERT INTO `product_color_size` (`id`, `product_id`, `color_id`, `size_id`, `quantity`) VALUES
(1, 1, 1, 2, 0),
(2, 1, 3, 2, 0),
(5, 1, 5, 2, 0),
(6, 1, 2, 1, 1),
(7, 1, 1, 1, 2),
(8, 1, 2, 2, 1),
(9, 6, 10, 3, 1),
(10, 1, 3, 5, 1),
(11, 9, 1, 2, 3),
(12, 2, 1, 1, 0),
(13, 5, 3, 5, 99),
(14, 12, 1, 1, 10),
(15, 9, 1, 1, 8),
(16, 9, 1, 3, 3),
(17, 9, 8, 4, 5),
(18, 9, 8, 5, 0),
(19, 5, 1, 2, 100),
(20, 5, 3, 10, 110),
(21, 5, 1, 3, 99),
(22, 5, 5, 1, 10),
(23, 5, 5, 2, 10),
(24, 5, 5, 3, 10),
(25, 5, 11, 1, 22),
(26, 5, 11, 6, 2),
(27, 90, 1, 1, 8),
(28, 90, 1, 2, 9),
(29, 90, 1, 3, 10),
(30, 90, 3, 4, 7),
(31, 90, 3, 5, 20),
(32, 90, 8, 5, 100),
(33, 90, 11, 6, 10),
(34, 91, 3, 10, 9),
(35, 93, 3, 11, 486),
(36, 95, 10, 10, 39),
(37, 95, 10, 12, 93),
(38, 95, 2, 12, 10),
(39, 96, 10, 6, 100),
(40, 96, 13, 14, 10),
(41, 96, 13, 10, 0),
(42, 96, 13, 11, 9),
(43, 60, 13, 10, 10),
(44, 62, 13, 10, 15),
(45, 97, 5, 10, 1000);

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image_url` varchar(300) NOT NULL COMMENT 'URL รูปภาพสินค้า',
  `is_main` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `image_url`, `is_main`) VALUES
(11, 2, 'product_images/8wI0Wx1iGmBXXyMKC6yL318HXOROEcq5kvJVkPHL.png', 1),
(12, 2, 'product_images/iz6t4Ld9wsZJRTQJ3xHW3DMrgSMO6zWfrtjWQAzJ.png', 0),
(21, 1, 'product_images/2022-12-05.png', 1),
(25, 1, 'product_images/ChatGPT Image 5 พ.ค. 2568 08_19_08.png', 0),
(30, 1, 'product_images/IMG_6637.JPG', 0),
(31, 1, 'product_images/ChatGPT Image 5 พ.ค. 2568 07_54_35.png', 0),
(33, 6, 'product_images/KK_347691.png', 1),
(35, 1, 'product_images/IMG_3664.JPG', 0),
(36, 5, 'product_images/IMG_8781.jpeg', 0),
(37, 5, 'product_images/IMG_8573.jpeg', 0),
(38, 9, 'product_images/IMG_8484.jpeg', 1),
(39, 13, 'product_images/QCYpbppqgUnxaPxRmQPRDx40CLQvn1CJSeYTEjKY.png', 0),
(40, 13, 'product_images/download.png', 1),
(41, 10, 'product_images/i0003.jpg', 1),
(42, 12, 'product_images/i0001.jpg', 1),
(43, 55, 'product_images/i0002.jpg', 1),
(44, 59, 'product_images/bg.jpg', 1),
(45, 5, 'product_images/IMG_2443.JPG', 1),
(46, 90, 'product_images/NcWylSw7U7u87xW0agQMKPM9IhbDAJV3yn0A388d.jpg', 0),
(47, 91, 'product_images/ZSBjfXZjymLsobxGUmOieI5ckTh0UxyJ3YciG9zw.jpg', 0),
(50, 91, 'product_images/g68mSiIojWEOUScEYPTGOxuJ9iIQhFtPw3d5coUs.jpg', 0),
(51, 91, 'product_images/Nklbv7tkfmeiUEAYkqE3jYWNAvKwYycJWU2amOtm.png', 1),
(52, 92, 'product_images/0RCKpUKX8ivRYxqvJDjaNy8WsbMgfudzpI6rEqBH.jpg', 0),
(53, 93, 'product_images/apjFCAPF6LZVHSbZCESuUxEgeM2qTwOoMrkENaA9.jpg', 0),
(54, 94, 'product_images/70ACxrLfXwBFwEuygIqBqoKKOuV9QKaepfukIcz7.jpg', 0),
(55, 95, 'product_images/BlHFQowMxP4oCRTAcOnE3hWKppbhdcJ1ywpFw749.jpg', 0),
(57, 95, 'product_images/มันกิ้น.jpg', 1),
(58, 96, 'product_images/nBlbxZcZIP7hO4rm0V3SfNStlJGkbOKr0QxITKa6.png', 0),
(60, 60, 'product_images/สก็อตติสโฟ.jpg', 0),
(61, 60, 'product_images/สังเกตุ.jpg', 1),
(62, 96, 'product_images/มันกิ้น.jpg', 0),
(63, 96, 'product_images/เม็ด.jpg', 1),
(64, 96, 'product_images/เมนคูน.jpg', 0),
(66, 62, 'product_images/สก็อตติสโฟ.jpg', 0),
(67, 62, 'product_images/สังเกตุ.jpg', 1),
(68, 97, 'product_images/3CyfBSS3L0HGJ28tThXwhxu1uuE9FyzSixqLyEB8.jpg', 0),
(69, 99, 'product_images/FKpRySr8pvpv3JRabTNY3ocaFoCCmqKi1M21IpCP.jpg', 0);

-- --------------------------------------------------------

--
-- Table structure for table `product_options`
--

CREATE TABLE `product_options` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `option_name` varchar(100) NOT NULL COMMENT 'ชื่อออปชั่น',
  `option_value` varchar(100) NOT NULL COMMENT 'ค่าของออปชั่น'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_options`
--

INSERT INTO `product_options` (`id`, `product_id`, `option_name`, `option_value`) VALUES
(1, 1, 'ขนาด', 'M'),
(2, 1, 'ขนาด', 'L'),
(3, 1, 'ขนาด', 'XL'),
(4, 2, 'วัสดุ', 'Cotton'),
(8, NULL, 'ฟหกหก', 'ห');

-- --------------------------------------------------------

--
-- Table structure for table `product_sizes`
--

CREATE TABLE `product_sizes` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `size_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_sizes`
--

INSERT INTO `product_sizes` (`id`, `product_id`, `size_id`) VALUES
(1, 1, 1),
(2, 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `product_tags`
--

CREATE TABLE `product_tags` (
  `product_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_tags`
--

INSERT INTO `product_tags` (`product_id`, `tag_id`) VALUES
(1, 2),
(1, 3),
(1, 4),
(2, 3),
(5, 2),
(55, 2),
(60, 1),
(60, 2),
(60, 3),
(95, 2),
(95, 3),
(95, 7),
(96, 7),
(97, 2),
(97, 3),
(98, 3);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `key` varchar(255) NOT NULL COMMENT 'ชื่อตัวแปร',
  `value` text DEFAULT NULL COMMENT 'ค่าที่เก็บ',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES
(1, 'default_shipping_fee', '53', '2026-01-19 13:46:15', '2026-02-02 15:13:46'),
(2, 'bank_accounts', '[{\"bank_name\":\"กสิกร\",\"account_number\":\"1234567893\",\"account_name\":\"คนหล่อ\"}]', '2026-01-19 13:46:15', '2026-02-02 15:13:46'),
(3, '_method', 'PUT', '2026-01-19 14:02:34', '2026-02-02 15:13:46'),
(4, 'shop_name', 'ขายทุกอย่างๆๆ', '2026-01-19 14:02:34', '2026-02-02 15:13:46'),
(5, 'shop_phone', '0963874973', '2026-01-19 14:02:34', '2026-02-02 15:13:46'),
(6, 'shop_address', 'บ้าน แหละ3', '2026-01-19 14:02:34', '2026-02-02 15:13:46'),
(7, 'low_stock_threshold', '13', '2026-01-19 14:02:34', '2026-02-02 15:13:46');

-- --------------------------------------------------------

--
-- Table structure for table `shipping_zones`
--

CREATE TABLE `shipping_zones` (
  `id` int(11) NOT NULL,
  `zone_name` varchar(100) NOT NULL COMMENT 'ชื่อเขต',
  `provinces` text NOT NULL COMMENT 'รายชื่อจังหวัด (JSON format)',
  `base_fee` decimal(10,2) NOT NULL COMMENT 'ค่าจัดส่งพื้นฐาน',
  `per_kg_fee` decimal(10,2) DEFAULT 0.00 COMMENT 'ค่าจัดส่งต่อกิโลกรัม',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shipping_zones`
--

INSERT INTO `shipping_zones` (`id`, `zone_name`, `provinces`, `base_fee`, `per_kg_fee`, `created_at`) VALUES
(1, 'กรุงเทพและปริมณฑล', '[\"กรุงเทพมหานคร\",\"นนทบุรี\",\"ปทุมธานี\",\"สมุทรปราการ\",\"สมุทรสาคร\",\"นครปฐม\"]', 50.00, 10.00, '2025-06-14 22:03:38'),
(2, 'ภาคกลาง', '[\"อยุธยา\",\"สระบุรี\",\"ลพบุรี\",\"สิงห์บุรี\",\"ชัยนาท\",\"อุทัยธานี\",\"นครสวรรค์\",\"กำแพงเพชร\",\"พิษณุโลก\",\"สุโขทัย\",\"ตาก\",\"พิจิตร\",\"เพชรบูรณ์\"]', 80.00, 15.00, '2025-06-14 22:03:38'),
(3, 'ภาคเหนือ', '[\"เชียงใหม่\",\"เชียงราย\",\"ลำปาง\",\"ลำพูน\",\"อุตรดิตถ์\",\"น่าน\",\"พะเยา\",\"แพร่\",\"แม่ฮ่องสอน\"]', 120.00, 20.00, '2025-06-14 22:03:38'),
(4, 'ภาคตะวันออกเฉียงเหนือ', '[\"นครราชสีมา\",\"บุรีรัมย์\",\"สุรินทร์\",\"ศิลาลัย\",\"อุบลราชธานี\",\"ยโสธร\",\"ชัยภูมิ\",\"อำนาจเจริญ\",\"หนองบัวลำภู\",\"ขอนแก่น\",\"อุดรธานี\",\"เลย\",\"หนองคาย\",\"บึงกาฬ\",\"สกลนคร\",\"นครพนม\",\"กาฬสินธุ์\",\"มหาสารคาม\",\"ร้อยเอ็ด\",\"มุกดาหาร\"]', 100.00, 18.00, '2025-06-14 22:03:38'),
(5, 'ภาคใต้', '[\"ประจวบคีรีขันธ์\",\"เพชรบุรี\",\"ราชบุรี\",\"สมุทรสงคราม\",\"ชุมพร\",\"ระนอง\",\"สุราษฎร์ธานี\",\"นครศรีธรรมราช\",\"กระบี่\",\"พังงา\",\"ภูเก็ต\",\"ตรัง\",\"สตูล\",\"สงขลา\",\"ปัตตานี\",\"ยะลา\",\"นราธิวาส\"]', 150.00, 25.00, '2025-06-14 22:03:38'),
(6, 'ภาคตะวันออก', '[\"ชลบุรี\",\"ระยอง\",\"จันทบุรี\",\"ตราด\",\"ฉะเชิงเทรา\",\"ปราจีนบุรี\",\"นครนายก\",\"สระแก้ว\"]', 90.00, 15.00, '2025-06-14 22:03:38');

-- --------------------------------------------------------

--
-- Table structure for table `sizes`
--

CREATE TABLE `sizes` (
  `id` int(11) NOT NULL,
  `size_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sizes`
--

INSERT INTO `sizes` (`id`, `size_name`) VALUES
(1, 'S'),
(2, 'M'),
(3, 'L'),
(4, 'XL'),
(5, 'SX'),
(6, 'หกหก'),
(7, '18'),
(8, '20'),
(9, '8'),
(10, '10'),
(11, '12'),
(12, '14'),
(14, 'หก'),
(16, 'XS');

-- --------------------------------------------------------

--
-- Table structure for table `stock_adjustments`
--

CREATE TABLE `stock_adjustments` (
  `id` int(11) NOT NULL,
  `product_color_size_id` int(11) NOT NULL,
  `adjustment_type` enum('increase','decrease') NOT NULL COMMENT 'เพิ่ม/ลด',
  `quantity` int(11) NOT NULL COMMENT 'จำนวนที่ปรับ',
  `quantity_before` int(11) NOT NULL COMMENT 'สต็อกก่อนปรับ',
  `quantity_after` int(11) NOT NULL COMMENT 'สต็อกหลังปรับ',
  `reason` text NOT NULL COMMENT 'เหตุผลการปรับสต็อก',
  `note` text DEFAULT NULL COMMENT 'หมายเหตุเพิ่มเติม',
  `user_id` int(11) DEFAULT NULL,
  `user_name` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='บันทึกการปรับสต็อกด้วยตนเอง';

--
-- Dumping data for table `stock_adjustments`
--

INSERT INTO `stock_adjustments` (`id`, `product_color_size_id`, `adjustment_type`, `quantity`, `quantity_before`, `quantity_after`, `reason`, `note`, `user_id`, `user_name`, `created_at`) VALUES
(1, 20, 'increase', 10, 100, 110, 'ตรวจนับสต็อก', '10', NULL, 'ผู้ดูแลระบบ', '2025-11-02 03:50:37'),
(2, 25, 'increase', 3, 17, 20, 'คืนสินค้าจากลูกค้า', 'เย้', NULL, 'ผู้ดูแลระบบ', '2025-11-02 04:26:22'),
(3, 25, 'increase', 3, 20, 23, 'สินค้าเข้าใหม่', '3', NULL, 'ผู้ดูแลระบบ', '2025-11-02 04:27:02'),
(4, 25, 'decrease', 1, 23, 22, 'สินค้าหาย', '1', NULL, 'ผู้ดูแลระบบ', '2025-11-02 04:27:12'),
(5, 30, 'increase', 6, 4, 10, 'สินค้าหาย', '10', NULL, 'ผู้ดูแลระบบ', '2025-11-02 05:17:47');

-- --------------------------------------------------------

--
-- Table structure for table `stock_holds`
--

CREATE TABLE `stock_holds` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_color_size_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `quantity` int(10) UNSIGNED NOT NULL,
  `status` enum('active','released','consumed') NOT NULL DEFAULT 'active',
  `expires_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_holds`
--

INSERT INTO `stock_holds` (`id`, `product_color_size_id`, `order_id`, `quantity`, `status`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 25, NULL, 3, 'released', NULL, '2025-11-02 03:37:23', '2025-11-02 03:37:38'),
(2, 26, NULL, 3, 'released', NULL, '2025-11-02 03:37:23', '2025-11-02 03:37:38'),
(3, 25, NULL, 3, 'active', NULL, '2025-11-02 03:37:38', '2025-11-02 03:37:38'),
(4, 26, NULL, 3, 'active', NULL, '2025-11-02 03:37:38', '2025-11-02 03:37:38'),
(5, 27, NULL, 1, 'released', NULL, '2025-11-02 04:46:38', '2025-11-02 04:51:16'),
(6, 28, NULL, 1, 'released', NULL, '2025-11-02 04:46:38', '2025-11-02 04:51:16'),
(7, 30, NULL, 1, 'released', NULL, '2025-11-02 04:46:38', '2025-11-02 04:51:16'),
(8, 27, NULL, 2, 'released', NULL, '2025-11-02 04:51:16', '2025-11-02 05:14:50'),
(9, 28, NULL, 1, 'released', NULL, '2025-11-02 04:51:16', '2025-11-02 05:14:50'),
(10, 30, NULL, 1, 'released', NULL, '2025-11-02 04:51:16', '2025-11-02 05:14:50'),
(11, 31, NULL, 4, 'released', NULL, '2025-11-02 04:51:16', '2025-11-02 05:14:50'),
(12, 27, NULL, 2, 'released', NULL, '2025-11-02 05:14:50', '2025-11-02 05:16:44'),
(13, 28, NULL, 1, 'released', NULL, '2025-11-02 05:14:50', '2025-11-02 05:16:44'),
(14, 30, NULL, 4, 'released', NULL, '2025-11-02 05:14:50', '2025-11-02 05:16:44'),
(15, 27, NULL, 8, 'released', NULL, '2025-11-02 05:16:44', '2025-11-02 05:18:14'),
(16, 28, NULL, 9, 'released', NULL, '2025-11-02 05:16:44', '2025-11-02 05:18:14'),
(17, 30, NULL, 6, 'released', NULL, '2025-11-02 05:16:45', '2025-11-02 05:18:14'),
(18, 27, NULL, 2, 'released', NULL, '2025-11-02 05:18:14', '2025-11-21 16:24:20'),
(19, 28, NULL, 1, 'released', NULL, '2025-11-02 05:18:15', '2025-11-21 16:24:20'),
(20, 30, NULL, 10, 'released', NULL, '2025-11-02 05:18:15', '2025-11-21 16:24:20'),
(23, 27, NULL, 2, 'active', NULL, '2025-11-21 16:24:20', '2025-11-21 16:24:20'),
(24, 28, NULL, 1, 'active', NULL, '2025-11-21 16:24:20', '2025-11-21 16:24:20'),
(25, 30, NULL, 6, 'active', NULL, '2025-11-21 16:24:20', '2025-11-21 16:24:20'),
(42, 23, NULL, 1, 'released', NULL, '2025-11-23 08:47:13', '2025-11-26 08:38:01'),
(43, 8, NULL, 1, 'released', NULL, '2025-11-26 08:35:30', '2025-11-26 08:37:41'),
(44, 23, NULL, 1, 'active', NULL, '2025-11-26 08:38:01', '2025-11-26 08:38:01'),
(45, 11, NULL, 1, 'active', NULL, '2025-12-22 10:00:43', '2025-12-22 10:00:43'),
(46, 16, NULL, 1, 'active', NULL, '2025-12-22 10:02:32', '2025-12-22 10:02:32'),
(47, 16, NULL, 1, 'active', NULL, '2026-01-04 08:19:34', '2026-01-04 08:19:34'),
(48, 15, 1, 1, 'active', NULL, '2026-01-06 15:01:46', '2026-01-06 15:01:46'),
(49, 35, 8, 10, 'active', NULL, '2026-01-14 07:01:31', '2026-01-14 07:01:31'),
(50, 37, 9, 5, 'consumed', NULL, '2026-01-14 15:44:48', '2026-01-14 15:47:46'),
(51, 37, 9, 4, 'consumed', NULL, '2026-01-14 16:59:57', '2026-01-14 16:59:57'),
(52, 37, 9, 4, 'consumed', NULL, '2026-01-14 17:01:30', '2026-01-14 17:01:30'),
(55, 37, 9, 4, 'consumed', NULL, '2026-01-14 17:41:54', '2026-01-14 17:41:54'),
(56, 37, 9, 4, 'consumed', NULL, '2026-01-14 17:44:27', '2026-01-14 17:44:27'),
(57, 37, 9, 4, 'consumed', NULL, '2026-01-14 17:44:42', '2026-01-14 17:44:42'),
(58, 35, 10, 1, 'active', NULL, '2026-01-15 04:13:41', '2026-01-15 04:13:41'),
(59, 35, 11, 1, 'active', NULL, '2026-01-15 13:58:23', '2026-01-15 13:58:23'),
(60, 38, NULL, 1, 'released', NULL, '2026-01-18 16:24:31', '2026-01-18 16:26:05'),
(61, 38, NULL, 5, 'released', NULL, '2026-01-18 16:26:05', '2026-01-18 16:32:45'),
(62, 38, NULL, 10, 'released', NULL, '2026-01-18 16:32:45', '2026-01-18 16:36:44'),
(63, 38, 13, 1, 'released', NULL, '2026-01-18 16:37:43', '2026-01-19 05:48:49'),
(64, 38, 13, 2, 'active', NULL, '2026-01-19 05:48:49', '2026-01-19 05:48:49'),
(65, 11, 14, 1, 'active', NULL, '2026-01-19 06:06:09', '2026-01-19 06:06:09'),
(66, 38, 15, 1, 'active', NULL, '2026-01-19 06:07:31', '2026-01-19 06:07:31'),
(68, 35, 19, 100, 'released', NULL, '2026-01-19 06:27:11', '2026-01-19 06:29:27'),
(69, 36, 19, 10, 'released', NULL, '2026-01-19 06:27:11', '2026-01-19 06:29:27'),
(71, 35, 19, 100, 'active', NULL, '2026-01-19 06:29:27', '2026-01-19 06:29:27'),
(72, 38, 21, 1, 'active', NULL, '2026-01-19 12:23:51', '2026-01-19 12:23:51'),
(73, 36, 22, 10, 'active', NULL, '2026-01-19 15:20:52', '2026-01-19 15:20:52'),
(74, 38, 25, 6, 'active', NULL, '2026-01-21 13:25:09', '2026-01-21 13:25:09'),
(75, 36, 25, 6, 'active', NULL, '2026-01-21 13:25:09', '2026-01-21 13:25:09'),
(76, 36, 26, 1, 'active', NULL, '2026-01-21 13:38:04', '2026-01-21 13:38:04'),
(77, 36, 27, 1, 'active', NULL, '2026-01-21 14:00:52', '2026-01-21 14:00:52'),
(78, 36, NULL, 1, 'released', NULL, '2026-01-21 14:01:07', '2026-01-21 14:06:32'),
(79, 36, 29, 1, 'released', NULL, '2026-01-21 14:15:13', '2026-01-21 14:26:47'),
(80, 36, 29, 1, 'released', NULL, '2026-01-21 14:26:47', '2026-01-21 14:27:19'),
(81, 36, 29, 2, 'active', NULL, '2026-01-21 14:27:19', '2026-01-21 14:27:19'),
(82, 36, 30, 1, 'released', NULL, '2026-01-25 14:16:22', '2026-01-25 14:16:38'),
(83, 35, 30, 1, 'released', NULL, '2026-01-25 14:16:22', '2026-01-25 14:16:38'),
(84, 36, 30, 10, 'active', NULL, '2026-01-25 14:16:38', '2026-01-25 14:16:38'),
(85, 35, 30, 1, 'active', NULL, '2026-01-25 14:16:38', '2026-01-25 14:16:38'),
(86, 41, 31, 5, 'consumed', NULL, '2026-01-25 14:49:59', '2026-01-29 06:58:11'),
(87, 41, 32, 1, 'released', NULL, '2026-01-25 14:53:07', '2026-01-25 14:54:30'),
(88, 41, 32, 5, 'released', NULL, '2026-01-25 14:54:30', '2026-01-25 16:06:55'),
(89, 41, 32, 5, 'released', NULL, '2026-01-25 16:06:55', '2026-01-25 16:11:44'),
(90, 37, 32, 1, 'released', NULL, '2026-01-25 16:06:55', '2026-01-25 16:11:44'),
(91, 41, 32, 5, 'released', NULL, '2026-01-25 16:11:44', '2026-01-27 18:24:23'),
(92, 37, 32, 2, 'released', NULL, '2026-01-25 16:11:44', '2026-01-27 18:24:23'),
(93, 41, 32, 5, 'released', NULL, '2026-01-27 18:24:23', '2026-01-27 18:24:24'),
(94, 37, 32, 2, 'released', NULL, '2026-01-27 18:24:23', '2026-01-27 18:24:24'),
(95, 36, 32, 1, 'released', NULL, '2026-01-27 18:24:23', '2026-01-27 18:24:24'),
(96, 41, 32, 5, 'consumed', NULL, '2026-01-27 18:24:24', '2026-01-29 06:57:19'),
(97, 37, 32, 2, 'consumed', NULL, '2026-01-27 18:24:24', '2026-01-29 06:57:19'),
(98, 36, 32, 1, 'consumed', NULL, '2026-01-27 18:24:24', '2026-01-29 06:57:19'),
(99, 35, 33, 1, 'active', NULL, '2026-01-27 18:25:02', '2026-01-27 18:25:02'),
(100, 35, 34, 120, 'consumed', NULL, '2026-01-27 18:42:08', '2026-01-27 18:43:26'),
(106, 35, 34, 121, 'consumed', NULL, '2026-01-27 19:34:32', '2026-01-27 19:34:32'),
(107, 35, 34, 151, 'consumed', NULL, '2026-01-27 19:34:52', '2026-01-27 19:34:52'),
(108, 35, 34, 1, 'consumed', NULL, '2026-01-27 19:49:27', '2026-01-27 19:49:27'),
(109, 36, 35, 1, 'released', NULL, '2026-02-02 07:31:26', '2026-02-02 21:22:11'),
(110, 35, 35, 1, 'released', NULL, '2026-02-02 07:31:27', '2026-02-02 21:22:11'),
(112, 36, 35, 1, 'active', NULL, '2026-02-02 21:22:11', '2026-02-02 21:22:11'),
(113, 35, 35, 1, 'active', NULL, '2026-02-02 21:22:11', '2026-02-02 21:22:11'),
(114, 36, 36, 1, 'active', NULL, '2026-02-04 14:40:43', '2026-02-04 14:40:43'),
(115, 35, 36, 1, 'active', NULL, '2026-02-04 14:40:43', '2026-02-04 14:40:43'),
(116, 37, 36, 1, 'active', NULL, '2026-02-04 14:40:43', '2026-02-04 14:40:43'),
(117, 36, 37, 1, 'released', NULL, '2026-02-05 07:22:07', '2026-02-05 07:23:18'),
(118, 35, 37, 1, 'released', NULL, '2026-02-05 07:22:07', '2026-02-05 07:23:18'),
(119, 36, 37, 1, 'consumed', NULL, '2026-02-05 07:23:19', '2026-02-05 07:23:51'),
(120, 35, 37, 1, 'consumed', NULL, '2026-02-05 07:23:19', '2026-02-05 07:23:51'),
(121, 42, 37, 1, 'consumed', NULL, '2026-02-05 07:23:19', '2026-02-05 07:23:51');

-- --------------------------------------------------------

--
-- Table structure for table `stock_transactions`
--

CREATE TABLE `stock_transactions` (
  `id` int(11) NOT NULL,
  `product_color_size_id` int(11) NOT NULL COMMENT 'รหัส variant (สี-ไซส์)',
  `product_id` bigint(20) DEFAULT NULL,
  `id_stock` varchar(50) DEFAULT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `variant_name` varchar(255) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL COMMENT 'รหัสออเดอร์ (ถ้ามี)',
  `type` enum('in','out','adjust','reserve','release') NOT NULL COMMENT 'ประเภท: เข้า/ออก/ปรับ/จอง/ปล่อย',
  `quantity` int(11) NOT NULL COMMENT 'จำนวนที่เปลี่ยนแปลง (+/-)',
  `quantity_before` int(11) NOT NULL COMMENT 'สต็อกก่อนเปลี่ยนแปลง',
  `quantity_after` int(11) NOT NULL COMMENT 'สต็อกหลังเปลี่ยนแปลง',
  `reason` varchar(255) NOT NULL COMMENT 'เหตุผล/หมายเหตุ',
  `user_id` int(11) DEFAULT NULL COMMENT 'ผู้ทำรายการ (admin)',
  `user_name` varchar(100) DEFAULT NULL COMMENT 'ชื่อผู้ทำรายการ',
  `reference_number` varchar(100) DEFAULT NULL COMMENT 'เลขที่อ้างอิง (เช่น เลขออเดอร์)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='ประวัติการเปลี่ยนแปลงสต็อก';

--
-- Dumping data for table `stock_transactions`
--

INSERT INTO `stock_transactions` (`id`, `product_color_size_id`, `product_id`, `id_stock`, `product_name`, `variant_name`, `order_id`, `type`, `quantity`, `quantity_before`, `quantity_after`, `reason`, `user_id`, `user_name`, `reference_number`, `created_at`, `updated_at`) VALUES
(1, 25, NULL, NULL, NULL, NULL, NULL, 'reserve', -3, 20, 17, 'จองสต็อกสำหรับออเดอร์ ORD0023', NULL, NULL, 'ORD0023', '2025-11-02 03:37:23', NULL),
(2, 26, NULL, NULL, NULL, NULL, NULL, 'reserve', -3, 5, 2, 'จองสต็อกสำหรับออเดอร์ ORD0023', NULL, NULL, 'ORD0023', '2025-11-02 03:37:23', NULL),
(3, 25, NULL, NULL, NULL, NULL, NULL, 'release', 3, 17, 20, 'แก้ไขออเดอร์ (ออเดอร์ ORD0023)', NULL, NULL, 'ORD0023', '2025-11-02 03:37:38', NULL),
(4, 26, NULL, NULL, NULL, NULL, NULL, 'release', 3, 2, 5, 'แก้ไขออเดอร์ (ออเดอร์ ORD0023)', NULL, NULL, 'ORD0023', '2025-11-02 03:37:38', NULL),
(5, 25, NULL, NULL, NULL, NULL, NULL, 'reserve', -3, 20, 17, 'จองสต็อกสำหรับออเดอร์ ORD0023', NULL, NULL, 'ORD0023', '2025-11-02 03:37:38', NULL),
(6, 26, NULL, NULL, NULL, NULL, NULL, 'reserve', -3, 5, 2, 'จองสต็อกสำหรับออเดอร์ ORD0023', NULL, NULL, 'ORD0023', '2025-11-02 03:37:38', NULL),
(7, 20, NULL, NULL, NULL, NULL, NULL, 'in', 10, 100, 110, 'ตรวจนับสต็อก', NULL, 'ผู้ดูแลระบบ', NULL, '2025-11-02 03:50:37', NULL),
(8, 25, NULL, NULL, NULL, NULL, NULL, 'in', 3, 17, 20, 'คืนสินค้าจากลูกค้า', NULL, 'ผู้ดูแลระบบ', NULL, '2025-11-02 04:26:22', NULL),
(9, 25, NULL, NULL, NULL, NULL, NULL, 'in', 3, 20, 23, 'สินค้าเข้าใหม่', NULL, 'ผู้ดูแลระบบ', NULL, '2025-11-02 04:27:02', NULL),
(10, 25, NULL, NULL, NULL, NULL, NULL, 'out', -1, 23, 22, 'สินค้าหาย', NULL, 'ผู้ดูแลระบบ', NULL, '2025-11-02 04:27:12', NULL),
(11, 27, NULL, NULL, NULL, NULL, NULL, 'reserve', -1, 10, 9, 'จองสต็อกสำหรับออเดอร์ ORD00064', NULL, NULL, 'ORD00064', '2025-11-02 04:46:38', NULL),
(12, 28, NULL, NULL, NULL, NULL, NULL, 'reserve', -1, 10, 9, 'จองสต็อกสำหรับออเดอร์ ORD00064', NULL, NULL, 'ORD00064', '2025-11-02 04:46:38', NULL),
(13, 30, NULL, NULL, NULL, NULL, NULL, 'reserve', -1, 10, 9, 'จองสต็อกสำหรับออเดอร์ ORD00064', NULL, NULL, 'ORD00064', '2025-11-02 04:46:38', NULL),
(14, 27, NULL, NULL, NULL, NULL, NULL, 'release', 1, 9, 10, 'แก้ไขออเดอร์ (ออเดอร์ ORD0024)', NULL, NULL, 'ORD0024', '2025-11-02 04:51:16', NULL),
(15, 28, NULL, NULL, NULL, NULL, NULL, 'release', 1, 9, 10, 'แก้ไขออเดอร์ (ออเดอร์ ORD0024)', NULL, NULL, 'ORD0024', '2025-11-02 04:51:16', NULL),
(16, 30, NULL, NULL, NULL, NULL, NULL, 'release', 1, 9, 10, 'แก้ไขออเดอร์ (ออเดอร์ ORD0024)', NULL, NULL, 'ORD0024', '2025-11-02 04:51:16', NULL),
(17, 27, NULL, NULL, NULL, NULL, NULL, 'reserve', -2, 10, 8, 'จองสต็อกสำหรับออเดอร์ ORD0024', NULL, NULL, 'ORD0024', '2025-11-02 04:51:16', NULL),
(18, 28, NULL, NULL, NULL, NULL, NULL, 'reserve', -1, 10, 9, 'จองสต็อกสำหรับออเดอร์ ORD0024', NULL, NULL, 'ORD0024', '2025-11-02 04:51:16', NULL),
(19, 30, NULL, NULL, NULL, NULL, NULL, 'reserve', -1, 10, 9, 'จองสต็อกสำหรับออเดอร์ ORD0024', NULL, NULL, 'ORD0024', '2025-11-02 04:51:16', NULL),
(20, 31, NULL, NULL, NULL, NULL, NULL, 'reserve', -4, 10, 6, 'จองสต็อกสำหรับออเดอร์ ORD0024', NULL, NULL, 'ORD0024', '2025-11-02 04:51:16', NULL),
(21, 27, NULL, NULL, NULL, NULL, NULL, 'release', 2, 8, 10, 'แก้ไขออเดอร์ (ออเดอร์ ORD0024)', NULL, NULL, 'ORD0024', '2025-11-02 05:14:50', NULL),
(22, 28, NULL, NULL, NULL, NULL, NULL, 'release', 1, 9, 10, 'แก้ไขออเดอร์ (ออเดอร์ ORD0024)', NULL, NULL, 'ORD0024', '2025-11-02 05:14:50', NULL),
(23, 30, NULL, NULL, NULL, NULL, NULL, 'release', 1, 9, 10, 'แก้ไขออเดอร์ (ออเดอร์ ORD0024)', NULL, NULL, 'ORD0024', '2025-11-02 05:14:50', NULL),
(24, 31, NULL, NULL, NULL, NULL, NULL, 'release', 4, 6, 10, 'แก้ไขออเดอร์ (ออเดอร์ ORD0024)', NULL, NULL, 'ORD0024', '2025-11-02 05:14:50', NULL),
(25, 27, NULL, NULL, NULL, NULL, NULL, 'reserve', -2, 10, 8, 'จองสต็อกสำหรับออเดอร์ ORD0024', NULL, NULL, 'ORD0024', '2025-11-02 05:14:50', NULL),
(26, 28, NULL, NULL, NULL, NULL, NULL, 'reserve', -1, 10, 9, 'จองสต็อกสำหรับออเดอร์ ORD0024', NULL, NULL, 'ORD0024', '2025-11-02 05:14:50', NULL),
(27, 30, NULL, NULL, NULL, NULL, NULL, 'reserve', -4, 10, 6, 'จองสต็อกสำหรับออเดอร์ ORD0024', NULL, NULL, 'ORD0024', '2025-11-02 05:14:50', NULL),
(28, 27, NULL, NULL, NULL, NULL, NULL, 'release', 2, 8, 10, 'แก้ไขออเดอร์ (ออเดอร์ ORD0024)', NULL, NULL, 'ORD0024', '2025-11-02 05:16:44', NULL),
(29, 28, NULL, NULL, NULL, NULL, NULL, 'release', 1, 9, 10, 'แก้ไขออเดอร์ (ออเดอร์ ORD0024)', NULL, NULL, 'ORD0024', '2025-11-02 05:16:44', NULL),
(30, 30, NULL, NULL, NULL, NULL, NULL, 'release', 4, 6, 10, 'แก้ไขออเดอร์ (ออเดอร์ ORD0024)', NULL, NULL, 'ORD0024', '2025-11-02 05:16:44', NULL),
(31, 27, NULL, NULL, NULL, NULL, NULL, 'reserve', -8, 10, 2, 'จองสต็อกสำหรับออเดอร์ ORD0024', NULL, NULL, 'ORD0024', '2025-11-02 05:16:44', NULL),
(32, 28, NULL, NULL, NULL, NULL, NULL, 'reserve', -9, 10, 1, 'จองสต็อกสำหรับออเดอร์ ORD0024', NULL, NULL, 'ORD0024', '2025-11-02 05:16:44', NULL),
(33, 30, NULL, NULL, NULL, NULL, NULL, 'reserve', -6, 10, 4, 'จองสต็อกสำหรับออเดอร์ ORD0024', NULL, NULL, 'ORD0024', '2025-11-02 05:16:45', NULL),
(34, 30, NULL, NULL, NULL, NULL, NULL, 'in', 6, 4, 10, 'สินค้าหาย', NULL, 'ผู้ดูแลระบบ', NULL, '2025-11-02 05:17:47', NULL),
(35, 27, NULL, NULL, NULL, NULL, NULL, 'release', 8, 2, 10, 'แก้ไขออเดอร์ (ออเดอร์ ORD0024)', NULL, NULL, 'ORD0024', '2025-11-02 05:18:14', NULL),
(36, 28, NULL, NULL, NULL, NULL, NULL, 'release', 9, 1, 10, 'แก้ไขออเดอร์ (ออเดอร์ ORD0024)', NULL, NULL, 'ORD0024', '2025-11-02 05:18:14', NULL),
(37, 30, NULL, NULL, NULL, NULL, NULL, 'release', 6, 10, 16, 'แก้ไขออเดอร์ (ออเดอร์ ORD0024)', NULL, NULL, 'ORD0024', '2025-11-02 05:18:14', NULL),
(38, 27, NULL, NULL, NULL, NULL, NULL, 'reserve', -2, 10, 8, 'จองสต็อกสำหรับออเดอร์ ORD0024', NULL, NULL, 'ORD0024', '2025-11-02 05:18:14', NULL),
(39, 28, NULL, NULL, NULL, NULL, NULL, 'reserve', -1, 10, 9, 'จองสต็อกสำหรับออเดอร์ ORD0024', NULL, NULL, 'ORD0024', '2025-11-02 05:18:15', NULL),
(40, 30, NULL, NULL, NULL, NULL, NULL, 'reserve', -10, 16, 6, 'จองสต็อกสำหรับออเดอร์ ORD0024', NULL, NULL, 'ORD0024', '2025-11-02 05:18:15', NULL),
(41, 23, NULL, NULL, NULL, NULL, NULL, 'reserve', -1, 9, 8, 'สร้างออเดอร์ใหม่ (ออเดอร์ ORD0026)', NULL, NULL, 'ORD0026', '2025-11-23 01:47:13', NULL),
(42, 8, NULL, NULL, NULL, NULL, NULL, 'reserve', -1, 0, -1, 'สร้างออเดอร์ใหม่ (ออเดอร์ ORD0027)', NULL, NULL, 'ORD0027', '2025-11-26 01:35:31', NULL),
(43, 31, NULL, NULL, NULL, NULL, NULL, 'in', 10, 10, 20, '10', NULL, NULL, '10', '2025-12-22 01:44:10', NULL),
(44, 11, NULL, NULL, NULL, NULL, NULL, 'reserve', -1, 2, 1, 'สร้างออเดอร์ใหม่ (ออเดอร์ ORD0027)', NULL, NULL, 'ORD0027', '2025-12-22 03:00:43', NULL),
(45, 16, NULL, NULL, NULL, NULL, NULL, 'reserve', -1, 2, 1, 'สร้างออเดอร์ใหม่ (ออเดอร์ ORD0028)', NULL, NULL, 'ORD0028', '2025-12-22 03:02:32', NULL),
(46, 16, NULL, NULL, NULL, NULL, NULL, 'reserve', -1, 1, 0, 'สร้างออเดอร์ใหม่ (ออเดอร์ ORD0029)', NULL, NULL, 'ORD0029', '2026-01-04 01:19:34', NULL),
(47, 15, NULL, NULL, NULL, NULL, 1, 'reserve', -1, 7, 6, 'สร้างออเดอร์ใหม่ (ออเดอร์ ORD0001)', NULL, NULL, 'ORD0001', '2026-01-06 08:01:46', NULL),
(48, 35, NULL, NULL, NULL, NULL, NULL, 'in', 10, 1010, 1020, 'รับสินค้าเข้า (manual)', 1, NULL, NULL, '2026-01-11 23:15:21', NULL),
(49, 35, NULL, NULL, NULL, NULL, NULL, 'out', -120, 1020, 900, 'ตัดสต๊อค (manual)', 1, NULL, NULL, '2026-01-13 08:13:47', NULL),
(50, 35, NULL, NULL, NULL, NULL, NULL, 'in', 1, 900, 901, 'รับสินค้าเข้า (manual)', 1, NULL, NULL, '2026-01-13 08:24:49', NULL),
(51, 30, NULL, NULL, NULL, NULL, NULL, 'in', 1, 6, 7, 'หกหกห', 1, NULL, 'กหหกห', '2026-01-13 08:30:54', NULL),
(52, 35, NULL, NULL, NULL, NULL, NULL, 'out', -1, 901, 900, '10', 1, 'admin', '10', '2026-01-13 08:47:56', NULL),
(53, 35, NULL, NULL, NULL, NULL, NULL, 'out', -10, 900, 890, '11', 2, 'admin11', '11', '2026-01-13 23:07:35', NULL),
(54, 35, NULL, NULL, NULL, NULL, 8, 'reserve', -10, 870, 860, 'สร้างออเดอร์ใหม่ (ออเดอร์ ORD-20260114-0002)', 1, 'admin', 'ORD-20260114-0002', '2026-01-14 00:01:31', NULL),
(55, 37, NULL, NULL, NULL, NULL, NULL, 'in', 10, 10, 20, '10', 1, 'admin', '10', '2026-01-14 08:43:50', NULL),
(56, 37, NULL, NULL, NULL, NULL, 9, 'reserve', -5, 15, 10, 'สร้างออเดอร์ใหม่ (ออเดอร์ ORD-20260114-0003)', 1, 'admin', 'ORD-20260114-0003', '2026-01-14 08:44:48', NULL),
(57, 37, NULL, NULL, NULL, NULL, 9, 'reserve', -4, 11, 7, 'แก้ไขออเดอร์ (Reserve New) (ออเดอร์ ORD-20260114-0003)', 1, 'admin', 'ORD-20260114-0003', '2026-01-14 09:59:57', NULL),
(58, 37, NULL, NULL, NULL, NULL, 9, 'reserve', -4, 7, 3, 'แก้ไขออเดอร์ (Reserve New) (ออเดอร์ ORD-20260114-0003)', 1, 'admin', 'ORD-20260114-0003', '2026-01-14 10:01:30', NULL),
(61, 37, NULL, NULL, NULL, NULL, 9, 'reserve', -4, 3, -1, 'แก้ไขออเดอร์ (Reserve New) (ออเดอร์ ORD-20260114-0003)', 1, 'admin', 'ORD-20260114-0003', '2026-01-14 10:41:54', NULL),
(62, 37, NULL, NULL, NULL, NULL, NULL, 'in', 100, 3, 103, 'รับสินค้าเข้า (manual)', 1, 'admin', NULL, '2026-01-14 10:44:13', NULL),
(63, 37, NULL, NULL, NULL, NULL, 9, 'reserve', -4, 99, 95, 'แก้ไขออเดอร์ (Reserve New) (ออเดอร์ ORD-20260114-0003)', 1, 'admin', 'ORD-20260114-0003', '2026-01-14 10:44:27', NULL),
(64, 37, NULL, NULL, NULL, NULL, 9, 'reserve', -4, 95, 91, 'แก้ไขออเดอร์ (Reserve New) (ออเดอร์ ORD-20260114-0003)', 1, 'admin', 'ORD-20260114-0003', '2026-01-14 10:44:42', NULL),
(65, 35, NULL, NULL, NULL, NULL, 10, 'reserve', -1, 869, 868, 'สร้างออเดอร์ใหม่ (ออเดอร์ ORD-20260115-0001)', 1, 'admin', 'ORD-20260115-0001', '2026-01-14 21:13:41', NULL),
(66, 35, NULL, NULL, NULL, NULL, 11, 'reserve', -1, 868, 867, 'สร้างออเดอร์ใหม่ (ออเดอร์ ORD-20260115-0002)', 6, 'sales', 'ORD-20260115-0002', '2026-01-15 06:58:23', NULL),
(67, 38, NULL, NULL, NULL, NULL, NULL, 'reserve', -1, 9, 8, 'สร้างออเดอร์ใหม่ (ออเดอร์ ORD-20260118-0001)', 6, 'sales', 'ORD-20260118-0001', '2026-01-18 09:24:31', NULL),
(68, 38, NULL, NULL, NULL, NULL, NULL, 'release', 1, 10, 11, 'แก้ไขออเดอร์ (Release Old) (ออเดอร์ ORD-20260118-0001)', 6, 'sales', 'ORD-20260118-0001', '2026-01-18 09:26:05', NULL),
(69, 38, NULL, NULL, NULL, NULL, NULL, 'reserve', -5, 5, 0, 'แก้ไขออเดอร์ (Reserve New) (ออเดอร์ ORD-20260118-0001)', 6, 'sales', 'ORD-20260118-0001', '2026-01-18 09:26:05', NULL),
(70, 38, NULL, NULL, NULL, NULL, NULL, 'release', 5, 10, 15, 'แก้ไขออเดอร์ (Release Old) (ออเดอร์ ORD-20260118-0001)', 6, 'sales', 'ORD-20260118-0001', '2026-01-18 09:32:45', NULL),
(71, 38, NULL, NULL, NULL, NULL, NULL, 'reserve', -10, 0, -10, 'แก้ไขออเดอร์ (Reserve New) (ออเดอร์ ORD-20260118-0001)', 6, 'sales', 'ORD-20260118-0001', '2026-01-18 09:32:45', NULL),
(72, 38, NULL, NULL, NULL, NULL, 13, 'reserve', -1, 9, 8, 'สร้างออเดอร์ใหม่ (ออเดอร์ ORD-20260118-0001)', 6, 'sales', 'ORD-20260118-0001', '2026-01-18 09:37:43', NULL),
(73, 38, NULL, NULL, NULL, NULL, 13, 'release', 1, 10, 11, 'แก้ไขออเดอร์ (Release Old) (ออเดอร์ ORD-20260118-0001)', 1, 'admin', 'ORD-20260118-0001', '2026-01-18 22:48:49', NULL),
(74, 38, NULL, NULL, NULL, NULL, 13, 'reserve', -2, 8, 6, 'แก้ไขออเดอร์ (Reserve New) (ออเดอร์ ORD-20260118-0001)', 1, 'admin', 'ORD-20260118-0001', '2026-01-18 22:48:49', NULL),
(75, 11, NULL, NULL, NULL, NULL, 14, 'reserve', -1, 1, 0, 'สร้างออเดอร์ใหม่ (ออเดอร์ ORD-20260119-0001)', 1, 'admin', 'ORD-20260119-0001', '2026-01-18 23:06:09', NULL),
(76, 38, NULL, NULL, NULL, NULL, 15, 'reserve', -1, 7, 6, 'สร้างออเดอร์ใหม่ (ออเดอร์ ORD-20260119-0002)', 6, 'sales', 'ORD-20260119-0002', '2026-01-18 23:07:31', NULL),
(78, 35, NULL, NULL, NULL, NULL, 19, 'reserve', -100, 768, 668, 'สร้างออเดอร์ใหม่ (ออเดอร์ ORD-20260119-0003)', 6, 'sales', 'ORD-20260119-0003', '2026-01-18 23:27:11', NULL),
(79, 36, NULL, NULL, NULL, NULL, 19, 'reserve', -10, 0, -10, 'สร้างออเดอร์ใหม่ (ออเดอร์ ORD-20260119-0003)', 6, 'sales', 'ORD-20260119-0003', '2026-01-18 23:27:11', NULL),
(83, 35, NULL, NULL, NULL, NULL, 19, 'release', 100, 868, 968, 'แก้ไขออเดอร์ (Release Old) (ออเดอร์ ORD-20260119-0003)', 6, 'sales', 'ORD-20260119-0003', '2026-01-18 23:29:27', NULL),
(84, 36, NULL, NULL, NULL, NULL, 19, 'release', 10, 10, 20, 'แก้ไขออเดอร์ (Release Old) (ออเดอร์ ORD-20260119-0003)', 6, 'sales', 'ORD-20260119-0003', '2026-01-18 23:29:27', NULL),
(85, 35, NULL, NULL, NULL, NULL, 19, 'reserve', -100, 768, 668, 'แก้ไขออเดอร์ (Reserve New) (ออเดอร์ ORD-20260119-0003)', 6, 'sales', 'ORD-20260119-0003', '2026-01-18 23:29:27', NULL),
(86, 38, NULL, NULL, NULL, NULL, 21, 'reserve', -1, 6, 5, 'สร้างออเดอร์ใหม่ (ออเดอร์ ORD-20260119-0004)', 1, 'admin', 'ORD-20260119-0004', '2026-01-19 05:23:51', NULL),
(87, 36, NULL, NULL, NULL, NULL, NULL, 'in', 10, 10, 20, 'รับสินค้าเข้า (manual)', 1, 'admin', NULL, '2026-01-19 08:20:03', NULL),
(88, 36, NULL, NULL, NULL, NULL, NULL, 'in', 10, 20, 30, 'รับสินค้าเข้า (manual)', 1, 'admin', NULL, '2026-01-19 08:20:09', NULL),
(89, 36, NULL, NULL, NULL, NULL, 22, 'reserve', -10, 20, 10, 'สร้างออเดอร์ใหม่ (ออเดอร์ ORD-20260119-0005)', 1, 'admin', 'ORD-20260119-0005', '2026-01-19 08:20:52', NULL),
(90, 38, NULL, NULL, NULL, NULL, 25, 'reserve', -6, 0, -6, 'สร้างออเดอร์ใหม่ (ออเดอร์ ORD-20260121-0001)', 1, 'admin', 'ORD-20260121-0001', '2026-01-21 06:25:09', NULL),
(91, 36, NULL, NULL, NULL, NULL, 25, 'reserve', -6, 14, 8, 'สร้างออเดอร์ใหม่ (ออเดอร์ ORD-20260121-0001)', 1, 'admin', 'ORD-20260121-0001', '2026-01-21 06:25:09', NULL),
(94, 36, NULL, NULL, NULL, NULL, 26, 'reserve', -1, 13, 12, 'สร้างออเดอร์ใหม่ (ออเดอร์ ORD-20260121-0002)', 1, 'admin', 'ORD-20260121-0002', '2026-01-21 06:38:05', NULL),
(95, 36, NULL, NULL, NULL, NULL, 27, 'reserve', -1, 12, 11, 'สร้างออเดอร์ใหม่ (ออเดอร์ ORD-20260121-0003)', 1, 'admin', 'ORD-20260121-0003', '2026-01-21 07:00:52', NULL),
(96, 36, NULL, NULL, NULL, NULL, NULL, 'reserve', -1, 11, 10, 'สร้างออเดอร์ใหม่ (ออเดอร์ ORD-20260121-0004)', 1, 'admin', 'ORD-20260121-0004', '2026-01-21 07:01:07', NULL),
(97, 36, NULL, NULL, NULL, NULL, 29, 'reserve', -1, 11, 10, 'สร้างออเดอร์ใหม่ (ออเดอร์ ORD-20260121-0004)', 1, 'admin', 'ORD-20260121-0004', '2026-01-21 07:15:13', NULL),
(98, 36, NULL, NULL, NULL, NULL, 29, 'release', 1, 12, 13, 'แก้ไขออเดอร์ (Release Old) (ออเดอร์ ORD-20260121-0004)', 1, 'admin', 'ORD-20260121-0004', '2026-01-21 07:26:47', NULL),
(99, 36, NULL, NULL, NULL, NULL, 29, 'reserve', -1, 11, 10, 'แก้ไขออเดอร์ (Reserve New) (ออเดอร์ ORD-20260121-0004)', 1, 'admin', 'ORD-20260121-0004', '2026-01-21 07:26:47', NULL),
(100, 36, NULL, NULL, NULL, NULL, 29, 'release', 1, 12, 13, 'แก้ไขออเดอร์ (Release Old) (ออเดอร์ ORD-20260121-0004)', 1, 'admin', 'ORD-20260121-0004', '2026-01-21 07:27:19', NULL),
(101, 36, NULL, NULL, NULL, NULL, 29, 'reserve', -2, 10, 8, 'แก้ไขออเดอร์ (Reserve New) (ออเดอร์ ORD-20260121-0004)', 1, 'admin', 'ORD-20260121-0004', '2026-01-21 07:27:19', NULL),
(102, 36, NULL, NULL, NULL, NULL, NULL, 'in', 10, 30, 40, 'รับสินค้าเข้า (manual)', 1, 'admin', NULL, '2026-01-25 02:40:25', NULL),
(103, 36, NULL, NULL, NULL, NULL, NULL, 'in', 1, 40, 41, 'รับสินค้าเข้า (manual)', 1, 'admin', NULL, '2026-01-25 03:18:25', NULL),
(104, 36, NULL, NULL, NULL, NULL, 30, 'reserve', -1, 20, 19, 'สร้างออเดอร์ใหม่ (ออเดอร์ ORD-20260125-0001)', 1, 'admin', 'ORD-20260125-0001', '2026-01-25 07:16:22', NULL),
(105, 35, NULL, NULL, NULL, NULL, 30, 'reserve', -1, 767, 766, 'สร้างออเดอร์ใหม่ (ออเดอร์ ORD-20260125-0001)', 1, 'admin', 'ORD-20260125-0001', '2026-01-25 07:16:22', NULL),
(106, 36, NULL, NULL, NULL, NULL, 30, 'release', 1, 21, 22, 'แก้ไขออเดอร์ (Release Old) (ออเดอร์ ORD-20260125-0001)', 1, 'admin', 'ORD-20260125-0001', '2026-01-25 07:16:38', NULL),
(107, 35, NULL, NULL, NULL, NULL, 30, 'release', 1, 768, 769, 'แก้ไขออเดอร์ (Release Old) (ออเดอร์ ORD-20260125-0001)', 1, 'admin', 'ORD-20260125-0001', '2026-01-25 07:16:38', NULL),
(108, 36, NULL, NULL, NULL, NULL, 30, 'reserve', -10, 11, 1, 'แก้ไขออเดอร์ (Reserve New) (ออเดอร์ ORD-20260125-0001)', 1, 'admin', 'ORD-20260125-0001', '2026-01-25 07:16:38', NULL),
(109, 35, NULL, NULL, NULL, NULL, 30, 'reserve', -1, 767, 766, 'แก้ไขออเดอร์ (Reserve New) (ออเดอร์ ORD-20260125-0001)', 1, 'admin', 'ORD-20260125-0001', '2026-01-25 07:16:38', NULL),
(110, 40, NULL, NULL, NULL, NULL, NULL, 'in', 10, 10, 20, 'รับสินค้าเข้า (manual)', 1, 'admin', NULL, '2026-01-25 07:42:03', NULL),
(111, 40, NULL, NULL, NULL, NULL, NULL, 'out', -10, 20, 10, 'ตัดสต๊อค (manual)', 1, 'admin', NULL, '2026-01-25 07:42:07', NULL),
(112, 41, NULL, NULL, NULL, NULL, 31, 'reserve', -5, 5, 0, 'สร้างออเดอร์ใหม่ (ออเดอร์ ORD-20260125-0002)', 1, 'admin', 'ORD-20260125-0002', '2026-01-25 07:49:59', NULL),
(113, 41, NULL, NULL, NULL, NULL, 32, 'reserve', -1, 4, 3, 'สร้างออเดอร์ใหม่ (ออเดอร์ ORD-20260125-0003)', 1, 'admin', 'ORD-20260125-0003', '2026-01-25 07:53:07', NULL),
(115, 41, NULL, NULL, NULL, NULL, 32, 'release', 1, 5, 6, 'แก้ไขออเดอร์ (Release Old) (ออเดอร์ ORD-20260125-0003)', 1, 'admin', 'ORD-20260125-0003', '2026-01-25 07:54:30', NULL),
(116, 41, NULL, NULL, NULL, NULL, 32, 'reserve', -5, 0, -5, 'แก้ไขออเดอร์ (Reserve New) (ออเดอร์ ORD-20260125-0003)', 1, 'admin', 'ORD-20260125-0003', '2026-01-25 07:54:30', NULL),
(117, 41, NULL, NULL, NULL, NULL, 32, 'release', 5, 5, 10, 'แก้ไขออเดอร์ (Release Old) (ออเดอร์ ORD-20260125-0003)', 1, 'admin', 'ORD-20260125-0003', '2026-01-25 09:06:55', NULL),
(118, 41, NULL, NULL, NULL, NULL, 32, 'reserve', -5, 0, -5, 'แก้ไขออเดอร์ (Reserve New) (ออเดอร์ ORD-20260125-0003)', 1, 'admin', 'ORD-20260125-0003', '2026-01-25 09:06:55', NULL),
(119, 37, NULL, NULL, NULL, NULL, 32, 'reserve', -1, 94, 93, 'แก้ไขออเดอร์ (Reserve New) (ออเดอร์ ORD-20260125-0003)', 1, 'admin', 'ORD-20260125-0003', '2026-01-25 09:06:55', NULL),
(120, 41, NULL, NULL, NULL, NULL, 32, 'release', 5, 5, 10, 'แก้ไขออเดอร์ (Release Old) (ออเดอร์ ORD-20260125-0003)', 1, 'admin', 'ORD-20260125-0003', '2026-01-25 09:11:44', NULL),
(121, 37, NULL, NULL, NULL, NULL, 32, 'release', 1, 95, 96, 'แก้ไขออเดอร์ (Release Old) (ออเดอร์ ORD-20260125-0003)', 1, 'admin', 'ORD-20260125-0003', '2026-01-25 09:11:44', NULL),
(122, 41, NULL, NULL, NULL, NULL, 32, 'reserve', -5, 0, -5, 'แก้ไขออเดอร์ (Reserve New) (ออเดอร์ ORD-20260125-0003)', 1, 'admin', 'ORD-20260125-0003', '2026-01-25 09:11:44', NULL),
(123, 37, NULL, NULL, NULL, NULL, 32, 'reserve', -2, 93, 91, 'แก้ไขออเดอร์ (Reserve New) (ออเดอร์ ORD-20260125-0003)', 1, 'admin', 'ORD-20260125-0003', '2026-01-25 09:11:44', NULL),
(124, 41, NULL, NULL, NULL, NULL, 32, 'release', 5, 5, 10, 'แก้ไขออเดอร์ (Release Old) (ออเดอร์ ORD-20260125-0003)', 1, 'admin', 'ORD-20260125-0003', '2026-01-27 11:24:23', NULL),
(125, 37, NULL, NULL, NULL, NULL, 32, 'release', 2, 95, 97, 'แก้ไขออเดอร์ (Release Old) (ออเดอร์ ORD-20260125-0003)', 1, 'admin', 'ORD-20260125-0003', '2026-01-27 11:24:23', NULL),
(126, 41, NULL, NULL, NULL, NULL, 32, 'reserve', -5, 0, -5, 'แก้ไขออเดอร์ (Reserve New) (ออเดอร์ ORD-20260125-0003)', 1, 'admin', 'ORD-20260125-0003', '2026-01-27 11:24:23', NULL),
(127, 37, NULL, NULL, NULL, NULL, 32, 'reserve', -2, 93, 91, 'แก้ไขออเดอร์ (Reserve New) (ออเดอร์ ORD-20260125-0003)', 1, 'admin', 'ORD-20260125-0003', '2026-01-27 11:24:23', NULL),
(128, 36, NULL, NULL, NULL, NULL, 32, 'reserve', -1, 10, 9, 'แก้ไขออเดอร์ (Reserve New) (ออเดอร์ ORD-20260125-0003)', 1, 'admin', 'ORD-20260125-0003', '2026-01-27 11:24:23', NULL),
(129, 41, NULL, NULL, NULL, NULL, 32, 'release', 5, 5, 10, 'แก้ไขออเดอร์ (Release Old) (ออเดอร์ ORD-20260125-0003)', 1, 'admin', 'ORD-20260125-0003', '2026-01-27 11:24:24', NULL),
(130, 37, NULL, NULL, NULL, NULL, 32, 'release', 2, 95, 97, 'แก้ไขออเดอร์ (Release Old) (ออเดอร์ ORD-20260125-0003)', 1, 'admin', 'ORD-20260125-0003', '2026-01-27 11:24:24', NULL),
(131, 36, NULL, NULL, NULL, NULL, 32, 'release', 1, 11, 12, 'แก้ไขออเดอร์ (Release Old) (ออเดอร์ ORD-20260125-0003)', 1, 'admin', 'ORD-20260125-0003', '2026-01-27 11:24:24', NULL),
(132, 41, NULL, NULL, NULL, NULL, 32, 'reserve', -5, 0, -5, 'แก้ไขออเดอร์ (Reserve New) (ออเดอร์ ORD-20260125-0003)', 1, 'admin', 'ORD-20260125-0003', '2026-01-27 11:24:24', NULL),
(133, 37, NULL, NULL, NULL, NULL, 32, 'reserve', -2, 93, 91, 'แก้ไขออเดอร์ (Reserve New) (ออเดอร์ ORD-20260125-0003)', 1, 'admin', 'ORD-20260125-0003', '2026-01-27 11:24:24', NULL),
(134, 36, NULL, NULL, NULL, NULL, 32, 'reserve', -1, 10, 9, 'แก้ไขออเดอร์ (Reserve New) (ออเดอร์ ORD-20260125-0003)', 1, 'admin', 'ORD-20260125-0003', '2026-01-27 11:24:24', NULL),
(135, 35, NULL, NULL, NULL, NULL, 33, 'reserve', -1, 766, 765, 'สร้างออเดอร์ใหม่ (ออเดอร์ ORD-20260127-0001)', 1, 'admin', 'ORD-20260127-0001', '2026-01-27 11:25:02', NULL),
(136, 35, NULL, NULL, NULL, NULL, 34, 'reserve', -120, 646, 526, 'สร้างออเดอร์ใหม่ (ออเดอร์ ORD-20260127-0002)', 1, 'admin', 'ORD-20260127-0002', '2026-01-27 11:42:08', NULL),
(142, 35, NULL, NULL, NULL, NULL, 34, 'reserve', -121, 525, 404, 'แก้ไขออเดอร์ (Reserve New) (ออเดอร์ ORD-20260127-0002)', 1, 'admin', 'ORD-20260127-0002', '2026-01-27 12:34:32', NULL),
(143, 35, 93, 'qwert', 'dfsd', 'ดำ - 12', 34, 'out', -121, 760, 639, 'ตัดสต็อกจริง (จัดส่ง ORD-20260127-0002)', NULL, 'system', 'ORD-20260127-0002', '2026-01-27 12:34:32', '2026-01-27 12:34:32'),
(144, 35, 93, 'qwert', 'dfsd', 'ดำ - 12', 34, 'release', 121, 0, 0, 'ปล่อยจอง (จัดส่ง ORD-20260127-0002)', NULL, 'system', 'ORD-20260127-0002', '2026-01-27 12:34:32', '2026-01-27 12:34:32'),
(145, 35, NULL, NULL, NULL, NULL, 34, 'reserve', -151, 374, 223, 'แก้ไขออเดอร์ (Reserve New) (ออเดอร์ ORD-20260127-0002)', 1, 'admin', 'ORD-20260127-0002', '2026-01-27 12:34:52', NULL),
(146, 35, 93, 'qwert', 'dfsd', 'ดำ - 12', 34, 'out', -151, 639, 488, 'ตัดสต็อกจริง (จัดส่ง ORD-20260127-0002)', NULL, 'system', 'ORD-20260127-0002', '2026-01-27 12:34:52', '2026-01-27 12:34:52'),
(147, 35, 93, 'qwert', 'dfsd', 'ดำ - 12', 34, 'release', 151, 0, 0, 'ปล่อยจอง (จัดส่ง ORD-20260127-0002)', NULL, 'system', 'ORD-20260127-0002', '2026-01-27 12:34:52', '2026-01-27 12:34:52'),
(148, 35, NULL, NULL, NULL, NULL, 34, 'reserve', -1, 373, 372, 'แก้ไขออเดอร์ (Reserve New) (ออเดอร์ ORD-20260127-0002)', 1, 'admin', 'ORD-20260127-0002', '2026-01-27 12:49:27', NULL),
(149, 35, 93, 'qwert', 'dfsd', 'ดำ - 12', 34, 'release', 1, 488, 488, 'ปล่อยจอง (จัดส่ง ORD-20260127-0002)', NULL, 'system', 'ORD-20260127-0002', '2026-01-27 12:49:27', '2026-01-27 12:49:27'),
(150, 35, 93, 'qwert', 'dfsd', 'ดำ - 12', 34, 'out', -1, 488, 487, 'ตัดสต็อกจริง (จัดส่ง ORD-20260127-0002)', NULL, 'system', 'ORD-20260127-0002', '2026-01-27 12:49:27', '2026-01-27 12:49:27'),
(151, 41, 96, 'w0001', 'w0001', 'กอล์ฟ - 10', 32, 'release', 5, 10, 10, 'ปล่อยจอง (จัดส่ง ORD-20260125-0003)', NULL, 'system', 'ORD-20260125-0003', '2026-01-28 23:57:19', '2026-01-28 23:57:19'),
(152, 41, 96, 'w0001', 'w0001', 'กอล์ฟ - 10', 32, 'out', -5, 10, 5, 'ตัดสต็อกจริง (จัดส่ง ORD-20260125-0003)', NULL, 'system', 'ORD-20260125-0003', '2026-01-28 23:57:19', '2026-01-28 23:57:19'),
(153, 37, 95, 'z0001', 'qqqqqqq', '14', 32, 'release', 2, 95, 95, 'ปล่อยจอง (จัดส่ง ORD-20260125-0003)', NULL, 'system', 'ORD-20260125-0003', '2026-01-28 23:57:19', '2026-01-28 23:57:19'),
(154, 37, 95, 'z0001', 'qqqqqqq', '14', 32, 'out', -2, 95, 93, 'ตัดสต็อกจริง (จัดส่ง ORD-20260125-0003)', NULL, 'system', 'ORD-20260125-0003', '2026-01-28 23:57:19', '2026-01-28 23:57:19'),
(155, 36, 95, 'z0001', 'qqqqqqq', '10', 32, 'release', 1, 41, 41, 'ปล่อยจอง (จัดส่ง ORD-20260125-0003)', NULL, 'system', 'ORD-20260125-0003', '2026-01-28 23:57:19', '2026-01-28 23:57:19'),
(156, 36, 95, 'z0001', 'qqqqqqq', '10', 32, 'out', -1, 41, 40, 'ตัดสต็อกจริง (จัดส่ง ORD-20260125-0003)', NULL, 'system', 'ORD-20260125-0003', '2026-01-28 23:57:19', '2026-01-28 23:57:19'),
(157, 41, 96, 'w0001', 'w0001', 'กอล์ฟ - 10', 31, 'release', 5, 5, 5, 'ปล่อยจอง (จัดส่ง ORD-20260125-0002)', NULL, 'system', 'ORD-20260125-0002', '2026-01-28 23:58:11', '2026-01-28 23:58:11'),
(158, 41, 96, 'w0001', 'w0001', 'กอล์ฟ - 10', 31, 'out', -5, 5, 0, 'ตัดสต็อกจริง (จัดส่ง ORD-20260125-0002)', NULL, 'system', 'ORD-20260125-0002', '2026-01-28 23:58:11', '2026-01-28 23:58:11'),
(159, 44, NULL, NULL, NULL, NULL, NULL, 'in', 10, 10, 20, 'รับสินค้าเข้า (manual)', 1, 'admin', NULL, '2026-02-01 22:45:16', NULL),
(160, 44, NULL, NULL, NULL, NULL, NULL, 'out', -5, 20, 15, 'ตัดสต๊อค (manual)', 1, 'admin', NULL, '2026-02-01 22:45:22', NULL),
(161, 36, NULL, NULL, NULL, NULL, 35, 'reserve', -1, 9, 8, 'สร้างออเดอร์ใหม่ (ออเดอร์ ORD-20260202-0001)', 1, 'admin', 'ORD-20260202-0001', '2026-02-02 00:31:27', NULL),
(162, 35, NULL, NULL, NULL, NULL, 35, 'reserve', -1, 372, 371, 'สร้างออเดอร์ใหม่ (ออเดอร์ ORD-20260202-0001)', 1, 'admin', 'ORD-20260202-0001', '2026-02-02 00:31:27', NULL),
(165, 36, NULL, NULL, NULL, NULL, 35, 'release', 1, 10, 11, 'แก้ไขออเดอร์ (Release Old) (ออเดอร์ ORD-20260202-0001)', 1, 'admin', 'ORD-20260202-0001', '2026-02-02 14:22:11', NULL),
(166, 35, NULL, NULL, NULL, NULL, 35, 'release', 1, 373, 374, 'แก้ไขออเดอร์ (Release Old) (ออเดอร์ ORD-20260202-0001)', 1, 'admin', 'ORD-20260202-0001', '2026-02-02 14:22:11', NULL),
(167, 36, NULL, NULL, NULL, NULL, 35, 'reserve', -1, 9, 8, 'แก้ไขออเดอร์ (Reserve New) (ออเดอร์ ORD-20260202-0001)', 1, 'admin', 'ORD-20260202-0001', '2026-02-02 14:22:11', NULL),
(168, 35, NULL, NULL, NULL, NULL, 35, 'reserve', -1, 372, 371, 'แก้ไขออเดอร์ (Reserve New) (ออเดอร์ ORD-20260202-0001)', 1, 'admin', 'ORD-20260202-0001', '2026-02-02 14:22:11', NULL),
(169, 36, NULL, NULL, NULL, NULL, 36, 'reserve', -1, 8, 7, 'สร้างออเดอร์ใหม่ (ออเดอร์ ORD-20260204-0001)', 6, 'sales', 'ORD-20260204-0001', '2026-02-04 07:40:43', NULL),
(170, 35, NULL, NULL, NULL, NULL, 36, 'reserve', -1, 371, 370, 'สร้างออเดอร์ใหม่ (ออเดอร์ ORD-20260204-0001)', 6, 'sales', 'ORD-20260204-0001', '2026-02-04 07:40:43', NULL),
(171, 37, NULL, NULL, NULL, NULL, 36, 'reserve', -1, 92, 91, 'สร้างออเดอร์ใหม่ (ออเดอร์ ORD-20260204-0001)', 6, 'sales', 'ORD-20260204-0001', '2026-02-04 07:40:43', NULL),
(172, 36, NULL, NULL, NULL, NULL, 37, 'reserve', -1, 7, 6, 'สร้างออเดอร์ใหม่ (ออเดอร์ ORD-20260205-0001)', 1, 'admin', 'ORD-20260205-0001', '2026-02-05 00:22:07', NULL),
(173, 35, NULL, NULL, NULL, NULL, 37, 'reserve', -1, 370, 369, 'สร้างออเดอร์ใหม่ (ออเดอร์ ORD-20260205-0001)', 1, 'admin', 'ORD-20260205-0001', '2026-02-05 00:22:07', NULL),
(174, 36, NULL, NULL, NULL, NULL, 37, 'release', 1, 8, 9, 'แก้ไขออเดอร์ (Release Old) (ออเดอร์ ORD-20260205-0001)', 1, 'admin', 'ORD-20260205-0001', '2026-02-05 00:23:18', NULL),
(175, 35, NULL, NULL, NULL, NULL, 37, 'release', 1, 371, 372, 'แก้ไขออเดอร์ (Release Old) (ออเดอร์ ORD-20260205-0001)', 1, 'admin', 'ORD-20260205-0001', '2026-02-05 00:23:18', NULL),
(176, 36, NULL, NULL, NULL, NULL, 37, 'reserve', -1, 7, 6, 'แก้ไขออเดอร์ (Reserve New) (ออเดอร์ ORD-20260205-0001)', 1, 'admin', 'ORD-20260205-0001', '2026-02-05 00:23:19', NULL),
(177, 35, NULL, NULL, NULL, NULL, 37, 'reserve', -1, 370, 369, 'แก้ไขออเดอร์ (Reserve New) (ออเดอร์ ORD-20260205-0001)', 1, 'admin', 'ORD-20260205-0001', '2026-02-05 00:23:19', NULL),
(178, 42, NULL, NULL, NULL, NULL, 37, 'reserve', -1, 9, 8, 'แก้ไขออเดอร์ (Reserve New) (ออเดอร์ ORD-20260205-0001)', 1, 'admin', 'ORD-20260205-0001', '2026-02-05 00:23:19', NULL),
(179, 36, 95, 'z0001', 'qqqqqqq', '10', 37, 'release', 1, 40, 40, 'ปล่อยจอง (จัดส่ง ORD-20260205-0001)', NULL, 'system', 'ORD-20260205-0001', '2026-02-05 00:23:51', '2026-02-05 00:23:51'),
(180, 36, 95, 'z0001', 'qqqqqqq', '10', 37, 'out', -1, 40, 39, 'ตัดสต็อกจริง (จัดส่ง ORD-20260205-0001)', NULL, 'system', 'ORD-20260205-0001', '2026-02-05 00:23:51', '2026-02-05 00:23:51'),
(181, 35, 93, 'qwert', 'dfsd', 'ดำ - 12', 37, 'release', 1, 487, 487, 'ปล่อยจอง (จัดส่ง ORD-20260205-0001)', NULL, 'system', 'ORD-20260205-0001', '2026-02-05 00:23:51', '2026-02-05 00:23:51'),
(182, 35, 93, 'qwert', 'dfsd', 'ดำ - 12', 37, 'out', -1, 487, 486, 'ตัดสต็อกจริง (จัดส่ง ORD-20260205-0001)', NULL, 'system', 'ORD-20260205-0001', '2026-02-05 00:23:51', '2026-02-05 00:23:51'),
(183, 42, 96, 'w0001', 'w0001', 'กอล์ฟ - 12', 37, 'release', 1, 10, 10, 'ปล่อยจอง (จัดส่ง ORD-20260205-0001)', NULL, 'system', 'ORD-20260205-0001', '2026-02-05 00:23:51', '2026-02-05 00:23:51'),
(184, 42, 96, 'w0001', 'w0001', 'กอล์ฟ - 12', 37, 'out', -1, 10, 9, 'ตัดสต็อกจริง (จัดส่ง ORD-20260205-0001)', NULL, 'system', 'ORD-20260205-0001', '2026-02-05 00:23:51', '2026-02-05 00:23:51'),
(185, 36, NULL, NULL, NULL, NULL, NULL, 'in', 20, 39, 59, 'รับสินค้าเข้า (manual)', 1, 'admin', NULL, '2026-02-05 23:58:04', NULL),
(186, 36, NULL, NULL, NULL, NULL, NULL, 'out', -20, 59, 39, 'ตัดสต๊อค (manual)', 1, 'admin', NULL, '2026-02-05 23:58:14', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `id` int(11) NOT NULL,
  `tag_name` varchar(100) NOT NULL COMMENT 'ชื่อแท็ก'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`id`, `tag_name`) VALUES
(1, 'ลดราคาk'),
(2, 'ยอดนิยม'),
(3, 'ใหม่ล่าสุด'),
(4, 'ขายดี'),
(5, 'ลดราคา'),
(6, 'ยอดนิยม'),
(7, 'ใหม่ล่าสุด'),
(8, 'ขายดี'),
(10, 'เสื้อยืดลายการ์ตูน'),
(13, 'หก'),
(14, '้้้่่าาาาา');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user','stock','sales') NOT NULL DEFAULT 'user' COMMENT 'admin=ผู้ดูแล, stock=ฝ่ายคลัง, sales=ฝ่ายขาย, user=ทั่วไป'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`) VALUES
(1, 'admin', 'admin1@shop.com', '$2y$12$jxDrrAAiQykdZ96iC8euwOPILPSbJCvhgKHz66cyUM1IUSKifnvw2', 'admin'),
(2, 'admin11', 'user1@example.com', '$2y$12$u1Yoo5FPU7qoN/R/VbJLL.1KJXvxQYijHFRrAOjeSqrkIbiegMDnS', 'admin'),
(4, 'stock', 'stock@shop.com', '$2y$12$7IRYRXtLiPNLEgjDKgggneLfjdX442GOn5WC7i0SJuhBC/QBv.TTG', 'stock'),
(6, 'sales', 'salesqq@shop.com', '$2y$12$amedTjK4PhGLuOgZq9AUV.FWMs0jZGsA0dI8raBSHBRDcE1SrHqHi', 'sales'),
(7, 'sales2', 'sales2@shop.com', '$2y$12$FSDDRBdI89Q6lVrJPyeww.pXKsM.Z3nGzV0CDEGDnL4TiNdU4qLYS', 'sales'),
(9, 'goft', 'nattaasakxcxc@com.com', '$2y$12$G.aHQ4LHu4mknJPkwQggTOt1n68rBwNrLsEcw8RHNLa31Bmw1r7By', 'admin');

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_current_stock`
-- (See below for the actual view)
--
CREATE TABLE `v_current_stock` (
`variant_id` int(11)
,`product_id` int(11)
,`color_id` int(11)
,`size_id` int(11)
,`id_stock` varchar(5)
,`product_name` varchar(300)
,`color_name` varchar(50)
,`size_name` varchar(50)
,`current_stock` int(11)
,`reserved_stock` decimal(32,0)
,`available_stock` decimal(33,0)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_stock_movements`
-- (See below for the actual view)
--
CREATE TABLE `v_stock_movements` (
`id` int(11)
,`type` enum('in','out','adjust','reserve','release')
,`quantity` int(11)
,`quantity_before` int(11)
,`quantity_after` int(11)
,`reason` varchar(255)
,`user_name` varchar(100)
,`reference_number` varchar(100)
,`created_at` timestamp
,`product_id` int(20)
,`product_name` varchar(300)
,`id_stock` varchar(5)
,`color_name` varchar(50)
,`size_name` varchar(50)
,`variant_name` varchar(103)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_stock_reserved`
-- (See below for the actual view)
--
CREATE TABLE `v_stock_reserved` (
`variant_id` int(11)
,`reserved_qty` decimal(32,0)
);

-- --------------------------------------------------------

--
-- Structure for view `v_current_stock`
--
DROP TABLE IF EXISTS `v_current_stock`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_current_stock`  AS SELECT `pcs`.`id` AS `variant_id`, `pcs`.`product_id` AS `product_id`, `pcs`.`color_id` AS `color_id`, `pcs`.`size_id` AS `size_id`, `p`.`id_stock` AS `id_stock`, `p`.`name` AS `product_name`, coalesce(`c`.`name`,'') AS `color_name`, coalesce(`s`.`size_name`,'') AS `size_name`, `pcs`.`quantity` AS `current_stock`, coalesce((select sum(`sh`.`quantity`) from `stock_holds` `sh` where `sh`.`product_color_size_id` = `pcs`.`id` and `sh`.`status` = 'active'),0) AS `reserved_stock`, `pcs`.`quantity`- coalesce((select sum(`sh`.`quantity`) from `stock_holds` `sh` where `sh`.`product_color_size_id` = `pcs`.`id` and `sh`.`status` = 'active'),0) AS `available_stock` FROM (((`product_color_size` `pcs` join `products` `p` on(`p`.`id` = `pcs`.`product_id`)) left join `colors` `c` on(`c`.`id` = `pcs`.`color_id`)) left join `sizes` `s` on(`s`.`id` = `pcs`.`size_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `v_stock_movements`
--
DROP TABLE IF EXISTS `v_stock_movements`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_stock_movements`  AS SELECT `st`.`id` AS `id`, `st`.`type` AS `type`, `st`.`quantity` AS `quantity`, `st`.`quantity_before` AS `quantity_before`, `st`.`quantity_after` AS `quantity_after`, `st`.`reason` AS `reason`, `st`.`user_name` AS `user_name`, `st`.`reference_number` AS `reference_number`, `st`.`created_at` AS `created_at`, `p`.`id` AS `product_id`, `p`.`name` AS `product_name`, `p`.`id_stock` AS `id_stock`, coalesce(`c`.`name`,'ไม่ระบุสี') AS `color_name`, coalesce(`s`.`size_name`,'ไม่ระบุไซส์') AS `size_name`, concat(coalesce(`c`.`name`,'ไม่ระบุ'),' - ',coalesce(`s`.`size_name`,'ไม่ระบุ')) AS `variant_name` FROM ((((`stock_transactions` `st` join `product_color_size` `pcs` on(`st`.`product_color_size_id` = `pcs`.`id`)) join `products` `p` on(`pcs`.`product_id` = `p`.`id`)) left join `colors` `c` on(`pcs`.`color_id` = `c`.`id`)) left join `sizes` `s` on(`pcs`.`size_id` = `s`.`id`)) ORDER BY `st`.`created_at` DESC ;

-- --------------------------------------------------------

--
-- Structure for view `v_stock_reserved`
--
DROP TABLE IF EXISTS `v_stock_reserved`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_stock_reserved`  AS SELECT `stock_holds`.`product_color_size_id` AS `variant_id`, sum(`stock_holds`.`quantity`) AS `reserved_qty` FROM `stock_holds` WHERE `stock_holds`.`status` = 'active' GROUP BY `stock_holds`.`product_color_size_id` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `colors`
--
ALTER TABLE `colors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer_addresses`
--
ALTER TABLE `customer_addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_customer_address` (`customer_id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `incomes`
--
ALTER TABLE `incomes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `fk_orders_customer` (`customer_id`),
  ADD KEY `fk_orders_customer_address` (`customer_address_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`,`id_stock`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `product_colors`
--
ALTER TABLE `product_colors`
  ADD PRIMARY KEY (`product_id`,`color_id`),
  ADD KEY `color_id` (`color_id`);

--
-- Indexes for table `product_color_size`
--
ALTER TABLE `product_color_size`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_id` (`product_id`,`color_id`,`size_id`),
  ADD KEY `color_id` (`color_id`),
  ADD KEY `size_id` (`size_id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product_options`
--
ALTER TABLE `product_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_options_ibfk_1` (`product_id`);

--
-- Indexes for table `product_sizes`
--
ALTER TABLE `product_sizes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `size_id` (`size_id`);

--
-- Indexes for table `product_tags`
--
ALTER TABLE `product_tags`
  ADD PRIMARY KEY (`product_id`,`tag_id`),
  ADD KEY `tag_id` (`tag_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `key` (`key`);

--
-- Indexes for table `shipping_zones`
--
ALTER TABLE `shipping_zones`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sizes`
--
ALTER TABLE `sizes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stock_adjustments`
--
ALTER TABLE `stock_adjustments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pcs` (`product_color_size_id`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `stock_holds`
--
ALTER TABLE `stock_holds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_hold_pcs_status` (`product_color_size_id`,`status`),
  ADD KEY `idx_hold_order_status` (`order_id`,`status`);

--
-- Indexes for table `stock_transactions`
--
ALTER TABLE `stock_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pcs` (`product_color_size_id`),
  ADD KEY `idx_order` (`order_id`),
  ADD KEY `idx_type` (`type`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `colors`
--
ALTER TABLE `colors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `customer_addresses`
--
ALTER TABLE `customer_addresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `incomes`
--
ALTER TABLE `incomes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=340;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT COMMENT 'รหัสสินค้า', AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT for table `product_color_size`
--
ALTER TABLE `product_color_size`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `shipping_zones`
--
ALTER TABLE `shipping_zones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `sizes`
--
ALTER TABLE `sizes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `stock_adjustments`
--
ALTER TABLE `stock_adjustments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `stock_holds`
--
ALTER TABLE `stock_holds`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=122;

--
-- AUTO_INCREMENT for table `stock_transactions`
--
ALTER TABLE `stock_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=187;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `customer_addresses`
--
ALTER TABLE `customer_addresses`
  ADD CONSTRAINT `fk_customer_address` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `fk_orders_customer_address` FOREIGN KEY (`customer_address_id`) REFERENCES `customer_addresses` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `stock_adjustments`
--
ALTER TABLE `stock_adjustments`
  ADD CONSTRAINT `fk_stock_adj_pcs` FOREIGN KEY (`product_color_size_id`) REFERENCES `product_color_size` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stock_holds`
--
ALTER TABLE `stock_holds`
  ADD CONSTRAINT `fk_hold_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_hold_pcs` FOREIGN KEY (`product_color_size_id`) REFERENCES `product_color_size` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `stock_transactions`
--
ALTER TABLE `stock_transactions`
  ADD CONSTRAINT `fk_stock_trans_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_stock_trans_pcs` FOREIGN KEY (`product_color_size_id`) REFERENCES `product_color_size` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
