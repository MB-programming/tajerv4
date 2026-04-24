-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 24, 2026 at 11:07 PM
-- Server version: 10.11.16-MariaDB
-- PHP Version: 8.4.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tajagri_tajerv1`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'admin', '$2y$12$YmbuHJeQY90i0WovPZ0hUuBsceHMFoaH92gaENKpyj9ekIdD2SMs2', '2026-04-19 10:31:59'),
(2, 'minaboules', '$2y$10$MHs/hyKSc.krsrt.XwH07ud/NUS6Qh5djLrjjpC8esVM/XU3N27vW', '2026-04-19 10:49:31'),
(3, 'tajagri-wida', '$2y$10$nApHFJKuTJ3YfppOJ6D7Mua7SoTQYgb4CmwPmDOeAlSrhYIOcLPMK', '2026-04-20 13:26:17');

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` int(11) NOT NULL,
  `name_ar` varchar(255) NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `map_url` varchar(1000) DEFAULT NULL,
  `address_ar` varchar(500) DEFAULT NULL,
  `address_en` varchar(500) DEFAULT NULL,
  `working_hours_ar` varchar(255) DEFAULT NULL,
  `working_hours_en` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `status` enum('active','not_active') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `name_ar`, `name_en`, `phone`, `map_url`, `address_ar`, `address_en`, `working_hours_ar`, `working_hours_en`, `sort_order`, `status`, `created_at`, `updated_at`) VALUES
(97, 'الرياض', 'Riyadh', '', '', 'العزيزية – شارع السلام، الرياض', 'Al-Aziziyah – Al-Salam Street, Riyadh', 'السبت - الخميس: 8ص - 5م', 'Sat - Thu: 8AM - 5PM', 1, 'active', '2026-04-20 09:05:17', '2026-04-21 09:10:16'),
(98, 'الخرج', 'Al-Kharj', NULL, NULL, 'الخرج، المملكة العربية السعودية', 'Al-Kharj, Saudi Arabia', 'السبت - الخميس: 8ص - 5م', 'Sat - Thu: 8AM - 5PM', 2, 'active', '2026-04-20 09:05:17', '2026-04-20 09:05:17'),
(99, 'وادي الدواسر', 'Wadi Al-Dawasir', NULL, NULL, 'وادي الدواسر، المملكة العربية السعودية', 'Wadi Al-Dawasir, Saudi Arabia', 'السبت - الخميس: 8ص - 5م', 'Sat - Thu: 8AM - 5PM', 3, 'active', '2026-04-20 09:05:17', '2026-04-20 09:05:17'),
(100, 'خميس مشيط', 'Khamis Mushait', NULL, NULL, 'خميس مشيط، المملكة العربية السعودية', 'Khamis Mushait, Saudi Arabia', 'السبت - الخميس: 8ص - 5م', 'Sat - Thu: 8AM - 5PM', 4, 'active', '2026-04-20 09:05:17', '2026-04-20 09:05:17'),
(101, 'الدمام', 'Dammam', NULL, NULL, 'الدمام، المملكة العربية السعودية', 'Dammam, Saudi Arabia', 'السبت - الخميس: 8ص - 5م', 'Sat - Thu: 8AM - 5PM', 5, 'active', '2026-04-20 09:05:17', '2026-04-20 09:05:17'),
(102, 'القرية العليا', 'Al-Qaryah Al-Ulya', NULL, NULL, 'القرية العليا، المملكة العربية السعودية', 'Al-Qaryah Al-Ulya, Saudi Arabia', 'السبت - الخميس: 8ص - 5م', 'Sat - Thu: 8AM - 5PM', 6, 'active', '2026-04-20 09:05:17', '2026-04-20 09:05:17'),
(103, 'حرض', 'Harad', NULL, NULL, 'حرض، المملكة العربية السعودية', 'Harad, Saudi Arabia', 'السبت - الخميس: 8ص - 5م', 'Sat - Thu: 8AM - 5PM', 7, 'active', '2026-04-20 09:05:17', '2026-04-20 09:05:17'),
(104, 'المنطقة الغربية', 'Western Region', NULL, NULL, 'المنطقة الغربية، المملكة العربية السعودية', 'Western Region, Saudi Arabia', 'السبت - الخميس: 8ص - 5م', 'Sat - Thu: 8AM - 5PM', 8, 'active', '2026-04-20 09:05:17', '2026-04-20 09:05:17'),
(105, 'تبوك', 'Tabuk', NULL, NULL, 'تبوك، المملكة العربية السعودية', 'Tabuk, Saudi Arabia', 'السبت - الخميس: 8ص - 5م', 'Sat - Thu: 8AM - 5PM', 9, 'active', '2026-04-20 09:05:17', '2026-04-20 09:05:17'),
(106, 'حائل', 'Hail', NULL, NULL, 'حائل، المملكة العربية السعودية', 'Hail, Saudi Arabia', 'السبت - الخميس: 8ص - 5م', 'Sat - Thu: 8AM - 5PM', 10, 'active', '2026-04-20 09:05:17', '2026-04-20 09:05:17'),
(107, 'القصيم', 'Al-Qassim', NULL, NULL, 'القصيم، المملكة العربية السعودية', 'Al-Qassim, Saudi Arabia', 'السبت - الخميس: 8ص - 5م', 'Sat - Thu: 8AM - 5PM', 11, 'active', '2026-04-20 09:05:17', '2026-04-20 09:05:17'),
(108, 'طبرجل', 'Tabarjal', NULL, NULL, 'طبرجل، المملكة العربية السعودية', 'Tabarjal, Saudi Arabia', 'السبت - الخميس: 8ص - 5م', 'Sat - Thu: 8AM - 5PM', 12, 'active', '2026-04-20 09:05:17', '2026-04-20 09:05:17'),
(109, 'ساجر', 'Sajir', NULL, NULL, 'ساجر، المملكة العربية السعودية', 'Sajir, Saudi Arabia', 'السبت - الخميس: 8ص - 5م', 'Sat - Thu: 8AM - 5PM', 13, 'active', '2026-04-20 09:05:17', '2026-04-20 09:05:17');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `numbers`
--

