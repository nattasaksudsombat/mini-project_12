-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Jan 07, 2026 at 06:14 PM
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
(8, 'เครื่องประดับ'),
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
(1, 'แดง', '#ff0000'),
(2, 'น้ำเงิน', '#0000FF'),
(3, 'ดำ', '#000000'),
(5, 'เขียว', '#00FF00'),
(7, 'เหลือง', '#fff700'),
(8, 'เทา', '#787878'),
(10, 'ชมพูอ่อน', '#a34d4d'),
(11, 'ไ', '#8b4646'),
(12, 'เขียวหกห', '#3cb4a0');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'ชื่อลูกค้า',
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `payment_method` enum('bank_transfer','cash_on_delivery','credit_card','e_wallet') NOT NULL,
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
(3, 'TGZ- Evening TV', '0963874972', 'goft12345678@gmail.com', 'bank_transfer', 'facebook', '10', '2026-01-06 07:05:52', '2026-01-06 07:05:52');

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
(2, 3, 'บ้าน', '9999', '99999', '99999', '9999', '99999', '99999', '9999', '2026-01-06 07:05:52', '2026-01-06 07:05:52');

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
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `description`, `amount`, `date`, `category`, `created_at`, `updated_at`) VALUES
(53, 'กิน', 200.00, '2025-03-30', 'อาหารและเครื่องดื่ม', '2025-03-29 22:59:40', '2025-03-29 22:59:40'),
(54, 'เติมมัน', 500.00, '2025-03-30', 'การเดินทาง', '2025-03-29 23:00:37', '2025-03-29 23:00:37');

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
(57, '10', 10.00, '2026-01-06', 'โบนัส', '2026-01-06 08:09:31', '2026-01-06 08:09:31', NULL);

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
(2, '2025_03_15_061356_create_incomes_table', 1);

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
(5, 'ORD-20260107-0001', 2, 1, 'pending', 'pending', 10.00, 10.00, 10.00, 10.00, 10.00, 'กด', NULL, NULL, NULL, NULL, NULL, '2026-01-07 09:35:51', '2026-01-07 09:35:51');

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
(265, 1, 9, 'ครีม.', 'แดง S', 1, 10.00, 10.00, '2026-01-07 09:46:02', '2026-01-07 09:46:02', 1, 1);

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
(60, 1, 'DR102', 'เดรสลายเชอร์รี่หวาน', 'เดรสลายผลไม้สไตล์ญี่ปุ่น สีชมพูสดใส', 620.00, 350.00, 1, '2025-08-02 03:55:35', '2025-08-02 03:55:35'),
(61, 1, 'DR103', 'เดรสโบว์ใหญ่ชมพู', 'โบว์ใหญ่น่ารักเหมือนตุ๊กตา ใส่แล้วน่ารักมาก', 650.00, 380.00, 1, '2025-08-02 03:55:35', '2025-08-02 03:55:35'),
(62, 1, 'DR104', 'เดรสลายหัวใจจิ๋ว', 'เดรสคอกลมแขนตุ๊กตา ลายหัวใจสีแดง', 599.00, 340.00, 1, '2025-08-02 03:55:35', '2025-08-02 03:55:35'),
(63, 1, 'DR105', 'เดรสลูกไม้หวานละมุน', 'ลุคคุณหนูด้วยลูกไม้ขาวฟูฟ่อง ใส่ออกงานได้', 750.00, 420.00, 1, '2025-08-02 03:55:35', '2025-08-02 03:55:35'),
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
(92, 17, 'a9990', 'กอล์ฟ', '10', 10.00, 10.00, 1, '2026-01-07 08:55:25', '2026-01-07 08:55:25');

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
(30, 90, 3, 4, 6),
(31, 90, 3, 5, 20),
(32, 90, 8, 5, 100),
(33, 90, 11, 6, 10),
(34, 91, 3, 10, 9);

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
(52, 92, 'product_images/0RCKpUKX8ivRYxqvJDjaNy8WsbMgfudzpI6rEqBH.jpg', 0);

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
(55, 2);

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
(13, 'กหก'),
(14, 'หก');

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
(48, 15, 1, 1, 'active', NULL, '2026-01-06 15:01:46', '2026-01-06 15:01:46');

-- --------------------------------------------------------

--
-- Table structure for table `stock_transactions`
--

