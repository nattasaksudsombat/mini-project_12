-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 20, 2025 at 09:38 AM
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
(6, 'รองเท้า'),
(8, 'เครื่องประดับ'),
(15, 'ลูกอม'),
(16, 'เสื้อ'),
(17, 'ครีมม'),
(18, 'ห');

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
(11, 'ไ', '#8b4646');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'ชื่อลูกค้า',
  `phone` varchar(20) DEFAULT NULL COMMENT 'เบอร์โทรศัพท์',
  `email` varchar(255) DEFAULT NULL COMMENT 'อีเมล',
  `address` text NOT NULL COMMENT 'ที่อยู่',
  `district` varchar(100) DEFAULT NULL COMMENT 'อำเภอ/เขต',
  `province` varchar(100) DEFAULT NULL COMMENT 'จังหวัด',
  `postal_code` varchar(10) DEFAULT NULL COMMENT 'รหัสไปรษณีย์',
  `payment_method` enum('bank_transfer','cash_on_delivery','credit_card','e_wallet') NOT NULL COMMENT 'วิธีการชำระเงิน',
  `purchase_channel` enum('facebook','line','website','shopee','lazada','offline') NOT NULL COMMENT 'ช่องทางการซื้อ',
  `notes` text DEFAULT NULL COMMENT 'หมายเหตุ',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `phone`, `email`, `address`, `district`, `province`, `postal_code`, `payment_method`, `purchase_channel`, `notes`, `created_at`, `updated_at`) VALUES