CREATE TABLE `numbers` (
  `id` int(11) NOT NULL,
  `number` varchar(50) NOT NULL,
  `description_ar` varchar(255) DEFAULT NULL,
  `description_en` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `status` enum('active','not_active') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `numbers`
--

INSERT INTO `numbers` (`id`, `number`, `description_ar`, `description_en`, `sort_order`, `status`, `created_at`, `updated_at`) VALUES
(1, '+18', 'سنة خبرة', 'Years of Experience', 1, 'active', '2026-04-19 10:31:59', '2026-04-19 10:31:59'),
(2, '+500', 'عميل راضٍ', 'Satisfied Clients', 2, 'active', '2026-04-19 10:31:59', '2026-04-19 10:31:59'),
(3, '+50', 'منتج زراعي', 'Agricultural Products', 3, 'active', '2026-04-19 10:31:59', '2026-04-19 10:31:59'),
(4, '+10', 'فروع حول المملكة', 'Branches Across KSA', 4, 'active', '2026-04-19 10:31:59', '2026-04-19 10:31:59'),
(5, '+100', 'شريك تجاري', 'Business Partners', 5, 'active', '2026-04-19 10:31:59', '2026-04-19 10:31:59');

-- --------------------------------------------------------

--
-- Table structure for table `partners`
--

CREATE TABLE `partners` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  `status` enum('active','not_active') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `partners`
--

INSERT INTO `partners` (`id`, `name`, `image`, `sort_order`, `status`, `created_at`, `updated_at`) VALUES
(127, '642d6c7f10c3e', 'assets/uploads/69e6291272eea.png', 0, 'active', '2026-04-20 13:24:34', '2026-04-20 13:24:34'),
(128, '642d6c9aea7a2', 'assets/uploads/69e62912736c4.png', 0, 'active', '2026-04-20 13:24:34', '2026-04-20 13:24:34'),
(129, '642d6c9130c74', 'assets/uploads/69e6291273a31.png', 0, 'active', '2026-04-20 13:24:34', '2026-04-20 13:24:34'),
(130, '642d6c77198d3', 'assets/uploads/69e6291273d07.png', 0, 'active', '2026-04-20 13:24:34', '2026-04-20 13:24:34'),
(131, '642d6ca40f350', 'assets/uploads/69e6291273fc6.png', 0, 'active', '2026-04-20 13:24:34', '2026-04-20 13:24:34'),
(132, '642d6cad8415f', 'assets/uploads/69e629127426b.png', 0, 'active', '2026-04-20 13:24:34', '2026-04-20 13:24:34'),
(133, '642d6cb91ec5f', 'assets/uploads/69e62912744d2.png', 0, 'active', '2026-04-20 13:24:34', '2026-04-20 13:24:34'),
(134, '642d6cc2dd1b8', 'assets/uploads/69e6291274739.png', 0, 'active', '2026-04-20 13:24:34', '2026-04-20 13:24:34'),
(135, '642d6cdd8ad8b', 'assets/uploads/69e6291274a2d.png', 0, 'active', '2026-04-20 13:24:34', '2026-04-20 13:24:34'),
(136, '642d6ce7a5ecf', 'assets/uploads/69e6291274cb5.png', 0, 'active', '2026-04-20 13:24:34', '2026-04-20 13:24:34'),
(137, '642d6d5d1d88b', 'assets/uploads/69e6291274f3f.png', 0, 'active', '2026-04-20 13:24:34', '2026-04-20 13:24:34'),
(138, '642d6d6b0fefb', 'assets/uploads/69e62912751c4.png', 0, 'active', '2026-04-20 13:24:34', '2026-04-20 13:24:34'),
(139, '642d6d18ae96e', 'assets/uploads/69e629127546f.png', 0, 'active', '2026-04-20 13:24:34', '2026-04-20 13:24:34'),
(140, '642d6d45b83eb', 'assets/uploads/69e62912756ef.png', 0, 'active', '2026-04-20 13:24:34', '2026-04-20 13:24:34'),
(141, '642d6d2864e89', 'assets/uploads/69e629127596e.png', 0, 'active', '2026-04-20 13:24:34', '2026-04-20 13:24:34'),
(142, '642d6d383319a', 'assets/uploads/69e6291275bf7.png', 0, 'active', '2026-04-20 13:24:34', '2026-04-20 13:24:34'),
(143, '642d6d5190582', 'assets/uploads/69e6291275e8c.png', 0, 'active', '2026-04-20 13:24:34', '2026-04-20 13:24:34');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name_ar` varchar(255) NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `url` varchar(500) DEFAULT NULL,
  `order_url` varchar(500) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `status` enum('active','not_active') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `name_en_display` varchar(500) DEFAULT NULL COMMENT 'English display name shown on product page',
  `formula_type` varchar(50) DEFAULT NULL COMMENT 'e.g. W/W, W/V',
  `description_ar` text DEFAULT NULL COMMENT 'Formula/composition description (Arabic)',
  `description_en` text DEFAULT NULL COMMENT 'Formula/composition description (English)',
  `registration_number` varchar(100) DEFAULT NULL COMMENT 'e.g. F00004924',
  `registration_authority_ar` varchar(255) DEFAULT NULL COMMENT 'e.g. مسجل بوزارة البيئة والمياه والزراعة',
  `registration_authority_en` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name_ar`, `name_en`, `slug`, `image`, `url`, `order_url`, `sort_order`, `status`, `created_at`, `updated_at`, `name_en_display`, `formula_type`, `description_ar`, `description_en`, `registration_number`, `registration_authority_ar`, `registration_authority_en`) VALUES