CREATE TABLE `stock_transactions` (
  `id` int(11) NOT NULL,
  `product_color_size_id` int(11) NOT NULL COMMENT 'รหัส variant (สี-ไซส์)',
  `order_id` int(11) DEFAULT NULL COMMENT 'รหัสออเดอร์ (ถ้ามี)',
  `type` enum('in','out','adjust','reserve','release') NOT NULL COMMENT 'ประเภท: เข้า/ออก/ปรับ/จอง/ปล่อย',
  `quantity` int(11) NOT NULL COMMENT 'จำนวนที่เปลี่ยนแปลง (+/-)',
  `quantity_before` int(11) NOT NULL COMMENT 'สต็อกก่อนเปลี่ยนแปลง',
  `quantity_after` int(11) NOT NULL COMMENT 'สต็อกหลังเปลี่ยนแปลง',
  `reason` varchar(255) NOT NULL COMMENT 'เหตุผล/หมายเหตุ',
  `user_id` int(11) DEFAULT NULL COMMENT 'ผู้ทำรายการ (admin)',
  `user_name` varchar(100) DEFAULT NULL COMMENT 'ชื่อผู้ทำรายการ',
  `reference_number` varchar(100) DEFAULT NULL COMMENT 'เลขที่อ้างอิง (เช่น เลขออเดอร์)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='ประวัติการเปลี่ยนแปลงสต็อก';

--
-- Dumping data for table `stock_transactions`
--

INSERT INTO `stock_transactions` (`id`, `product_color_size_id`, `order_id`, `type`, `quantity`, `quantity_before`, `quantity_after`, `reason`, `user_id`, `user_name`, `reference_number`, `created_at`) VALUES
(1, 25, NULL, 'reserve', -3, 20, 17, 'จองสต็อกสำหรับออเดอร์ ORD0023', NULL, NULL, 'ORD0023', '2025-11-02 03:37:23'),
(2, 26, NULL, 'reserve', -3, 5, 2, 'จองสต็อกสำหรับออเดอร์ ORD0023', NULL, NULL, 'ORD0023', '2025-11-02 03:37:23'),
(3, 25, NULL, 'release', 3, 17, 20, 'แก้ไขออเดอร์ (ออเดอร์ ORD0023)', NULL, NULL, 'ORD0023', '2025-11-02 03:37:38'),
(4, 26, NULL, 'release', 3, 2, 5, 'แก้ไขออเดอร์ (ออเดอร์ ORD0023)', NULL, NULL, 'ORD0023', '2025-11-02 03:37:38'),
(5, 25, NULL, 'reserve', -3, 20, 17, 'จองสต็อกสำหรับออเดอร์ ORD0023', NULL, NULL, 'ORD0023', '2025-11-02 03:37:38'),
(6, 26, NULL, 'reserve', -3, 5, 2, 'จองสต็อกสำหรับออเดอร์ ORD0023', NULL, NULL, 'ORD0023', '2025-11-02 03:37:38'),
(7, 20, NULL, 'in', 10, 100, 110, 'ตรวจนับสต็อก', NULL, 'ผู้ดูแลระบบ', NULL, '2025-11-02 03:50:37'),
(8, 25, NULL, 'in', 3, 17, 20, 'คืนสินค้าจากลูกค้า', NULL, 'ผู้ดูแลระบบ', NULL, '2025-11-02 04:26:22'),
(9, 25, NULL, 'in', 3, 20, 23, 'สินค้าเข้าใหม่', NULL, 'ผู้ดูแลระบบ', NULL, '2025-11-02 04:27:02'),
(10, 25, NULL, 'out', -1, 23, 22, 'สินค้าหาย', NULL, 'ผู้ดูแลระบบ', NULL, '2025-11-02 04:27:12'),
(11, 27, NULL, 'reserve', -1, 10, 9, 'จองสต็อกสำหรับออเดอร์ ORD00064', NULL, NULL, 'ORD00064', '2025-11-02 04:46:38'),
(12, 28, NULL, 'reserve', -1, 10, 9, 'จองสต็อกสำหรับออเดอร์ ORD00064', NULL, NULL, 'ORD00064', '2025-11-02 04:46:38'),
(13, 30, NULL, 'reserve', -1, 10, 9, 'จองสต็อกสำหรับออเดอร์ ORD00064', NULL, NULL, 'ORD00064', '2025-11-02 04:46:38'),
(14, 27, NULL, 'release', 1, 9, 10, 'แก้ไขออเดอร์ (ออเดอร์ ORD0024)', NULL, NULL, 'ORD0024', '2025-11-02 04:51:16'),
(15, 28, NULL, 'release', 1, 9, 10, 'แก้ไขออเดอร์ (ออเดอร์ ORD0024)', NULL, NULL, 'ORD0024', '2025-11-02 04:51:16'),
(16, 30, NULL, 'release', 1, 9, 10, 'แก้ไขออเดอร์ (ออเดอร์ ORD0024)', NULL, NULL, 'ORD0024', '2025-11-02 04:51:16'),
(17, 27, NULL, 'reserve', -2, 10, 8, 'จองสต็อกสำหรับออเดอร์ ORD0024', NULL, NULL, 'ORD0024', '2025-11-02 04:51:16'),
(18, 28, NULL, 'reserve', -1, 10, 9, 'จองสต็อกสำหรับออเดอร์ ORD0024', NULL, NULL, 'ORD0024', '2025-11-02 04:51:16'),
(19, 30, NULL, 'reserve', -1, 10, 9, 'จองสต็อกสำหรับออเดอร์ ORD0024', NULL, NULL, 'ORD0024', '2025-11-02 04:51:16'),
(20, 31, NULL, 'reserve', -4, 10, 6, 'จองสต็อกสำหรับออเดอร์ ORD0024', NULL, NULL, 'ORD0024', '2025-11-02 04:51:16'),
(21, 27, NULL, 'release', 2, 8, 10, 'แก้ไขออเดอร์ (ออเดอร์ ORD0024)', NULL, NULL, 'ORD0024', '2025-11-02 05:14:50'),
(22, 28, NULL, 'release', 1, 9, 10, 'แก้ไขออเดอร์ (ออเดอร์ ORD0024)', NULL, NULL, 'ORD0024', '2025-11-02 05:14:50'),
(23, 30, NULL, 'release', 1, 9, 10, 'แก้ไขออเดอร์ (ออเดอร์ ORD0024)', NULL, NULL, 'ORD0024', '2025-11-02 05:14:50'),
(24, 31, NULL, 'release', 4, 6, 10, 'แก้ไขออเดอร์ (ออเดอร์ ORD0024)', NULL, NULL, 'ORD0024', '2025-11-02 05:14:50'),
(25, 27, NULL, 'reserve', -2, 10, 8, 'จองสต็อกสำหรับออเดอร์ ORD0024', NULL, NULL, 'ORD0024', '2025-11-02 05:14:50'),
(26, 28, NULL, 'reserve', -1, 10, 9, 'จองสต็อกสำหรับออเดอร์ ORD0024', NULL, NULL, 'ORD0024', '2025-11-02 05:14:50'),
(27, 30, NULL, 'reserve', -4, 10, 6, 'จองสต็อกสำหรับออเดอร์ ORD0024', NULL, NULL, 'ORD0024', '2025-11-02 05:14:50'),
(28, 27, NULL, 'release', 2, 8, 10, 'แก้ไขออเดอร์ (ออเดอร์ ORD0024)', NULL, NULL, 'ORD0024', '2025-11-02 05:16:44'),
(29, 28, NULL, 'release', 1, 9, 10, 'แก้ไขออเดอร์ (ออเดอร์ ORD0024)', NULL, NULL, 'ORD0024', '2025-11-02 05:16:44'),
(30, 30, NULL, 'release', 4, 6, 10, 'แก้ไขออเดอร์ (ออเดอร์ ORD0024)', NULL, NULL, 'ORD0024', '2025-11-02 05:16:44'),
(31, 27, NULL, 'reserve', -8, 10, 2, 'จองสต็อกสำหรับออเดอร์ ORD0024', NULL, NULL, 'ORD0024', '2025-11-02 05:16:44'),
(32, 28, NULL, 'reserve', -9, 10, 1, 'จองสต็อกสำหรับออเดอร์ ORD0024', NULL, NULL, 'ORD0024', '2025-11-02 05:16:44'),
(33, 30, NULL, 'reserve', -6, 10, 4, 'จองสต็อกสำหรับออเดอร์ ORD0024', NULL, NULL, 'ORD0024', '2025-11-02 05:16:45'),
(34, 30, NULL, 'in', 6, 4, 10, 'สินค้าหาย', NULL, 'ผู้ดูแลระบบ', NULL, '2025-11-02 05:17:47'),
(35, 27, NULL, 'release', 8, 2, 10, 'แก้ไขออเดอร์ (ออเดอร์ ORD0024)', NULL, NULL, 'ORD0024', '2025-11-02 05:18:14'),
(36, 28, NULL, 'release', 9, 1, 10, 'แก้ไขออเดอร์ (ออเดอร์ ORD0024)', NULL, NULL, 'ORD0024', '2025-11-02 05:18:14'),
(37, 30, NULL, 'release', 6, 10, 16, 'แก้ไขออเดอร์ (ออเดอร์ ORD0024)', NULL, NULL, 'ORD0024', '2025-11-02 05:18:14'),
(38, 27, NULL, 'reserve', -2, 10, 8, 'จองสต็อกสำหรับออเดอร์ ORD0024', NULL, NULL, 'ORD0024', '2025-11-02 05:18:14'),
(39, 28, NULL, 'reserve', -1, 10, 9, 'จองสต็อกสำหรับออเดอร์ ORD0024', NULL, NULL, 'ORD0024', '2025-11-02 05:18:15'),
(40, 30, NULL, 'reserve', -10, 16, 6, 'จองสต็อกสำหรับออเดอร์ ORD0024', NULL, NULL, 'ORD0024', '2025-11-02 05:18:15'),
(41, 23, NULL, 'reserve', -1, 9, 8, 'สร้างออเดอร์ใหม่ (ออเดอร์ ORD0026)', NULL, NULL, 'ORD0026', '2025-11-23 01:47:13'),
(42, 8, NULL, 'reserve', -1, 0, -1, 'สร้างออเดอร์ใหม่ (ออเดอร์ ORD0027)', NULL, NULL, 'ORD0027', '2025-11-26 01:35:31'),
(43, 31, NULL, 'in', 10, 10, 20, '10', NULL, NULL, '10', '2025-12-22 01:44:10'),
(44, 11, NULL, 'reserve', -1, 2, 1, 'สร้างออเดอร์ใหม่ (ออเดอร์ ORD0027)', NULL, NULL, 'ORD0027', '2025-12-22 03:00:43'),
(45, 16, NULL, 'reserve', -1, 2, 1, 'สร้างออเดอร์ใหม่ (ออเดอร์ ORD0028)', NULL, NULL, 'ORD0028', '2025-12-22 03:02:32'),
(46, 16, NULL, 'reserve', -1, 1, 0, 'สร้างออเดอร์ใหม่ (ออเดอร์ ORD0029)', NULL, NULL, 'ORD0029', '2026-01-04 01:19:34'),
(47, 15, 1, 'reserve', -1, 7, 6, 'สร้างออเดอร์ใหม่ (ออเดอร์ ORD0001)', NULL, NULL, 'ORD0001', '2026-01-06 08:01:46');

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
(1, 'ลดราคา'),
(2, 'ยอดนิยม'),
(3, 'ใหม่ล่าสุด'),
(4, 'ขายดี'),
(5, 'ลดราคา'),
(6, 'ยอดนิยม'),
(7, 'ใหม่ล่าสุด'),
(8, 'ขายดี'),
(9, 'ไก'),
(10, 'เสื้อยืดลายการ์ตูน');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`) VALUES
(1, 'admin', 'admin@example.com', '482c811da5d5b4bc6d497ffa98491e38'),
(2, 'user1', 'user1@example.com', '34819d7beeabb9260a5c854bc85b3e44'),
(3, 'user2', 'user2@example.com', '25d55ad283aa400af464c76d713c07ad');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `colors`
--
ALTER TABLE `colors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `customer_addresses`
--
ALTER TABLE `customer_addresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=266;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT COMMENT 'รหัสสินค้า', AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT for table `product_color_size`
--
ALTER TABLE `product_color_size`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `shipping_zones`
--
ALTER TABLE `shipping_zones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `sizes`
--
ALTER TABLE `sizes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `stock_adjustments`
--
ALTER TABLE `stock_adjustments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `stock_holds`
--
ALTER TABLE `stock_holds`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `stock_transactions`
--
ALTER TABLE `stock_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
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