(7, 'ฟ', NULL, NULL, 'ฟ', NULL, NULL, NULL, 'bank_transfer', 'facebook', NULL, '2025-06-15 23:51:44', '2025-06-15 23:51:44'),
(8, '10', NULL, NULL, 'ฟผ', NULL, NULL, NULL, 'bank_transfer', 'facebook', NULL, '2025-06-15 23:52:54', '2025-06-15 23:52:54'),
(9, 'ฟห', NULL, NULL, 'หฟ', NULL, NULL, NULL, 'bank_transfer', 'facebook', NULL, '2025-06-15 23:53:21', '2025-06-15 23:53:21'),
(10, 'klk', NULL, NULL, 'klkl', NULL, NULL, NULL, 'bank_transfer', 'facebook', NULL, '2025-06-17 08:16:24', '2025-06-17 08:16:24'),
(12, '10', NULL, NULL, 'gb', NULL, NULL, NULL, 'bank_transfer', 'shopee', NULL, '2025-06-17 10:05:41', '2025-06-17 10:05:41'),
(13, 'ไำก', NULL, NULL, 'ไำ', NULL, NULL, NULL, 'bank_transfer', 'facebook', NULL, '2025-06-17 10:30:34', '2025-06-17 10:30:34'),
(15, 'หก', NULL, NULL, 'กหก', NULL, NULL, NULL, 'bank_transfer', 'facebook', NULL, '2025-06-17 10:59:50', '2025-06-17 10:59:50'),
(16, 'sd', NULL, NULL, 'sd', NULL, NULL, NULL, 'bank_transfer', 'facebook', NULL, '2025-06-17 11:03:43', '2025-06-17 11:03:43'),
(18, 's', NULL, NULL, 's', NULL, NULL, NULL, 'bank_transfer', 'facebook', NULL, '2025-06-17 11:24:55', '2025-06-17 11:24:55'),
(19, 'หก', NULL, NULL, 'หก', NULL, NULL, NULL, 'bank_transfer', 'facebook', NULL, '2025-06-17 11:30:54', '2025-06-17 11:30:54'),
(20, 'sd', NULL, NULL, 'sd', NULL, NULL, NULL, 'bank_transfer', 'facebook', NULL, '2025-06-17 11:32:14', '2025-06-17 11:32:14'),
(21, '10', NULL, NULL, 'sa', NULL, NULL, NULL, 'bank_transfer', 'facebook', NULL, '2025-06-17 11:40:04', '2025-06-17 11:40:04'),
(22, 'd', NULL, NULL, 'd', NULL, NULL, NULL, 'bank_transfer', 'facebook', NULL, '2025-06-18 22:43:00', '2025-06-18 22:43:00'),
(23, '10', NULL, NULL, 'หก', NULL, NULL, NULL, 'bank_transfer', 'facebook', NULL, '2025-06-18 23:54:00', '2025-06-18 23:54:00'),
(24, '10', NULL, NULL, 'ดะเ', NULL, NULL, NULL, 'bank_transfer', 'facebook', NULL, '2025-06-19 00:02:47', '2025-06-19 00:02:47'),
(25, 'sd', NULL, NULL, 'sd', NULL, NULL, NULL, 'bank_transfer', 'facebook', NULL, '2025-06-19 03:24:38', '2025-06-19 03:24:38');

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
(55, 'we', 10.00, '2025-06-08', 'อื่นๆ', '2025-06-08 02:00:48', '2025-06-08 02:00:48', NULL);

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
  `order_number` varchar(255) DEFAULT NULL,
  `customer_id` int(11) NOT NULL COMMENT 'รหัสลูกค้า',
  `status` enum('pending','confirmed','processing','shipped','delivered','cancelled') DEFAULT 'pending' COMMENT 'สถานะออเดอร์',
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `shipping_fee` decimal(10,2) DEFAULT 0.00 COMMENT 'ค่าจัดส่ง',
  `discount` decimal(10,2) DEFAULT 0.00 COMMENT 'ส่วนลด',
  `total_price` decimal(10,2) NOT NULL COMMENT 'ราคารวมทั้งหมด',
  `payment_status` enum('pending','paid','refunded') DEFAULT 'pending' COMMENT 'สถานะการชำระเงิน',
  `shipping_address` text NOT NULL COMMENT 'ที่อยู่จัดส่ง',
  `tracking_number` varchar(100) DEFAULT NULL COMMENT 'หมายเลขติดตาม',
  `notes` text DEFAULT NULL COMMENT 'หมายเหตุ',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `slip_image` varchar(255) DEFAULT NULL COMMENT 'ภาพสลิป'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_number`, `customer_id`, `status`, `subtotal`, `shipping_fee`, `discount`, `total_price`, `payment_status`, `shipping_address`, `tracking_number`, `notes`, `created_at`, `updated_at`, `slip_image`) VALUES
(18, 'ORD0001', 24, 'pending', 9496.00, 10.00, 0.00, 9506.00, 'pending', 'ดะเ', NULL, NULL, '2025-06-19 00:02:47', '2025-06-19 00:02:47', NULL),
(19, 'ORD0002', 25, 'pending', 2999.00, 0.00, 0.00, 2999.00, 'pending', 'sd', '10101010010', NULL, '2025-06-19 03:24:38', '2025-06-19 03:26:26', NULL);

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
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `variant_name`, `quantity`, `unit_price`, `total_price`, `created_at`, `updated_at`) VALUES
(15, 18, 1, 'เสื้อยืดลายการ์ตูน', 'แดง - M', 1, 2999.00, 2999.00, '2025-06-19 00:02:47', '2025-06-19 00:02:47'),
(16, 18, 1, 'เสื้อยืดลายการ์ตูน', 'เขียว - M', 1, 2999.00, 2999.00, '2025-06-19 00:02:47', '2025-06-19 00:02:47'),
(17, 18, 1, 'เสื้อยืดลายการ์ตูน', 'น้ำเงิน - M', 1, 2999.00, 2999.00, '2025-06-19 00:02:47', '2025-06-19 00:02:47'),
(18, 18, 6, 'เสื้อเชิ้ตแขนยาว', 'ชมพูอ่อน - L', 1, 499.00, 499.00, '2025-06-19 00:02:47', '2025-06-19 00:02:47'),
(19, 19, 1, 'เสื้อยืดลายการ์ตูน', 'แดง - M', 1, 2999.00, 2999.00, '2025-06-19 03:24:38', '2025-06-19 03:24:38');

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
(12, 1, 'P0003', 'น้ำส้มคั้น', 'น้ำส้มแท้ 100%', 25.00, 12.00, 0, '2025-06-13 16:24:10', '2025-06-13 16:24:10');

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
(1, 1, 1, 2, 2),
(2, 1, 3, 2, 0),
(5, 1, 5, 2, 3),
(6, 1, 2, 1, 4),
(7, 1, 1, 1, 4),
(8, 1, 2, 2, 5),
(9, 6, 10, 3, 1),
(10, 1, 3, 5, 0),
(11, 9, 1, 1, 0),
(12, 2, 1, 1, 0),
(13, 5, 1, 6, 0);

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
(37, 5, 'product_images/IMG_8573.jpeg', 1),
(38, 9, 'product_images/IMG_8484.jpeg', 1);

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
(5, 2);

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
(6, 'หกหก');

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
(9, 'ไก');

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

--
-- Indexes for dumped tables
--

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
  ADD KEY `customer_id` (`customer_id`);

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
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `colors`
--
ALTER TABLE `colors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `incomes`
--
ALTER TABLE `incomes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT COMMENT 'รหัสสินค้า', AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `product_color_size`
--
ALTER TABLE `product_color_size`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `shipping_zones`
--
ALTER TABLE `shipping_zones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `sizes`
--
ALTER TABLE `sizes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