(1, 'البذور والشتلات', 'Seeds & Seedlings', NULL, NULL, NULL, NULL, 1, 'active', '2026-04-19 10:31:59', '2026-04-19 10:31:59', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'الأسمدة', 'Fertilizers', NULL, NULL, NULL, NULL, 2, 'active', '2026-04-19 10:31:59', '2026-04-19 10:31:59', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'المبيدات', 'Pesticides', NULL, NULL, NULL, NULL, 3, 'active', '2026-04-19 10:31:59', '2026-04-19 10:31:59', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'معدات الري', 'Irrigation Equipment', NULL, NULL, NULL, NULL, 4, 'active', '2026-04-19 10:31:59', '2026-04-19 10:31:59', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 'التربة والبيئات', 'Soil & Substrates', NULL, NULL, NULL, NULL, 5, 'active', '2026-04-19 10:31:59', '2026-04-19 10:31:59', NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product_benefits`
--

CREATE TABLE `product_benefits` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `text_ar` text DEFAULT NULL,
  `text_en` text DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_benefits`
--

INSERT INTO `product_benefits` (`id`, `product_id`, `text_ar`, `text_en`, `sort_order`) VALUES
(13, 8, 'بي', NULL, 0),
(14, 0, 'dfdfdf', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `product_characteristics`
--

CREATE TABLE `product_characteristics` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `text_ar` text DEFAULT NULL,
  `text_en` text DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_characteristics`
--

INSERT INTO `product_characteristics` (`id`, `product_id`, `text_ar`, `text_en`, `sort_order`) VALUES
(10, 8, 'يب', NULL, 0),
(11, 0, 'fdd', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `product_chemicals`
--

CREATE TABLE `product_chemicals` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `name_ar` varchar(255) NOT NULL,
  `name_en` varchar(255) DEFAULT NULL,
  `percentage` varchar(50) DEFAULT NULL COMMENT 'e.g. 17.1%',
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_chemicals`
--

INSERT INTO `product_chemicals` (`id`, `product_id`, `name_ar`, `name_en`, `percentage`, `sort_order`) VALUES
(15, 8, 'يسسي', NULL, '21%', 0),
(16, 8, 'ewew', NULL, '32', 0),
(17, 8, 'fd', NULL, '33', 0),
(18, 8, 'ds', NULL, '44', 0),
(19, 0, 'fddf', NULL, '2323', 0);

-- --------------------------------------------------------

--
-- Table structure for table `product_features`
--

CREATE TABLE `product_features` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `icon` varchar(255) DEFAULT NULL COMMENT 'Image path or icon class',
  `label_ar` varchar(255) DEFAULT NULL,
  `label_en` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_features`
--

INSERT INTO `product_features` (`id`, `product_id`, `icon`, `label_ar`, `label_en`, `sort_order`) VALUES
(6, 8, 'assets/uploads/1776961760_  .jpeg', 'بي', NULL, 0),
(7, 0, 'assets/uploads/1777071721_Netlab Academy (4).png', 'dfdf', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `product_logos`
--

CREATE TABLE `product_logos` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `alt_ar` varchar(255) DEFAULT NULL,
  `alt_en` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_logos`
--

INSERT INTO `product_logos` (`id`, `product_id`, `image`, `alt_ar`, `alt_en`, `sort_order`) VALUES
(6, 8, 'assets/uploads/1776961760_38f1ce6c-bc1b-4114-a2b9-036d1b39fb05.jpeg', 'يبيب', NULL, 0),
(7, 0, 'assets/uploads/1777071721_Netlab Academy (4).png', 'fdfd', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `product_usages`
--

CREATE TABLE `product_usages` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `icon` varchar(255) DEFAULT NULL COMMENT 'Icon image path',
  `label_ar` varchar(255) DEFAULT NULL,
  `label_en` varchar(255) DEFAULT NULL,
  `value_ar` varchar(500) DEFAULT NULL,
  `value_en` varchar(500) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_usages`
--

INSERT INTO `product_usages` (`id`, `product_id`, `icon`, `label_ar`, `label_en`, `value_ar`, `value_en`, `sort_order`) VALUES
(6, 8, 'assets/uploads/1776961760_  .jpeg', 'بي', NULL, 'بي', NULL, 0),
(7, 0, 'assets/uploads/1777071721_Netlab Academy (4).png', 'fddf', NULL, '4343', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL DEFAULT 1,
  `logo` varchar(255) DEFAULT NULL,
  `home_image` varchar(255) DEFAULT NULL,
  `about_image` varchar(255) DEFAULT NULL,
  `branch_image` varchar(255) DEFAULT NULL,
  `footer_image` varchar(255) DEFAULT NULL,
  `address` varchar(500) DEFAULT NULL,
  `post_address` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `mobile` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `about_button_url` varchar(500) DEFAULT NULL,
  `about_ar` text DEFAULT NULL,
  `about_en` text DEFAULT NULL,
  `vision_ar` text DEFAULT NULL,
  `vision_en` text DEFAULT NULL,
  `message_ar` text DEFAULT NULL,
  `message_en` text DEFAULT NULL,
  `product_description_ar` text DEFAULT NULL,
  `product_description_en` text DEFAULT NULL,
  `numbers_description_ar` text DEFAULT NULL,
  `numbers_description_en` text DEFAULT NULL,
  `team_description_ar` text DEFAULT NULL,
  `team_description_en` text DEFAULT NULL,
  `description_ar` text DEFAULT NULL,
  `description_en` text DEFAULT NULL,
  `about_button_text_ar` varchar(255) DEFAULT NULL,
  `about_button_text_en` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `head_code` text DEFAULT NULL,
  `body_code` text DEFAULT NULL,
  `nav_json` text DEFAULT NULL,
  `address_en` varchar(255) DEFAULT NULL,
  `post_address_en` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `logo`, `home_image`, `about_image`, `branch_image`, `footer_image`, `address`, `post_address`, `phone`, `mobile`, `email`, `website`, `about_button_url`, `about_ar`, `about_en`, `vision_ar`, `vision_en`, `message_ar`, `message_en`, `product_description_ar`, `product_description_en`, `numbers_description_ar`, `numbers_description_en`, `team_description_ar`, `team_description_en`, `description_ar`, `description_en`, `about_button_text_ar`, `about_button_text_en`, `updated_at`, `head_code`, `body_code`, `nav_json`, `address_en`, `post_address_en`) VALUES
(1, NULL, NULL, 'assets/uploads/69e62a5060fc1.jpeg', NULL, NULL, 'شارع محمد بن سعد بن عبدالعزيز - الرياض', '300061 – الرياض: 11372', '920011666', '0500000000', 'info@tajagri.sa', 'www.tajagri.sa', 'https://tajagri.sa/', 'انطلقت شركة تاج الزراعية عام 2006 لتكون جزءاً من إسهامات القطاع الزراعي في تحسين الممارسات الزراعية، وزيادة الإنتاجية المحصولية، وتشجيع الزراعة الآمنة والمستدامة من خلال المنتجات النظيفة والصديقة للبيئة، وذلك بتوفير مدخلات زراعية عالية الجودة وحلول زراعية فنية متكاملة.', 'Taj Agri Company was launched in 2006 to become part of the agricultural sector\'s contribution to improving agricultural practices, increasing crop productivity, and encouraging safe and sustainable agriculture through clean and environmentally friendly products, by providing high-quality agricultural inputs and technical agricultural solutions.', 'أن نكون الشركة الرائدة في تقديم الحلول الزراعية المتكاملة في المملكة العربية السعودية، وأن نسهم في تحقيق التنمية الزراعية المستدامة.', 'To be the leading company in providing integrated agricultural solutions in Saudi Arabia, and to contribute to achieving sustainable agricultural development.', 'نؤمن بأن الزراعة المستدامة هي مستقبل المملكة وأساس أمنها الغذائي، ونسعى بكل طاقتنا لتوفير أفضل المنتجات والحلول الزراعية التي ترفع الإنتاجية وتحافظ على البيئة.', 'We believe that sustainable agriculture is the future of the Kingdom and the foundation of its food security. We strive with all our energy to provide the best agricultural products and solutions that increase productivity and preserve the environment.', 'نقدم مجموعة متكاملة من المدخلات الزراعية عالية الجودة لجميع احتياجاتك الزراعية.', 'We offer a comprehensive range of high-quality agricultural inputs for all your farming needs.', 'أرقام وإنجازات تعكس مسيرتنا وثقة عملائنا على مدار أكثر من 18 عاماً.', 'Numbers and achievements reflecting our journey and our clients\' trust over more than 18 years.', 'فريق متخصص من الخبراء الزراعيين يعمل بشغف لتقديم أفضل الحلول والخدمات لعملائنا.', 'A specialized team of agricultural experts working with passion to provide the best solutions and services to our clients.', 'شركة تاج الزراعية – شريكك الزراعي الموثوق منذ عام 2006. نقدم أفضل المدخلات الزراعية والحلول التقنية لتحقيق زراعة آمنة ومستدامة.', 'Taj Agri Company – Your trusted agricultural partner since 2006. We provide the best agricultural inputs and technical solutions for safe and sustainable farming.', 'تعرف على المزيد', 'Discover More', '2026-04-21 11:05:18', '<style>\r\n\r\n.item-team figure img {\r\n    display: none;\r\n}\r\n</style>', '', '[{\"label_ar\":\"منتجاتنا\",\"label_en\":\"Our Products\",\"href\":\"#products\",\"is_button\":false,\"enabled\":true},{\"label_ar\":\"شركاؤنا\",\"label_en\":\"Our Partners\",\"href\":\"#partners\",\"is_button\":false,\"enabled\":true},{\"label_ar\":\"فروعنا\",\"label_en\":\"Our Branches\",\"href\":\"#branches\",\"is_button\":false,\"enabled\":true},{\"label_ar\":\"عن تاج\",\"label_en\":\"About Taj Agri\",\"href\":\"#about-section\",\"is_button\":false,\"enabled\":true},{\"label_ar\":\"تواصل معنا\",\"label_en\":\"Contact Us\",\"href\":\"#footer\",\"is_button\":false,\"enabled\":true},{\"label_ar\":\"المتجر الإلكتروني\",\"label_en\":\"Online Store\",\"href\":\"https://tajagri.sa/\",\"is_button\":true,\"enabled\":true}]', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `teams`
--

CREATE TABLE `teams` (
  `id` int(11) NOT NULL,
  `name_ar` varchar(255) NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `description_ar` text DEFAULT NULL,
  `description_en` text DEFAULT NULL,
  `mobile` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `status` enum('active','not_active') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `teams`
--

INSERT INTO `teams` (`id`, `name_ar`, `name_en`, `description_ar`, `description_en`, `mobile`, `email`, `image`, `sort_order`, `status`, `created_at`, `updated_at`) VALUES
(1, 'أ / إبراهيم عبد العزيز الجريوي', 'Mr. Ibrahim Abdulaziz Al-Juraibi', 'المدير التنفيذي', 'Chief Executive Officer (CEO)', '0551233558', 'ibrahim@tajagri.sa', NULL, 0, 'active', '2026-04-19 10:31:59', '2026-04-21 09:06:04'),
(2, 'م / أسماعيل مفيد عرفة', 'Eng. Ismail Mufeed Arafah', 'مدير المبيعات', 'Sales Manager', '05581111246', 'sales@tajagri.sa', NULL, 0, 'active', '2026-04-19 10:31:59', '2026-04-21 09:07:10'),
(3, 'أ/ كريم الله بيباني', 'Mr. Kareem Allah Bibani', 'مدير الاستيراد والمشتريات', 'Import & Procurement Manager', '0539755330', 'purchasing@tajagri.sa', NULL, 0, 'active', '2026-04-19 10:31:59', '2026-04-21 09:08:01'),
(4, 'م / علاء أحمد نافع', 'Eng. Alaa Ahmed Nafea', 'الدعم الفني', 'Technical Support', '0503857531', 'alaanafea@tajagri.sa', NULL, 0, 'active', '2026-04-19 10:31:59', '2026-04-21 09:08:45');

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
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `numbers`
--
ALTER TABLE `numbers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `partners`
--
ALTER TABLE `partners`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `product_benefits`
--
ALTER TABLE `product_benefits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product_characteristics`
--
ALTER TABLE `product_characteristics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product_chemicals`
--
ALTER TABLE `product_chemicals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product_features`
--
ALTER TABLE `product_features`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product_logos`
--
ALTER TABLE `product_logos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product_usages`
--
ALTER TABLE `product_usages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `teams`
--
ALTER TABLE `teams`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `numbers`
--
ALTER TABLE `numbers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `partners`
--
ALTER TABLE `partners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=144;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `product_benefits`
--
ALTER TABLE `product_benefits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `product_characteristics`
--
ALTER TABLE `product_characteristics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `product_chemicals`
--
ALTER TABLE `product_chemicals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `product_features`
--
ALTER TABLE `product_features`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `product_logos`
--
ALTER TABLE `product_logos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `product_usages`
--
ALTER TABLE `product_usages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `teams`
--
ALTER TABLE `teams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
