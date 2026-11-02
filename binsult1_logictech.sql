-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 31, 2026 at 05:01 PM
-- Server version: 8.0.46
-- PHP Version: 8.3.36

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `binsult1_logictech`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` bigint UNSIGNED NOT NULL,
  `head_id` bigint UNSIGNED DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `opening_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `current_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Debit',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `head_id`, `title`, `account_code`, `opening_balance`, `current_balance`, `type`, `status`, `is_active`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Accounts Payable', 'AP', 0.00, -122440.00, 'Credit', 1, 1, '2026-10-14 10:19:30', '2026-10-31 10:29:46'),
(2, 1, 'BANK OFT', 'ACC-0002', 1500000.00, 2708000.00, 'Debit', 1, 1, '2026-10-16 07:09:59', '2026-10-31 10:34:40'),
(3, 2, 'CASH', 'ACC-0003', 500000.00, 1005000.00, 'Debit', 1, 1, '2026-10-16 07:15:22', '2026-10-30 19:05:03'),
(4, 1, 'LTE Meezan', 'ACC-0004', 1500000.00, 1400000.00, 'Debit', 1, 1, '2026-10-16 07:17:01', '2026-10-29 10:15:02'),
(5, NULL, 'Accounts Receivable', 'AR', 0.00, 12275402.00, 'Debit', 1, 1, '2026-10-17 12:44:49', '2026-10-31 11:49:59'),
(6, NULL, 'Sales Revenue', 'SALES', 0.00, -12367902.00, 'Credit', 1, 1, '2026-10-17 12:44:49', '2026-10-31 11:49:59'),
(7, NULL, 'Purchase Expense', 'PURCHASE', 0.00, 307440.00, 'Debit', 1, 1, '2026-10-17 13:17:10', '2026-10-31 10:29:46'),
(8, 3, 'General Expense', 'GEN-EXP', 0.00, 5000.00, 'Debit', 1, 1, '2026-10-19 06:36:16', '2026-10-19 06:36:16');

-- --------------------------------------------------------

--
-- Table structure for table `account_heads`
--

CREATE TABLE `account_heads` (
  `id` bigint UNSIGNED NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('Asset','Liability','Equity','Revenue','Expense') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `level` tinyint NOT NULL DEFAULT '1' COMMENT '1=Group, 2=Control, 3=Detail',
  `opening_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `account_heads`
--

INSERT INTO `account_heads` (`id`, `code`, `parent_id`, `name`, `type`, `level`, `opening_balance`, `created_at`, `updated_at`) VALUES
(1, NULL, NULL, 'BANK', NULL, 1, 0.00, '2026-10-14 11:19:15', '2026-10-14 11:19:15'),
(2, NULL, NULL, 'CASH', NULL, 1, 0.00, '2026-10-14 11:19:33', '2026-10-14 11:19:33'),
(3, NULL, NULL, 'Expense', NULL, 1, 0.00, '2026-10-19 06:36:16', '2026-10-19 06:36:16');

-- --------------------------------------------------------

--
-- Table structure for table `account_histories`
--

CREATE TABLE `account_histories` (
  `id` bigint UNSIGNED NOT NULL,
  `account_id` bigint UNSIGNED NOT NULL,
  `old_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `new_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `account_histories`
--

INSERT INTO `account_histories` (`id`, `account_id`, `old_balance`, `new_balance`, `user_id`, `user_name`, `note`, `created_at`, `updated_at`) VALUES
(1, 2, 0.00, 1500000.00, 2, 'Branch Manager Admin', 'Account created with initial balance: Rs 1,500,000.00', '2026-10-16 07:09:59', '2026-10-16 07:09:59'),
(2, 3, 0.00, 500000.00, 2, 'Branch Manager Admin', 'Account created with initial balance: Rs 500,000.00', '2026-10-16 07:15:22', '2026-10-16 07:15:22'),
(3, 4, 0.00, 1500000.00, 2, 'Branch Manager Admin', 'Account created with initial balance: Rs 1,500,000.00', '2026-10-16 07:17:01', '2026-10-16 07:17:01');

-- --------------------------------------------------------

--
-- Table structure for table `biometric_devices`
--

CREATE TABLE `biometric_devices` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `port` int NOT NULL DEFAULT '4370',
  `username` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `last_sync_at` timestamp NULL DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` bigint UNSIGNED NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, '1-PHASE 220V AC  50Hz/60Hz', '2026-10-17 11:46:19', '2026-10-17 11:46:19'),
(2, '3-PHASE 440V AC  50Hz/60Hz', '2026-10-17 11:46:25', '2026-10-17 11:46:25'),
(3, 'LTZ', '2026-10-17 13:07:53', '2026-10-17 13:07:53'),
(4, 'IGBT', '2026-10-19 08:39:37', '2026-10-19 08:39:37'),
(5, 'Coil', '2026-10-28 06:45:46', '2026-10-28 06:45:46'),
(6, 'Installation', '2026-10-28 12:19:17', '2026-10-28 12:19:17'),
(7, 'other', '2026-10-28 12:34:23', '2026-10-28 12:34:23');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `show_on_website` tinyint(1) NOT NULL DEFAULT '0',
  `web_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `created_at`, `updated_at`, `show_on_website`, `web_image`) VALUES
(1, 'Induction Heater', '2026-10-17 11:45:44', '2026-10-17 11:45:44', 0, NULL),
(2, 'PARTS', '2026-10-17 13:07:25', '2026-10-17 13:07:25', 0, NULL),
(3, 'Services', '2026-10-28 12:18:40', '2026-10-28 12:18:40', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` bigint UNSIGNED NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('fixed','percent') COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` decimal(10,2) NOT NULL,
  `min_spend` decimal(10,2) DEFAULT NULL,
  `max_uses` int DEFAULT NULL,
  `uses` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_synced` tinyint NOT NULL DEFAULT '0',
  `customer_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_name_ur` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cnic` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ntn` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `strn` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `filer_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_person` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_person_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_address_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sales_officer_id` bigint UNSIGNED DEFAULT NULL,
  `opening_balance` decimal(15,2) NOT NULL,
  `previous_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `balance_range` decimal(12,2) NOT NULL DEFAULT '0.00',
  `address` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `payment_reminder_date` date DEFAULT NULL,
  `reminder_day` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reminder_snoozed_at` date DEFAULT NULL,
  `source` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Manual'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `uuid`, `is_synced`, `customer_id`, `customer_name`, `customer_name_ur`, `cnic`, `ntn`, `strn`, `filer_type`, `zone`, `contact_person`, `mobile`, `email_address`, `contact_person_2`, `mobile_2`, `email_address_2`, `customer_type`, `sales_officer_id`, `opening_balance`, `previous_balance`, `balance_range`, `address`, `created_at`, `updated_at`, `status`, `payment_reminder_date`, `reminder_day`, `reminder_snoozed_at`, `source`) VALUES
(1, 'af7281f7-59ae-4b8c-8ad0-54100a6feb44', 0, 'CUST-0001', 'Zeeshan sb Karachi', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Main Customer', NULL, 80000.00, 0.00, 0.00, NULL, '2026-10-17 11:25:07', '2026-10-17 11:27:49', 'active', NULL, NULL, NULL, 'Manual'),
(2, 'a2ff3c24-7a70-4f5f-9c55-7d27f25c705f', 0, 'CUST-0002', 'ARSA Enterprises Sialkot', NULL, NULL, '0', NULL, NULL, NULL, NULL, '03316645506', NULL, NULL, NULL, NULL, 'Main Customer', NULL, 0.00, 350000.00, 0.00, 'Sialkot city', '2026-10-17 11:44:19', '2026-10-17 12:44:49', 'active', NULL, NULL, NULL, 'Manual'),
(3, '93ffde4f-e89d-420b-a9a7-3b8cb525bc21', 0, 'CUST-0003', 'Majid Sb, Tariq Sb Sialkot', NULL, NULL, '0', NULL, NULL, NULL, NULL, '0315-7894300', NULL, NULL, NULL, NULL, 'Main Customer', NULL, 0.00, 0.00, 0.00, 'Sialkot city', '2026-10-19 08:13:35', '2026-10-19 08:13:35', 'active', NULL, NULL, NULL, 'Manual'),
(4, '298026aa-ac53-4d51-b1b0-b6d4a9dcb12b', 0, 'CUST-0004', 'AJR METAL FABRICATORS (PVT.) LTD', NULL, NULL, '12664472', '0305870830464', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Main Customer', NULL, 0.00, 65500.00, 0.00, '16/17 Km, LHR-SKP Road, Suwa Misson Calor, Lahore -39250', '2026-10-19 10:23:19', '2026-10-28 06:53:47', 'active', NULL, NULL, NULL, 'Manual'),
(5, 'ebc79e6d-4393-4109-9c0a-82c18d865522', 0, 'CUST-0005', 'Qaisar Mughal sb Lahore', NULL, NULL, '0', '0', NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, 'Main Customer', NULL, 0.00, 0.00, 0.00, 'Lahore Daroghawala', '2026-10-19 12:45:51', '2026-10-19 12:45:51', 'active', NULL, NULL, NULL, 'Manual'),
(6, '25041069-53cf-4a1e-8f3d-8aa7011c3600', 0, 'CUST-0006', 'Excel Engineering pvt ltd', NULL, NULL, '0', NULL, NULL, '5', NULL, '03218414840', NULL, NULL, NULL, NULL, 'Main Customer', NULL, 507000.00, 0.00, 0.00, 'LHR SKP ROAD', '2026-10-20 18:30:43', '2026-10-29 06:12:37', 'active', NULL, 'Monday', NULL, 'Manual'),
(7, '01fb0675-281b-4b59-9903-43d8dd496e14', 0, 'CUST-0007', 'Bright Engineering Pvt Ltd', NULL, NULL, '0', NULL, NULL, '2', NULL, NULL, NULL, NULL, NULL, NULL, 'Main Customer', NULL, 0.00, 640000.00, 0.00, 'Lahore', '2026-10-21 05:55:02', '2026-10-28 20:07:20', 'active', NULL, NULL, NULL, 'Manual'),
(8, '7b494922-19b0-47b7-bca9-b084327596ea', 0, 'CUST-0008', 'Affaq Corporation Mandiali', NULL, NULL, '0', NULL, NULL, '5', NULL, '0', NULL, NULL, NULL, NULL, 'Main Customer', NULL, 0.00, 0.00, 0.00, 'Mandiali Lhr Road', '2026-10-21 08:28:07', '2026-10-21 08:28:07', 'active', NULL, NULL, NULL, 'Manual'),
(9, 'cb6c5e7b-5e84-4b0a-aa6d-f7feafc85834', 0, 'CUST-0009', 'Shafqat Sb Surgical Sialkot', NULL, NULL, '0', NULL, NULL, '1', NULL, '03216100018', NULL, NULL, NULL, NULL, 'Main Customer', NULL, 0.00, 0.00, 0.00, 'Sialkot city', '2026-10-21 09:38:37', '2026-10-30 12:12:20', 'active', NULL, NULL, NULL, 'Manual'),
(10, '82b6546c-b5c1-4df3-b6cd-097fbb5110a7', 0, 'CUST-0010', 'Naeem sb Sialkot', NULL, NULL, '0', '0', NULL, '1', NULL, '0', NULL, NULL, NULL, NULL, 'Main Customer', NULL, 0.00, 0.00, 0.00, 'Sialkot city', '2026-10-23 05:47:30', '2026-10-23 05:47:30', 'active', NULL, NULL, NULL, 'Manual'),
(11, '8fd24a79-4c50-4c3d-b8b0-c574d912fad8', 0, 'CUST-0011', 'Rajab Sb Sialkot', NULL, NULL, '0', '0', NULL, '1', NULL, '0302-4220493', NULL, NULL, NULL, NULL, 'Main Customer', NULL, 0.00, 0.00, 0.00, 'Sialkot city', '2026-10-24 07:55:40', '2026-10-24 07:55:40', 'active', NULL, NULL, NULL, 'Manual'),
(12, 'fdce71d9-f763-4c2f-85ad-ff22f601b890', 0, 'CUST-0012', 'Yasir Sb Faisalabad', NULL, NULL, '0', '0', NULL, NULL, NULL, '03027933020', NULL, NULL, NULL, NULL, 'Main Customer', NULL, 0.00, 480000.00, 0.00, 'Faisalabad', '2026-10-24 09:45:03', '2026-10-24 10:06:26', 'active', NULL, NULL, NULL, 'Manual'),
(13, 'e9400694-6084-47f6-acc4-14bfa47632c5', 0, 'CUST-0013', 'Irfan sb Wazirabad', NULL, NULL, NULL, NULL, NULL, '6', NULL, '03068774376', NULL, NULL, NULL, NULL, 'Main Customer', NULL, 0.00, 0.00, 0.00, NULL, '2026-10-24 11:49:52', '2026-10-24 11:49:52', 'active', NULL, NULL, NULL, 'Manual'),
(14, 'c431cf43-b41a-4e83-be54-e07863d64b0e', 0, 'CUST-0014', 'Lal Din Rickshaw Pvt Ltd.', NULL, NULL, '4232432-7', '4232432-7', NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, 'Main Customer', NULL, 0.00, 1227200.00, 0.00, '10.25 km of Sheikhupura Road,Near Dosaco Chowk, Lahore-Pakistan', '2026-10-24 18:46:08', '2026-10-24 18:52:36', 'active', NULL, NULL, NULL, 'Manual'),
(15, 'e5923fc9-760e-487d-8d99-e2948bdd5c5e', 0, 'CUST-0015', 'Rashid Sb Sialkot', NULL, NULL, NULL, NULL, NULL, '1', NULL, '03016177960', NULL, NULL, NULL, NULL, 'Main Customer', NULL, 0.00, 400000.00, 0.00, NULL, '2026-10-26 08:33:17', '2026-10-26 08:44:37', 'active', NULL, NULL, NULL, 'Manual'),
(16, '4c0e1122-9160-493c-ada4-c11810dd57ee', 0, 'CUST-0016', 'MIQRAZ', NULL, NULL, NULL, NULL, NULL, '1', NULL, '03018614766', NULL, NULL, NULL, NULL, 'Main Customer', NULL, 0.00, 580000.00, 0.00, NULL, '2026-10-26 11:10:32', '2026-10-26 12:00:04', 'active', NULL, NULL, NULL, 'Manual'),
(17, '995235cf-5960-42d4-8801-0d02f6276673', 0, 'CUST-0017', 'Syed Liaquat Shah', NULL, NULL, NULL, NULL, NULL, '7', NULL, '03215094341', NULL, NULL, NULL, NULL, 'Main Customer', NULL, 0.00, 0.00, 0.00, NULL, '2026-10-26 12:14:21', '2026-10-26 12:18:17', 'active', NULL, NULL, NULL, 'Manual'),
(18, '01598c4e-9479-4c14-a600-c026fb599a3c', 0, 'CUST-0018', 'Bead Tech Pvt Ltd.', NULL, NULL, NULL, NULL, NULL, '5', NULL, '03094377332', NULL, NULL, NULL, NULL, 'Main Customer', NULL, 18500.00, 0.00, 0.00, NULL, '2026-10-26 12:24:18', '2026-10-26 12:24:18', 'active', NULL, NULL, NULL, 'Manual'),
(19, 'd75afa76-4ea2-4629-b113-f92e87893e31', 0, 'CUST-0019', 'Shahid Sb', NULL, NULL, NULL, NULL, NULL, '5', NULL, '03008467357', NULL, NULL, NULL, NULL, 'Main Customer', NULL, 0.00, 0.00, 0.00, NULL, '2026-10-26 20:19:24', '2026-10-26 20:19:24', 'active', NULL, NULL, NULL, 'Manual'),
(20, '1aed64ec-7093-4b46-9a70-cea08178e3d8', 0, 'CUST-0020', 'Rana Atif sb Daska', NULL, NULL, NULL, NULL, NULL, '8', NULL, '03061718170', NULL, NULL, NULL, NULL, 'Main Customer', NULL, 125000.00, 1955000.00, 0.00, NULL, '2026-10-26 20:39:12', '2026-10-26 20:56:29', 'active', NULL, NULL, NULL, 'Manual'),
(21, '46b8d1f1-ed32-4419-b0c3-f3b62857703a', 0, 'CUST-0021', 'Zulfqar Sb Lahore', NULL, NULL, NULL, NULL, NULL, '2', NULL, '03007860943', NULL, NULL, NULL, NULL, 'Main Customer', NULL, 0.00, 0.00, 0.00, NULL, '2026-10-26 20:59:35', '2026-10-26 20:59:35', 'active', NULL, NULL, NULL, 'Manual'),
(22, 'e292883d-7b94-4667-a7b8-caacdcf3fbf6', 0, 'CUST-0022', 'Kashif Sb Gujranwala', NULL, NULL, NULL, NULL, NULL, '4', NULL, '03016660525', NULL, NULL, NULL, NULL, 'Main Customer', NULL, 0.00, 0.00, 0.00, NULL, '2026-10-26 21:10:11', '2026-10-26 21:10:11', 'active', NULL, NULL, NULL, 'Manual'),
(23, '8d094504-b46d-4ff5-b337-acc793338557', 0, 'CUST-0023', 'Abdul Moiz', NULL, NULL, NULL, NULL, NULL, '5', NULL, '03236621248', NULL, NULL, NULL, NULL, 'Main Customer', NULL, 0.00, 0.00, 0.00, NULL, '2026-10-26 21:19:06', '2026-10-26 21:19:06', 'active', NULL, NULL, NULL, 'Manual'),
(24, 'e1bb554d-d3ed-4783-8332-6351f024031e', 0, 'CUST-0024', 'Metlink Steel Industry', NULL, NULL, NULL, NULL, NULL, '3', NULL, '03391001168', NULL, NULL, NULL, NULL, 'Main Customer', NULL, 94500.00, 0.00, 0.00, NULL, '2026-10-27 08:28:12', '2026-10-27 08:28:12', 'active', NULL, NULL, NULL, 'Manual'),
(25, 'afd1edc8-bff9-4df9-80df-aba07f6ac419', 0, 'CUST-0025', 'Sufyan sb Sialkot', NULL, NULL, '0', NULL, NULL, '1', NULL, '03310432122', NULL, NULL, NULL, NULL, 'Main Customer', NULL, 380000.00, 0.00, 0.00, 'Sialkot city', '2026-10-27 09:11:47', '2026-10-27 09:11:47', 'active', NULL, NULL, NULL, 'Manual'),
(26, '53201f7e-31ca-454c-93d2-1a95d63a5abe', 0, 'CUST-0026', 'Mohsin Sb Gujranwala', NULL, NULL, '0', NULL, NULL, '4', NULL, NULL, NULL, NULL, NULL, NULL, 'Main Customer', NULL, 0.00, 0.00, 0.00, 'Gujranwala', '2026-10-28 06:11:43', '2026-10-28 06:11:43', 'active', NULL, NULL, NULL, 'Manual'),
(27, '814900bb-9bfc-4eb3-96e0-76541280b22f', 0, 'CUST-0027', 'Ishfaq Sb, Reactor Heating Lhr', NULL, NULL, '0', '0', NULL, NULL, NULL, '03004353172', NULL, NULL, NULL, NULL, 'Main Customer', NULL, 0.00, 950000.00, 0.00, 'SAGIYANWALA BYPASS LAHORE', '2026-10-28 11:34:55', '2026-10-28 12:41:09', 'active', NULL, NULL, NULL, 'Manual'),
(28, 'a166bc09-ca91-4dde-8746-56da9a2c135b', 0, 'CUST-0028', 'Tanzeel Sb Daska', NULL, NULL, NULL, NULL, NULL, '8', NULL, '03008746762', NULL, NULL, NULL, NULL, 'Main Customer', NULL, 0.00, 0.00, 0.00, NULL, '2026-10-28 11:51:13', '2026-10-28 11:51:13', 'active', NULL, NULL, NULL, 'Manual'),
(29, '2c063df8-954f-4c49-8ca7-41fbac8d3398', 0, 'CUST-0029', 'Najam Sb Daska', NULL, NULL, NULL, NULL, NULL, '8', NULL, '03006120064', NULL, NULL, NULL, NULL, 'Main Customer', NULL, 36500.00, 89000.00, 0.00, NULL, '2026-10-28 12:20:18', '2026-10-30 12:23:27', 'active', NULL, NULL, NULL, 'Manual'),
(30, '23522a4f-9cce-4d36-8f14-a019ecd58df7', 0, 'CUST-0030', 'Khaleel ur Rehman', NULL, NULL, NULL, NULL, NULL, '9', NULL, '03008303164', NULL, NULL, NULL, NULL, 'Main Customer', NULL, 64000.00, 0.00, 0.00, NULL, '2026-10-28 13:38:24', '2026-10-28 13:38:24', 'active', NULL, NULL, NULL, 'Manual'),
(31, 'eeb6494f-e851-4d95-8bfa-45e0b93c8ad9', 0, 'CUST-0031', 'Javed Sb Islamabad', NULL, NULL, NULL, NULL, NULL, '10', NULL, '03215365310', NULL, NULL, NULL, NULL, 'Main Customer', NULL, 330000.00, 0.00, 0.00, NULL, '2026-10-28 19:52:10', '2026-10-28 19:52:10', 'active', NULL, NULL, NULL, 'Manual'),
(32, '233838e6-9b14-404e-9b54-541cf7dbaf3e', 0, 'CUST-0032', 'Mughal Innovations', NULL, NULL, NULL, NULL, NULL, '5', NULL, '03218333337', NULL, NULL, NULL, NULL, 'Main Customer', NULL, 0.00, 0.00, 0.00, NULL, '2026-10-29 19:26:52', '2026-10-29 19:26:52', 'active', NULL, NULL, NULL, 'Manual'),
(33, 'ceb5a7f3-e34c-4c9d-961c-75af13a017a2', 0, 'CUST-0033', 'Danish Sb Gujranwala', NULL, NULL, '0', '0', NULL, '4', NULL, '03004720201', NULL, NULL, NULL, NULL, 'Main Customer', NULL, 0.00, 0.00, 0.00, 'Gujranwala', '2026-10-30 06:44:57', '2026-10-30 06:44:57', 'active', NULL, NULL, NULL, 'Manual'),
(34, 'bb342a77-c6fb-4da3-8762-4cf8b3f0f5a5', 0, 'CUST-0034', 'NATIONAL DEVELOPMENT COMPLEX/CENTRE (NDC)', NULL, NULL, '9010629-0', 'FTN: 9010629-0', NULL, '10', NULL, '03194380106', NULL, NULL, NULL, NULL, 'Main Customer', NULL, 0.00, 2173600.00, 0.00, 'Plot No. 94, NESCOM Complex (NDC), H-11/4, Islamabad', '2026-10-30 08:03:21', '2026-10-30 19:05:03', 'active', NULL, NULL, NULL, 'Manual'),
(35, 'ed4709a3-55d1-4c0c-b2e1-2127ae87660d', 0, 'CUST-0035', 'Sufyan bhai Daroghawala', NULL, NULL, '0', '0', NULL, '2', NULL, '03214298580', NULL, NULL, NULL, NULL, 'Main Customer', NULL, 85000.00, 0.00, 0.00, 'Mominpura Daroghawala Lhore', '2026-10-30 13:01:54', '2026-10-30 13:01:54', 'active', NULL, NULL, NULL, 'Manual'),
(36, 'bd191293-5386-4eee-986e-bf1d3915ae3e', 0, 'CUST-0036', 'Ravi Autos Sheikhupura Pvt Ltd', NULL, NULL, NULL, NULL, NULL, '5', NULL, '03084396792', NULL, NULL, NULL, NULL, 'Main Customer', NULL, 0.00, 0.00, 0.00, NULL, '2026-10-30 19:01:39', '2026-10-30 19:01:39', 'active', NULL, NULL, NULL, 'Manual'),
(37, 'f0eae324-f834-450d-8fb8-85b0abbad496', 0, 'CUST-0037', 'test', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Main Customer', NULL, 0.00, 2.00, 0.00, NULL, '2026-10-30 19:10:34', '2026-10-31 11:26:45', 'active', NULL, NULL, NULL, 'Manual'),
(38, 'f8572700-dde2-4d16-9ea5-02fe2ffb2985', 0, 'CUST-0038', 'Test', NULL, NULL, '0', '0', NULL, NULL, NULL, 'Test', NULL, NULL, NULL, NULL, 'Main Customer', NULL, 0.00, 0.00, 0.00, 'test', '2026-10-30 19:25:01', '2026-10-30 19:25:01', 'active', NULL, NULL, NULL, 'Manual'),
(39, '1a7dbd6b-9c88-4009-855b-88b583876b6d', 0, 'CUST-0039', 'Five Star Rim Industry Lahore Mustafa sb', NULL, NULL, NULL, NULL, NULL, '2', NULL, '0321555700', NULL, NULL, NULL, NULL, 'Main Customer', NULL, 0.00, 500000.00, 0.00, NULL, '2026-10-31 09:33:16', '2026-10-31 09:46:57', 'active', NULL, NULL, NULL, 'Manual'),
(40, '6254c876-7e45-4a1a-9aa8-e781570b85bf', 0, 'CUST-0040', 'Waqar sb Shakeel Sb Hyderabad', NULL, NULL, '0', '0', NULL, NULL, NULL, '03013582869', NULL, NULL, NULL, NULL, 'Main Customer', NULL, 0.00, 0.00, 0.00, 'Hyderabad', '2026-10-31 11:22:57', '2026-10-31 11:33:13', 'active', NULL, NULL, NULL, 'Manual');

-- --------------------------------------------------------

--
-- Table structure for table `customer_ledgers`
--

CREATE TABLE `customer_ledgers` (
  `id` bigint UNSIGNED NOT NULL,
  `customer_id` bigint UNSIGNED NOT NULL,
  `admin_or_user_id` bigint UNSIGNED NOT NULL,
  `previous_balance` decimal(12,2) NOT NULL,
  `closing_balance` decimal(12,2) NOT NULL,
  `opening_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customer_ledgers`
--

INSERT INTO `customer_ledgers` (`id`, `customer_id`, `admin_or_user_id`, `previous_balance`, `closing_balance`, `opening_balance`, `description`, `created_at`, `updated_at`) VALUES
(1, 2, 2, 0.00, 750000.00, 0.00, 'Sale Invoice #INV-0001', '2026-10-17 12:44:49', '2026-10-17 12:44:49'),
(2, 2, 2, 750000.00, 350000.00, 0.00, 'Receipt #RE-2026-0001 for Invoice #INV-0001', '2026-10-17 12:44:49', '2026-10-17 12:44:49'),
(4, 1, 1, 0.00, 80000.00, 80000.00, 'Opening Balance', '2026-10-17 11:25:07', '2026-10-20 19:43:03'),
(5, 12, 67, 0.00, 580000.00, 0.00, 'Sale Invoice #INV-0002', '2026-10-24 10:06:26', '2026-10-24 10:06:26'),
(6, 12, 67, 580000.00, 480000.00, 0.00, 'Receipt #RE-2026-0002 for Invoice #INV-0002', '2026-10-24 10:06:26', '2026-10-24 10:06:26'),
(7, 14, 67, 0.00, 1227200.00, 0.00, 'Sale Invoice #INV-0003', '2026-10-24 18:52:36', '2026-10-24 18:52:36'),
(8, 15, 68, 0.00, 400000.00, 0.00, 'Sale Invoice #INV-0004', '2026-10-26 08:40:14', '2026-10-26 08:40:14'),
(9, 16, 68, 0.00, 580000.00, 0.00, 'Sale Invoice #INV-0005', '2026-10-26 12:00:04', '2026-10-26 12:00:04'),
(10, 18, 68, 0.00, 18500.00, 18500.00, 'Opening Balance', '2026-10-26 12:24:18', '2026-10-26 12:24:18'),
(11, 20, 68, 0.00, 125000.00, 125000.00, 'Opening Balance', '2026-10-26 20:39:12', '2026-10-26 20:39:12'),
(12, 20, 68, 125000.00, 655000.00, 0.00, 'Sale Invoice #INV-0006', '2026-10-26 20:45:12', '2026-10-26 20:45:12'),
(13, 20, 68, 655000.00, 1185000.00, 0.00, 'Sale Invoice #INV-0007', '2026-10-26 20:47:12', '2026-10-26 20:47:12'),
(14, 20, 68, 1185000.00, 1955000.00, 0.00, 'Sale Invoice #INV-0008', '2026-10-26 20:56:29', '2026-10-26 20:56:29'),
(15, 24, 68, 0.00, 94500.00, 94500.00, 'Opening Balance', '2026-10-27 08:28:12', '2026-10-27 08:28:12'),
(16, 25, 67, 0.00, 380000.00, 380000.00, 'Opening Balance', '2026-10-27 09:11:47', '2026-10-27 09:11:47'),
(18, 4, 67, 54500.00, 65500.00, 0.00, 'Sale Invoice #INV-0009', '2026-10-28 06:53:47', '2026-10-28 06:53:47'),
(19, 29, 68, 0.00, 36500.00, 36500.00, 'Opening Balance', '2026-10-28 12:20:18', '2026-10-28 12:20:18'),
(20, 27, 67, 0.00, 950000.00, 0.00, 'Sale Invoice #INV-0010', '2026-10-28 12:41:09', '2026-10-28 12:41:09'),
(21, 30, 68, 0.00, 64000.00, 64000.00, 'Opening Balance', '2026-10-28 13:38:24', '2026-10-28 13:38:24'),
(25, 31, 68, 0.00, 330000.00, 330000.00, 'Opening Balance', '2026-10-28 19:52:10', '2026-10-28 19:52:10'),
(27, 7, 68, 0.00, 640000.00, 0.00, 'Sale Invoice #INV-0012', '2026-10-28 20:07:20', '2026-10-28 20:07:20'),
(28, 6, 68, 0.00, 507000.00, 507000.00, 'Opening Balance', '2026-10-20 18:30:43', '2026-10-29 07:26:32'),
(29, 27, 67, 950000.00, 600000.00, 0.00, 'Receipt Voucher RVID-002 | INV-0010', '2026-10-29 10:44:16', '2026-10-29 10:44:16'),
(33, 34, 67, 1486800.00, 1086800.00, 0.00, 'Receipt Voucher RVID-003 | INV-0013', '2026-10-30 10:34:18', '2026-10-30 10:34:18'),
(34, 30, 68, 64000.00, 22000.00, 0.00, 'Receipt Voucher RVID-004 | REP-00022', '2026-10-30 12:04:44', '2026-10-30 12:04:44'),
(35, 29, 68, 36500.00, 89000.00, 0.00, 'Sale Invoice #INV-0011', '2026-10-30 12:23:27', '2026-10-30 12:23:27'),
(36, 35, 67, 0.00, 85000.00, 85000.00, 'Opening Balance', '2026-10-30 13:01:54', '2026-10-30 13:01:54'),
(39, 34, 67, 1086800.00, 2573600.00, 0.00, 'Sale Invoice #INV-0013', '2026-10-30 19:05:03', '2026-10-30 19:05:03'),
(40, 34, 67, 2573600.00, 2173600.00, 0.00, 'Receipt #RE-2026-0006 for Invoice #INV-0013', '2026-10-30 19:05:03', '2026-10-30 19:05:03'),
(46, 23, 68, 0.00, -17500.00, 0.00, 'Receipt Voucher RVID-005 | REP-00018', '2026-10-31 07:51:39', '2026-10-31 07:51:39'),
(47, 33, 68, 0.00, -33000.00, 0.00, 'Receipt Voucher RVID-006 | REP-00027', '2026-10-31 08:33:53', '2026-10-31 08:33:53'),
(52, 39, 68, 0.00, 550000.00, 0.00, 'Sale Invoice #INV-0015', '2026-10-31 09:41:20', '2026-10-31 09:41:20'),
(53, 39, 68, 550000.00, 500000.00, 0.00, 'Receipt #RE-2026-0009 for Invoice #INV-0015', '2026-10-31 09:41:20', '2026-10-31 09:41:20'),
(54, 22, 68, 0.00, -20500.00, 0.00, 'Receipt Voucher RVID-007 | REP-00017', '2026-10-31 10:34:40', '2026-10-31 10:34:40'),
(55, 37, 67, 0.00, 2.00, 0.00, 'Sale Invoice #INV-0014', '2026-10-31 11:26:45', '2026-10-31 11:26:45');

-- --------------------------------------------------------

--
-- Table structure for table `customer_payments`
--

CREATE TABLE `customer_payments` (
  `id` bigint UNSIGNED NOT NULL,
  `customer_id` bigint UNSIGNED NOT NULL,
  `admin_or_user_id` bigint UNSIGNED NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `day_closings`
--

CREATE TABLE `day_closings` (
  `id` bigint UNSIGNED NOT NULL,
  `opening_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `inflow_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `outflow_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `expected_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `actual_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `difference` decimal(15,2) NOT NULL DEFAULT '0.00',
  `opened_at` timestamp NULL DEFAULT NULL,
  `closed_at` timestamp NULL DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `drawer_transactions`
--

CREATE TABLE `drawer_transactions` (
  `id` bigint UNSIGNED NOT NULL,
  `day_closing_id` bigint UNSIGNED NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'settled',
  `returned_in_closing_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ecommerce_orders`
--

CREATE TABLE `ecommerce_orders` (
  `id` bigint UNSIGNED NOT NULL,
  `order_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `web_customer_id` bigint UNSIGNED DEFAULT NULL,
  `subtotal` decimal(15,2) NOT NULL,
  `discount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `coupon_id` bigint UNSIGNED DEFAULT NULL,
  `delivery_charges` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total` decimal(15,2) NOT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `transaction_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_screenshot` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `courier_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tracking_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tracking_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paid_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `payment_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `order_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `is_stock_deducted` tinyint(1) NOT NULL DEFAULT '0',
  `order_notes` text COLLATE utf8mb4_unicode_ci,
  `shipping_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipping_phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipping_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipping_city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `coupon_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ecommerce_order_items`
--

CREATE TABLE `ecommerce_order_items` (
  `id` bigint UNSIGNED NOT NULL,
  `ecommerce_order_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED DEFAULT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `quantity` int NOT NULL,
  `size` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_salary_structures`
--

CREATE TABLE `employee_salary_structures` (
  `id` bigint UNSIGNED NOT NULL,
  `employee_id` bigint UNSIGNED NOT NULL,
  `salary_structure_id` bigint UNSIGNED NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_custom` tinyint(1) NOT NULL DEFAULT '0',
  `assigned_by` bigint UNSIGNED DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expense_categories`
--

CREATE TABLE `expense_categories` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expense_categories`
--

INSERT INTO `expense_categories` (`id`, `name`, `code`, `description`, `created_at`, `updated_at`) VALUES
(1, 'ENTERTAINMENT', 'ENTERTAINMENT', NULL, '2026-10-19 06:34:33', '2026-10-19 06:34:33');

-- --------------------------------------------------------

--
-- Table structure for table `expense_vouchers`
--

CREATE TABLE `expense_vouchers` (
  `id` bigint UNSIGNED NOT NULL,
  `evid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entry_date` date DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `party_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tel` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `reference_no` text COLLATE utf8mb4_unicode_ci,
  `narration_id` text COLLATE utf8mb4_unicode_ci,
  `row_account_head` text COLLATE utf8mb4_unicode_ci,
  `row_account_id` text COLLATE utf8mb4_unicode_ci,
  `amount` text COLLATE utf8mb4_unicode_ci,
  `total_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expense_vouchers`
--

INSERT INTO `expense_vouchers` (`id`, `evid`, `entry_date`, `type`, `party_id`, `tel`, `remarks`, `reference_no`, `narration_id`, `row_account_head`, `row_account_id`, `amount`, `total_amount`, `created_at`, `updated_at`) VALUES
(1, 'EVID-0001', '2026-10-19', '2', '3', 'ACC-0003', 'Expense paid through CASH', NULL, '[\"1\"]', '[\"0\"]', '[\"1\"]', '[\"5000\"]', 5000.00, '2026-10-19 06:36:16', '2026-10-19 06:36:16');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `grn_items`
--

CREATE TABLE `grn_items` (
  `id` bigint UNSIGNED NOT NULL,
  `grn_id` bigint UNSIGNED NOT NULL,
  `purchase_item_id` bigint UNSIGNED DEFAULT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `warehouse_id` bigint UNSIGNED NOT NULL,
  `ordered_qty` decimal(12,3) NOT NULL DEFAULT '0.000',
  `received_qty` decimal(12,3) NOT NULL DEFAULT '0.000',
  `accepted_qty` decimal(12,3) NOT NULL DEFAULT '0.000',
  `rejected_qty` decimal(12,3) NOT NULL DEFAULT '0.000',
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `batch_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `unit_price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `debit_note_created` tinyint(1) NOT NULL DEFAULT '0',
  `debit_note_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `grn_receipts`
--

CREATE TABLE `grn_receipts` (
  `id` bigint UNSIGNED NOT NULL,
  `grn_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `purchase_id` bigint UNSIGNED NOT NULL,
  `received_date` date NOT NULL,
  `delivery_challan_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `received_by` bigint UNSIGNED DEFAULT NULL,
  `store_incharge_id` bigint UNSIGNED DEFAULT NULL,
  `qc_status` enum('pending','passed','failed','partial') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `qc_notes` text COLLATE utf8mb4_unicode_ci,
  `status` enum('draft','confirmed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `stock_updated` tinyint(1) NOT NULL DEFAULT '0',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_attendances`
--

CREATE TABLE `hr_attendances` (
  `id` bigint UNSIGNED NOT NULL,
  `employee_id` bigint UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `check_in_time` time DEFAULT NULL,
  `check_out_time` time DEFAULT NULL,
  `check_in_photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `check_in_latitude` decimal(10,8) DEFAULT NULL,
  `check_in_longitude` decimal(11,8) DEFAULT NULL,
  `check_in_location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `check_out_photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `check_out_latitude` decimal(10,8) DEFAULT NULL,
  `check_out_longitude` decimal(11,8) DEFAULT NULL,
  `check_out_location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `clock_in` time DEFAULT NULL,
  `clock_out` time DEFAULT NULL,
  `status` enum('present','absent','late','leave') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'present',
  `is_late` tinyint(1) NOT NULL DEFAULT '0',
  `late_minutes` int NOT NULL DEFAULT '0',
  `is_early_in` tinyint(1) NOT NULL DEFAULT '0',
  `early_in_minutes` int NOT NULL DEFAULT '0',
  `is_early_leave` tinyint(1) NOT NULL DEFAULT '0',
  `early_leave_minutes` int NOT NULL DEFAULT '0',
  `total_hours` decimal(5,2) DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `device_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_departments`
--

CREATE TABLE `hr_departments` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hr_departments`
--

INSERT INTO `hr_departments` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'General', NULL, '2026-10-19 07:54:52', '2026-10-19 07:54:52');

-- --------------------------------------------------------

--
-- Table structure for table `hr_designations`
--

CREATE TABLE `hr_designations` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `requires_location` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hr_designations`
--

INSERT INTO `hr_designations` (`id`, `name`, `description`, `requires_location`, `created_at`, `updated_at`) VALUES
(1, 'General', NULL, 0, '2026-10-19 07:54:52', '2026-10-19 07:54:52');

-- --------------------------------------------------------

--
-- Table structure for table `hr_employees`
--

CREATE TABLE `hr_employees` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `department_id` bigint UNSIGNED NOT NULL,
  `designation_id` bigint UNSIGNED NOT NULL,
  `shift_id` bigint UNSIGNED DEFAULT NULL,
  `custom_start_time` time DEFAULT NULL,
  `custom_end_time` time DEFAULT NULL,
  `face_encoding` text COLLATE utf8mb4_unicode_ci,
  `face_photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `biometric_device_id` bigint UNSIGNED DEFAULT NULL,
  `device_user_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fingerprint_enrolled_at` timestamp NULL DEFAULT NULL,
  `last_device_sync_at` timestamp NULL DEFAULT NULL,
  `punch_gap_minutes` int UNSIGNED DEFAULT NULL,
  `pending_deductions` decimal(10,2) NOT NULL DEFAULT '0.00',
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `is_docs_submitted` tinyint(1) NOT NULL DEFAULT '0',
  `date_of_birth` date DEFAULT NULL,
  `joining_date` date NOT NULL,
  `status` enum('active','non-active','terminated') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hr_employees`
--

INSERT INTO `hr_employees` (`id`, `user_id`, `department_id`, `designation_id`, `shift_id`, `custom_start_time`, `custom_end_time`, `face_encoding`, `face_photo`, `biometric_device_id`, `device_user_id`, `fingerprint_enrolled_at`, `last_device_sync_at`, `punch_gap_minutes`, `pending_deductions`, `first_name`, `last_name`, `email`, `phone`, `address`, `is_docs_submitted`, `date_of_birth`, `joining_date`, `status`, `created_at`, `updated_at`) VALUES
(1, 58, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 'UMAR SULTAN', '', 'umarsultan_1792396492_105@logictech.local', '03137063274', NULL, 0, NULL, '2026-10-19', 'active', '2026-10-19 07:54:52', '2026-10-19 07:54:52'),
(2, 59, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 'QAMAR SULTAN', '', 'qamarsultan_1792396571_812@logictech.local', '03137063274', NULL, 0, NULL, '2026-10-19', 'active', '2026-10-19 07:56:11', '2026-10-19 07:56:11'),
(3, 60, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 'ATIF ALI', '', 'atifali_1792396617_435@logictech.local', '03094646885', NULL, 0, NULL, '2026-10-19', 'active', '2026-10-19 07:56:57', '2026-10-19 07:56:57'),
(4, 61, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 'ARSLAN BHATTI', '', 'arslanbhatti_1792396675_834@logictech.local', '03170084382', NULL, 0, NULL, '2026-10-19', 'active', '2026-10-19 07:57:55', '2026-10-19 07:57:55'),
(5, 62, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 'SHAHZADA', '', 'shahzada_1792397099_687@logictech.local', '03094737774', NULL, 0, NULL, '2026-10-19', 'active', '2026-10-19 08:04:59', '2026-10-19 08:04:59'),
(6, 63, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 'SHUJAT SHAH', '', 'shujatshah_1792397160_296@logictech.local', '03281782307', NULL, 0, NULL, '2026-10-19', 'active', '2026-10-19 08:06:01', '2026-10-19 08:06:01'),
(7, 64, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 'SHAZIM ALI', '', 'shazimali_1792397215_200@logictech.local', '03099825325', NULL, 0, NULL, '2026-10-19', 'active', '2026-10-19 08:06:55', '2026-10-19 08:06:55'),
(8, 65, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 'ZAKI', '', 'zaki_1792397242_301@logictech.local', '03474875592', NULL, 0, NULL, '2026-10-19', 'active', '2026-10-19 08:07:22', '2026-10-19 08:07:56'),
(9, 66, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 'MOHSIN OFFC', '', 'abdurrehman_1792397324_612@logictech.local', '03714367998', NULL, 0, NULL, '2026-10-19', 'active', '2026-10-19 08:08:44', '2026-10-19 08:11:48');

-- --------------------------------------------------------

--
-- Table structure for table `hr_employee_documents`
--

CREATE TABLE `hr_employee_documents` (
  `id` bigint UNSIGNED NOT NULL,
  `employee_id` bigint UNSIGNED NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_holidays`
--

CREATE TABLE `hr_holidays` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `type` enum('public','company','optional') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'public',
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_leaves`
--

CREATE TABLE `hr_leaves` (
  `id` bigint UNSIGNED NOT NULL,
  `employee_id` bigint UNSIGNED NOT NULL,
  `leave_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_loans`
--

CREATE TABLE `hr_loans` (
  `id` bigint UNSIGNED NOT NULL,
  `employee_id` bigint UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `installment_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Monthly deductible amount, 0 for manual/large sum',
  `status` enum('pending','approved','rejected','paid') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `reason` text COLLATE utf8mb4_unicode_ci,
  `paid_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_loan_payments`
--

CREATE TABLE `hr_loan_payments` (
  `id` bigint UNSIGNED NOT NULL,
  `loan_id` bigint UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` date NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'salary_deduction',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_loan_scheduled_deductions`
--

CREATE TABLE `hr_loan_scheduled_deductions` (
  `id` bigint UNSIGNED NOT NULL,
  `loan_id` bigint UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `deduction_month` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','deducted','skipped') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_payrolls`
--

CREATE TABLE `hr_payrolls` (
  `id` bigint UNSIGNED NOT NULL,
  `employee_id` bigint UNSIGNED NOT NULL,
  `payroll_type` enum('monthly','daily') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'monthly',
  `month` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `p_days` decimal(5,2) NOT NULL DEFAULT '30.00',
  `basic_salary` decimal(10,2) NOT NULL,
  `salary_count` decimal(10,2) NOT NULL DEFAULT '0.00',
  `overtime_hours` decimal(8,2) NOT NULL DEFAULT '0.00',
  `overtime_days` decimal(5,2) NOT NULL DEFAULT '0.00',
  `overtime_pay` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_pay` decimal(10,2) NOT NULL DEFAULT '0.00',
  `advances` decimal(10,2) NOT NULL DEFAULT '0.00',
  `previous_advance_balance` decimal(10,2) NOT NULL DEFAULT '0.00',
  `other_allowance` decimal(10,2) NOT NULL DEFAULT '0.00',
  `gross_salary` decimal(10,2) NOT NULL DEFAULT '0.00',
  `allowances` decimal(10,2) NOT NULL DEFAULT '0.00',
  `attendance_deductions` decimal(10,2) NOT NULL DEFAULT '0.00',
  `manual_deductions` decimal(10,2) NOT NULL DEFAULT '0.00',
  `manual_allowances` decimal(10,2) NOT NULL DEFAULT '0.00',
  `carried_forward_deduction` decimal(10,2) NOT NULL DEFAULT '0.00',
  `carried_forward_to_next` decimal(10,2) NOT NULL DEFAULT '0.00',
  `deductions` decimal(10,2) NOT NULL DEFAULT '0.00',
  `bonuses` decimal(10,2) NOT NULL DEFAULT '0.00',
  `net_salary` decimal(10,2) NOT NULL,
  `payment_this_month` decimal(10,2) NOT NULL DEFAULT '0.00',
  `closing_balance` decimal(10,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `auto_generated` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('generated','reviewed','paid') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'generated',
  `payment_date` date DEFAULT NULL,
  `account_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `reviewed_by` bigint UNSIGNED DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_payroll_details`
--

CREATE TABLE `hr_payroll_details` (
  `id` bigint UNSIGNED NOT NULL,
  `payroll_id` bigint UNSIGNED NOT NULL,
  `type` enum('allowance','deduction') COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_salary_structures`
--

CREATE TABLE `hr_salary_structures` (
  `id` bigint UNSIGNED NOT NULL,
  `parent_structure_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employee_id` bigint UNSIGNED DEFAULT NULL,
  `salary_type` enum('salary','commission','both') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'salary',
  `base_salary` decimal(12,2) NOT NULL DEFAULT '0.00',
  `daily_wages` decimal(10,2) DEFAULT NULL,
  `use_daily_wages` tinyint(1) NOT NULL DEFAULT '0',
  `commission_percentage` decimal(5,2) DEFAULT NULL,
  `sales_target` decimal(12,2) DEFAULT NULL,
  `commission_tiers` json DEFAULT NULL,
  `allowances` json DEFAULT NULL,
  `deductions` json DEFAULT NULL,
  `attendance_deduction_policy` json DEFAULT NULL,
  `carry_forward_deductions` tinyint(1) NOT NULL DEFAULT '0',
  `leave_salary_per_day` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hr_salary_structures`
--

INSERT INTO `hr_salary_structures` (`id`, `parent_structure_id`, `name`, `employee_id`, `salary_type`, `base_salary`, `daily_wages`, `use_daily_wages`, `commission_percentage`, `sales_target`, `commission_tiers`, `allowances`, `deductions`, `attendance_deduction_policy`, `carry_forward_deductions`, `leave_salary_per_day`, `created_at`, `updated_at`) VALUES
(1, NULL, NULL, 1, 'salary', 45000.00, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, '2026-10-19 07:54:52', '2026-10-19 07:54:52'),
(2, NULL, NULL, 2, 'salary', 40000.00, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, '2026-10-19 07:56:11', '2026-10-19 07:56:11'),
(3, NULL, NULL, 3, 'salary', 40000.00, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, '2026-10-19 07:56:57', '2026-10-19 07:56:57'),
(4, NULL, NULL, 4, 'salary', 33000.00, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, '2026-10-19 07:57:55', '2026-10-19 07:57:55'),
(5, NULL, NULL, 5, 'salary', 35000.00, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, '2026-10-19 08:04:59', '2026-10-19 08:04:59'),
(6, NULL, NULL, 6, 'salary', 28000.00, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, '2026-10-19 08:06:01', '2026-10-19 08:06:01'),
(7, NULL, NULL, 7, 'salary', 50000.00, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, '2026-10-19 08:06:55', '2026-10-19 08:06:55'),
(8, NULL, NULL, 8, 'salary', 30000.00, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, '2026-10-19 08:07:22', '2026-10-19 08:07:56'),
(9, NULL, NULL, 9, 'salary', 25000.00, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, '2026-10-19 08:08:44', '2026-10-19 08:11:48');

-- --------------------------------------------------------

--
-- Table structure for table `hr_settings`
--

CREATE TABLE `hr_settings` (
  `id` bigint UNSIGNED NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'string',
  `group` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_shifts`
--

CREATE TABLE `hr_shifts` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `break_start` time DEFAULT NULL,
  `break_end` time DEFAULT NULL,
  `grace_minutes` int NOT NULL DEFAULT '15',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `import_shipment_costs`
--

CREATE TABLE `import_shipment_costs` (
  `id` bigint UNSIGNED NOT NULL,
  `purchase_id` bigint UNSIGNED NOT NULL,
  `freight_charges` decimal(12,2) NOT NULL DEFAULT '0.00',
  `insurance_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `port_charges` decimal(12,2) NOT NULL DEFAULT '0.00',
  `customs_duty` decimal(12,2) NOT NULL DEFAULT '0.00',
  `regulatory_duty` decimal(12,2) NOT NULL DEFAULT '0.00',
  `sales_tax_import` decimal(12,2) NOT NULL DEFAULT '0.00',
  `clearing_agent_charges` decimal(12,2) NOT NULL DEFAULT '0.00',
  `inland_freight` decimal(12,2) NOT NULL DEFAULT '0.00',
  `misc_costs` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_extra_cost` decimal(12,2) NOT NULL DEFAULT '0.00',
  `allocation_method` enum('qty','weight','value') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'qty',
  `landed_cost_per_unit` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_series`
--

CREATE TABLE `invoice_series` (
  `id` bigint UNSIGNED NOT NULL,
  `prefix` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `next_number` bigint UNSIGNED NOT NULL DEFAULT '1',
  `padding` int NOT NULL DEFAULT '4',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoice_series`
--

INSERT INTO `invoice_series` (`id`, `prefix`, `next_number`, `padding`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 'INV', 16, 4, 0, '2026-10-17 12:43:00', '2026-10-31 09:35:01'),
(2, 'LTE_QT26', 385, 8, 0, '2026-10-26 13:39:05', '2026-10-26 13:39:05');

-- --------------------------------------------------------

--
-- Table structure for table `inward_gatepasses`
--

CREATE TABLE `inward_gatepasses` (
  `id` bigint UNSIGNED NOT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `warehouse_id` bigint UNSIGNED NOT NULL,
  `vendor_id` bigint UNSIGNED NOT NULL,
  `purchase_id` bigint UNSIGNED DEFAULT NULL,
  `gatepass_date` date DEFAULT NULL,
  `gatepass_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','linked','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inward_gatepass_items`
--

CREATE TABLE `inward_gatepass_items` (
  `id` bigint UNSIGNED NOT NULL,
  `inward_gatepass_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `qty` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `journal_entries`
--

CREATE TABLE `journal_entries` (
  `id` bigint UNSIGNED NOT NULL,
  `source_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source_id` bigint UNSIGNED DEFAULT NULL,
  `account_id` bigint UNSIGNED NOT NULL,
  `entry_date` date NOT NULL,
  `debit` decimal(15,2) NOT NULL DEFAULT '0.00',
  `credit` decimal(15,2) NOT NULL DEFAULT '0.00',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_reconciled` tinyint(1) NOT NULL DEFAULT '0',
  `reconciled_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `party_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `party_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `journal_entries`
--

INSERT INTO `journal_entries` (`id`, `source_type`, `source_id`, `account_id`, `entry_date`, `debit`, `credit`, `description`, `is_reconciled`, `reconciled_at`, `created_at`, `updated_at`, `party_type`, `party_id`) VALUES
(1, 'App\\Models\\VoucherMaster', 1, 5, '2026-10-17', 750000.00, 0.00, 'Sale Invoice #INV-0001', 0, NULL, '2026-10-17 12:44:49', '2026-10-17 12:44:49', 'App\\Models\\Customer', 2),
(2, 'App\\Models\\VoucherMaster', 1, 6, '2026-10-17', 0.00, 750000.00, 'Sale Invoice #INV-0001', 0, NULL, '2026-10-17 12:44:49', '2026-10-17 12:44:49', NULL, NULL),
(3, 'App\\Models\\VoucherMaster', 2, 2, '2026-10-17', 400000.00, 0.00, 'Payment received from Invoice #INV-0001', 0, NULL, '2026-10-17 12:44:49', '2026-10-17 12:44:49', NULL, NULL),
(4, 'App\\Models\\VoucherMaster', 2, 5, '2026-10-17', 0.00, 400000.00, 'Payment for Invoice #INV-0001', 0, NULL, '2026-10-17 12:44:49', '2026-10-17 12:44:49', 'App\\Models\\Customer', 2),
(7, 'App\\Models\\VoucherMaster', 4, 3, '2026-10-19', 5000.00, 0.00, 'Payment to 2', 0, NULL, '2026-10-19 06:05:38', '2026-10-19 06:05:38', 'App\\Models\\Account', 3),
(8, 'App\\Models\\VoucherMaster', 4, 2, '2026-10-19', 0.00, 5000.00, 'ATM WITHDRAW', 0, NULL, '2026-10-19 06:05:38', '2026-10-19 06:05:38', NULL, NULL),
(9, 'App\\Models\\ExpenseVoucher', 1, 8, '2026-10-19', 5000.00, 0.00, 'Expense Voucher #EVID-0001 (ENTERTAINMENT)', 0, NULL, '2026-10-19 06:36:16', '2026-10-19 06:36:16', NULL, NULL),
(10, 'App\\Models\\ExpenseVoucher', 1, 3, '2026-10-19', 0.00, 5000.00, 'Expense Voucher #EVID-0001', 0, NULL, '2026-10-19 06:36:16', '2026-10-19 06:36:16', NULL, NULL),
(13, 'App\\Models\\VoucherMaster', 6, 7, '2026-10-16', 19270.00, 0.00, 'Purchase Invoice #PUR-003', 0, NULL, '2026-10-19 07:14:33', '2026-10-19 07:14:33', NULL, NULL),
(14, 'App\\Models\\VoucherMaster', 6, 1, '2026-10-16', 0.00, 19270.00, 'Payable to Vendor MHM Enterprices', 0, NULL, '2026-10-19 07:14:33', '2026-10-19 07:14:33', 'App\\Models\\Vendor', 2),
(16, 'App\\Models\\Customer', 1, 5, '2026-10-17', 80000.00, 0.00, 'Opening Balance', 0, NULL, '2026-10-20 19:43:03', '2026-10-20 19:43:03', 'App\\Models\\Customer', 1),
(17, 'App\\Models\\VoucherMaster', 8, 7, '2026-10-18', 0.00, 0.00, 'Purchase Invoice #PUR-002', 0, NULL, '2026-10-22 06:47:25', '2026-10-22 06:47:25', NULL, NULL),
(18, 'App\\Models\\VoucherMaster', 8, 1, '2026-10-18', 0.00, 0.00, 'Payable to Vendor CHINA', 0, NULL, '2026-10-22 06:47:25', '2026-10-22 06:47:25', 'App\\Models\\Vendor', 1),
(19, 'App\\Models\\VoucherMaster', 9, 7, '2026-10-17', 0.00, 0.00, 'Purchase Invoice #PUR-001', 0, NULL, '2026-10-23 12:04:01', '2026-10-23 12:04:01', NULL, NULL),
(20, 'App\\Models\\VoucherMaster', 9, 1, '2026-10-17', 0.00, 0.00, 'Payable to Vendor CHINA', 0, NULL, '2026-10-23 12:04:01', '2026-10-23 12:04:01', 'App\\Models\\Vendor', 1),
(21, 'App\\Models\\VoucherMaster', 10, 7, '2026-10-12', 91110.00, 0.00, 'Purchase Invoice #PUR-004', 0, NULL, '2026-10-24 07:16:10', '2026-10-24 07:16:10', NULL, NULL),
(22, 'App\\Models\\VoucherMaster', 10, 1, '2026-10-12', 0.00, 91110.00, 'Payable to Vendor EPS kHALIL UR REHMAN', 0, NULL, '2026-10-24 07:16:10', '2026-10-24 07:16:10', 'App\\Models\\Vendor', 3),
(23, 'App\\Models\\VoucherMaster', 11, 7, '2026-10-20', 100000.00, 0.00, 'Purchase Invoice #PUR-005', 0, NULL, '2026-10-24 07:17:47', '2026-10-24 07:17:47', NULL, NULL),
(24, 'App\\Models\\VoucherMaster', 11, 1, '2026-10-20', 0.00, 100000.00, 'Payable to Vendor Fahad Electric Company Faizan', 0, NULL, '2026-10-24 07:17:47', '2026-10-24 07:17:47', 'App\\Models\\Vendor', 4),
(25, 'App\\Models\\VoucherMaster', 12, 5, '2026-08-22', 580000.00, 0.00, 'Sale Invoice #INV-0002', 0, NULL, '2026-10-24 10:06:26', '2026-10-24 10:06:26', 'App\\Models\\Customer', 12),
(26, 'App\\Models\\VoucherMaster', 12, 6, '2026-08-22', 0.00, 580000.00, 'Sale Invoice #INV-0002', 0, NULL, '2026-10-24 10:06:26', '2026-10-24 10:06:26', NULL, NULL),
(27, 'App\\Models\\VoucherMaster', 13, 3, '2026-10-24', 100000.00, 0.00, 'Payment received from Invoice #INV-0002', 0, NULL, '2026-10-24 10:06:26', '2026-10-24 10:06:26', NULL, NULL),
(28, 'App\\Models\\VoucherMaster', 13, 5, '2026-10-24', 0.00, 100000.00, 'Payment for Invoice #INV-0002', 0, NULL, '2026-10-24 10:06:26', '2026-10-24 10:06:26', 'App\\Models\\Customer', 12),
(29, 'App\\Models\\VoucherMaster', 14, 5, '2026-10-13', 1227200.00, 0.00, 'Sale Invoice #INV-0003', 0, NULL, '2026-10-24 18:52:36', '2026-10-24 18:52:36', 'App\\Models\\Customer', 14),
(30, 'App\\Models\\VoucherMaster', 14, 6, '2026-10-13', 0.00, 1227200.00, 'Sale Invoice #INV-0003', 0, NULL, '2026-10-24 18:52:36', '2026-10-24 18:52:36', NULL, NULL),
(39, 'App\\Models\\VoucherMaster', 16, 5, '2026-10-26', 400000.00, 0.00, 'Sale Invoice #INV-0004', 0, NULL, '2026-10-26 08:40:14', '2026-10-26 08:40:14', 'App\\Models\\Customer', 15),
(40, 'App\\Models\\VoucherMaster', 16, 6, '2026-10-26', 0.00, 400000.00, 'Sale Invoice #INV-0004', 0, NULL, '2026-10-26 08:40:14', '2026-10-26 08:40:14', NULL, NULL),
(45, 'App\\Models\\VoucherMaster', 17, 5, '2026-10-26', 580000.00, 0.00, 'Sale Invoice #INV-0005', 0, NULL, '2026-10-26 12:00:04', '2026-10-26 12:00:04', 'App\\Models\\Customer', 16),
(46, 'App\\Models\\VoucherMaster', 17, 6, '2026-10-26', 0.00, 580000.00, 'Sale Invoice #INV-0005', 0, NULL, '2026-10-26 12:00:04', '2026-10-26 12:00:04', NULL, NULL),
(47, 'App\\Models\\Customer', 18, 5, '2026-10-26', 18500.00, 0.00, 'Opening Balance', 0, NULL, '2026-10-26 12:24:18', '2026-10-26 12:24:18', 'App\\Models\\Customer', 18),
(48, 'App\\Models\\VoucherMaster', 18, 5, '2026-10-26', 47000.00, 0.00, 'Repair Invoice #REP-00014 - IGBT modules', 0, NULL, '2026-10-26 12:58:20', '2026-10-26 12:58:20', 'App\\Models\\Customer', 17),
(49, 'App\\Models\\VoucherMaster', 18, 6, '2026-10-26', 0.00, 47000.00, 'Repair Invoice #REP-00014 - IGBT modules', 0, NULL, '2026-10-26 12:58:20', '2026-10-26 12:58:20', NULL, NULL),
(50, 'App\\Models\\VoucherMaster', 19, 7, '2026-10-26', 25230.00, 0.00, 'Purchase Invoice #PUR-006', 0, NULL, '2026-10-26 13:35:31', '2026-10-26 13:35:31', NULL, NULL),
(51, 'App\\Models\\VoucherMaster', 19, 1, '2026-10-26', 0.00, 25230.00, 'Payable to Vendor EPS kHALIL UR REHMAN', 0, NULL, '2026-10-26 13:35:31', '2026-10-26 13:35:31', 'App\\Models\\Vendor', 3),
(52, 'App\\Models\\VoucherMaster', 20, 5, '2026-10-19', 26000.00, 0.00, 'Repair Invoice #REP-00003 - CT repair (Rewinding)', 0, NULL, '2026-10-26 19:43:13', '2026-10-26 19:43:13', 'App\\Models\\Customer', 4),
(53, 'App\\Models\\VoucherMaster', 20, 6, '2026-10-19', 0.00, 26000.00, 'Repair Invoice #REP-00003 - CT repair (Rewinding)', 0, NULL, '2026-10-26 19:43:13', '2026-10-26 19:43:13', NULL, NULL),
(54, 'App\\Models\\VoucherMaster', 7, 5, '2026-10-21', 28500.00, 0.00, 'Repair Invoice #REP-00004 - IGBT', 0, NULL, '2026-10-26 19:47:56', '2026-10-26 19:47:56', 'App\\Models\\Customer', 4),
(55, 'App\\Models\\VoucherMaster', 7, 6, '2026-10-21', 0.00, 28500.00, 'Repair Invoice #REP-00004 - IGBT', 0, NULL, '2026-10-26 19:47:56', '2026-10-26 19:47:56', NULL, NULL),
(56, 'App\\Models\\VoucherMaster', 21, 5, '2026-10-27', 14000.00, 0.00, 'Repair Invoice #REP-00015 - Induction Heater B-unit Repair \ni. Primary repair\nii. Secondary repair', 0, NULL, '2026-10-26 20:31:14', '2026-10-26 20:31:14', 'App\\Models\\Customer', 19),
(57, 'App\\Models\\VoucherMaster', 21, 6, '2026-10-27', 0.00, 14000.00, 'Repair Invoice #REP-00015 - Induction Heater B-unit Repair \ni. Primary repair\nii. Secondary repair', 0, NULL, '2026-10-26 20:31:14', '2026-10-26 20:31:14', NULL, NULL),
(58, 'App\\Models\\Customer', 20, 5, '2026-10-27', 125000.00, 0.00, 'Opening Balance', 0, NULL, '2026-10-26 20:39:12', '2026-10-26 20:39:12', 'App\\Models\\Customer', 20),
(59, 'App\\Models\\VoucherMaster', 22, 5, '2026-10-27', 530000.00, 0.00, 'Sale Invoice #INV-0006', 0, NULL, '2026-10-26 20:45:12', '2026-10-26 20:45:12', 'App\\Models\\Customer', 20),
(60, 'App\\Models\\VoucherMaster', 22, 6, '2026-10-27', 0.00, 530000.00, 'Sale Invoice #INV-0006', 0, NULL, '2026-10-26 20:45:12', '2026-10-26 20:45:12', NULL, NULL),
(61, 'App\\Models\\VoucherMaster', 23, 5, '2026-10-27', 530000.00, 0.00, 'Sale Invoice #INV-0007', 0, NULL, '2026-10-26 20:47:12', '2026-10-26 20:47:12', 'App\\Models\\Customer', 20),
(62, 'App\\Models\\VoucherMaster', 23, 6, '2026-10-27', 0.00, 530000.00, 'Sale Invoice #INV-0007', 0, NULL, '2026-10-26 20:47:12', '2026-10-26 20:47:12', NULL, NULL),
(63, 'App\\Models\\VoucherMaster', 24, 5, '2026-10-27', 770000.00, 0.00, 'Sale Invoice #INV-0008', 0, NULL, '2026-10-26 20:56:29', '2026-10-26 20:56:29', 'App\\Models\\Customer', 20),
(64, 'App\\Models\\VoucherMaster', 24, 6, '2026-10-27', 0.00, 770000.00, 'Sale Invoice #INV-0008', 0, NULL, '2026-10-26 20:56:29', '2026-10-26 20:56:29', NULL, NULL),
(65, 'App\\Models\\VoucherMaster', 25, 5, '2026-10-15', 11500.00, 0.00, 'Repair Invoice #REP-00016 - B-unit Water wiring (New)', 0, NULL, '2026-10-26 21:07:43', '2026-10-26 21:07:43', 'App\\Models\\Customer', 21),
(66, 'App\\Models\\VoucherMaster', 25, 6, '2026-10-15', 0.00, 11500.00, 'Repair Invoice #REP-00016 - B-unit Water wiring (New)', 0, NULL, '2026-10-26 21:07:43', '2026-10-26 21:07:43', NULL, NULL),
(67, 'App\\Models\\VoucherMaster', 26, 5, '2026-10-27', 20500.00, 0.00, 'Repair Invoice #REP-00017 - Transformer Rewind (New)', 0, NULL, '2026-10-26 21:15:08', '2026-10-26 21:15:08', 'App\\Models\\Customer', 22),
(68, 'App\\Models\\VoucherMaster', 26, 6, '2026-10-27', 0.00, 20500.00, 'Repair Invoice #REP-00017 - Transformer Rewind (New)', 0, NULL, '2026-10-26 21:15:08', '2026-10-26 21:15:08', NULL, NULL),
(69, 'App\\Models\\VoucherMaster', 27, 5, '2026-10-27', 17500.00, 0.00, 'Repair Invoice #REP-00018 - New Driver Board', 0, NULL, '2026-10-26 21:24:32', '2026-10-26 21:24:32', 'App\\Models\\Customer', 23),
(70, 'App\\Models\\VoucherMaster', 27, 6, '2026-10-27', 0.00, 17500.00, 'Repair Invoice #REP-00018 - New Driver Board', 0, NULL, '2026-10-26 21:24:32', '2026-10-26 21:24:32', NULL, NULL),
(73, 'App\\Models\\Customer', 24, 5, '2026-10-27', 94500.00, 0.00, 'Opening Balance', 0, NULL, '2026-10-27 08:28:12', '2026-10-27 08:28:12', 'App\\Models\\Customer', 24),
(74, 'App\\Models\\Customer', 25, 5, '2026-10-27', 380000.00, 0.00, 'Opening Balance', 0, NULL, '2026-10-27 09:11:47', '2026-10-27 09:11:47', 'App\\Models\\Customer', 25),
(75, 'App\\Models\\Vendor', 5, 1, '2026-10-27', 0.00, 15000.00, 'Opening Balance', 0, NULL, '2026-10-27 09:56:04', '2026-10-27 09:56:04', 'App\\Models\\Vendor', 5),
(78, 'App\\Models\\VoucherMaster', 30, 5, '2026-10-28', 11000.00, 0.00, 'Sale Invoice #INV-0009', 0, NULL, '2026-10-28 06:53:47', '2026-10-28 06:53:47', 'App\\Models\\Customer', 4),
(79, 'App\\Models\\VoucherMaster', 30, 6, '2026-10-28', 0.00, 11000.00, 'Sale Invoice #INV-0009', 0, NULL, '2026-10-28 06:53:47', '2026-10-28 06:53:47', NULL, NULL),
(84, 'App\\Models\\VoucherMaster', 15, 5, '2026-10-26', 220000.00, 0.00, 'Repair Invoice #REP-00007 - Induction Heater upgrade to LTZ-40kw', 0, NULL, '2026-10-28 07:26:31', '2026-10-28 07:26:31', 'App\\Models\\Customer', 8),
(85, 'App\\Models\\VoucherMaster', 15, 6, '2026-10-26', 0.00, 220000.00, 'Repair Invoice #REP-00007 - Induction Heater upgrade to LTZ-40kw', 0, NULL, '2026-10-28 07:26:31', '2026-10-28 07:26:31', NULL, NULL),
(86, 'App\\Models\\VoucherMaster', 28, 5, '2026-10-27', 21500.00, 0.00, 'Repair Invoice #REP-00019 - IGBT', 0, NULL, '2026-10-28 08:23:38', '2026-10-28 08:23:38', 'App\\Models\\Customer', 19),
(87, 'App\\Models\\VoucherMaster', 28, 6, '2026-10-27', 0.00, 21500.00, 'Repair Invoice #REP-00019 - IGBT', 0, NULL, '2026-10-28 08:23:38', '2026-10-28 08:23:38', NULL, NULL),
(91, 'App\\Models\\VoucherMaster', 32, 7, '2026-10-28', 19250.00, 0.00, 'Purchase Invoice #PUR-007', 0, NULL, '2026-10-28 11:16:53', '2026-10-28 11:16:53', NULL, NULL),
(92, 'App\\Models\\VoucherMaster', 32, 1, '2026-10-28', 0.00, 19250.00, 'Payable to Vendor Chouhan Plastic Stretch Roll', 0, NULL, '2026-10-28 11:16:53', '2026-10-28 11:16:53', 'App\\Models\\Vendor', 5),
(93, 'App\\Models\\Customer', 29, 5, '2026-10-28', 36500.00, 0.00, 'Opening Balance', 0, NULL, '2026-10-28 12:20:18', '2026-10-28 12:20:18', 'App\\Models\\Customer', 29),
(94, 'App\\Models\\VoucherMaster', 33, 5, '2026-10-28', 950000.00, 0.00, 'Sale Invoice #INV-0010', 0, NULL, '2026-10-28 12:41:09', '2026-10-28 12:41:09', 'App\\Models\\Customer', 27),
(95, 'App\\Models\\VoucherMaster', 33, 6, '2026-10-28', 0.00, 950000.00, 'Sale Invoice #INV-0010', 0, NULL, '2026-10-28 12:41:09', '2026-10-28 12:41:09', NULL, NULL),
(96, 'App\\Models\\Customer', 30, 5, '2026-10-28', 64000.00, 0.00, 'Opening Balance', 0, NULL, '2026-10-28 13:38:24', '2026-10-28 13:38:24', 'App\\Models\\Customer', 30),
(103, 'App\\Models\\Customer', 31, 5, '2026-10-29', 330000.00, 0.00, 'Opening Balance', 0, NULL, '2026-10-28 19:52:10', '2026-10-28 19:52:10', 'App\\Models\\Customer', 31),
(106, 'App\\Models\\VoucherMaster', 38, 5, '2026-09-01', 640000.00, 0.00, 'Sale Invoice #INV-0012', 0, NULL, '2026-10-28 20:07:20', '2026-10-28 20:07:20', 'App\\Models\\Customer', 7),
(107, 'App\\Models\\VoucherMaster', 38, 6, '2026-09-01', 0.00, 640000.00, 'Sale Invoice #INV-0012', 0, NULL, '2026-10-28 20:07:20', '2026-10-28 20:07:20', NULL, NULL),
(108, 'App\\Models\\Customer', 6, 5, '2026-10-20', 507000.00, 0.00, 'Opening Balance', 0, NULL, '2026-10-29 06:12:37', '2026-10-29 06:12:37', 'App\\Models\\Customer', 6),
(109, 'App\\Models\\VoucherMaster', 39, 1, '2026-10-27', 100000.00, 0.00, 'Payment to vendor', 0, NULL, '2026-10-29 10:15:02', '2026-10-29 10:15:02', 'App\\Models\\Vendor', 3),
(110, 'App\\Models\\VoucherMaster', 39, 4, '2026-10-27', 0.00, 100000.00, 'venders payments from LTE Meezan', 0, NULL, '2026-10-29 10:15:02', '2026-10-29 10:15:02', NULL, NULL),
(111, 'App\\Models\\VoucherMaster', 40, 1, '2026-10-29', 100000.00, 0.00, 'Payment to vendor', 0, NULL, '2026-10-29 10:16:42', '2026-10-29 10:16:42', 'App\\Models\\Vendor', 4),
(112, 'App\\Models\\VoucherMaster', 40, 2, '2026-10-29', 0.00, 100000.00, 'vender payments from LTE Meezan', 0, NULL, '2026-10-29 10:16:42', '2026-10-29 10:16:42', NULL, NULL),
(113, 'App\\Models\\VoucherMaster', 41, 2, '2026-10-27', 350000.00, 0.00, 'Receipt', 0, NULL, '2026-10-29 10:44:16', '2026-10-29 10:44:16', NULL, NULL),
(114, 'App\\Models\\VoucherMaster', 41, 5, '2026-10-27', 0.00, 350000.00, 'Receipt from customer', 0, NULL, '2026-10-29 10:44:16', '2026-10-29 10:44:16', 'App\\Models\\Customer', 27),
(125, 'App\\Models\\VoucherMaster', 46, 2, '2026-10-30', 400000.00, 0.00, 'Receipt', 0, NULL, '2026-10-30 10:34:18', '2026-10-30 10:34:18', NULL, NULL),
(126, 'App\\Models\\VoucherMaster', 46, 5, '2026-10-30', 0.00, 400000.00, 'Receipt from customer', 0, NULL, '2026-10-30 10:34:18', '2026-10-30 10:34:18', 'App\\Models\\Customer', 34),
(127, 'App\\Models\\VoucherMaster', 47, 5, '2026-10-30', 42000.00, 0.00, 'Repair Invoice #REP-00022 - IGBT', 0, NULL, '2026-10-30 11:40:56', '2026-10-30 11:40:56', 'App\\Models\\Customer', 30),
(128, 'App\\Models\\VoucherMaster', 47, 6, '2026-10-30', 0.00, 42000.00, 'Repair Invoice #REP-00022 - IGBT', 0, NULL, '2026-10-30 11:40:56', '2026-10-30 11:40:56', NULL, NULL),
(129, 'App\\Models\\VoucherMaster', 48, 2, '2026-10-30', 42000.00, 0.00, 'Receipt', 0, NULL, '2026-10-30 12:04:44', '2026-10-30 12:04:44', NULL, NULL),
(130, 'App\\Models\\VoucherMaster', 48, 5, '2026-10-30', 0.00, 42000.00, 'Receipt from customer', 0, NULL, '2026-10-30 12:04:44', '2026-10-30 12:04:44', 'App\\Models\\Customer', 30),
(131, 'App\\Models\\VoucherMaster', 49, 5, '2026-10-29', 52500.00, 0.00, 'Sale Invoice #INV-0011', 0, NULL, '2026-10-30 12:23:27', '2026-10-30 12:23:27', 'App\\Models\\Customer', 29),
(132, 'App\\Models\\VoucherMaster', 49, 6, '2026-10-29', 0.00, 52500.00, 'Sale Invoice #INV-0011', 0, NULL, '2026-10-30 12:23:27', '2026-10-30 12:23:27', NULL, NULL),
(133, 'App\\Models\\Customer', 35, 5, '2026-10-30', 85000.00, 0.00, 'Opening Balance', 0, NULL, '2026-10-30 13:01:54', '2026-10-30 13:01:54', 'App\\Models\\Customer', 35),
(138, 'App\\Models\\VoucherMaster', 52, 5, '2026-10-30', 1486800.00, 0.00, 'Sale Invoice #INV-0013', 0, NULL, '2026-10-30 19:05:03', '2026-10-30 19:05:03', 'App\\Models\\Customer', 34),
(139, 'App\\Models\\VoucherMaster', 52, 6, '2026-10-30', 0.00, 1486800.00, 'Sale Invoice #INV-0013', 0, NULL, '2026-10-30 19:05:03', '2026-10-30 19:05:03', NULL, NULL),
(140, 'App\\Models\\VoucherMaster', 53, 3, '2026-10-31', 400000.00, 0.00, 'Payment received from Invoice #INV-0013', 0, NULL, '2026-10-30 19:05:03', '2026-10-30 19:05:03', NULL, NULL),
(141, 'App\\Models\\VoucherMaster', 53, 5, '2026-10-31', 0.00, 400000.00, 'Payment for Invoice #INV-0013', 0, NULL, '2026-10-30 19:05:03', '2026-10-30 19:05:03', 'App\\Models\\Customer', 34),
(142, 'App\\Models\\VoucherMaster', 54, 5, '2026-10-31', 43000.00, 0.00, 'Repair Invoice #REP-00025 - Driver Board repair', 0, NULL, '2026-10-30 19:07:06', '2026-10-30 19:07:06', 'App\\Models\\Customer', 36),
(143, 'App\\Models\\VoucherMaster', 54, 6, '2026-10-31', 0.00, 43000.00, 'Repair Invoice #REP-00025 - Driver Board repair', 0, NULL, '2026-10-30 19:07:06', '2026-10-30 19:07:06', NULL, NULL),
(150, 'App\\Models\\VoucherMaster', 56, 5, '2026-10-30', 4000.00, 0.00, 'Repair Invoice #REP-00026 - TEST DRIVER BOARD', 0, NULL, '2026-10-30 19:45:24', '2026-10-30 19:45:24', 'App\\Models\\Customer', 38),
(151, 'App\\Models\\VoucherMaster', 56, 6, '2026-10-30', 0.00, 4000.00, 'Repair Invoice #REP-00026 - TEST DRIVER BOARD', 0, NULL, '2026-10-30 19:45:24', '2026-10-30 19:45:24', NULL, NULL),
(162, 'App\\Models\\VoucherMaster', 62, 5, '2026-10-31', 44000.00, 0.00, 'Repair Invoice #REP-00028 - IGBT', 0, NULL, '2026-10-31 05:56:13', '2026-10-31 05:56:13', 'App\\Models\\Customer', 33),
(163, 'App\\Models\\VoucherMaster', 62, 6, '2026-10-31', 0.00, 44000.00, 'Repair Invoice #REP-00028 - IGBT', 0, NULL, '2026-10-31 05:56:13', '2026-10-31 05:56:13', NULL, NULL),
(164, 'App\\Models\\VoucherMaster', 63, 2, '2026-10-31', 17500.00, 0.00, 'Receipt', 0, NULL, '2026-10-31 07:51:39', '2026-10-31 07:51:39', NULL, NULL),
(165, 'App\\Models\\VoucherMaster', 63, 5, '2026-10-31', 0.00, 17500.00, 'Receipt from customer', 0, NULL, '2026-10-31 07:51:39', '2026-10-31 07:51:39', 'App\\Models\\Customer', 23),
(166, 'App\\Models\\VoucherMaster', 42, 5, '2026-10-31', 216700.00, 0.00, 'Repair Invoice #REP-00021 - Main Control Board', 0, NULL, '2026-10-31 08:09:53', '2026-10-31 08:09:53', 'App\\Models\\Customer', 32),
(167, 'App\\Models\\VoucherMaster', 42, 6, '2026-10-31', 0.00, 216700.00, 'Repair Invoice #REP-00021 - Main Control Board', 0, NULL, '2026-10-31 08:09:53', '2026-10-31 08:09:53', NULL, NULL),
(168, 'App\\Models\\VoucherMaster', 64, 5, '2026-10-30', 6000.00, 0.00, 'Repair Invoice #REP-00024 - New Driver Boards', 0, NULL, '2026-10-31 08:18:27', '2026-10-31 08:18:27', 'App\\Models\\Customer', 35),
(169, 'App\\Models\\VoucherMaster', 64, 6, '2026-10-30', 0.00, 6000.00, 'Repair Invoice #REP-00024 - New Driver Boards', 0, NULL, '2026-10-31 08:18:27', '2026-10-31 08:18:27', NULL, NULL),
(170, 'App\\Models\\VoucherMaster', 61, 5, '2026-06-29', 41000.00, 0.00, 'Repair Invoice #REP-00027 - IGBT modules', 0, NULL, '2026-10-31 08:31:16', '2026-10-31 08:31:16', 'App\\Models\\Customer', 33),
(171, 'App\\Models\\VoucherMaster', 61, 6, '2026-06-29', 0.00, 41000.00, 'Repair Invoice #REP-00027 - IGBT modules', 0, NULL, '2026-10-31 08:31:16', '2026-10-31 08:31:16', NULL, NULL),
(172, 'App\\Models\\VoucherMaster', 65, 2, '2026-10-31', 33000.00, 0.00, 'Receipt', 0, NULL, '2026-10-31 08:33:53', '2026-10-31 08:33:53', NULL, NULL),
(173, 'App\\Models\\VoucherMaster', 65, 5, '2026-10-31', 0.00, 33000.00, 'Receipt from customer', 0, NULL, '2026-10-31 08:33:53', '2026-10-31 08:33:53', 'App\\Models\\Customer', 33),
(174, 'App\\Models\\VoucherMaster', 66, 5, '2026-10-31', 5000.00, 0.00, 'Repair Invoice #REP-00029 - Induction 40KW FOR REPAIR (Fault : OVER VOLTAGE)', 0, NULL, '2026-10-31 08:57:50', '2026-10-31 08:57:50', 'App\\Models\\Customer', 38),
(175, 'App\\Models\\VoucherMaster', 66, 6, '2026-10-31', 0.00, 5000.00, 'Repair Invoice #REP-00029 - Induction 40KW FOR REPAIR (Fault : OVER VOLTAGE)', 0, NULL, '2026-10-31 08:57:50', '2026-10-31 08:57:50', NULL, NULL),
(184, 'App\\Models\\VoucherMaster', 71, 5, '2026-10-31', 550000.00, 0.00, 'Sale Invoice #INV-0015', 0, NULL, '2026-10-31 09:41:20', '2026-10-31 09:41:20', 'App\\Models\\Customer', 39),
(185, 'App\\Models\\VoucherMaster', 71, 6, '2026-10-31', 0.00, 550000.00, 'Sale Invoice #INV-0015', 0, NULL, '2026-10-31 09:41:20', '2026-10-31 09:41:20', NULL, NULL),
(186, 'App\\Models\\VoucherMaster', 72, 2, '2026-10-31', 50000.00, 0.00, 'Payment received from Invoice #INV-0015', 0, NULL, '2026-10-31 09:41:20', '2026-10-31 09:41:20', NULL, NULL),
(187, 'App\\Models\\VoucherMaster', 72, 5, '2026-10-31', 0.00, 50000.00, 'Payment for Invoice #INV-0015', 0, NULL, '2026-10-31 09:41:20', '2026-10-31 09:41:20', 'App\\Models\\Customer', 39),
(188, 'App\\Models\\VoucherMaster', 73, 7, '2026-10-28', 52580.00, 0.00, 'Purchase Invoice #PUR-008', 0, NULL, '2026-10-31 10:29:46', '2026-10-31 10:29:46', NULL, NULL),
(189, 'App\\Models\\VoucherMaster', 73, 1, '2026-10-28', 0.00, 52580.00, 'Payable to Vendor EPS kHALIL UR REHMAN', 0, NULL, '2026-10-31 10:29:46', '2026-10-31 10:29:46', 'App\\Models\\Vendor', 3),
(190, 'App\\Models\\VoucherMaster', 74, 2, '2026-10-31', 20500.00, 0.00, 'Receipt', 0, NULL, '2026-10-31 10:34:40', '2026-10-31 10:34:40', NULL, NULL),
(191, 'App\\Models\\VoucherMaster', 74, 5, '2026-10-31', 0.00, 20500.00, 'Receipt from customer', 0, NULL, '2026-10-31 10:34:40', '2026-10-31 10:34:40', 'App\\Models\\Customer', 22),
(192, 'App\\Models\\VoucherMaster', 75, 5, '2026-10-31', 2.00, 0.00, 'Sale Invoice #INV-0014', 0, NULL, '2026-10-31 11:26:45', '2026-10-31 11:26:45', 'App\\Models\\Customer', 37),
(193, 'App\\Models\\VoucherMaster', 75, 6, '2026-10-31', 0.00, 2.00, 'Sale Invoice #INV-0014', 0, NULL, '2026-10-31 11:26:45', '2026-10-31 11:26:45', NULL, NULL),
(194, 'App\\Models\\VoucherMaster', 76, 5, '2026-10-31', 25000.00, 0.00, 'Repair Invoice #REP-00012 - IGBT', 0, NULL, '2026-10-31 11:49:59', '2026-10-31 11:49:59', 'App\\Models\\Customer', 13),
(195, 'App\\Models\\VoucherMaster', 76, 6, '2026-10-31', 0.00, 25000.00, 'Repair Invoice #REP-00012 - IGBT', 0, NULL, '2026-10-31 11:49:59', '2026-10-31 11:49:59', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `material_usages`
--

CREATE TABLE `material_usages` (
  `id` bigint UNSIGNED NOT NULL,
  `usage_no` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `warehouse_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `production_order_no` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `purpose` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `total_items` int NOT NULL DEFAULT '0',
  `total_qty` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_cost` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `material_usages`
--

INSERT INTO `material_usages` (`id`, `usage_no`, `date`, `warehouse_id`, `user_id`, `production_order_no`, `purpose`, `remarks`, `total_items`, `total_qty`, `total_cost`, `created_at`, `updated_at`) VALUES
(1, 'MU-0001', '2026-10-19', 1, 2, NULL, 'Production Consumption', NULL, 4, 11.00, 0.00, '2026-10-19 06:59:29', '2026-10-19 06:59:29'),
(2, 'MU-0002', '2026-10-20', 1, 67, NULL, 'Production Consumption', 'Stock Issued', 6, 10.00, 0.00, '2026-10-20 05:31:46', '2026-10-20 05:31:46'),
(3, 'MU-0003', '2026-10-21', 1, 67, NULL, 'Production Consumption', NULL, 6, 25.00, 0.00, '2026-10-21 05:47:18', '2026-10-21 05:47:18'),
(6, 'MU-0004', '2026-10-23', 1, 67, NULL, 'Production Consumption', 'Stock used', 6, 9.00, 0.00, '2026-10-24 06:29:06', '2026-10-24 06:29:06'),
(7, 'MU-0007', '2026-10-24', 1, 67, NULL, 'Production Consumption', 'stock issued', 2, 4.00, 0.00, '2026-10-27 09:16:59', '2026-10-27 09:16:59');

-- --------------------------------------------------------

--
-- Table structure for table `material_usage_items`
--

CREATE TABLE `material_usage_items` (
  `id` bigint UNSIGNED NOT NULL,
  `material_usage_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `unit_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `available_stock_at_time` decimal(15,2) NOT NULL DEFAULT '0.00',
  `qty_used` decimal(15,2) NOT NULL,
  `unit_cost` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_cost` decimal(15,2) NOT NULL DEFAULT '0.00',
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `material_usage_items`
--

INSERT INTO `material_usage_items` (`id`, `material_usage_id`, `product_id`, `unit_name`, `available_stock_at_time`, `qty_used`, `unit_cost`, `total_cost`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 14, 'Pcs', 10.00, 2.00, 0.00, 0.00, 'ATIF 1014 , 1015', '2026-10-19 06:59:29', '2026-10-19 06:59:29'),
(2, 1, 15, 'Pcs', 20.00, 4.00, 0.00, 0.00, 'ATIF 1014', '2026-10-19 06:59:29', '2026-10-19 06:59:29'),
(3, 1, 16, 'Pcs', 10.00, 2.00, 0.00, 0.00, 'ATIF 1014 , 1015', '2026-10-19 06:59:29', '2026-10-19 06:59:29'),
(4, 1, 18, 'Pcs', 20.00, 3.00, 0.00, 0.00, '02 ATIF  01 ARSLAN 1003,1014,1015', '2026-10-19 06:59:29', '2026-10-19 06:59:29'),
(11, 3, 14, 'Pcs', 7.00, 5.00, 0.00, 0.00, '1013,1007, atif : 1017,1018: Qamar: 1016 Zaki', '2026-10-21 05:47:18', '2026-10-21 05:47:18'),
(12, 3, 15, 'Pcs', 16.00, 7.00, 0.00, 0.00, '1013, 1007, 1017, 1018, 1016', '2026-10-21 05:47:18', '2026-10-21 05:47:18'),
(13, 3, 16, 'Pcs', 7.00, 5.00, 0.00, 0.00, 'same as main board', '2026-10-21 05:47:18', '2026-10-21 05:47:18'),
(14, 3, 17, 'Pcs', 4.00, 3.00, 0.00, 0.00, '1007 , 1017, 1013', '2026-10-21 05:47:18', '2026-10-21 05:47:18'),
(15, 3, 21, 'Pcs', 17.00, 2.00, 0.00, 0.00, '1013, 1007', '2026-10-21 05:47:18', '2026-10-21 05:47:18'),
(16, 3, 18, 'Pcs', 16.00, 3.00, 0.00, 0.00, '1013, 1016 ,1007', '2026-10-21 05:47:18', '2026-10-21 05:47:18'),
(18, 2, 14, 'Pcs', 3.00, 1.00, 0.00, 0.00, 'atif 1011', '2026-10-21 12:35:52', '2026-10-21 12:35:52'),
(19, 2, 16, 'Pcs', 3.00, 1.00, 0.00, 0.00, 'atif 1011', '2026-10-21 12:35:52', '2026-10-21 12:35:52'),
(20, 2, 15, 'Pcs', 5.00, 4.00, 0.00, 0.00, 'atif 1011', '2026-10-21 12:35:52', '2026-10-21 12:35:52'),
(21, 2, 17, 'Pcs', 7.00, 2.00, 0.00, 0.00, 'atif 1011', '2026-10-21 12:35:52', '2026-10-21 12:35:52'),
(22, 2, 18, 'Pcs', 14.00, 1.00, 0.00, 0.00, 'atif 1011', '2026-10-21 12:35:52', '2026-10-21 12:35:52'),
(23, 2, 21, 'Pcs', 16.00, 1.00, 0.00, 0.00, 'atif 1011', '2026-10-21 12:35:52', '2026-10-21 12:35:52'),
(25, 6, 14, 'Pcs', 2.00, 2.00, 0.00, 0.00, '1019 Qamar , 1012 Atif', '2026-10-24 06:29:06', '2026-10-24 06:29:06'),
(26, 6, 15, 'Pcs', 5.00, 2.00, 0.00, 0.00, '1012 Atif', '2026-10-24 06:29:06', '2026-10-24 06:29:06'),
(27, 6, 16, 'Pcs', 2.00, 2.00, 0.00, 0.00, '1019 Qamar , 1012 Atif', '2026-10-24 06:29:06', '2026-10-24 06:29:06'),
(28, 6, 17, 'Pcs', 1.00, 1.00, 0.00, 0.00, '1012 atif', '2026-10-24 06:29:06', '2026-10-24 06:29:06'),
(29, 6, 21, 'Pcs', 15.00, 1.00, 0.00, 0.00, '1012 atif', '2026-10-24 06:29:06', '2026-10-24 06:29:06'),
(30, 6, 18, 'Pcs', 13.00, 1.00, 0.00, 0.00, '1012 atif', '2026-10-24 06:29:06', '2026-10-24 06:29:06'),
(31, 7, 15, 'Pcs', 3.00, 3.00, 0.00, 0.00, 'Qamar 1020', '2026-10-27 09:16:59', '2026-10-27 09:16:59'),
(32, 7, 18, 'Pcs', 12.00, 1.00, 0.00, 0.00, 'Atif 1021', '2026-10-27 09:16:59', '2026-10-27 09:16:59');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2025_07_18_203013_create_categories_table', 1),
(6, '2025_07_18_215154_create_brands_table', 1),
(7, '2025_07_18_220702_create_units_table', 1),
(8, '2025_07_18_231906_create_subcategories_table', 1),
(9, '2025_07_19_074903_create_products_table', 1),
(10, '2025_07_20_220859_create_vendors_table', 1),
(11, '2025_07_20_220905_create_warehouses_table', 1),
(12, '2025_07_20_232951_create_branches_table', 1),
(13, '2025_07_21_125354_create_customers_table', 1),
(14, '2025_07_21_135411_add_status_to_customers_table', 1),
(15, '2025_07_21_181654_create_zones_table', 1),
(16, '2025_07_21_185626_create_sales_officers_table', 1),
(17, '2025_07_21_195751_create_permission_tables', 1),
(18, '2025_07_21_203215_create_transports_table', 1),
(19, '2025_07_21_213504_create_purchases_table', 1),
(20, '2025_07_21_214736_add_urdu_fields_to_transports_table', 1),
(21, '2025_08_09_220420_create_narrations_table', 1),
(22, '2025_08_09_231230_create_vouchers_table', 1),
(23, '2025_08_11_071804_add_barcode_path_to_products_table', 1),
(24, '2025_08_11_092258_add_missing_columns_to_products_table', 1),
(25, '2025_08_11_133922_add_brand_id_to_products_table', 1),
(26, '2025_08_13_225511_create_purchase_items_table', 1),
(27, '2025_08_13_225620_create_stocks_table', 1),
(28, '2025_08_17_213427_create_table_vendor_ledgers', 1),
(29, '2025_08_17_221914_create_vendor_payments_table', 1),
(30, '2025_08_17_222748_create_vendor_bilties_table', 1),
(31, '2025_08_17_225451_create_customer_ledgers_table', 1),
(32, '2025_08_17_225912_create_customer_payments_table', 1),
(33, '2025_08_20_235830_create_table_sales', 1),
(34, '2025_08_22_122351_create_product_discounts_table', 1),
(35, '2025_08_22_213952_create_warehouse_stocks_table', 1),
(36, '2025_08_22_214859_create_stock_transfers_table', 1),
(37, '2025_08_28_223934_create_inward_gatepasses_table', 1),
(38, '2025_08_28_224111_create_inward_gatepass_items_table', 1),
(39, '2025_08_31_093412_create_product_bookings_table', 1),
(40, '2025_09_02_164836_create_purchase_returns_table', 1),
(41, '2025_09_02_164843_create_purchase_return_items_table', 1),
(42, '2025_09_10_181016_add_opening_balance_to_customer_ledgers_table', 1),
(43, '2025_09_13_012942_add_part_fields_to_products_table', 1),
(44, '2025_09_13_022223_create_product_boms_table', 1),
(45, '2025_09_13_022411_create_stock_movements_table', 1),
(46, '2025_09_13_055335_add_index_and_view_for_onhand', 1),
(47, '2025_11_09_135454_add_is_auto_pluck_to_stock_movements_table', 1),
(48, '2025_11_09_135841_add_ref_uuid_to_stock_movements_table', 1),
(49, '2025_12_19_072030_create_package_types_table', 1),
(50, '2025_12_22_190616_add_columns_to_products_table', 1),
(51, '2025_12_30_020855_create_sessions_table', 1),
(52, '2026_01_16_000000_create_modules_table', 1),
(53, '2026_01_17_000000_create_hr_module_tables', 1),
(54, '2026_01_17_000001_create_designations_table', 1),
(55, '2026_01_17_000002_add_details_to_hr_employees_table', 1),
(56, '2026_01_17_000003_create_hr_employee_documents_table', 1),
(57, '2026_01_17_000004_create_hr_salary_structures_table', 1),
(58, '2026_01_17_000005_add_commission_tiers_to_hr_salary_structures', 1),
(59, '2026_01_17_000006_create_hr_shifts_table', 1),
(60, '2026_01_17_000007_create_hr_holidays_table', 1),
(61, '2026_01_17_000008_update_hr_attendance_system', 1),
(62, '2026_01_17_000009_add_location_to_hr_attendances', 1),
(63, '2026_01_19_140000_change_employee_status_inactive_to_non_active', 1),
(64, '2026_01_19_150000_add_requires_location_to_designations', 1),
(65, '2026_01_19_163000_create_hr_loans_tables', 1),
(66, '2026_01_19_185007_add_face_encoding_to_hr_employees_table', 1),
(67, '2026_01_20_162642_create_biometric_devices_table', 1),
(68, '2026_01_20_162647_add_biometric_fields_to_hr_employees_table', 1),
(69, '2026_01_21_024920_create_hr_settings_table', 1),
(70, '2026_01_21_025224_add_punch_gap_minutes_to_hr_employees_table', 1),
(71, '2026_01_21_162017_add_early_in_to_hr_attendances_table', 1),
(72, '2026_01_22_000000_remove_parts_and_bom', 1),
(73, '2026_01_22_152000_add_name_to_modules_table', 1),
(74, '2026_01_22_204519_add_daily_wages_to_salary_structures_table', 1),
(75, '2026_01_22_211414_add_attendance_deduction_policy_to_salary_structures_table', 1),
(76, '2026_01_23_004309_enhance_hr_payrolls_table', 1),
(77, '2026_01_23_004533_create_hr_payroll_details_table', 1),
(78, '2026_01_23_004536_add_pending_deductions_to_hr_employees', 1),
(79, '2026_01_23_012507_add_carried_forward_to_next_to_hr_payrolls', 1),
(80, '2026_01_23_154045_create_employee_salary_structures_table', 1),
(81, '2026_01_23_164530_update_salary_structures_for_standalone_and_naming', 1),
(82, '2026_01_23_180926_add_is_custom_to_employee_salary_structures', 1),
(83, '2026_01_23_182108_add_parent_id_to_salary_structures', 1),
(84, '2026_01_23_230000_drop_basic_salary_from_employees', 1),
(85, '2026_01_24_161500_modify_products_table_for_m2_pricing', 1),
(86, '2026_01_24_165800_add_extended_size_mode_fields', 1),
(87, '2026_01_24_170900_add_purchase_fields', 1),
(88, '2026_01_25_000000_migrate_legacy_stocks_to_warehouse_stocks', 1),
(89, '2026_01_26_013600_rename_column_in_products_table', 1),
(90, '2026_01_26_183704_add_description_to_customer_ledgers_table', 1),
(91, '2026_01_27_000000_add_extra_columns_to_sales_table', 1),
(92, '2026_01_27_000001_add_loose_pieces_to_sales_table', 1),
(93, '2026_01_27_134746_add_stock_details_to_warehouse_stocks_table', 1),
(94, '2026_01_27_135604_remove_stock_columns_from_products_table', 1),
(95, '2026_01_27_135755_remove_price_from_warehouse_stocks_table', 1),
(96, '2026_01_27_141644_simplify_warehouse_stocks_table', 1),
(97, '2026_01_27_143933_add_pricing_columns_to_products_table', 1),
(98, '2026_01_27_144520_add_boxes_quantity_to_warehouse_stocks', 1),
(99, '2026_01_27_144857_rename_pieces_per_box_to_total_pieces_in_warehouse_stocks', 1),
(100, '2026_01_27_190000_update_sales_and_create_sale_items', 1),
(101, '2026_01_27_191000_align_sale_items_columns', 1),
(102, '2026_01_28_014000_make_legacy_sales_columns_nullable', 1),
(103, '2026_01_28_015500_drop_product_column_from_sales', 1),
(104, '2026_01_28_020000_drop_legacy_sales_columns', 1),
(105, '2026_01_28_030000_add_ids_to_sale_items', 1),
(106, '2026_01_28_040000_refactor_sales_table_final', 1),
(107, '2026_01_28_050000_reorder_sales_columns', 1),
(108, '2026_01_28_070000_add_balance_range_to_customers', 1),
(109, '2026_01_28_194600_add_previous_balance_to_customers', 1),
(110, '2026_01_30_000000_refactor_sale_status_and_items', 1),
(111, '2026_01_30_000001_create_financial_accounts_tables', 1),
(112, '2026_01_30_000002_create_voucher_tables', 1),
(113, '2026_01_31_000003_create_settings_table', 1),
(114, '2026_01_31_000004_create_erp_voucher_system', 1),
(115, '2026_01_31_000005_create_system_notifications_table', 1),
(116, '2026_01_31_030620_add_party_to_journal_entries', 1),
(117, '2026_01_31_205213_add_opening_balance_to_vendors_table', 1),
(118, '2026_02_01_011152_fix_sale_status_column_type', 1),
(119, '2026_02_02_010450_add_voucher_id_to_customer_payments_table', 1),
(120, '2026_02_02_013524_create_system_settings_table', 1),
(121, '2026_02_02_015156_add_can_approve_returns_to_users_table', 1),
(122, '2026_02_02_153600_add_pieces_per_m2_to_products', 1),
(123, '2026_02_02_175600_add_due_date_to_sales_table', 1),
(124, '2026_02_02_181246_create_notifications_table', 1),
(125, '2026_02_02_181921_add_credit_days_to_sales_table', 1),
(126, '2026_02_03_135824_change_total_m2_column_type_in_products_table', 1),
(127, '2026_02_04_184220_add_size_mode_to_sale_items_table', 1),
(128, '2026_02_05_040428_add_stock_fields_to_products_table', 1),
(129, '2026_02_05_042018_change_total_m2_decimal_precision', 1),
(130, '2026_02_06_170933_add_snapshot_columns_to_purchase_items_table', 1),
(131, '2026_02_07_133524_add_purchase_id_to_purchase_returns_table', 1),
(132, '2026_02_07_142845_create_sale_returns_table', 1),
(133, '2026_02_07_143037_create_sale_return_items_table', 1),
(134, '2026_02_07_143751_drop_old_sales_returns_table', 1),
(135, '2026_02_18_110000_add_sales_officer_id_to_customers_table', 1),
(136, '2026_02_22_173050_fix_purchase_items_columns', 1),
(137, '2026_02_24_022723_add_default_discounts_to_products_table', 1),
(138, '2026_02_28_040416_add_reminder_fields_to_customers_table', 1),
(139, '2026_03_06_214616_add_reminder_day_to_customers_table', 1),
(140, '2026_05_01_213524_add_is_active_to_products_table', 1),
(141, '2026_05_20_231443_add_alert_quantity_to_products_table', 1),
(142, '2026_05_21_043429_create_expense_categories_table', 1),
(143, '2026_05_21_043430_add_reference_no_to_expense_vouchers_table', 1),
(144, '2026_05_30_192619_add_alert_carton_quantity_to_products_table', 1),
(145, '2026_06_01_171926_add_is_booking_to_sales_table', 1),
(146, '2026_06_02_190558_add_additional_discount_to_purchases_table', 1),
(147, '2026_07_08_083556_add_walkin_name_to_sales_table', 1),
(148, '2026_07_08_235807_add_social_links_to_settings', 1),
(149, '2026_07_09_044903_add_color_to_sale_return_items_table', 1),
(150, '2026_07_09_050550_create_day_closings_table', 1),
(151, '2026_07_09_054826_create_drawer_transactions_table', 1),
(152, '2026_07_09_165000_add_color_to_purchase_items_and_purchase_return_items', 1),
(153, '2026_07_11_141000_add_weight_and_convert_stock_columns_to_decimal', 1),
(154, '2026_07_13_120000_add_sync_columns_to_sales_and_customers', 1),
(155, '2026_07_17_150000_make_customer_id_nullable_in_sale_returns', 1),
(156, '2026_07_19_020202_add_manual_product_columns', 1),
(157, '2026_07_19_024330_add_manual_product_columns_to_sale_return_items', 1),
(158, '2026_08_03_000000_create_stock_adjustments_table', 1),
(159, '2026_08_03_164500_create_ecommerce_tables', 1),
(160, '2026_08_03_173328_add_web_main_image_to_products_table', 1),
(161, '2026_08_03_214719_add_website_fields_to_categories_table', 1),
(162, '2026_08_04_010900_add_coupon_fields_to_ecommerce_orders_table', 1),
(163, '2026_08_05_050000_create_account_histories_table', 1),
(164, '2026_08_05_165217_add_is_stock_deducted_to_ecommerce_orders_table', 1),
(165, '2026_08_05_192914_add_easypaisa_fields_to_ecommerce_orders_table', 1),
(166, '2026_08_06_050000_create_invoice_series_table', 1),
(167, '2026_08_09_033500_add_customer_sync_fields', 1),
(168, '2026_08_09_172703_add_paid_amount_to_ecommerce_orders_table', 1),
(169, '2026_08_09_175932_add_courier_fields_to_ecommerce_orders_table', 1),
(170, '2026_08_19_000001_add_company_logo_to_settings', 1),
(171, '2026_09_03_000001_enhance_vendors_for_manufacturing', 1),
(172, '2026_09_03_000002_create_purchase_requisitions_table', 1),
(173, '2026_09_03_000003_create_purchase_requisition_items_table', 1),
(174, '2026_09_03_000004_enhance_purchases_for_manufacturing', 1),
(175, '2026_09_03_000005_add_tax_fields_to_purchase_items', 1),
(176, '2026_09_03_000006_create_grn_receipts_table', 1),
(177, '2026_09_03_000007_create_grn_items_table', 1),
(178, '2026_09_03_000008_create_import_shipment_costs_table', 1),
(179, '2026_09_04_000001_add_item_type_to_products_table', 1),
(180, '2026_09_05_094327_add_booking_fields_to_sales_and_sale_items', 1),
(181, '2026_09_05_105500_add_order_status_to_sales_and_clean_data', 1),
(182, '2026_09_07_211322_add_payment_details_to_sales_table', 1),
(183, '2026_09_08_232338_add_financial_fields_to_sale_returns_table', 1),
(184, '2026_09_09_000001_create_material_usages_table', 1),
(185, '2026_09_10_000001_add_technical_specs_to_sales_and_items', 1),
(186, '2026_09_10_200720_add_tax_and_terms_to_sales_table', 1),
(187, '2026_09_10_203649_add_tax_to_sale_items_table', 1),
(188, '2026_09_11_114737_add_ntn_strn_to_customers_table', 1),
(189, '2026_09_11_210000_create_repair_orders_table', 2),
(190, '2026_09_15_200000_add_doc_type_to_repair_orders_table', 3),
(191, '2026_09_19_023000_add_excel_fields_to_hr_payrolls_table', 4),
(192, '2026_09_19_023500_add_account_id_to_hr_payrolls_table', 4),
(193, '2026_09_21_000000_add_delivery_notes_to_repair_orders_table', 5),
(194, '2026_09_25_000000_fix_approved_purchase_po_status', 6),
(195, '2026_09_25_000001_add_subject_to_repair_orders_table', 7),
(196, '2026_10_12_000000_cleanup_duplicate_payroll_account_history', 8),
(197, '2026_10_26_171429_add_invoice_title_to_sales_table', 9),
(198, '2026_10_28_175459_add_party_to_voucher_details_table', 10),
(199, '2026_10_28_183200_add_settled_invoices_to_receipts_vouchers_table', 10);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(2, 'App\\Models\\User', 2),
(2, 'App\\Models\\User', 3),
(4, 'App\\Models\\User', 24),
(2, 'App\\Models\\User', 67),
(2, 'App\\Models\\User', 68);

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE `modules` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `modules`
--

INSERT INTO `modules` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'home', NULL, NULL),
(2, 'profile', NULL, NULL),
(3, 'products', NULL, NULL),
(4, 'product.bookings', NULL, NULL),
(5, 'discount.products', NULL, NULL),
(6, 'categories', NULL, NULL),
(7, 'subcategories', NULL, NULL),
(8, 'brands', NULL, NULL),
(9, 'units', NULL, NULL),
(10, 'warehouse', NULL, NULL),
(11, 'warehouse.stock', NULL, NULL),
(12, 'stock.transfer', NULL, NULL),
(13, 'stock.adjust', NULL, NULL),
(14, 'stocks', NULL, NULL),
(15, 'material.usage', NULL, NULL),
(16, 'material.usage.report', NULL, NULL),
(17, 'purchases', NULL, NULL),
(18, 'purchase.returns', NULL, NULL),
(19, 'vendors', NULL, NULL),
(20, 'vendor.bilties', NULL, NULL),
(21, 'inward.gatepass', NULL, NULL),
(22, 'sales', NULL, NULL),
(23, 'sales.returns', NULL, NULL),
(24, 'customers', NULL, NULL),
(25, 'customer.ledger', NULL, NULL),
(26, 'bookings', NULL, NULL),
(27, 'checkbook', NULL, NULL),
(28, 'chart.of.accounts', NULL, NULL),
(29, 'expense.voucher', NULL, NULL),
(30, 'receipts.voucher', NULL, NULL),
(31, 'journal.voucher', NULL, NULL),
(32, 'payment.voucher', NULL, NULL),
(33, 'income.voucher', NULL, NULL),
(34, 'item.stock.report', NULL, NULL),
(35, 'purchase.report', NULL, NULL),
(36, 'sale.report', NULL, NULL),
(37, 'reporting', NULL, NULL),
(38, 'inventory.onhand', NULL, NULL),
(39, 'users', NULL, NULL),
(40, 'roles', NULL, NULL),
(41, 'permissions', NULL, NULL),
(42, 'branches', NULL, NULL),
(43, 'zones', NULL, NULL),
(44, 'sales.officers', NULL, NULL),
(45, 'narrations', NULL, NULL),
(46, 'package.types', NULL, NULL),
(47, 'hr.departments', NULL, NULL),
(48, 'hr.employees', NULL, NULL),
(49, 'hr.attendance', NULL, NULL),
(50, 'hr.payroll', NULL, NULL),
(51, 'hr.leaves', NULL, NULL),
(52, 'hr.designations', NULL, NULL),
(53, 'hr.shifts', NULL, NULL),
(54, 'hr.holidays', NULL, NULL),
(55, 'hr.salary.structure', NULL, NULL),
(56, 'hr.loans', NULL, NULL),
(57, 'hr.biometric.devices', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `narrations`
--

CREATE TABLE `narrations` (
  `id` bigint UNSIGNED NOT NULL,
  `expense_head` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `narration` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `narrations`
--

INSERT INTO `narrations` (`id`, `expense_head`, `narration`, `created_at`, `updated_at`) VALUES
(1, 'Expense voucher', 'Staff Lunch', '2026-10-19 06:36:16', '2026-10-19 06:36:16');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint UNSIGNED NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `package_types`
--

CREATE TABLE `package_types` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_vouchers`
--

CREATE TABLE `payment_vouchers` (
  `id` bigint UNSIGNED NOT NULL,
  `pvid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receipt_date` date DEFAULT NULL,
  `entry_date` date DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `party_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tel` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `narration_id` text COLLATE utf8mb4_unicode_ci,
  `reference_no` text COLLATE utf8mb4_unicode_ci,
  `row_account_head` text COLLATE utf8mb4_unicode_ci,
  `row_account_id` text COLLATE utf8mb4_unicode_ci,
  `discount_value` text COLLATE utf8mb4_unicode_ci,
  `kg` text COLLATE utf8mb4_unicode_ci,
  `rate` text COLLATE utf8mb4_unicode_ci,
  `amount` text COLLATE utf8mb4_unicode_ci,
  `total_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_vouchers`
--

INSERT INTO `payment_vouchers` (`id`, `pvid`, `receipt_date`, `entry_date`, `type`, `party_id`, `tel`, `remarks`, `narration_id`, `reference_no`, `row_account_head`, `row_account_id`, `discount_value`, `kg`, `rate`, `amount`, `total_amount`, `created_at`, `updated_at`) VALUES
(1, 'PVID-0001', '2026-10-19', '2026-10-19', '[\"2\"]', '[\"3\"]', NULL, 'ATM WITHDRAW', '[\"\"]', '[null]', '1', '2', '[\"0\"]', NULL, '[\"0\"]', '[\"5000\"]', 5000.00, '2026-10-19 06:05:38', '2026-10-19 06:05:38'),
(2, 'PVID-0002', '2026-10-27', '2026-10-28', '[\"vendor\"]', '[\"3\"]', NULL, 'venders payments from LTE Meezan', '[\"\"]', '[null]', '1', '4', '[\"0\"]', NULL, '[\"0\"]', '[\"100000\"]', 100000.00, '2026-10-28 09:46:55', '2026-10-29 10:15:02'),
(3, 'PVID-0003', '2026-10-29', '2026-10-29', '[\"vendor\"]', '[\"4\"]', NULL, 'vender payments from LTE Meezan', '[\"\"]', '[null]', '1', '2', '[\"0\"]', NULL, '[\"0\"]', '[\"100000\"]', 100000.00, '2026-10-29 10:16:42', '2026-10-29 10:16:42');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'home.view', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(2, 'home.create', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(3, 'home.edit', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(4, 'home.delete', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(5, 'profile.view', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(6, 'profile.create', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(7, 'profile.edit', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(8, 'profile.delete', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(9, 'products.view', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(10, 'products.create', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(11, 'products.edit', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(12, 'products.delete', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(13, 'product.bookings.view', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(14, 'product.bookings.create', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(15, 'product.bookings.edit', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(16, 'product.bookings.delete', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(17, 'discount.products.view', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(18, 'discount.products.create', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(19, 'discount.products.edit', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(20, 'discount.products.delete', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(21, 'categories.view', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(22, 'categories.create', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(23, 'categories.edit', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(24, 'categories.delete', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(25, 'subcategories.view', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(26, 'subcategories.create', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(27, 'subcategories.edit', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(28, 'subcategories.delete', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(29, 'brands.view', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(30, 'brands.create', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(31, 'brands.edit', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(32, 'brands.delete', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(33, 'units.view', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(34, 'units.create', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(35, 'units.edit', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(36, 'units.delete', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(37, 'warehouse.view', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(38, 'warehouse.create', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(39, 'warehouse.edit', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(40, 'warehouse.delete', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(41, 'warehouse.stock.view', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(42, 'warehouse.stock.create', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(43, 'warehouse.stock.edit', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(44, 'warehouse.stock.delete', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(45, 'stock.transfer.view', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(46, 'stock.transfer.create', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(47, 'stock.transfer.edit', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(48, 'stock.transfer.delete', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(49, 'stock.adjust.view', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(50, 'stock.adjust.create', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(51, 'stock.adjust.edit', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(52, 'stock.adjust.delete', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(53, 'stocks.view', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(54, 'stocks.create', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(55, 'stocks.edit', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(56, 'stocks.delete', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(57, 'material.usage.view', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(58, 'material.usage.create', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(59, 'material.usage.edit', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(60, 'material.usage.delete', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(61, 'material.usage.report.view', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(62, 'material.usage.report.create', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(63, 'material.usage.report.edit', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(64, 'material.usage.report.delete', 'web', '2026-09-11 10:46:21', '2026-09-11 10:46:21'),
(65, 'purchases.view', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(66, 'purchases.create', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(67, 'purchases.edit', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(68, 'purchases.delete', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(69, 'purchase.returns.view', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(70, 'purchase.returns.create', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(71, 'purchase.returns.edit', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(72, 'purchase.returns.delete', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(73, 'vendors.view', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(74, 'vendors.create', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(75, 'vendors.edit', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(76, 'vendors.delete', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(77, 'vendor.bilties.view', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(78, 'vendor.bilties.create', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(79, 'vendor.bilties.edit', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(80, 'vendor.bilties.delete', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(81, 'inward.gatepass.view', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(82, 'inward.gatepass.create', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(83, 'inward.gatepass.edit', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(84, 'inward.gatepass.delete', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(85, 'sales.view', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(86, 'sales.create', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(87, 'sales.edit', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(88, 'sales.delete', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(89, 'sales.returns.view', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(90, 'sales.returns.create', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(91, 'sales.returns.edit', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(92, 'sales.returns.delete', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(93, 'customers.view', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(94, 'customers.create', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(95, 'customers.edit', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(96, 'customers.delete', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(97, 'customer.ledger.view', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(98, 'customer.ledger.create', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(99, 'customer.ledger.edit', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(100, 'customer.ledger.delete', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(101, 'bookings.view', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(102, 'bookings.create', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(103, 'bookings.edit', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(104, 'bookings.delete', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(105, 'checkbook.view', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(106, 'checkbook.create', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(107, 'checkbook.edit', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(108, 'checkbook.delete', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(109, 'chart.of.accounts.view', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(110, 'chart.of.accounts.create', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(111, 'chart.of.accounts.edit', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(112, 'chart.of.accounts.delete', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(113, 'expense.voucher.view', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(114, 'expense.voucher.create', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(115, 'expense.voucher.edit', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(116, 'expense.voucher.delete', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(117, 'receipts.voucher.view', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(118, 'receipts.voucher.create', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(119, 'receipts.voucher.edit', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(120, 'receipts.voucher.delete', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(121, 'journal.voucher.view', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(122, 'journal.voucher.create', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(123, 'journal.voucher.edit', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(124, 'journal.voucher.delete', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(125, 'payment.voucher.view', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(126, 'payment.voucher.create', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(127, 'payment.voucher.edit', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(128, 'payment.voucher.delete', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(129, 'income.voucher.view', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(130, 'income.voucher.create', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(131, 'income.voucher.edit', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(132, 'income.voucher.delete', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(133, 'item.stock.report.view', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(134, 'item.stock.report.create', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(135, 'item.stock.report.edit', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(136, 'item.stock.report.delete', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(137, 'purchase.report.view', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(138, 'purchase.report.create', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(139, 'purchase.report.edit', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(140, 'purchase.report.delete', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(141, 'sale.report.view', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(142, 'sale.report.create', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(143, 'sale.report.edit', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(144, 'sale.report.delete', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(145, 'reporting.view', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(146, 'reporting.create', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(147, 'reporting.edit', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(148, 'reporting.delete', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(149, 'recovery.report.view', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(150, 'recovery.report.create', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(151, 'recovery.report.edit', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(152, 'recovery.report.delete', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(153, 'payable.report.view', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(154, 'payable.report.create', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(155, 'payable.report.edit', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(156, 'payable.report.delete', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(157, 'parties.balance.report.view', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(158, 'parties.balance.report.create', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(159, 'parties.balance.report.edit', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(160, 'parties.balance.report.delete', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(161, 'aging.report.view', 'web', '2026-09-11 10:46:22', '2026-09-11 10:46:22'),
(162, 'aging.report.create', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(163, 'aging.report.edit', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(164, 'aging.report.delete', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(165, 'balance.sheet.report.view', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(166, 'balance.sheet.report.create', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(167, 'balance.sheet.report.edit', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(168, 'balance.sheet.report.delete', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(169, 'profit.loss.report.view', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(170, 'profit.loss.report.create', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(171, 'profit.loss.report.edit', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(172, 'profit.loss.report.delete', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(173, 'inventory.onhand.view', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(174, 'inventory.onhand.create', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(175, 'inventory.onhand.edit', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(176, 'inventory.onhand.delete', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(177, 'vendor.ledger.view', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(178, 'vendor.ledger.create', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(179, 'vendor.ledger.edit', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(180, 'vendor.ledger.delete', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(181, 'users.view', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(182, 'users.create', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(183, 'users.edit', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(184, 'users.delete', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(185, 'roles.view', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(186, 'roles.create', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(187, 'roles.edit', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(188, 'roles.delete', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(189, 'permissions.view', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(190, 'permissions.create', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(191, 'permissions.edit', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(192, 'permissions.delete', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(193, 'branches.view', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(194, 'branches.create', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(195, 'branches.edit', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(196, 'branches.delete', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(197, 'zones.view', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(198, 'zones.create', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(199, 'zones.edit', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(200, 'zones.delete', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(201, 'sales.officers.view', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(202, 'sales.officers.create', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(203, 'sales.officers.edit', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(204, 'sales.officers.delete', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(205, 'narrations.view', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(206, 'narrations.create', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(207, 'narrations.edit', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(208, 'narrations.delete', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(209, 'executive.report.view', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(210, 'executive.report.create', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(211, 'executive.report.edit', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(212, 'executive.report.delete', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(213, 'package.types.view', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(214, 'package.types.create', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(215, 'package.types.edit', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(216, 'package.types.delete', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(217, 'hr.departments.view', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(218, 'hr.departments.create', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(219, 'hr.departments.edit', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(220, 'hr.departments.delete', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(221, 'hr.employees.view', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(222, 'hr.employees.create', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(223, 'hr.employees.edit', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(224, 'hr.employees.delete', 'web', '2026-09-11 10:46:23', '2026-09-11 10:46:23'),
(225, 'hr.attendance.view', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(226, 'hr.attendance.create', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(227, 'hr.attendance.edit', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(228, 'hr.attendance.delete', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(229, 'hr.payroll.view', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(230, 'hr.payroll.create', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(231, 'hr.payroll.edit', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(232, 'hr.payroll.delete', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(233, 'hr.leaves.view', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(234, 'hr.leaves.create', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(235, 'hr.leaves.edit', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(236, 'hr.leaves.delete', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(237, 'hr.designations.view', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(238, 'hr.designations.create', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(239, 'hr.designations.edit', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(240, 'hr.designations.delete', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(241, 'hr.shifts.view', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(242, 'hr.shifts.create', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(243, 'hr.shifts.edit', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(244, 'hr.shifts.delete', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(245, 'hr.holidays.view', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(246, 'hr.holidays.create', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(247, 'hr.holidays.edit', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(248, 'hr.holidays.delete', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(249, 'hr.salary.structure.view', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(250, 'hr.salary.structure.create', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(251, 'hr.salary.structure.edit', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(252, 'hr.salary.structure.delete', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(253, 'hr.loans.view', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(254, 'hr.loans.create', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(255, 'hr.loans.edit', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(256, 'hr.loans.delete', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(257, 'hr.biometric.devices.view', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(258, 'hr.biometric.devices.create', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(259, 'hr.biometric.devices.edit', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(260, 'hr.biometric.devices.delete', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(261, 'web_products.view', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(262, 'web_products.create', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(263, 'web_products.edit', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(264, 'web_products.delete', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(265, 'coupons.view', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(266, 'coupons.create', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(267, 'coupons.edit', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(268, 'coupons.delete', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(269, 'web_orders.view', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(270, 'web_orders.create', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(271, 'web_orders.edit', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(272, 'web_orders.delete', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(273, 'settings.view', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(274, 'settings.create', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(275, 'settings.edit', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(276, 'settings.delete', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(277, 'web_users.view', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(278, 'web_users.create', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(279, 'web_users.edit', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(280, 'web_users.delete', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(281, 'website-settings.view', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(282, 'website-settings.create', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(283, 'website-settings.edit', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(284, 'website-settings.delete', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(285, 'website-settings.update', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(286, 'website-settings.upload_manage', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(287, 'pos.view', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(288, 'purchase.requisitions.view', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(289, 'purchase.requisitions.create', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(290, 'purchase.requisitions.edit', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(291, 'purchase.requisitions.delete', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(292, 'grn.view', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(293, 'grn.create', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(294, 'grn.edit', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(295, 'grn.delete', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(296, 'settings.add', 'web', '2026-09-11 10:53:05', '2026-09-11 10:53:05'),
(297, 'settings.read', 'web', '2026-09-11 10:53:05', '2026-09-11 10:53:05'),
(298, 'settings.update', 'web', '2026-09-11 10:53:05', '2026-09-11 10:53:05'),
(299, 'purchase_pos.create', 'web', '2026-09-19 00:13:18', '2026-09-19 00:13:18'),
(300, 'web_products.read', 'web', '2026-09-19 00:13:18', '2026-09-19 00:13:18'),
(301, 'web_products.add', 'web', '2026-09-19 00:13:18', '2026-09-19 00:13:18'),
(302, 'coupons.read', 'web', '2026-09-19 00:13:18', '2026-09-19 00:13:18'),
(303, 'coupons.add', 'web', '2026-09-19 00:13:18', '2026-09-19 00:13:18'),
(304, 'web_orders.read', 'web', '2026-09-19 00:13:18', '2026-09-19 00:13:18'),
(305, 'web_orders.add', 'web', '2026-09-19 00:13:18', '2026-09-19 00:13:18'),
(306, 'web_users.read', 'web', '2026-09-19 00:13:18', '2026-09-19 00:13:18'),
(307, 'web_users.add', 'web', '2026-09-19 00:13:18', '2026-09-19 00:13:18');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint UNSIGNED NOT NULL,
  `creater_id` text COLLATE utf8mb4_unicode_ci,
  `category_id` text COLLATE utf8mb4_unicode_ci,
  `sub_category_id` text COLLATE utf8mb4_unicode_ci,
  `brand_id` bigint UNSIGNED DEFAULT NULL,
  `item_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'both',
  `is_part` tinyint(1) NOT NULL DEFAULT '0',
  `is_assembled` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_web_visible` tinyint(1) NOT NULL DEFAULT '0',
  `show_on_homepage` tinyint(1) NOT NULL DEFAULT '0',
  `promo_tag` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_code` text COLLATE utf8mb4_unicode_ci,
  `unit_id` text COLLATE utf8mb4_unicode_ci,
  `item_name` text COLLATE utf8mb4_unicode_ci,
  `size_mode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'by_size',
  `height` decimal(8,2) DEFAULT NULL COMMENT 'Height in cm',
  `width` decimal(8,2) DEFAULT NULL COMMENT 'Width in cm',
  `weight_per_piece` decimal(12,4) DEFAULT '0.0000',
  `pieces_per_box` int NOT NULL DEFAULT '0',
  `pieces_per_m2` decimal(10,2) NOT NULL DEFAULT '0.00',
  `alert_quantity` int DEFAULT NULL,
  `alert_carton_quantity` int DEFAULT NULL,
  `total_m2` decimal(10,2) NOT NULL,
  `price_per_m2` decimal(12,2) NOT NULL DEFAULT '0.00',
  `sale_price_per_box` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT 'Used for By Cartons and By Pieces',
  `purchase_price_per_piece` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT 'Used for By Cartons and By Pieces',
  `purchase_price_per_box` decimal(15,2) DEFAULT '0.00',
  `sale_price_per_piece` decimal(15,2) DEFAULT '0.00',
  `web_sale_price` decimal(15,2) DEFAULT NULL,
  `auto_hide_out_of_stock` tinyint(1) NOT NULL DEFAULT '0',
  `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` text COLLATE utf8mb4_unicode_ci,
  `wholesale_price` decimal(12,2) DEFAULT '0.00',
  `purchase_price_per_m2` decimal(12,2) NOT NULL DEFAULT '0.00',
  `color` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `barcode_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `web_main_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hs_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `boxes_quantity` int DEFAULT '0',
  `loose_pieces` int DEFAULT '0',
  `piece_quantity` int DEFAULT '0',
  `total_stock_qty` decimal(10,2) DEFAULT '0.00',
  `purchase_discount_percent` decimal(8,2) NOT NULL DEFAULT '0.00',
  `sale_discount_percent` decimal(8,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `creater_id`, `category_id`, `sub_category_id`, `brand_id`, `item_type`, `is_part`, `is_assembled`, `is_active`, `is_web_visible`, `show_on_homepage`, `promo_tag`, `item_code`, `unit_id`, `item_name`, `size_mode`, `height`, `width`, `weight_per_piece`, `pieces_per_box`, `pieces_per_m2`, `alert_quantity`, `alert_carton_quantity`, `total_m2`, `price_per_m2`, `sale_price_per_box`, `purchase_price_per_piece`, `purchase_price_per_box`, `sale_price_per_piece`, `web_sale_price`, `auto_hide_out_of_stock`, `meta_title`, `meta_description`, `wholesale_price`, `purchase_price_per_m2`, `color`, `created_at`, `updated_at`, `deleted_at`, `barcode_path`, `image`, `web_main_image`, `model`, `hs_code`, `boxes_quantity`, `loose_pieces`, `piece_quantity`, `total_stock_qty`, `purchase_discount_percent`, `sale_discount_percent`) VALUES
(1, '2', '1', '2', 2, 'finish_goods', 0, 1, 1, 0, 0, NULL, 'ITEM-0001', NULL, 'HF INDUCTION HEATER 15KW', 'by_pieces', 0.00, 0.00, 0.0000, 1, 0.00, NULL, 0, 0.00, 0.00, 170000.00, 0.00, 0.00, 170000.00, NULL, 0, NULL, NULL, 0.00, 0.00, '[{\"name\":\"HF INDUCTION HEATER 15KW\",\"size\":\"24\\/7 100%\",\"color\":\"WATER-COOLED\",\"stock\":0,\"sale_price\":\"170000\",\"wholesale_price\":\"0\",\"weight_per_piece\":0,\"purch_price\":\"0\",\"alert\":\"0\",\"barcode\":\"641185\",\"conv_factor\":1,\"is_base_variant\":\"1\",\"unit\":\"Pcs\"}]', '2026-10-17 11:47:06', '2026-10-17 11:47:06', NULL, '699625392876', NULL, NULL, NULL, NULL, 0, 0, 0, 0.00, 0.00, 0.00),
(2, '2', '1', '1', 1, 'finish_goods', 0, 1, 1, 0, 0, NULL, 'ITEM-0002', NULL, 'HF INDUCTION HEATER 18KW', 'by_pieces', 0.00, 0.00, 0.0000, 1, 0.00, NULL, 0, 0.00, 0.00, 190000.00, 0.00, 0.00, 190000.00, NULL, 0, NULL, NULL, 0.00, 0.00, '[{\"name\":\"HF INDUCTION HEATER 18KW\",\"size\":\"24\\/7 100%\",\"color\":\"WATER-COOLED\",\"stock\":0,\"sale_price\":\"190000\",\"wholesale_price\":\"0\",\"weight_per_piece\":0,\"purch_price\":\"0\",\"alert\":\"0\",\"barcode\":\"569353\",\"conv_factor\":1,\"is_base_variant\":\"1\",\"unit\":\"Pcs\"}]', '2026-10-17 11:47:53', '2026-10-17 11:47:53', NULL, '177501169002', NULL, NULL, NULL, NULL, 0, 0, 0, 0.00, 0.00, 0.00),
(3, '2', '1', '1', 2, 'finish_goods', 0, 1, 1, 0, 0, NULL, 'ITEM-0003', NULL, 'HF INDUCTION HEATER 25KW-A', 'by_pieces', 0.00, 0.00, 0.0000, 1, 0.00, NULL, 0, 0.00, 0.00, 260000.00, 0.00, 0.00, 260000.00, NULL, 0, NULL, NULL, 0.00, 0.00, '[{\"name\":\"HF INDUCTION HEATER 25KW-A\",\"size\":\"24\\/7 100%\",\"color\":\"WATER-COOLED\",\"stock\":0,\"sale_price\":\"260000\",\"wholesale_price\":\"0\",\"weight_per_piece\":0,\"purch_price\":\"0\",\"alert\":\"0\",\"barcode\":\"972210\",\"conv_factor\":1,\"is_base_variant\":\"1\",\"unit\":\"Pcs\"}]', '2026-10-17 11:49:22', '2026-10-17 11:49:22', NULL, '780823257819', NULL, NULL, NULL, NULL, 0, 0, 0, 0.00, 0.00, 0.00),
(4, '2', '1', '1', 2, 'finish_goods', 0, 1, 1, 0, 0, NULL, 'ITEM-0004', NULL, 'HF INDUCTION HEATER 25KW-AB', 'by_pieces', 0.00, 0.00, 0.0000, 1, 0.00, NULL, 0, 0.00, 0.00, 310000.00, 0.00, 0.00, 310000.00, NULL, 0, NULL, NULL, 0.00, 0.00, '[{\"name\":\"HF INDUCTION HEATER 25KW-AB\",\"size\":\"24\\/7 100%\",\"color\":\"WATER-COOLED\",\"stock\":0,\"sale_price\":\"310000\",\"wholesale_price\":\"0\",\"weight_per_piece\":0,\"purch_price\":\"0\",\"alert\":\"0\",\"barcode\":\"943575\",\"conv_factor\":1,\"is_base_variant\":\"1\",\"unit\":\"Pcs\"}]', '2026-10-17 11:55:38', '2026-10-17 11:55:38', NULL, '812797120186', NULL, NULL, NULL, NULL, 0, 0, 0, 0.00, 0.00, 0.00),
(5, '2', '1', '1', 2, 'finish_goods', 0, 1, 1, 0, 0, NULL, 'ITEM-0005', NULL, 'HF INDUCTION HEATER 35KW-AB', 'by_pieces', 0.00, 0.00, 0.0000, 1, 0.00, NULL, 0, 0.00, 0.00, 360000.00, 0.00, 0.00, 360000.00, NULL, 0, NULL, NULL, 0.00, 0.00, '[{\"name\":\"HF INDUCTION HEATER 35KW-AB\",\"size\":\"24\\/7 100%\",\"color\":\"WATER-COOLED\",\"stock\":0,\"sale_price\":\"360000\",\"wholesale_price\":\"0\",\"weight_per_piece\":0,\"purch_price\":\"0\",\"alert\":\"0\",\"barcode\":\"720678\",\"conv_factor\":1,\"is_base_variant\":\"1\",\"unit\":\"Pcs\"}]', '2026-10-17 12:08:18', '2026-10-17 12:08:18', NULL, '785415387466', NULL, NULL, NULL, NULL, 0, 0, 0, 0.00, 0.00, 0.00),
(6, '2', '1', '1', 2, 'finish_goods', 0, 1, 1, 0, 0, NULL, 'ITEM-0006', NULL, 'HF INDUCTION HEATER 40KW-AB', 'by_pieces', 0.00, 0.00, 0.0000, 1, 0.00, NULL, 0, 0.00, 0.00, 450000.00, 0.00, 0.00, 450000.00, NULL, 0, NULL, NULL, 0.00, 0.00, '[{\"name\":\"HF INDUCTION HEATER 40KW-AB\",\"size\":\"24\\/7 100%\",\"color\":\"WATER-COOLED\",\"stock\":0,\"sale_price\":\"450000\",\"wholesale_price\":\"0\",\"weight_per_piece\":0,\"purch_price\":\"0\",\"alert\":\"0\",\"barcode\":\"187099\",\"conv_factor\":1,\"is_base_variant\":\"1\",\"unit\":\"Pcs\"}]', '2026-10-17 12:09:28', '2026-10-17 12:09:28', NULL, '111909384487', NULL, NULL, NULL, NULL, 0, 0, 0, 0.00, 0.00, 0.00),
(7, '2', '1', '1', 2, 'finish_goods', 0, 1, 1, 0, 0, NULL, 'ITEM-0007', NULL, 'HF INDUCTION HEATER 60KW-AB', 'by_pieces', 0.00, 0.00, 0.0000, 1, 0.00, NULL, 0, 0.00, 0.00, 580000.00, 0.00, 0.00, 580000.00, NULL, 0, NULL, NULL, 0.00, 0.00, '[{\"name\":\"HF INDUCTION HEATER 60KW-AB\",\"size\":\"24\\/7 100%\",\"color\":\"WATER-COOLED\",\"stock\":0,\"sale_price\":\"580000\",\"wholesale_price\":\"0\",\"weight_per_piece\":0,\"purch_price\":\"0\",\"alert\":\"0\",\"barcode\":\"943412\",\"conv_factor\":1,\"is_base_variant\":\"1\",\"unit\":\"Pcs\"}]', '2026-10-17 12:10:10', '2026-10-17 12:10:10', NULL, '956189734490', NULL, NULL, NULL, NULL, 0, 0, 0, 0.00, 0.00, 0.00),
(8, '2', '1', '1', 2, 'finish_goods', 0, 1, 1, 0, 0, NULL, 'ITEM-0008', NULL, 'HF INDUCTION HEATER 80KW-AB', 'by_pieces', 0.00, 0.00, 0.0000, 1, 0.00, NULL, 0, 0.00, 0.00, 750000.00, 0.00, 0.00, 750000.00, NULL, 0, NULL, NULL, 0.00, 0.00, '[{\"name\":\"HF INDUCTION HEATER 80KW-AB\",\"size\":\"24\\/7 100%\",\"color\":\"WATER-COOLED\",\"stock\":0,\"sale_price\":\"750000\",\"wholesale_price\":\"0\",\"weight_per_piece\":0,\"purch_price\":\"0\",\"alert\":\"0\",\"barcode\":\"316752\",\"conv_factor\":1,\"is_base_variant\":\"1\",\"unit\":\"Pcs\"}]', '2026-10-17 12:21:44', '2026-10-17 12:21:44', NULL, '521264606163', NULL, NULL, NULL, NULL, 0, 0, 0, 0.00, 0.00, 0.00),
(9, '2', '1', '1', 2, 'finish_goods', 0, 1, 1, 0, 0, NULL, 'ITEM-0009', NULL, 'HF INDUCTION HEATER 100KW-AB', 'by_pieces', 0.00, 0.00, 0.0000, 1, 0.00, NULL, 0, 0.00, 0.00, 1150000.00, 0.00, 0.00, 1150000.00, NULL, 0, NULL, NULL, 0.00, 0.00, '[{\"name\":\"HF INDUCTION HEATER 100KW-AB\",\"size\":\"24\\/7 100%\",\"color\":\"WATER-COOLED\",\"stock\":0,\"sale_price\":\"1150000\",\"wholesale_price\":\"0\",\"weight_per_piece\":0,\"purch_price\":\"0\",\"alert\":\"0\",\"barcode\":\"870328\",\"conv_factor\":1,\"is_base_variant\":\"1\",\"unit\":\"Pcs\"}]', '2026-10-17 12:31:54', '2026-10-17 12:31:54', NULL, '253143505679', NULL, NULL, NULL, NULL, 0, 0, 0, 0.00, 0.00, 0.00),
(10, '2', '1', '1', 2, 'finish_goods', 0, 1, 1, 0, 0, NULL, 'ITEM-0010', NULL, 'HF INDUCTION HEATER 120KW-AB', 'by_pieces', 0.00, 0.00, 0.0000, 1, 0.00, NULL, 0, 0.00, 0.00, 1350000.00, 0.00, 0.00, 1350000.00, NULL, 0, NULL, NULL, 0.00, 0.00, '[{\"name\":\"HF INDUCTION HEATER 120KW-AB\",\"size\":\"24\\/7 100%\",\"color\":\"WATER-COOLED\",\"stock\":0,\"sale_price\":\"1350000\",\"wholesale_price\":\"0\",\"weight_per_piece\":0,\"purch_price\":\"0\",\"alert\":\"0\",\"barcode\":\"204832\",\"conv_factor\":1,\"is_base_variant\":\"1\",\"unit\":\"Pcs\"}]', '2026-10-17 12:32:52', '2026-10-17 12:32:52', NULL, '843156866801', NULL, NULL, NULL, NULL, 0, 0, 0, 0.00, 0.00, 0.00),
(11, '2', '1', '1', 2, 'finish_goods', 0, 1, 1, 0, 0, NULL, 'ITEM-0011', NULL, 'HF INDUCTION HEATER 160KW-AB', 'by_pieces', 0.00, 0.00, 0.0000, 1, 0.00, NULL, 0, 0.00, 0.00, 1750000.00, 0.00, 0.00, 1750000.00, NULL, 0, NULL, NULL, 0.00, 0.00, '[{\"name\":\"HF INDUCTION HEATER 160KW-AB\",\"size\":\"24\\/7 100%\",\"color\":\"WATER-COOLED\",\"stock\":0,\"sale_price\":\"1750000\",\"wholesale_price\":\"0\",\"weight_per_piece\":0,\"purch_price\":\"0\",\"alert\":\"0\",\"barcode\":\"879576\",\"conv_factor\":1,\"is_base_variant\":\"1\",\"unit\":\"Pcs\"}]', '2026-10-17 12:33:29', '2026-10-17 12:33:29', NULL, '525677219630', NULL, NULL, NULL, NULL, 0, 0, 0, 0.00, 0.00, 0.00),
(12, '2', '1', '1', 2, 'finish_goods', 0, 1, 1, 0, 0, NULL, 'ITEM-0012', NULL, 'HF INDUCTION HEATER 200KW-AB', 'by_pieces', 0.00, 0.00, 0.0000, 1, 0.00, NULL, 0, 0.00, 0.00, 2200000.00, 0.00, 0.00, 2200000.00, NULL, 0, NULL, NULL, 0.00, 0.00, '[{\"name\":\"HF INDUCTION HEATER 200KW-AB\",\"size\":\"24\\/7 100%\",\"color\":\"WATER-COOLED\",\"stock\":0,\"sale_price\":\"2200000\",\"wholesale_price\":\"0\",\"weight_per_piece\":0,\"purch_price\":\"0\",\"alert\":\"0\",\"barcode\":\"870873\",\"conv_factor\":1,\"is_base_variant\":\"1\",\"unit\":\"Pcs\"}]', '2026-10-17 12:34:15', '2026-10-17 12:34:15', NULL, '954167080282', NULL, NULL, NULL, NULL, 0, 0, 0, 0.00, 0.00, 0.00),
(13, '67', '2', NULL, NULL, 'finish_goods', 0, 1, 1, 0, 0, NULL, 'ITEM-0013', NULL, 'Conveyer for Sciessor Forging', 'by_pieces', 0.00, 0.00, 0.0000, 0, 0.00, 0, 0, 0.00, 0.00, 170000.00, 0.00, 0.00, 170000.00, NULL, 0, NULL, NULL, 0.00, 0.00, '[{\"name\":\"Conveyer for Sciessor Forging\",\"size\":\"-\",\"color\":\"-\",\"stock\":\"0\",\"sale_price\":\"170000\",\"wholesale_price\":\"0\",\"weight_per_piece\":\"1000\",\"purch_price\":\"0\",\"alert\":\"0\",\"barcode\":\"851438\",\"conv_factor\":1,\"is_base_variant\":\"1\",\"unit\":\"Pcs\"}]', '2026-10-17 12:36:43', '2026-10-30 18:57:47', NULL, '956847990068', NULL, NULL, NULL, NULL, 0, 0, 0, 0.00, 0.00, 0.00),
(14, '2', '2', NULL, 3, 'raw_material', 1, 0, 1, 0, 0, NULL, 'ITEM-0014', NULL, 'MAIN BOARD LTZ', 'by_pieces', 0.00, 0.00, 0.0000, 1, 0.00, NULL, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, 0, NULL, NULL, 0.00, 0.00, '[{\"name\":\"MAIN BOARD LTZ\",\"size\":\"-\",\"color\":\"-\",\"stock\":0,\"sale_price\":\"0\",\"wholesale_price\":\"0\",\"weight_per_piece\":0,\"purch_price\":\"0\",\"alert\":\"0\",\"barcode\":\"414554\",\"conv_factor\":1,\"is_base_variant\":\"1\",\"unit\":\"Pcs\"}]', '2026-10-17 13:08:21', '2026-10-17 13:08:21', NULL, '360124959661', NULL, NULL, NULL, NULL, 0, 0, 0, 0.00, 0.00, 0.00),
(15, '2', '2', NULL, 3, 'raw_material', 1, 0, 1, 0, 0, NULL, 'ITEM-0015', NULL, 'DRIVER BOARD LTZ', 'by_pieces', 0.00, 0.00, 0.0000, 1, 0.00, NULL, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, 0, NULL, NULL, 0.00, 0.00, '[{\"name\":\"DRIVER BOARD LTZ\",\"size\":\"-\",\"color\":\"-\",\"stock\":0,\"sale_price\":\"0\",\"wholesale_price\":\"0\",\"weight_per_piece\":0,\"purch_price\":\"0\",\"alert\":\"0\",\"barcode\":\"619075\",\"conv_factor\":1,\"is_base_variant\":\"1\",\"unit\":\"Pcs\"}]', '2026-10-17 13:09:18', '2026-10-17 13:09:18', NULL, '673057095733', NULL, NULL, NULL, NULL, 0, 0, 0, 0.00, 0.00, 0.00),
(16, '2', '2', NULL, 3, 'raw_material', 1, 0, 1, 0, 0, NULL, 'ITEM-0016', NULL, 'POWER SUPPLY BOARD LTZ', 'by_pieces', 0.00, 0.00, 0.0000, 1, 0.00, NULL, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, 0, NULL, NULL, 0.00, 0.00, '[{\"name\":\"POWER SUPPLY BOARD LTZ\",\"size\":\"-\",\"color\":\"-\",\"stock\":0,\"sale_price\":\"0\",\"wholesale_price\":\"0\",\"weight_per_piece\":0,\"purch_price\":\"0\",\"alert\":\"0\",\"barcode\":\"498690\",\"conv_factor\":1,\"is_base_variant\":\"1\",\"unit\":\"Pcs\"}]', '2026-10-17 13:09:49', '2026-10-17 13:09:49', NULL, '642232505683', NULL, NULL, NULL, NULL, 0, 0, 0, 0.00, 0.00, 0.00),
(17, '2', '2', NULL, 3, 'raw_material', 1, 0, 1, 0, 0, NULL, 'ITEM-0017', NULL, 'CHOPPER DRIVER CARD LTZ', 'by_pieces', 0.00, 0.00, 0.0000, 1, 0.00, NULL, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, 0, NULL, NULL, 0.00, 0.00, '[{\"name\":\"CHOPPER DRIVER CARD LTZ\",\"size\":\"-\",\"color\":\"-\",\"stock\":0,\"sale_price\":\"0\",\"wholesale_price\":\"0\",\"weight_per_piece\":0,\"purch_price\":\"0\",\"alert\":\"0\",\"barcode\":\"302156\",\"conv_factor\":1,\"is_base_variant\":\"1\",\"unit\":\"Pcs\"}]', '2026-10-17 13:10:14', '2026-10-17 13:10:14', NULL, '459100662333', NULL, NULL, NULL, NULL, 0, 0, 0, 0.00, 0.00, 0.00),
(18, '2', '2', NULL, 3, 'raw_material', 1, 0, 1, 0, 0, NULL, 'ITEM-0018', NULL, 'FREQUENCY METER LTZ', 'by_pieces', 0.00, 0.00, 0.0000, 1, 0.00, NULL, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, 0, NULL, NULL, 0.00, 0.00, '[{\"name\":\"FREQUENCY METER LTZ\",\"size\":\"-\",\"color\":\"-\",\"stock\":0,\"sale_price\":\"0\",\"wholesale_price\":\"0\",\"weight_per_piece\":0,\"purch_price\":\"0\",\"alert\":\"0\",\"barcode\":\"236930\",\"conv_factor\":1,\"is_base_variant\":\"1\",\"unit\":\"Pcs\"}]', '2026-10-17 13:10:37', '2026-10-17 13:10:37', NULL, '452320981649', NULL, NULL, NULL, NULL, 0, 0, 0, 0.00, 0.00, 0.00),
(19, '2', '2', NULL, 3, 'raw_material', 1, 0, 1, 0, 0, NULL, 'ITEM-0019', NULL, 'ANALOGE AMP METER', 'by_pieces', 0.00, 0.00, 0.0000, 1, 0.00, NULL, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, 0, NULL, NULL, 0.00, 0.00, '[{\"name\":\"ANALOGE AMP METER\",\"size\":\"-\",\"color\":\"-\",\"stock\":0,\"sale_price\":\"0\",\"wholesale_price\":\"0\",\"weight_per_piece\":0,\"purch_price\":\"0\",\"alert\":\"0\",\"barcode\":\"750754\",\"conv_factor\":1,\"is_base_variant\":\"1\",\"unit\":\"Pcs\"}]', '2026-10-17 13:10:59', '2026-10-17 13:10:59', NULL, '610699157152', NULL, NULL, NULL, NULL, 0, 0, 0, 0.00, 0.00, 0.00),
(20, '2', '2', NULL, 3, 'raw_material', 1, 0, 1, 0, 0, NULL, 'ITEM-0020', NULL, 'ANALOGE VOLT METER', 'by_pieces', 0.00, 0.00, 0.0000, 1, 0.00, NULL, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, 0, NULL, NULL, 0.00, 0.00, '[{\"name\":\"ANALOGE VOLT METER\",\"size\":\"-\",\"color\":\"-\",\"stock\":0,\"sale_price\":\"0\",\"wholesale_price\":\"0\",\"weight_per_piece\":0,\"purch_price\":\"0\",\"alert\":\"0\",\"barcode\":\"153092\",\"conv_factor\":1,\"is_base_variant\":\"1\",\"unit\":\"Pcs\"}]', '2026-10-17 13:11:28', '2026-10-17 13:11:28', NULL, '612349064275', NULL, NULL, NULL, NULL, 0, 0, 0, 0.00, 0.00, 0.00),
(21, '2', '2', NULL, 3, 'raw_material', 1, 0, 1, 0, 0, NULL, 'ITEM-0021', NULL, 'Current Sensor CT', 'by_pieces', 0.00, 0.00, 0.0000, 1, 0.00, NULL, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, 0, NULL, NULL, 0.00, 0.00, '[{\"name\":\"Current Sensor CT\",\"size\":\"-\",\"color\":\"-\",\"stock\":18,\"sale_price\":\"0\",\"wholesale_price\":\"0\",\"weight_per_piece\":0,\"purch_price\":\"0\",\"alert\":\"0\",\"barcode\":\"138752\",\"conv_factor\":1,\"is_base_variant\":\"1\",\"unit\":\"Pcs\"}]', '2026-10-19 06:45:57', '2026-10-19 06:45:57', NULL, '653296410941', NULL, NULL, NULL, NULL, 0, 0, 0, 0.00, 0.00, 0.00),
(22, '2', '2', NULL, 4, 'raw_material', 1, 0, 1, 0, 0, NULL, 'ITEM-0022', NULL, 'FF100 R12 KS4', 'by_pieces', 0.00, 0.00, 0.0000, 1, 0.00, NULL, 10, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, 0, NULL, NULL, 0.00, 0.00, '[{\"name\":\"FF100 R12 KS4\",\"size\":\"-\",\"color\":\"-\",\"stock\":8,\"sale_price\":\"0\",\"wholesale_price\":\"0\",\"weight_per_piece\":0,\"purch_price\":\"0\",\"alert\":\"10\",\"barcode\":\"638258\",\"conv_factor\":1,\"is_base_variant\":\"1\",\"unit\":\"Pcs\"}]', '2026-10-19 08:39:43', '2026-10-19 08:39:43', NULL, '638367841655', NULL, NULL, NULL, NULL, 0, 0, 0, 0.00, 0.00, 0.00),
(23, '67', '2', NULL, NULL, 'finish_goods', 0, 1, 1, 0, 0, NULL, 'ITEM-0023', NULL, 'Induction Coil', 'by_pieces', 0.00, 0.00, 0.0000, 0, 0.00, 0, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, 0, NULL, NULL, 0.00, 0.00, '[{\"name\":\"Induction Coil\",\"size\":\"-\",\"color\":\"-\",\"stock\":\"0\",\"sale_price\":\"0\",\"wholesale_price\":\"0\",\"weight_per_piece\":\"1000\",\"purch_price\":\"0\",\"alert\":\"0\",\"barcode\":\"260887\",\"conv_factor\":1,\"is_base_variant\":\"1\",\"unit\":\"Pcs\"}]', '2026-10-28 06:45:52', '2026-10-30 18:58:53', NULL, '910354664330', NULL, NULL, NULL, NULL, 0, 0, 0, 0.00, 0.00, 0.00),
(24, '67', '1', '2', 2, 'finish_goods', 0, 1, 1, 0, 0, NULL, 'ITEM-0024', NULL, 'AIR-COOLED INDUCTION HEATER 25KW', 'by_pieces', 0.00, 0.00, 0.0000, 1, 0.00, NULL, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, 0, NULL, NULL, 0.00, 0.00, '[{\"name\":\"AIR-COOLED INDUCTION HEATER 25KW\",\"size\":\"24\\/7 100%\",\"color\":\"AIR COOLED\",\"stock\":0,\"sale_price\":\"0\",\"wholesale_price\":\"0\",\"weight_per_piece\":0,\"purch_price\":\"0\",\"alert\":\"0\",\"barcode\":\"638163\",\"conv_factor\":1,\"is_base_variant\":\"1\",\"unit\":\"Pcs\"}]', '2026-10-28 12:02:53', '2026-10-28 12:02:53', NULL, '693666776005', NULL, NULL, NULL, NULL, 0, 0, 0, 0.00, 0.00, 0.00),
(25, '67', '1', '2', 2, 'finish_goods', 0, 1, 1, 0, 0, NULL, 'ITEM-0025', NULL, 'AIR-COOLED INDUCTION HEATER 40KW', 'by_pieces', 0.00, 0.00, 0.0000, 1, 0.00, NULL, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, 0, NULL, NULL, 0.00, 0.00, '[{\"name\":\"AIR-COOLED INDUCTION HEATER 40KW\",\"size\":\"24\\/7 100%\",\"color\":\"AIR COOLED\",\"stock\":0,\"sale_price\":\"0\",\"wholesale_price\":\"0\",\"weight_per_piece\":0,\"purch_price\":\"0\",\"alert\":\"0\",\"barcode\":\"622662\",\"conv_factor\":1,\"is_base_variant\":\"1\",\"unit\":\"Pcs\"}]', '2026-10-28 12:03:28', '2026-10-28 12:03:28', NULL, '532590881453', NULL, NULL, NULL, NULL, 0, 0, 0, 0.00, 0.00, 0.00),
(26, '67', '1', '2', 2, 'finish_goods', 0, 1, 1, 0, 0, NULL, 'ITEM-0026', NULL, 'AIR-COOLED INDUCTION HEATER 60KW', 'by_pieces', 0.00, 0.00, 0.0000, 1, 0.00, NULL, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, 0, NULL, NULL, 0.00, 0.00, '[{\"name\":\"AIR-COOLED INDUCTION HEATER 60KW\",\"size\":\"24\\/7 100%\",\"color\":\"AIR COOLED\",\"stock\":0,\"sale_price\":\"0\",\"wholesale_price\":\"0\",\"weight_per_piece\":0,\"purch_price\":\"0\",\"alert\":\"0\",\"barcode\":\"823554\",\"conv_factor\":1,\"is_base_variant\":\"1\",\"unit\":\"Pcs\"}]', '2026-10-28 12:04:17', '2026-10-28 12:04:17', NULL, '814308497174', NULL, NULL, NULL, NULL, 0, 0, 0, 0.00, 0.00, 0.00),
(27, '67', '1', '2', 2, 'finish_goods', 0, 1, 1, 0, 0, NULL, 'ITEM-0027', NULL, 'AIR-COOLED INDUCTION HEATER 80KW', 'by_pieces', 0.00, 0.00, 0.0000, 1, 0.00, NULL, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, 0, NULL, NULL, 0.00, 0.00, '[{\"name\":\"AIR-COOLED INDUCTION HEATER 80KW\",\"size\":\"24\\/7 100%\",\"color\":\"AIR COOLED\",\"stock\":0,\"sale_price\":\"0\",\"wholesale_price\":\"0\",\"weight_per_piece\":0,\"purch_price\":\"0\",\"alert\":\"0\",\"barcode\":\"723505\",\"conv_factor\":1,\"is_base_variant\":\"1\",\"unit\":\"Pcs\"}]', '2026-10-28 12:05:59', '2026-10-28 12:05:59', NULL, '439049642135', NULL, NULL, NULL, NULL, 0, 0, 0, 0.00, 0.00, 0.00),
(28, '67', '2', '5', 5, 'finish_goods', 0, 1, 1, 0, 0, NULL, 'ITEM-0028', NULL, 'Heat Proof Wire Coil', 'by_pieces', 0.00, 0.00, 0.0000, 1, 0.00, NULL, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, 0, NULL, NULL, 0.00, 0.00, '[{\"name\":\"Heat Proof Wire Coil\",\"size\":\"-\",\"color\":\"-\",\"stock\":0,\"sale_price\":\"0\",\"wholesale_price\":\"0\",\"weight_per_piece\":0,\"purch_price\":\"0\",\"alert\":\"0\",\"barcode\":\"458502\",\"conv_factor\":1,\"is_base_variant\":\"1\",\"unit\":\"Pcs\"}]', '2026-10-28 12:17:57', '2026-10-28 12:17:57', NULL, '481844099240', NULL, NULL, NULL, NULL, 0, 0, 0, 0.00, 0.00, 0.00),
(29, '67', '3', NULL, NULL, 'finish_goods', 0, 1, 1, 0, 0, NULL, 'ITEM-0029', NULL, 'Installation Charges', 'by_pieces', 0.00, 0.00, 0.0000, 0, 0.00, 0, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, 0, NULL, NULL, 0.00, 0.00, '[{\"name\":\"Installation Charges\",\"size\":\"-\",\"color\":\"-\",\"stock\":\"0\",\"sale_price\":\"0\",\"wholesale_price\":\"0\",\"weight_per_piece\":\"1000\",\"purch_price\":\"0\",\"alert\":\"0\",\"barcode\":\"362534\",\"conv_factor\":1,\"is_base_variant\":\"1\",\"unit\":\"Pcs\"}]', '2026-10-28 12:19:22', '2026-10-30 18:57:01', NULL, '752571169432', NULL, NULL, NULL, NULL, 0, 0, 0, 0.00, 0.00, 0.00),
(30, '67', '2', NULL, 7, 'finish_goods', 0, 1, 1, 0, 0, NULL, 'ITEM-0030', NULL, 'Insulation/Other Materials', 'by_pieces', 0.00, 0.00, 0.0000, 1, 0.00, NULL, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, 0, NULL, NULL, 0.00, 0.00, '[{\"name\":\"Insulation\\/Other Materials\",\"size\":\"-\",\"color\":\"-\",\"stock\":0,\"sale_price\":\"0\",\"wholesale_price\":\"0\",\"weight_per_piece\":0,\"purch_price\":\"0\",\"alert\":\"0\",\"barcode\":\"852269\",\"conv_factor\":1,\"is_base_variant\":\"1\",\"unit\":\"Pcs\"}]', '2026-10-28 12:34:31', '2026-10-28 12:34:31', NULL, '598189074543', NULL, NULL, NULL, NULL, 0, 0, 0, 0.00, 0.00, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `product_bookings`
--

CREATE TABLE `product_bookings` (
  `id` bigint UNSIGNED NOT NULL,
  `customer` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference` text COLLATE utf8mb4_unicode_ci,
  `product` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_code` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `per_price` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `per_discount` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `per_total` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_amount_Words` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_bill_amount` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_extradiscount` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_net` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `cash` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `card` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `change` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_items` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `booking_date` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `sale_date` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_discounts`
--

CREATE TABLE `product_discounts` (
  `id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `actual_price` decimal(10,2) DEFAULT NULL,
  `discount_percentage` decimal(5,2) DEFAULT NULL,
  `discount_amount` decimal(10,2) DEFAULT NULL,
  `final_price` decimal(10,2) NOT NULL,
  `total_discount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `date` date NOT NULL DEFAULT '2026-09-11',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_web_images`
--

CREATE TABLE `product_web_images` (
  `id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `id` bigint UNSIGNED NOT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `warehouse_id` bigint UNSIGNED NOT NULL,
  `vendor_id` bigint UNSIGNED NOT NULL,
  `purchase_type` enum('local','import') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'local',
  `pr_id` bigint UNSIGNED DEFAULT NULL,
  `currency` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PKR',
  `exchange_rate` decimal(12,6) NOT NULL DEFAULT '1.000000',
  `po_status` enum('pending','approved','in_transit','partially_received','received','partially_complete','complete','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `proforma_invoice_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_method` enum('cash','bank_transfer','lc','tt_advance','credit') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expected_delivery_date` date DEFAULT NULL,
  `delivery_terms` enum('FOB','CIF','CFR','EXW','DDP','DAP') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bl_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `port_of_loading` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `port_of_discharge` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `actual_arrival_date` date DEFAULT NULL,
  `clearance_date` date DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `invoice_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `additional_discount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `extra_cost` decimal(12,2) NOT NULL DEFAULT '0.00',
  `net_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `paid_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `due_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `status_purchase` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchases`
--

INSERT INTO `purchases` (`id`, `branch_id`, `warehouse_id`, `vendor_id`, `purchase_type`, `pr_id`, `currency`, `exchange_rate`, `po_status`, `proforma_invoice_no`, `payment_method`, `expected_delivery_date`, `delivery_terms`, `bl_number`, `port_of_loading`, `port_of_discharge`, `actual_arrival_date`, `clearance_date`, `created_by`, `purchase_date`, `invoice_no`, `note`, `subtotal`, `discount`, `additional_discount`, `extra_cost`, `net_amount`, `paid_amount`, `due_amount`, `status_purchase`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 1, 'local', NULL, 'PKR', 1.000000, 'complete', NULL, 'bank_transfer', NULL, 'FOB', NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-17', 'PUR-001', 'China Stock In', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'approved', '2026-10-17 13:17:10', '2026-10-23 12:04:01', NULL),
(2, 1, 1, 1, 'local', NULL, 'PKR', 1.000000, 'complete', NULL, 'bank_transfer', NULL, 'FOB', NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-18', 'PUR-002', 'Chopper stock added', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'approved', '2026-10-19 06:49:55', '2026-10-22 06:47:25', NULL),
(3, 1, 1, 2, 'local', NULL, 'PKR', 1.000000, 'complete', '2821', 'credit', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, '2026-10-16', 'PUR-003', '2821 - adamjee and SS NUT BOLTS', 19270.00, 0.00, 0.00, 0.00, 19270.00, 0.00, 19270.00, 'approved', '2026-10-19 07:14:32', '2026-10-19 07:14:32', NULL),
(4, 1, 1, 3, 'local', NULL, 'PKR', 1.000000, 'complete', '1262', 'credit', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 67, '2026-10-12', 'PUR-004', '1262 - Clump,Thimbles,Timer,Relay,Heat shrinks', 91110.00, 0.00, 0.00, 0.00, 91110.00, 0.00, 91110.00, 'approved', '2026-10-24 07:16:10', '2026-10-24 07:16:10', NULL),
(5, 1, 1, 4, 'local', NULL, 'PKR', 1.000000, 'complete', '3055', 'credit', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 67, '2026-10-20', 'PUR-005', '3055 - N3 Connectors Qty:8 @12500', 100000.00, 0.00, 0.00, 0.00, 100000.00, 0.00, 100000.00, 'approved', '2026-10-24 07:17:47', '2026-10-24 07:17:47', NULL),
(6, 1, 1, 3, 'local', NULL, 'PKR', 1.000000, 'complete', '001', 'credit', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 67, '2026-10-26', 'PUR-006', '001 - Heat shrink 16 ,8 no . duct patti , water in out tags', 25230.00, 0.00, 0.00, 0.00, 25230.00, 0.00, 25230.00, 'approved', '2026-10-26 13:35:31', '2026-10-26 13:35:31', NULL),
(7, 1, 1, 5, 'local', NULL, 'PKR', 1.000000, 'complete', '281026', 'credit', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 67, '2026-10-28', 'PUR-007', '281026 - 6\" x2 , 10\"x1cotoon, 20\"x1 cotton', 19250.00, 0.00, 0.00, 0.00, 19250.00, 0.00, 19250.00, 'approved', '2026-10-28 11:16:53', '2026-10-28 11:16:53', NULL),
(8, 1, 1, 3, 'local', NULL, 'PKR', 1.000000, 'complete', '003', 'credit', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 67, '2026-10-28', 'PUR-008', '003 - Relay, Timer,Thimbles,Foot switch,Nozzle,clump', 52580.00, 0.00, 0.00, 0.00, 52580.00, 0.00, 52580.00, 'approved', '2026-10-31 10:29:46', '2026-10-31 10:29:46', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `purchase_items`
--

CREATE TABLE `purchase_items` (
  `id` bigint UNSIGNED NOT NULL,
  `purchase_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED DEFAULT NULL,
  `color` text COLLATE utf8mb4_unicode_ci,
  `size_mode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pieces_per_box` decimal(12,2) NOT NULL DEFAULT '1.00',
  `pieces_per_m2` decimal(12,2) NOT NULL DEFAULT '0.00',
  `boxes_qty` decimal(12,2) NOT NULL DEFAULT '0.00',
  `loose_qty` decimal(12,2) NOT NULL DEFAULT '0.00',
  `length` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `width` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `item_discount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `tax_percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `foreign_unit_price` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `qty` int NOT NULL DEFAULT '0',
  `received_qty` decimal(12,3) NOT NULL DEFAULT '0.000',
  `line_total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_items`
--

INSERT INTO `purchase_items` (`id`, `purchase_id`, `product_id`, `color`, `size_mode`, `pieces_per_box`, `pieces_per_m2`, `boxes_qty`, `loose_qty`, `length`, `width`, `unit`, `price`, `item_discount`, `tax_percentage`, `tax_amount`, `foreign_unit_price`, `qty`, `received_qty`, `line_total`, `created_at`, `updated_at`) VALUES
(6, 2, 17, 'eyJuYW1lIjoiQ0hPUFBFUiBEUklWRVIgQ0FSRCBMVFoiLCJzaXplIjoiLSIsImNvbG9yIjoiLSIsInN0b2NrIjowLCJzYWxlX3ByaWNlIjoiMCIsIndob2xlc2FsZV9wcmljZSI6IjAiLCJ3ZWlnaHRfcGVyX3BpZWNlIjowLCJwdXJjaF9wcmljZSI6IjAiLCJhbGVydCI6IjAiLCJiYXJjb2RlIjoiMzAyMTU2IiwiY29udl9mYWN0b3IiOjEsImlzX2Jhc2VfdmFyaWFudCI6IjEiLCJ1bml0IjoiUGNzIiwiY3VycmVudF9zdG9jayI6MH0=', 'by_pieces', 1.00, 0.00, 0.00, 0.00, NULL, NULL, 'Pcs', 0.00, 0.00, 0.00, 0.00, 0.0000, 6, 0.000, 0.00, '2026-10-22 06:47:25', '2026-10-22 06:47:25'),
(7, 1, 14, 'eyJuYW1lIjoiTUFJTiBCT0FSRCBMVFoiLCJzaXplIjoiLSIsImNvbG9yIjoiLSIsInN0b2NrIjowLCJzYWxlX3ByaWNlIjoiMCIsIndob2xlc2FsZV9wcmljZSI6IjAiLCJ3ZWlnaHRfcGVyX3BpZWNlIjowLCJwdXJjaF9wcmljZSI6IjAiLCJhbGVydCI6IjAiLCJiYXJjb2RlIjoiNDE0NTU0IiwiY29udl9mYWN0b3IiOjEsImlzX2Jhc2VfdmFyaWFudCI6IjEiLCJ1bml0IjoiUGNzIiwiY3VycmVudF9zdG9jayI6MH0=', 'by_pieces', 1.00, 0.00, 0.00, 0.00, NULL, NULL, 'Pcs', 0.00, 0.00, 0.00, 0.00, 0.0000, 10, 0.000, 0.00, '2026-10-23 12:04:01', '2026-10-23 12:04:01'),
(8, 1, 15, 'eyJuYW1lIjoiRFJJVkVSIEJPQVJEIExUWiIsInNpemUiOiItIiwiY29sb3IiOiItIiwic3RvY2siOjAsInNhbGVfcHJpY2UiOiIwIiwid2hvbGVzYWxlX3ByaWNlIjoiMCIsIndlaWdodF9wZXJfcGllY2UiOjAsInB1cmNoX3ByaWNlIjoiMCIsImFsZXJ0IjoiMCIsImJhcmNvZGUiOiI2MTkwNzUiLCJjb252X2ZhY3RvciI6MSwiaXNfYmFzZV92YXJpYW50IjoiMSIsInVuaXQiOiJQY3MiLCJjdXJyZW50X3N0b2NrIjowfQ==', 'by_pieces', 1.00, 0.00, 0.00, 0.00, NULL, NULL, 'Pcs', 0.00, 0.00, 0.00, 0.00, 0.0000, 20, 0.000, 0.00, '2026-10-23 12:04:01', '2026-10-23 12:04:01'),
(9, 1, 16, 'eyJuYW1lIjoiUE9XRVIgU1VQUExZIEJPQVJEIExUWiIsInNpemUiOiItIiwiY29sb3IiOiItIiwic3RvY2siOjAsInNhbGVfcHJpY2UiOiIwIiwid2hvbGVzYWxlX3ByaWNlIjoiMCIsIndlaWdodF9wZXJfcGllY2UiOjAsInB1cmNoX3ByaWNlIjoiMCIsImFsZXJ0IjoiMCIsImJhcmNvZGUiOiI0OTg2OTAiLCJjb252X2ZhY3RvciI6MSwiaXNfYmFzZV92YXJpYW50IjoiMSIsInVuaXQiOiJQY3MiLCJjdXJyZW50X3N0b2NrIjowfQ==', 'by_pieces', 1.00, 0.00, 0.00, 0.00, NULL, NULL, 'Pcs', 0.00, 0.00, 0.00, 0.00, 0.0000, 10, 0.000, 0.00, '2026-10-23 12:04:01', '2026-10-23 12:04:01'),
(10, 1, 18, 'eyJuYW1lIjoiRlJFUVVFTkNZIE1FVEVSIExUWiIsInNpemUiOiItIiwiY29sb3IiOiItIiwic3RvY2siOjAsInNhbGVfcHJpY2UiOiIwIiwid2hvbGVzYWxlX3ByaWNlIjoiMCIsIndlaWdodF9wZXJfcGllY2UiOjAsInB1cmNoX3ByaWNlIjoiMCIsImFsZXJ0IjoiMCIsImJhcmNvZGUiOiIyMzY5MzAiLCJjb252X2ZhY3RvciI6MSwiaXNfYmFzZV92YXJpYW50IjoiMSIsInVuaXQiOiJQY3MiLCJjdXJyZW50X3N0b2NrIjowfQ==', 'by_pieces', 1.00, 0.00, 0.00, 0.00, NULL, NULL, 'Pcs', 0.00, 0.00, 0.00, 0.00, 0.0000, 20, 0.000, 0.00, '2026-10-23 12:04:01', '2026-10-23 12:04:01');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_requisitions`
--

CREATE TABLE `purchase_requisitions` (
  `id` bigint UNSIGNED NOT NULL,
  `pr_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `warehouse_id` bigint UNSIGNED NOT NULL,
  `requested_by` bigint UNSIGNED NOT NULL,
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `status` enum('draft','pending_approval','approved','rejected','partially_ordered','closed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `required_by_date` date DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_requisition_items`
--

CREATE TABLE `purchase_requisition_items` (
  `id` bigint UNSIGNED NOT NULL,
  `pr_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `required_qty` decimal(12,3) NOT NULL DEFAULT '0.000',
  `uom` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estimated_unit_price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `reason` text COLLATE utf8mb4_unicode_ci,
  `ordered_qty` decimal(12,3) NOT NULL DEFAULT '0.000',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_returns`
--

CREATE TABLE `purchase_returns` (
  `id` bigint UNSIGNED NOT NULL,
  `purchase_id` bigint UNSIGNED DEFAULT NULL,
  `vendor_id` bigint UNSIGNED NOT NULL,
  `return_invoice` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `return_date` date NOT NULL,
  `return_reason` text COLLATE utf8mb4_unicode_ci,
  `transport` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicle_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `driver_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_person` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `warehouse_id` bigint UNSIGNED DEFAULT NULL,
  `bill_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `item_discount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `extra_discount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `net_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `paid` decimal(12,2) NOT NULL DEFAULT '0.00',
  `balance` decimal(12,2) NOT NULL DEFAULT '0.00',
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_return_items`
--

CREATE TABLE `purchase_return_items` (
  `id` bigint UNSIGNED NOT NULL,
  `purchase_return_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `color` text COLLATE utf8mb4_unicode_ci,
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qty` int NOT NULL DEFAULT '0',
  `price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `item_discount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `line_total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `receipts_vouchers`
--

CREATE TABLE `receipts_vouchers` (
  `id` bigint UNSIGNED NOT NULL,
  `rvid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receipt_date` date DEFAULT NULL,
  `entry_date` date DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `party_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tel` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `narration_id` text COLLATE utf8mb4_unicode_ci,
  `reference_no` text COLLATE utf8mb4_unicode_ci,
  `row_account_head` text COLLATE utf8mb4_unicode_ci,
  `row_account_id` text COLLATE utf8mb4_unicode_ci,
  `discount_value` text COLLATE utf8mb4_unicode_ci,
  `kg` text COLLATE utf8mb4_unicode_ci,
  `rate` text COLLATE utf8mb4_unicode_ci,
  `amount` text COLLATE utf8mb4_unicode_ci,
  `total_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `processed` tinyint(1) NOT NULL DEFAULT '0',
  `settled_invoices` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `receipts_vouchers`
--

INSERT INTO `receipts_vouchers` (`id`, `rvid`, `receipt_date`, `entry_date`, `type`, `party_id`, `tel`, `remarks`, `narration_id`, `reference_no`, `row_account_head`, `row_account_id`, `discount_value`, `kg`, `rate`, `amount`, `total_amount`, `processed`, `settled_invoices`, `created_at`, `updated_at`) VALUES
(1, 'RE-2026-0002', '2026-10-24', '2026-10-24', 'customer', '12', NULL, 'Advance Receipt for Sale Invoice #INV-0002. Total: 100000', NULL, NULL, '[\"2\"]', '[\"3\"]', NULL, NULL, NULL, '[\"100000.00\"]', 100000.00, 1, '[{\"type\": \"sale\", \"sale_id\": 2, \"invoice_no\": \"INV-0002\", \"amount_applied\": 100000}]', '2026-10-28 14:39:25', '2026-10-28 14:39:25'),
(2, 'RVID-002', '2026-10-27', '2026-10-27', 'customer', '27', '03004353172', 'Advance Payment OFT Recieved', '[\"\"]', '[null]', '[\"1\"]', '[\"2\"]', '[\"0\"]', NULL, '[\"0\"]', '[\"350000\"]', 350000.00, 1, '[{\"type\": \"sale\", \"sale_id\": 10, \"invoice_no\": \"INV-0010\", \"amount_applied\": 350000}]', '2026-10-29 10:44:16', '2026-10-29 10:44:16'),
(3, 'RVID-003', '2026-10-30', '2026-10-30', 'customer', '34', '03194380106', 'Receipt from NATIONAL DEVELOPMENT COMPLEX/CENTRE (NDC) (Bal: 1,486,800 Dr)', '[\"\"]', '[null]', '[\"1\"]', '[\"2\"]', '[\"0\"]', NULL, '[\"0\"]', '[\"400000\"]', 400000.00, 1, '[{\"type\": \"sale\", \"sale_id\": 13, \"invoice_no\": \"INV-0013\", \"amount_applied\": 400000}]', '2026-10-30 10:34:18', '2026-10-30 10:34:18'),
(4, 'RVID-004', '2026-10-30', '2026-10-30', 'customer', '30', '03008303164', 'Receipt from Khaleel ur Rehman (Bal: 106,000 Dr)', '[\"\"]', '[null]', '[\"1\"]', '[\"2\"]', '[\"0\"]', NULL, '[\"0\"]', '[\"42000\"]', 42000.00, 1, '[{\"type\": \"repair\", \"sale_id\": 22, \"invoice_no\": \"REP-00022\", \"amount_applied\": 42000}]', '2026-10-30 12:04:44', '2026-10-30 12:04:44'),
(5, 'RVID-005', '2026-10-31', '2026-10-31', 'customer', '23', '03236621248', 'Receipt from Abdul Moiz (Bal: 17,500 Dr)', '[\"\"]', '[null]', '[\"1\"]', '[\"2\"]', '[\"0\"]', NULL, '[\"0\"]', '[\"17500\"]', 17500.00, 1, '[{\"type\": \"repair\", \"sale_id\": 18, \"invoice_no\": \"REP-00018\", \"amount_applied\": 17500}]', '2026-10-31 07:51:39', '2026-10-31 07:51:39'),
(6, 'RVID-006', '2026-10-31', '2026-10-31', 'customer', '33', '03004720201', 'Receipt from Danish Sb Gujranwala (Bal: 85,000 Dr)', '[\"\"]', '[null]', '[\"1\"]', '[\"2\"]', '[\"0\"]', NULL, '[\"0\"]', '[\"33000\"]', 33000.00, 1, '[{\"type\": \"repair\", \"sale_id\": 27, \"invoice_no\": \"REP-00027\", \"amount_applied\": 33000}]', '2026-10-31 08:33:53', '2026-10-31 08:33:53'),
(7, 'RVID-007', '2026-10-31', '2026-10-31', 'customer', '22', '03016660525', 'Receipt from Kashif Sb Gujranwala (Bal: 20,500 Dr)', '[\"\"]', '[null]', '[\"1\"]', '[\"2\"]', '[\"0\"]', NULL, '[\"0\"]', '[\"20500\"]', 20500.00, 1, '[{\"type\": \"repair\", \"sale_id\": 17, \"invoice_no\": \"REP-00017\", \"amount_applied\": 20500}]', '2026-10-31 10:34:40', '2026-10-31 10:34:40');

-- --------------------------------------------------------

--
-- Table structure for table `repair_orders`
--

CREATE TABLE `repair_orders` (
  `id` bigint UNSIGNED NOT NULL,
  `repair_no` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` bigint UNSIGNED DEFAULT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_address` text COLLATE utf8mb4_unicode_ci,
  `subject` text COLLATE utf8mb4_unicode_ci,
  `product_id` bigint UNSIGNED DEFAULT NULL,
  `item_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand_model` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `serial_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `accessories_received` text COLLATE utf8mb4_unicode_ci,
  `problem_description` text COLLATE utf8mb4_unicode_ci,
  `physical_condition` text COLLATE utf8mb4_unicode_ci,
  `technician_notes` text COLLATE utf8mb4_unicode_ci,
  `estimated_cost` decimal(15,2) NOT NULL DEFAULT '0.00',
  `service_charges` decimal(15,2) NOT NULL DEFAULT '0.00',
  `parts_charges` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_charges` decimal(15,2) NOT NULL DEFAULT '0.00',
  `advance_paid` decimal(15,2) NOT NULL DEFAULT '0.00',
  `advance_account_id` bigint UNSIGNED DEFAULT NULL,
  `final_paid` decimal(15,2) NOT NULL DEFAULT '0.00',
  `final_account_id` bigint UNSIGNED DEFAULT NULL,
  `due_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `status` enum('received','diagnosing','in_progress','waiting_parts','completed','delivered','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'received',
  `doc_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'invoice',
  `priority` enum('normal','urgent','high') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal',
  `received_date` date NOT NULL,
  `expected_delivery_date` date DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `delivery_notes` text COLLATE utf8mb4_unicode_ci,
  `received_by` bigint UNSIGNED DEFAULT NULL,
  `delivered_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `repair_orders`
--

INSERT INTO `repair_orders` (`id`, `repair_no`, `customer_id`, `customer_name`, `customer_phone`, `customer_address`, `subject`, `product_id`, `item_name`, `brand_model`, `serial_no`, `accessories_received`, `problem_description`, `physical_condition`, `technician_notes`, `estimated_cost`, `service_charges`, `parts_charges`, `total_charges`, `advance_paid`, `advance_account_id`, `final_paid`, `final_account_id`, `due_amount`, `status`, `doc_type`, `priority`, `received_date`, `expected_delivery_date`, `delivered_at`, `delivery_notes`, `received_by`, `delivered_by`, `created_at`, `updated_at`) VALUES
(1, 'REP-00001', 1, 'Zeeshan sb Karachi', '03002785277', 'karachi', NULL, NULL, 'JKZ 60kw Induction Heater', '60kw jkz', NULL, NULL, '[{\"sn\":1,\"item_name\":\"JKZ 60kw Induction Heater\",\"brand_model\":\"60kw jkz\",\"serial_no\":\"\",\"problem_description\":\"AB unit\\r\\nno coil\\r\\nno coupling cables\",\"estimated_cost\":0}]', NULL, NULL, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, 0.00, NULL, 0.00, 'received', 'invoice', 'normal', '2026-10-01', '2026-10-22', NULL, NULL, 2, NULL, '2026-10-17 11:36:16', '2026-10-17 11:36:16'),
(2, 'REP-00002', 3, 'Majid Sb, Tariq Sb Sialkot', '0315-7894300', 'Sialkot city', NULL, 7, 'HF INDUCTION HEATER 60KW-AB', 'LTE', NULL, NULL, '[{\"sn\":1,\"item_name\":\"HF INDUCTION HEATER 60KW-AB\",\"brand_model\":\"LTE\",\"serial_no\":\"\",\"problem_description\":\"AB UNITS ONLY \\r\\nNO COIL\\r\\nNO CABLES\",\"estimated_cost\":0}]', NULL, NULL, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, 0.00, NULL, 0.00, 'received', 'invoice', 'normal', '2026-10-19', '2026-10-25', NULL, NULL, 1, NULL, '2026-10-19 08:15:06', '2026-10-19 08:15:06'),
(3, 'REP-00003', 4, 'AJR METAL FABRICATORS (PVT.) LTD', '0', '16/17 Km, LHR-SKP Road, Suwa Misson Calor, Lahore -39250', 'Induction Heater 25kw AB', NULL, 'CT repair (Rewinding)', 'LTE', NULL, NULL, '[{\"sn\":\"1\",\"desc\":\"CT repair (Rewinding)\",\"qty\":1,\"rate\":21000,\"gross\":21000,\"gst_pct\":0,\"gst_amt\":0,\"total\":21000},{\"sn\":\"2\",\"desc\":\"Water wiring (New)\",\"qty\":1,\"rate\":5000,\"gross\":5000,\"gst_pct\":0,\"gst_amt\":0,\"total\":5000}]', NULL, NULL, 26000.00, 26000.00, 0.00, 26000.00, 0.00, NULL, 0.00, NULL, 26000.00, 'delivered', 'invoice', 'normal', '2026-10-19', '2026-10-26', '2026-10-28 07:01:23', 'AJR Loader riksha', 2, 67, '2026-10-19 10:27:00', '2026-10-28 07:01:23'),
(4, 'REP-00004', 4, 'AJR METAL FABRICATORS (PVT.) LTD', '0', NULL, 'Induction Heater Single Body (Model: china)', NULL, 'IGBT', 'china', NULL, NULL, '[{\"sn\":\"1\",\"desc\":\"IGBT\",\"qty\":1,\"rate\":17500,\"gross\":17500,\"gst_pct\":0,\"gst_amt\":0,\"total\":17500},{\"sn\":\"2\",\"desc\":\"Protection Boards\",\"qty\":2,\"rate\":3500,\"gross\":7000,\"gst_pct\":0,\"gst_amt\":0,\"total\":7000},{\"sn\":\"3\",\"desc\":\"Driver Boards Repair\",\"qty\":2,\"rate\":2000,\"gross\":4000,\"gst_pct\":0,\"gst_amt\":0,\"total\":4000}]', NULL, 'Updated to LTZ Model New CARD ETC', 28500.00, 28500.00, 0.00, 28500.00, 0.00, NULL, 0.00, NULL, 28500.00, 'delivered', 'invoice', 'normal', '2026-10-21', '2026-10-24', '2026-10-28 07:00:27', 'AJR Loader riksha', 2, 67, '2026-10-19 12:37:34', '2026-10-28 07:00:27'),
(5, 'REP-00005', 5, 'Qaisar Mughal sb Lahore', '0', 'Lahore Daroghawala', NULL, NULL, 'B-BOX For Repairing', 'LTE', '0', NULL, '[{\"sn\":1,\"item_name\":\"B-BOX For Repairing\",\"brand_model\":\"LTE\",\"serial_no\":\"0\",\"problem_description\":\"ONLY B BOX\",\"estimated_cost\":0}]', NULL, 'Repaired b box - issue resolved , some parts replaced', 0.00, 0.00, 0.00, 0.00, 0.00, NULL, 0.00, NULL, 0.00, 'delivered', 'invoice', 'normal', '2026-10-17', '2026-10-19', '2026-10-19 12:49:51', 'delivered > Lhore riksha > driver no 0325-3060006', 2, 2, '2026-10-19 12:47:00', '2026-10-19 12:49:51'),
(6, 'REP-00006', 7, 'Bright Engineering Pvt Ltd', '03008458204', 'Lahore', NULL, 11, 'HF INDUCTION HEATER 60KW-AB For Repair', 'LTE', NULL, '01 Coil +AB Units', '[{\"sn\":1,\"item_name\":\"HF INDUCTION HEATER 60KW-AB For Repair\",\"brand_model\":\"LTE\",\"serial_no\":\"\",\"problem_description\":\"B Box without Cover , A bOX 1 SINDE without one side sheet ,\",\"estimated_cost\":0}]', 'ok', NULL, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, 0.00, NULL, 0.00, 'received', 'invoice', 'normal', '2026-10-21', '2026-10-26', NULL, NULL, 67, NULL, '2026-10-21 05:59:46', '2026-10-26 19:52:19'),
(7, 'REP-00007', 8, 'Affaq Corporation Mandiali', '0', 'Mandiali Lhr Road', 'Induction Heater upgrade to LTZ-40kw (SN: 1015 (NEW) | Upgrade to LTZ)', NULL, 'Induction Heater upgrade to LTZ-40kw', NULL, '1015 (NEW)', 'COIL + CABLES +A UNIT', '[{\"sn\":\"1\",\"desc\":\"Induction Heater upgrade to LTZ-40kw\",\"qty\":1,\"rate\":239000,\"gross\":239000,\"gst_pct\":0,\"gst_amt\":0,\"total\":239000},{\"sn\":\"2\",\"desc\":\"Induction Coil (New)\",\"qty\":1,\"rate\":6500,\"gross\":6500,\"gst_pct\":0,\"gst_amt\":0,\"total\":6500},{\"sn\":\"3\",\"desc\":\"induction coil (New)\",\"qty\":1,\"rate\":4800,\"gross\":4800,\"gst_pct\":0,\"gst_amt\":0,\"total\":4800},{\"sn\":\"4\",\"desc\":\"Special Discount\",\"qty\":1,\"rate\":-30300,\"gross\":-30300,\"gst_pct\":0,\"gst_amt\":0,\"total\":-30300},{\"sn\":\"5\",\"desc\":\"01-year\\u00a0 Free Service Warranty.\",\"qty\":0,\"rate\":0,\"gross\":0,\"gst_pct\":0,\"gst_amt\":0,\"total\":0}]', NULL, 'Upgraded to LTZ MODEL SN 1015', 220000.00, 0.00, 0.00, 220000.00, 0.00, NULL, 0.00, NULL, 220000.00, 'delivered', 'invoice', 'normal', '2026-10-26', '2026-10-21', '2026-10-21 08:42:43', 'self collected by AFFAQ CORP.. SN ALLOTED 1015', 67, 67, '2026-10-21 08:40:47', '2026-10-28 07:24:46'),
(8, 'REP-00008', 9, 'Shafqat Sb Surgical Sialkot', '0', NULL, NULL, NULL, 'Induction Heater 60kw-AB Unit (for upgrade)', 'LTE', 'NA', 'ONLY A & B UNITS', '[{\"sn\":1,\"item_name\":\"Induction Heater 60kw-AB Unit (for upgrade)\",\"brand_model\":\"LTE\",\"serial_no\":\"NA\",\"problem_description\":\"ONLY A UNIT FOR UPGRADE\",\"estimated_cost\":0}]', 'OK', NULL, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, 0.00, NULL, 0.00, 'received', 'invoice', 'normal', '2026-10-21', '2026-10-30', NULL, NULL, 67, NULL, '2026-10-21 09:40:13', '2026-10-27 05:38:03'),
(9, 'REP-00009', 9, 'Shafqat Sb Surgical Sialkot', '0', 'Sialkot city', NULL, NULL, 'Induction Haeter 60kw AB Upgraded to LTZ', 'LTE', '1007 (New)', 'AB UNITS', '[{\"sn\":1,\"item_name\":\"Induction Haeter 60kw AB Upgraded to LTZ\",\"brand_model\":\"LTE\",\"serial_no\":\"1007 (New)\",\"problem_description\":\"For Upgrade to LTZ\",\"estimated_cost\":0}]', 'OK', 'UPGRADED TO LTZ', 0.00, 0.00, 0.00, 0.00, 0.00, NULL, 0.00, NULL, 0.00, 'delivered', 'invoice', 'normal', '2026-08-18', '2026-10-21', '2026-10-21 12:11:38', 'SELF COLLECTED BY SHAFQAT SB (SN ALLOTED 1007)', 67, 67, '2026-10-21 12:10:23', '2026-10-21 12:11:38'),
(10, 'REP-00010', 10, 'Naeem sb Sialkot', '0', 'Sialkot city', NULL, NULL, 'Induction Heater 25kw-A ITECH Body (upgrade to LTZ)', 'itech', '1018 (New alloted)', 'A Unit only', '[{\"sn\":1,\"item_name\":\"Induction Heater 25kw-A ITECH Body (upgrade to LTZ)\",\"brand_model\":\"itech\",\"serial_no\":\"1018 (New alloted)\",\"problem_description\":\"Upgrade to LTZ\",\"estimated_cost\":0}]', 'OK', 'Upgraded to LTZ Model', 0.00, 0.00, 0.00, 0.00, 0.00, NULL, 0.00, NULL, 0.00, 'delivered', 'invoice', 'normal', '2026-09-01', '2026-10-26', '2026-10-27 10:28:40', 'ASIA CARGO booked', 67, 67, '2026-10-23 05:51:53', '2026-10-27 10:28:40'),
(11, 'REP-00011', 11, 'Rajab Sb Sialkot', '0302-4220493', 'Sialkot city', NULL, 11, 'HF INDUCTION HEATER 160KW-AB', 'LTE / LTHF-60KW', 'NA', 'NO ACCERIES ONLY AB UNITS', '[{\"sn\":1,\"item_name\":\"HF INDUCTION HEATER 160KW-AB\",\"brand_model\":\"LTE \\/ LTHF-60KW\",\"serial_no\":\"NA\",\"problem_description\":\"NOT WORKING\",\"estimated_cost\":0}]', 'OK', NULL, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, 0.00, NULL, 0.00, 'received', 'invoice', 'normal', '2026-10-24', '2026-10-27', NULL, NULL, 67, NULL, '2026-10-24 07:58:40', '2026-10-24 07:58:40'),
(12, 'REP-00012', 13, 'Irfan sb Wazirabad (03068774376)', '03068774376', NULL, 'China model repair 60kw (Model: china | Driver boards issues)', NULL, 'IGBT', 'china', NULL, 'coupling cable set, coil 01, AB unit china made', '[{\"sn\":\"1\",\"desc\":\"IGBT\",\"item_name\":\"IGBT\",\"qty\":1,\"rate\":16500,\"gross\":16500,\"gst_pct\":0,\"gst_amt\":0,\"total\":16500,\"estimated_cost\":16500,\"problem_description\":\"IGBT\"},{\"sn\":\"2\",\"desc\":\"Driver board repair\",\"item_name\":\"Driver board repair\",\"qty\":1,\"rate\":5000,\"gross\":5000,\"gst_pct\":0,\"gst_amt\":0,\"total\":5000,\"estimated_cost\":5000,\"problem_description\":\"Driver board repair\"},{\"sn\":\"3\",\"desc\":\"coil\",\"item_name\":\"coil\",\"qty\":1,\"rate\":3500,\"gross\":3500,\"gst_pct\":0,\"gst_amt\":0,\"total\":3500,\"estimated_cost\":3500,\"problem_description\":\"coil\"}]', 'AB units with complete body', 'igbt 300ks4, driver board repair coil', 25000.00, 0.00, 0.00, 25000.00, 0.00, NULL, 0.00, NULL, 25000.00, 'completed', 'invoice', 'high', '2026-10-31', '2026-10-26', NULL, NULL, 68, NULL, '2026-10-24 12:16:00', '2026-10-31 11:49:59'),
(13, 'REP-00013', 16, 'MIQRAZ', '03018614766', NULL, NULL, NULL, 'Induction Heater 40kw', 'Logic Tech', NULL, 'AB unit only no coupling wires no coil', '[{\"sn\":1,\"item_name\":\"Induction Heater 40kw\",\"brand_model\":\"Logic Tech\",\"serial_no\":\"\",\"problem_description\":\"not working\\r\\nigbt burn out\",\"estimated_cost\":0}]', 'AB units with complete body', NULL, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, 0.00, NULL, 0.00, 'received', 'invoice', 'normal', '2026-10-26', '2026-10-29', NULL, NULL, 68, NULL, '2026-10-26 11:14:08', '2026-10-26 11:14:08'),
(14, 'REP-00014', 17, 'Syed Liaquat Shah Rawalpindi', '03215094341', NULL, 'induction melting furnace (Model: china )', NULL, 'IGBT modules', 'china', NULL, 'only machine', '[{\"sn\":\"1\",\"desc\":\"IGBT modules\",\"qty\":2,\"rate\":9000,\"gross\":18000,\"gst_pct\":0,\"gst_amt\":0,\"total\":18000},{\"sn\":\"2\",\"desc\":\"Water cool Silver Block with accessories\",\"qty\":1,\"rate\":7500,\"gross\":7500,\"gst_pct\":0,\"gst_amt\":0,\"total\":7500},{\"sn\":\"3\",\"desc\":\"Snober Boards\",\"qty\":2,\"rate\":2500,\"gross\":5000,\"gst_pct\":0,\"gst_amt\":0,\"total\":5000},{\"sn\":\"4\",\"desc\":\"Heat limit switch + Charging resistance\",\"qty\":1,\"rate\":5000,\"gross\":5000,\"gst_pct\":0,\"gst_amt\":0,\"total\":5000},{\"sn\":\"5\",\"desc\":\"Front Board new with card\",\"qty\":1,\"rate\":11500,\"gross\":11500,\"gst_pct\":0,\"gst_amt\":0,\"total\":11500}]', NULL, NULL, 47000.00, 0.00, 0.00, 47000.00, 0.00, NULL, 0.00, NULL, 47000.00, 'delivered', 'invoice', 'normal', '2026-10-26', '2026-10-29', '2026-10-15 12:53:52', 'Daewoo Rawalpindi', 68, 68, '2026-10-26 12:52:34', '2026-10-26 12:58:20'),
(15, 'REP-00015', 19, 'Shahid Sb (03008467357)', '03008467357', NULL, 'Induction Heater B-unit (Model: China | Boosting)', NULL, 'Induction Heater B-unit Repair \ni. Primary repair\nii. Secondary repair', 'China', NULL, 'only B-Unit', '[{\"sn\":\"1\",\"desc\":\"Induction Heater B-unit Repair\\u00a0\\ni. Primary repair\\nii. Secondary repair\",\"qty\":1,\"rate\":14000,\"gross\":14000,\"gst_pct\":0,\"gst_amt\":0,\"total\":14000}]', NULL, NULL, 14000.00, 14000.00, 0.00, 14000.00, 0.00, NULL, 0.00, NULL, 14000.00, 'delivered', 'invoice', 'normal', '2026-10-27', '2026-10-30', '2026-10-26 18:17:23', 'self collection', 68, 68, '2026-10-26 20:24:31', '2026-10-28 18:17:23'),
(16, 'REP-00016', 21, 'Zulfqar Sb Lahore (03007860943)', '03007860943', NULL, 'B-unit Repair (Model: Logic Tech | Change new water wiring)', NULL, 'B-unit Water wiring (New)', 'Logic Tech', NULL, NULL, '[{\"sn\":\"1\",\"desc\":\"B-unit Water wiring (New)\",\"qty\":1,\"rate\":5500,\"gross\":5500,\"gst_pct\":0,\"gst_amt\":0,\"total\":5500},{\"sn\":\"2\",\"desc\":\"Water Nozzles\",\"qty\":1,\"rate\":6000,\"gross\":6000,\"gst_pct\":0,\"gst_amt\":0,\"total\":6000}]', NULL, NULL, 11500.00, 0.00, 0.00, 11500.00, 0.00, NULL, 0.00, NULL, 11500.00, 'delivered', 'invoice', 'normal', '2026-10-15', '2026-10-30', '2026-10-14 21:05:15', NULL, 68, 68, '2026-10-26 21:02:38', '2026-10-26 21:07:43'),
(17, 'REP-00017', 22, 'Kashif Sb Gujranwala (03016660525)', '03016660525', NULL, 'HF INDUCTION HEATER 25KW-A (Model: Logic Tech | Transformer Boosting)', 3, 'Transformer Rewind (New)', 'Logic Tech', NULL, NULL, '[{\"sn\":\"1\",\"desc\":\"Transformer Rewind (New)\",\"qty\":1,\"rate\":17000,\"gross\":17000,\"gst_pct\":0,\"gst_amt\":0,\"total\":17000},{\"sn\":\"2\",\"desc\":\"Water Wiring (New)\",\"qty\":1,\"rate\":3500,\"gross\":3500,\"gst_pct\":0,\"gst_amt\":0,\"total\":3500}]', NULL, NULL, 20500.00, 0.00, 0.00, 20500.00, 0.00, NULL, 20500.00, NULL, 0.00, 'delivered', 'invoice', 'normal', '2026-10-27', '2026-10-30', '2026-10-14 21:12:49', NULL, 68, 68, '2026-10-26 21:11:58', '2026-10-31 10:34:40'),
(18, 'REP-00018', 23, 'Abdul Moiz', '03236621248', NULL, 'Induction Heater', NULL, 'New Driver Board', 'Logic Tech', NULL, NULL, '[{\"sn\":\"1\",\"desc\":\"New Driver Board\",\"qty\":1,\"rate\":6000,\"gross\":6000,\"gst_pct\":0,\"gst_amt\":0,\"total\":6000},{\"sn\":\"2\",\"desc\":\"Control Board Repair\",\"qty\":1,\"rate\":3000,\"gross\":3000,\"gst_pct\":0,\"gst_amt\":0,\"total\":3000},{\"sn\":\"3\",\"desc\":\"Control Transformer\",\"qty\":1,\"rate\":8500,\"gross\":8500,\"gst_pct\":0,\"gst_amt\":0,\"total\":8500}]', NULL, NULL, 17500.00, 0.00, 0.00, 17500.00, 0.00, NULL, 17500.00, NULL, 0.00, 'delivered', 'invoice', 'normal', '2026-10-27', '2026-10-30', '2026-10-26 21:22:14', NULL, 68, 68, '2026-10-26 21:21:42', '2026-10-31 07:51:39'),
(19, 'REP-00019', 19, 'Shahid Sb (03008467357)', '03008467357', NULL, 'Induction Heater Model: China', NULL, 'IGBT', 'China', NULL, NULL, '[{\"sn\":\"1\",\"desc\":\"IGBT\",\"qty\":1,\"rate\":12500,\"gross\":12500,\"gst_pct\":0,\"gst_amt\":0,\"total\":12500},{\"sn\":\"2\",\"desc\":\"Driver Board New\",\"qty\":1,\"rate\":6000,\"gross\":6000,\"gst_pct\":0,\"gst_amt\":0,\"total\":6000},{\"sn\":\"3\",\"desc\":\"Main Board repair\",\"qty\":1,\"rate\":3000,\"gross\":3000,\"gst_pct\":0,\"gst_amt\":0,\"total\":3000}]', NULL, NULL, 21500.00, 0.00, 0.00, 21500.00, 0.00, NULL, 0.00, NULL, 21500.00, 'delivered', 'invoice', 'normal', '2026-10-27', '2026-10-30', '2026-10-26 21:27:32', NULL, 68, 68, '2026-10-26 21:26:56', '2026-10-28 08:23:38'),
(20, 'REP-00020', 26, 'Mohsin Sb Gujranwala', '0', NULL, NULL, NULL, 'Induction Heater 25kw AB China Body for Repair', 'CN', NULL, '01 coil 01 set coupling cables', '[{\"sn\":1,\"item_name\":\"Induction Heater 25kw AB China Body for Repair\",\"brand_model\":\"CN\",\"serial_no\":\"\",\"problem_description\":\"Repair ki thi chali nai , phir wapis aai hai\",\"estimated_cost\":0}]', 'color: blue', NULL, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, 0.00, NULL, 0.00, 'received', 'invoice', 'normal', '2026-10-27', '2026-10-31', NULL, NULL, 67, NULL, '2026-10-28 06:14:50', '2026-10-28 18:10:52'),
(21, 'REP-00021', 32, 'Mughal Innovations', '03218333337', NULL, '80kw induction heater (Model: China )', NULL, 'Main Control Board', 'China', '1020 new alloted', 'AB unit, coupling cables , 1 coil', '[{\"sn\":\"1\",\"desc\":\"Main Control Board\",\"item_name\":\"Main Control Board\",\"qty\":1,\"rate\":45500,\"gross\":45500,\"gst_pct\":0,\"gst_amt\":0,\"total\":45500,\"estimated_cost\":45500,\"problem_description\":\"Main Control Board\"},{\"sn\":\"2\",\"desc\":\"Driver Boards\",\"item_name\":\"Driver Boards\",\"qty\":4,\"rate\":8500,\"gross\":34000,\"gst_pct\":0,\"gst_amt\":0,\"total\":34000,\"estimated_cost\":34000,\"problem_description\":\"Driver Boards\"},{\"sn\":\"3\",\"desc\":\"Power supply Board\",\"item_name\":\"Power supply Board\",\"qty\":1,\"rate\":9000,\"gross\":9000,\"gst_pct\":0,\"gst_amt\":0,\"total\":9000,\"estimated_cost\":9000,\"problem_description\":\"Power supply Board\"},{\"sn\":\"4\",\"desc\":\"IGBT modules\",\"item_name\":\"IGBT modules\",\"qty\":4,\"rate\":16000,\"gross\":64000,\"gst_pct\":0,\"gst_amt\":0,\"total\":64000,\"estimated_cost\":64000,\"problem_description\":\"IGBT modules\"},{\"sn\":\"5\",\"desc\":\"Complete machine Control wiring New\",\"item_name\":\"Complete machine Control wiring New\",\"qty\":1,\"rate\":12000,\"gross\":12000,\"gst_pct\":0,\"gst_amt\":0,\"total\":12000,\"estimated_cost\":12000,\"problem_description\":\"Complete machine Control wiring New\"},{\"sn\":\"6\",\"desc\":\"CT rewinding\",\"item_name\":\"CT rewinding\",\"qty\":1,\"rate\":28000,\"gross\":28000,\"gst_pct\":0,\"gst_amt\":0,\"total\":28000,\"estimated_cost\":28000,\"problem_description\":\"CT rewinding\"},{\"sn\":\"7\",\"desc\":\"Front Panel Error lights & Switches with accessories\",\"item_name\":\"Front Panel Error lights & Switches with accessories\",\"qty\":1,\"rate\":11000,\"gross\":11000,\"gst_pct\":0,\"gst_amt\":0,\"total\":11000,\"estimated_cost\":11000,\"problem_description\":\"Front Panel Error lights & Switches with accessories\"},{\"sn\":\"8\",\"desc\":\"Pressure Switch\",\"item_name\":\"Pressure Switch\",\"qty\":1,\"rate\":5500,\"gross\":5500,\"gst_pct\":0,\"gst_amt\":0,\"total\":5500,\"estimated_cost\":5500,\"problem_description\":\"Pressure Switch\"},{\"sn\":\"9\",\"desc\":\"Protections boards\",\"item_name\":\"Protections boards\",\"qty\":4,\"rate\":800,\"gross\":3200,\"gst_pct\":0,\"gst_amt\":0,\"total\":3200,\"estimated_cost\":3200,\"problem_description\":\"Protections boards\"},{\"sn\":\"10\",\"desc\":\"Current Sensor\",\"item_name\":\"Current Sensor\",\"qty\":1,\"rate\":4500,\"gross\":4500,\"gst_pct\":0,\"gst_amt\":0,\"total\":4500,\"estimated_cost\":4500,\"problem_description\":\"Current Sensor\"}]', 'A unit without side covers', NULL, 216700.00, 0.00, 0.00, 216700.00, 0.00, NULL, 0.00, NULL, 216700.00, 'delivered', 'invoice', 'normal', '2026-10-31', '2026-10-29', '2026-10-28 19:31:20', NULL, 68, 68, '2026-10-29 19:30:51', '2026-10-31 08:09:53'),
(22, 'REP-00022', 30, 'Khaleel ur Rehman (03008303164)', '03008303164', NULL, '80kw china model (Model: china )', NULL, 'IGBT', 'china', NULL, 'AB unit', '[{\"sn\":\"1\",\"desc\":\"IGBT\",\"qty\":2,\"rate\":16500,\"gross\":33000,\"gst_pct\":0,\"gst_amt\":0,\"total\":33000},{\"sn\":\"2\",\"desc\":\"Driver Board repair\",\"qty\":1,\"rate\":6000,\"gross\":6000,\"gst_pct\":0,\"gst_amt\":0,\"total\":6000},{\"sn\":\"3\",\"desc\":\"Protection boards\",\"qty\":2,\"rate\":1500,\"gross\":3000,\"gst_pct\":0,\"gst_amt\":0,\"total\":3000}]', 'ok', NULL, 42000.00, 0.00, 0.00, 42000.00, 0.00, NULL, 42000.00, NULL, 0.00, 'delivered', 'invoice', 'normal', '2026-10-30', '2026-11-02', '2026-10-30 11:39:33', NULL, 68, 68, '2026-10-30 11:39:02', '2026-10-30 12:04:44'),
(23, 'REP-00023', 9, 'Shafqat Sb Surgical Sialkot', '03216100018', NULL, NULL, 7, 'HF INDUCTION HEATER 60KW-A UNIT', 'LTE', '0', 'ONLY A UNIT', '[{\"sn\":1,\"item_name\":\"HF INDUCTION HEATER 60KW-A UNIT\",\"brand_model\":\"LTE\",\"serial_no\":\"0\",\"problem_description\":\"FOR UPGRADE TO 80KW LTZ\",\"estimated_cost\":0}]', NULL, NULL, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, 0.00, NULL, 0.00, 'received', 'invoice', 'normal', '2026-08-19', '2026-11-10', NULL, NULL, 67, NULL, '2026-10-30 12:13:54', '2026-10-30 12:39:43'),
(24, 'REP-00024', 35, 'Sufyan bhai Daroghawala', '03214298580', 'Mominpura Daroghawala Lhore', 'HF INDUCTION HEATER 25KW-AB (Model: LTE )', 4, 'New Driver Boards', 'LTE', NULL, 'ONL AB AND COIL', '[{\"sn\":\"1\",\"desc\":\"New Driver Boards\",\"item_name\":\"New Driver Boards\",\"qty\":2,\"rate\":3000,\"gross\":6000,\"gst_pct\":0,\"gst_amt\":0,\"total\":6000,\"estimated_cost\":6000,\"problem_description\":\"New Driver Boards\"}]', 'OK', 'DRIVER CARD CHANGED BOTH', 6000.00, 0.00, 0.00, 6000.00, 0.00, NULL, 0.00, NULL, 6000.00, 'delivered', 'invoice', 'normal', '2026-10-30', '2026-10-30', '2026-10-30 13:08:45', 'SELF', 67, 67, '2026-10-30 13:04:26', '2026-10-31 08:18:27'),
(25, 'REP-00025', 36, 'Ravi Autos Sheikhupura Pvt Ltd (03084396792)', '03084396792', NULL, 'Induction Heater (Model: China )', NULL, 'Driver Board repair', 'China', NULL, 'work on site', '[{\"sn\":\"1\",\"desc\":\"Driver Board repair\",\"qty\":1,\"rate\":15000,\"gross\":15000,\"gst_pct\":0,\"gst_amt\":0,\"total\":15000},{\"sn\":\"2\",\"desc\":\"Protection Boards repair\",\"qty\":1,\"rate\":3000,\"gross\":3000,\"gst_pct\":0,\"gst_amt\":0,\"total\":3000},{\"sn\":\"3\",\"desc\":\"Service Charges\",\"qty\":1,\"rate\":25000,\"gross\":25000,\"gst_pct\":0,\"gst_amt\":0,\"total\":25000}]', NULL, NULL, 43000.00, 0.00, 0.00, 43000.00, 0.00, NULL, 0.00, NULL, 43000.00, 'delivered', 'invoice', 'normal', '2026-10-31', '2026-11-03', '2026-10-28 19:04:09', 'work on site. Driver cards repair', 68, 68, '2026-10-30 19:03:20', '2026-10-30 19:07:06'),
(26, 'REP-00026', 38, 'Test (Test)', 'Test', 'test', 'test repair', NULL, 'TEST DRIVER BOARD', 'test model', 'test sn', 'test accessories', '[{\"sn\":\"1\",\"desc\":\"TEST DRIVER BOARD\",\"qty\":1,\"rate\":1500,\"gross\":1500,\"gst_pct\":0,\"gst_amt\":0,\"total\":1500},{\"sn\":\"2\",\"desc\":\"TEST MAIN BOARD\",\"qty\":1,\"rate\":2000,\"gross\":2000,\"gst_pct\":0,\"gst_amt\":0,\"total\":2000},{\"sn\":\"3\",\"desc\":\"TEST IGBT\",\"qty\":1,\"rate\":500,\"gross\":500,\"gst_pct\":0,\"gst_amt\":0,\"total\":500}]', 'test condition', NULL, 4000.00, 0.00, 0.00, 4000.00, 0.00, NULL, 0.00, NULL, 4000.00, 'delivered', 'invoice', 'normal', '2026-10-30', '2026-11-03', '2026-10-30 19:28:17', 'test delivery driver', 67, 67, '2026-10-30 19:26:58', '2026-10-30 19:45:24'),
(27, 'REP-00027', 33, 'Danish Sb Gujranwala', '03004720201', NULL, 'Induction Heater 40kw (Model: China )', NULL, 'IGBT modules', 'China', NULL, 'AB unit, cables', '[{\"sn\":\"1\",\"desc\":\"IGBT modules\",\"item_name\":\"IGBT modules\",\"qty\":2,\"rate\":16500,\"gross\":33000,\"gst_pct\":0,\"gst_amt\":0,\"total\":33000,\"estimated_cost\":33000,\"problem_description\":\"IGBT modules\"},{\"sn\":\"2\",\"desc\":\"Main Board repair\",\"item_name\":\"Main Board repair\",\"qty\":1,\"rate\":5000,\"gross\":5000,\"gst_pct\":0,\"gst_amt\":0,\"total\":5000,\"estimated_cost\":5000,\"problem_description\":\"Main Board repair\"},{\"sn\":\"3\",\"desc\":\"Driver Board repair\",\"item_name\":\"Driver Board repair\",\"qty\":1,\"rate\":3000,\"gross\":3000,\"gst_pct\":0,\"gst_amt\":0,\"total\":3000,\"estimated_cost\":3000,\"problem_description\":\"Driver Board repair\"}]', NULL, NULL, 0.00, 0.00, 0.00, 41000.00, 0.00, NULL, 33000.00, NULL, 8000.00, 'delivered', 'invoice', 'normal', '2026-06-29', '2026-11-03', '2026-07-01 05:39:12', 'Hiace', 68, 68, '2026-10-31 05:37:59', '2026-10-31 08:33:53'),
(28, 'REP-00028', 33, 'Danish Sb Gujranwala', '03004720201', 'Gujranwala', 'Induction Heater 40kw (Model: China )', NULL, 'IGBT', 'China', NULL, 'AB unit, coupling cables', '[{\"sn\":\"1\",\"desc\":\"IGBT\",\"item_name\":\"IGBT\",\"qty\":2,\"rate\":16500,\"gross\":33000,\"gst_pct\":0,\"gst_amt\":0,\"total\":33000,\"estimated_cost\":33000,\"problem_description\":\"IGBT\"},{\"sn\":\"2\",\"desc\":\"Main Board repair\",\"item_name\":\"Main Board repair\",\"qty\":1,\"rate\":5000,\"gross\":5000,\"gst_pct\":0,\"gst_amt\":0,\"total\":5000,\"estimated_cost\":5000,\"problem_description\":\"Main Board repair\"},{\"sn\":\"3\",\"desc\":\"Driver board repair\",\"item_name\":\"Driver board repair\",\"qty\":2,\"rate\":3000,\"gross\":6000,\"gst_pct\":0,\"gst_amt\":0,\"total\":6000,\"estimated_cost\":6000,\"problem_description\":\"Driver board repair\"}]', 'ok', 'IGBT module 02, Control card repair, Driver card repair 1.', 44000.00, 0.00, 0.00, 44000.00, 0.00, NULL, 0.00, NULL, 44000.00, 'completed', 'invoice', 'normal', '2026-10-31', '2026-10-31', NULL, NULL, 68, NULL, '2026-10-31 05:53:17', '2026-10-31 05:56:13'),
(29, 'REP-00029', 38, 'Test (Test)', '03004748050', 'sheikhupura', 'Induction 40KW FOR REPAIR (Model: Brand / Model | SN: Serial / Machine # | Fault : OVER VOLTAGE)', NULL, 'Induction 40KW FOR REPAIR (Fault : OVER VOLTAGE)', 'Brand / Model', 'Serial / Machine #', 'only a unit', '[{\"sn\":\"1\",\"desc\":\"Induction 40KW FOR REPAIR (Fault : OVER VOLTAGE)\",\"item_name\":\"Induction 40KW FOR REPAIR (Fault : OVER VOLTAGE)\",\"qty\":1,\"rate\":0,\"gross\":0,\"gst_pct\":0,\"gst_amt\":0,\"total\":0,\"estimated_cost\":0,\"problem_description\":\"Induction 40KW FOR REPAIR (Fault : OVER VOLTAGE)\"},{\"sn\":\"2\",\"desc\":\"driver board repair\",\"item_name\":\"driver board repair\",\"qty\":1,\"rate\":5000,\"gross\":5000,\"gst_pct\":0,\"gst_amt\":0,\"total\":5000,\"estimated_cost\":5000,\"problem_description\":\"driver board repair\"}]', 'ok', 'Troubleshot started', 5000.00, 0.00, 0.00, 5000.00, 1000.00, NULL, 0.00, NULL, 4000.00, 'delivered', 'invoice', 'normal', '2026-10-31', '2026-11-03', '2026-10-31 06:16:50', NULL, 68, 68, '2026-10-31 06:16:40', '2026-10-31 08:57:50'),
(30, 'REP-00030', 40, 'Waqar sb Shakeel Sb Hyderabad', '03013582869', 'Hyderabad', NULL, NULL, 'Induction Melting Furnace 3-Phase Tower Type', 'LTE', 'NA', 'only unit', '[{\"sn\":1,\"desc\":\"Induction Melting Furnace 3-Phase Tower Type\",\"item_name\":\"Induction Melting Furnace 3-Phase Tower Type\",\"brand_model\":\"LTE\",\"serial_no\":\"NA\",\"problem_description\":\"Dead\",\"qty\":1,\"rate\":0,\"gross\":0,\"gst_pct\":0,\"gst_amt\":0,\"total\":0,\"estimated_cost\":0}]', NULL, NULL, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, 0.00, NULL, 0.00, 'received', 'invoice', 'normal', '2026-10-31', '2026-11-10', NULL, NULL, 67, NULL, '2026-10-31 11:35:14', '2026-10-31 11:35:14');

-- --------------------------------------------------------

--
-- Table structure for table `repair_order_logs`
--

CREATE TABLE `repair_order_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `repair_order_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `action` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `from_status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `to_status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(15,2) DEFAULT NULL,
  `account_id` bigint UNSIGNED DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `repair_order_logs`
--

INSERT INTO `repair_order_logs` (`id`, `repair_order_id`, `user_id`, `action`, `from_status`, `to_status`, `amount`, `account_id`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'created', NULL, 'received', 0.00, NULL, 'Product received for repair. Advance collected: Rs. 0.00', '2026-10-17 11:36:16', '2026-10-17 11:36:16'),
(2, 2, 1, 'created', NULL, 'received', 0.00, NULL, 'Product received for repair. Advance collected: Rs. 0.00', '2026-10-19 08:15:06', '2026-10-19 08:15:06'),
(3, 3, 2, 'created', NULL, 'received', 0.00, NULL, 'Product received for repair. Advance collected: Rs. 0.00', '2026-10-19 10:27:00', '2026-10-19 10:27:00'),
(4, 4, 2, 'created', NULL, 'received', 0.00, NULL, 'Product received for repair. Advance collected: Rs. 0.00', '2026-10-19 12:37:34', '2026-10-19 12:37:34'),
(5, 4, 2, 'status_updated', 'received', 'completed', NULL, NULL, 'Status changed from received to completed', '2026-10-19 12:38:24', '2026-10-19 12:38:24'),
(6, 4, 2, 'a4_saved', NULL, 'completed', 0.00, NULL, 'A4 Invoice saved live from screen. Bill: Rs. 0.00 (Invoice - Ledger Reflected)', '2026-10-19 12:38:52', '2026-10-19 12:38:52'),
(7, 4, 2, 'delivered', 'completed', 'delivered', 0.00, NULL, 'Item delivered. Remarks: 01 Old coil and Fan fitting Return GP NO . 246', '2026-10-19 12:40:07', '2026-10-19 12:40:07'),
(8, 5, 2, 'created', NULL, 'received', 0.00, NULL, 'Product received for repair. Advance collected: Rs. 0.00', '2026-10-19 12:47:00', '2026-10-19 12:47:00'),
(9, 5, 2, 'status_updated', 'received', 'completed', NULL, NULL, 'Status changed from received to completed', '2026-10-19 12:48:13', '2026-10-19 12:48:13'),
(10, 5, 2, 'delivered', 'completed', 'delivered', 0.00, NULL, 'Item delivered. Remarks: delivered > Lhore riksha > driver no 0325-3060006', '2026-10-19 12:49:51', '2026-10-19 12:49:51'),
(11, 4, 67, 'updated', 'delivered', 'delivered', 0.00, NULL, 'Repair job intake details updated.', '2026-10-20 18:17:01', '2026-10-20 18:17:01'),
(12, 6, 67, 'created', NULL, 'received', 0.00, NULL, 'Product received for repair. Advance collected: Rs. 0.00', '2026-10-21 05:59:46', '2026-10-21 05:59:46'),
(13, 4, 67, 'updated', 'delivered', 'received', 0.00, NULL, 'Repair job intake details updated.', '2026-10-21 08:08:26', '2026-10-21 08:08:26'),
(14, 7, 67, 'created', NULL, 'received', 0.00, NULL, 'Product received for repair. Advance collected: Rs. 0.00', '2026-10-21 08:40:47', '2026-10-21 08:40:47'),
(15, 7, 67, 'status_updated', 'received', 'completed', NULL, NULL, 'Status changed from received to completed', '2026-10-21 08:41:47', '2026-10-21 08:41:47'),
(16, 7, 67, 'delivered', 'completed', 'delivered', 0.00, NULL, 'Item delivered. Remarks: self collected by AFFAQ CORP.. SN ALLOTED 1015', '2026-10-21 08:42:43', '2026-10-21 08:42:43'),
(17, 8, 67, 'created', NULL, 'received', 0.00, NULL, 'Product received for repair. Advance collected: Rs. 0.00', '2026-10-21 09:40:13', '2026-10-21 09:40:13'),
(18, 9, 67, 'created', NULL, 'received', 0.00, NULL, 'Product received for repair. Advance collected: Rs. 0.00', '2026-10-21 12:10:23', '2026-10-21 12:10:23'),
(19, 9, 67, 'status_updated', 'received', 'completed', NULL, NULL, 'Status changed from received to completed', '2026-10-21 12:11:04', '2026-10-21 12:11:04'),
(20, 9, 67, 'delivered', 'completed', 'delivered', 0.00, NULL, 'Item delivered. Remarks: SELF COLLECTED BY SHAFQAT SB (SN ALLOTED 1007)', '2026-10-21 12:11:38', '2026-10-21 12:11:38'),
(21, 10, 67, 'created', NULL, 'received', 0.00, NULL, 'Product received for repair. Advance collected: Rs. 0.00', '2026-10-23 05:51:53', '2026-10-23 05:51:53'),
(22, 10, 67, 'status_updated', 'received', 'completed', NULL, NULL, 'Status changed from received to completed', '2026-10-23 05:53:39', '2026-10-23 05:53:39'),
(23, 11, 67, 'created', NULL, 'received', 0.00, NULL, 'Product received for repair. Advance collected: Rs. 0.00', '2026-10-24 07:58:40', '2026-10-24 07:58:40'),
(24, 12, 68, 'created', NULL, 'received', 0.00, NULL, 'Product received for repair. Advance collected: Rs. 0.00', '2026-10-24 12:16:00', '2026-10-24 12:16:00'),
(25, 7, 68, 'a4_saved', NULL, 'delivered', 245500.00, NULL, 'A4 Invoice saved live from screen. Bill: Rs. 245,500.00 (Invoice - Ledger Reflected)', '2026-10-26 07:42:20', '2026-10-26 07:42:20'),
(26, 7, 68, 'a4_saved', NULL, 'delivered', 250300.00, NULL, 'A4 Invoice saved live from screen. Bill: Rs. 250,300.00 (Invoice - Ledger Reflected)', '2026-10-26 08:01:04', '2026-10-26 08:01:04'),
(27, 7, 68, 'a4_saved', NULL, 'delivered', 250300.00, NULL, 'A4 Invoice saved live from screen. Bill: Rs. 250,300.00 (Invoice - Ledger Reflected)', '2026-10-26 08:04:54', '2026-10-26 08:04:54'),
(28, 7, 68, 'a4_saved', NULL, 'delivered', 250300.00, NULL, 'A4 Invoice saved live from screen. Bill: Rs. 250,300.00 (Invoice - Ledger Reflected)', '2026-10-26 08:07:27', '2026-10-26 08:07:27'),
(29, 7, 68, 'a4_saved', NULL, 'delivered', 231300.00, NULL, 'A4 Invoice saved live from screen. Bill: Rs. 231,300.00 (Invoice - Ledger Reflected)', '2026-10-26 09:13:30', '2026-10-26 09:13:30'),
(30, 7, 68, 'a4_saved', NULL, 'delivered', 220000.00, NULL, 'A4 Invoice saved live from screen. Bill: Rs. 220,000.00 (Invoice - Ledger Reflected)', '2026-10-26 09:26:55', '2026-10-26 09:26:55'),
(31, 13, 68, 'created', NULL, 'received', 0.00, NULL, 'Product received for repair. Advance collected: Rs. 0.00', '2026-10-26 11:14:08', '2026-10-26 11:14:08'),
(32, 14, 68, 'created', NULL, 'received', 0.00, NULL, 'Product received for repair. Advance collected: Rs. 0.00', '2026-10-26 12:52:34', '2026-10-26 12:52:34'),
(33, 14, 68, 'delivered', 'received', 'delivered', 0.00, NULL, 'Item delivered. Remarks: Daewoo Rawalpindi', '2026-10-26 12:53:52', '2026-10-26 12:53:52'),
(34, 14, 68, 'a4_saved', NULL, 'delivered', 47000.00, NULL, 'A4 Invoice saved live from screen. Bill: Rs. 47,000.00 (Invoice - Ledger Reflected)', '2026-10-26 12:58:20', '2026-10-26 12:58:20'),
(35, 3, 68, 'a4_saved', NULL, 'received', 26000.00, NULL, 'A4 Invoice saved live from screen. Bill: Rs. 26,000.00 (Invoice - Ledger Reflected)', '2026-10-26 19:43:13', '2026-10-26 19:43:13'),
(36, 4, 68, 'a4_saved', NULL, 'received', 28500.00, NULL, 'A4 Invoice saved live from screen. Bill: Rs. 28,500.00 (Invoice - Ledger Reflected)', '2026-10-26 19:47:56', '2026-10-26 19:47:56'),
(37, 6, 68, 'updated', 'received', 'received', 0.00, NULL, 'Repair job intake details updated.', '2026-10-26 19:52:19', '2026-10-26 19:52:19'),
(38, 15, 68, 'created', NULL, 'received', 0.00, NULL, 'Product received for repair. Advance collected: Rs. 0.00', '2026-10-26 20:24:31', '2026-10-26 20:24:31'),
(39, 15, 68, 'a4_saved', NULL, 'received', 14000.00, NULL, 'A4 Invoice saved live from screen. Bill: Rs. 14,000.00 (Invoice - Ledger Reflected)', '2026-10-26 20:31:14', '2026-10-26 20:31:14'),
(40, 16, 68, 'created', NULL, 'received', 0.00, NULL, 'Product received for repair. Advance collected: Rs. 0.00', '2026-10-26 21:02:38', '2026-10-26 21:02:38'),
(41, 16, 68, 'delivered', 'received', 'delivered', 0.00, NULL, 'Item delivered to customer. Final Bill: Rs. 0.00', '2026-10-26 21:05:15', '2026-10-26 21:05:15'),
(42, 16, 68, 'a4_saved', NULL, 'delivered', 11500.00, NULL, 'A4 Invoice saved live from screen. Bill: Rs. 11,500.00 (Invoice - Ledger Reflected)', '2026-10-26 21:07:43', '2026-10-26 21:07:43'),
(43, 17, 68, 'created', NULL, 'received', 0.00, NULL, 'Product received for repair. Advance collected: Rs. 0.00', '2026-10-26 21:11:58', '2026-10-26 21:11:58'),
(44, 17, 68, 'delivered', 'received', 'delivered', 0.00, NULL, 'Item delivered to customer. Final Bill: Rs. 0.00', '2026-10-26 21:12:49', '2026-10-26 21:12:49'),
(45, 17, 68, 'a4_saved', NULL, 'delivered', 20500.00, NULL, 'A4 Invoice saved live from screen. Bill: Rs. 20,500.00 (Invoice - Ledger Reflected)', '2026-10-26 21:15:08', '2026-10-26 21:15:08'),
(46, 18, 68, 'created', NULL, 'received', 0.00, NULL, 'Product received for repair. Advance collected: Rs. 0.00', '2026-10-26 21:21:42', '2026-10-26 21:21:42'),
(47, 18, 68, 'delivered', 'received', 'delivered', 0.00, NULL, 'Item delivered to customer. Final Bill: Rs. 0.00', '2026-10-26 21:22:14', '2026-10-26 21:22:14'),
(48, 18, 68, 'a4_saved', NULL, 'delivered', 17500.00, NULL, 'A4 Invoice saved live from screen. Bill: Rs. 17,500.00 (Invoice - Ledger Reflected)', '2026-10-26 21:24:32', '2026-10-26 21:24:32'),
(49, 19, 68, 'created', NULL, 'received', 0.00, NULL, 'Product received for repair. Advance collected: Rs. 0.00', '2026-10-26 21:26:56', '2026-10-26 21:26:56'),
(50, 19, 68, 'delivered', 'received', 'delivered', 0.00, NULL, 'Item delivered to customer. Final Bill: Rs. 0.00', '2026-10-26 21:27:32', '2026-10-26 21:27:32'),
(51, 19, 68, 'a4_saved', NULL, 'delivered', 21500.00, NULL, 'A4 Invoice saved live from screen. Bill: Rs. 21,500.00 (Invoice - Ledger Reflected)', '2026-10-26 21:29:27', '2026-10-26 21:29:27'),
(52, 8, 67, 'updated', 'received', 'received', 0.00, NULL, 'Repair job intake details updated.', '2026-10-27 05:38:03', '2026-10-27 05:38:03'),
(53, 19, 68, 'updated', 'delivered', 'delivered', 0.00, NULL, 'Repair job intake details updated.', '2026-10-27 05:59:08', '2026-10-27 05:59:08'),
(54, 10, 67, 'delivered', 'completed', 'delivered', 0.00, NULL, 'Item delivered. Remarks: ASIA CARGO booked', '2026-10-27 10:28:40', '2026-10-27 10:28:40'),
(55, 20, 67, 'created', NULL, 'received', 0.00, NULL, 'Product received for repair. Advance collected: Rs. 0.00', '2026-10-28 06:14:50', '2026-10-28 06:14:50'),
(56, 4, 67, 'delivered', 'received', 'delivered', 0.00, NULL, 'Item delivered. Remarks: AJR Loader riksha', '2026-10-28 07:00:27', '2026-10-28 07:00:27'),
(57, 3, 67, 'delivered', 'received', 'delivered', 0.00, NULL, 'Item delivered. Remarks: AJR Loader riksha', '2026-10-28 07:01:23', '2026-10-28 07:01:23'),
(58, 7, 67, 'a4_saved', NULL, 'delivered', 220000.00, NULL, 'A4 Invoice saved live from screen. Bill: Rs. 220,000.00 (Invoice - Ledger Reflected)', '2026-10-28 07:24:46', '2026-10-28 07:24:46'),
(59, 7, 67, 'a4_saved', NULL, 'delivered', 220000.00, NULL, 'A4 Invoice saved live from screen. Bill: Rs. 220,000.00 (Invoice - Ledger Reflected)', '2026-10-28 07:25:15', '2026-10-28 07:25:15'),
(60, 7, 67, 'a4_saved', NULL, 'delivered', 220000.00, NULL, 'A4 Invoice saved live from screen. Bill: Rs. 220,000.00 (Invoice - Ledger Reflected)', '2026-10-28 07:26:31', '2026-10-28 07:26:31'),
(61, 19, 68, 'a4_saved', NULL, 'delivered', 21500.00, NULL, 'A4 Invoice saved live from screen. Bill: Rs. 21,500.00 (Invoice - Ledger Reflected)', '2026-10-28 08:23:38', '2026-10-28 08:23:38'),
(62, 20, 68, 'updated', 'received', 'received', 0.00, NULL, 'Repair job intake details updated.', '2026-10-28 18:10:52', '2026-10-28 18:10:52'),
(63, 15, 68, 'delivered', 'received', 'delivered', 0.00, NULL, 'Item delivered. Remarks: self collection', '2026-10-28 18:17:23', '2026-10-28 18:17:23'),
(64, 21, 68, 'created', NULL, 'received', 0.00, NULL, 'Product received for repair. Advance collected: Rs. 0.00', '2026-10-29 19:30:51', '2026-10-29 19:30:51'),
(65, 21, 68, 'delivered', 'received', 'delivered', 0.00, NULL, 'Item delivered to customer. Final Bill: Rs. 0.00', '2026-10-29 19:31:20', '2026-10-29 19:31:20'),
(66, 21, 68, 'a4_saved', NULL, 'delivered', 259500.00, NULL, 'A4 Invoice saved live from screen. Bill: Rs. 259,500.00 (Invoice - Ledger Reflected)', '2026-10-29 19:43:49', '2026-10-29 19:43:49'),
(67, 21, 68, 'a4_saved', NULL, 'delivered', 259500.00, NULL, 'A4 Invoice saved live from screen. Bill: Rs. 259,500.00 (Invoice - Ledger Reflected)', '2026-10-29 19:47:58', '2026-10-29 19:47:58'),
(68, 22, 68, 'created', NULL, 'received', 0.00, NULL, 'Product received for repair. Advance collected: Rs. 0.00', '2026-10-30 11:39:02', '2026-10-30 11:39:02'),
(69, 22, 68, 'delivered', 'received', 'delivered', 0.00, NULL, 'Item delivered to customer. Final Bill: Rs. 0.00', '2026-10-30 11:39:33', '2026-10-30 11:39:33'),
(70, 22, 68, 'a4_saved', NULL, 'delivered', 42000.00, NULL, 'A4 Invoice saved live from screen. Bill: Rs. 42,000.00 (Invoice - Ledger Reflected)', '2026-10-30 11:40:56', '2026-10-30 11:40:56'),
(71, 22, 68, 'payment_received', NULL, NULL, 42000.00, NULL, 'Payment of Rs. 42,000.00 received via Receipt Voucher RVID-004', '2026-10-30 12:04:44', '2026-10-30 12:04:44'),
(72, 23, 67, 'created', NULL, 'received', 0.00, NULL, 'Product received for repair. Advance collected: Rs. 0.00', '2026-10-30 12:13:54', '2026-10-30 12:13:54'),
(73, 23, 67, 'updated', 'received', 'received', 0.00, NULL, 'Repair job intake details updated.', '2026-10-30 12:39:43', '2026-10-30 12:39:43'),
(74, 21, 68, 'updated', 'delivered', 'delivered', 0.00, NULL, 'Repair job intake details updated.', '2026-10-30 12:51:59', '2026-10-30 12:51:59'),
(75, 24, 67, 'created', NULL, 'received', 0.00, NULL, 'Product received for repair. Advance collected: Rs. 0.00', '2026-10-30 13:04:26', '2026-10-30 13:04:26'),
(76, 24, 67, 'status_updated', 'received', 'completed', NULL, NULL, 'Status changed from received to completed', '2026-10-30 13:06:16', '2026-10-30 13:06:16'),
(77, 24, 67, 'delivered', 'completed', 'delivered', 0.00, NULL, 'Item delivered. Remarks: SELF', '2026-10-30 13:08:45', '2026-10-30 13:08:45'),
(78, 25, 68, 'created', NULL, 'received', 0.00, NULL, 'Product received for repair. Advance collected: Rs. 0.00', '2026-10-30 19:03:20', '2026-10-30 19:03:20'),
(79, 25, 68, 'delivered', 'received', 'delivered', 0.00, NULL, 'Item delivered. Remarks: work on site. Driver cards repair', '2026-10-30 19:04:09', '2026-10-30 19:04:09'),
(80, 25, 68, 'a4_saved', NULL, 'delivered', 43000.00, NULL, 'A4 Invoice saved live from screen. Bill: Rs. 43,000.00 (Invoice - Ledger Reflected)', '2026-10-30 19:07:06', '2026-10-30 19:07:06'),
(81, 26, 67, 'created', NULL, 'received', 0.00, NULL, 'Product received for repair. Advance collected: Rs. 0.00', '2026-10-30 19:26:58', '2026-10-30 19:26:58'),
(82, 26, 67, 'delivered', 'received', 'delivered', 0.00, NULL, 'Item delivered. Remarks: test delivery driver', '2026-10-30 19:28:17', '2026-10-30 19:28:17'),
(83, 26, 67, 'a4_saved', NULL, 'delivered', 4000.00, NULL, 'A4 Invoice saved live from screen. Bill: Rs. 4,000.00 (Invoice - Ledger Reflected)', '2026-10-30 19:30:35', '2026-10-30 19:30:35'),
(84, 26, 67, 'a4_saved', NULL, 'delivered', 4000.00, NULL, 'A4 Invoice saved live from screen. Bill: Rs. 4,000.00 (Invoice - Ledger Reflected)', '2026-10-30 19:44:50', '2026-10-30 19:44:50'),
(85, 26, 67, 'a4_saved', NULL, 'delivered', 4000.00, NULL, 'A4 Invoice saved live from screen. Bill: Rs. 4,000.00 (Invoice - Ledger Reflected)', '2026-10-30 19:45:24', '2026-10-30 19:45:24'),
(86, 27, 68, 'created', NULL, 'received', 0.00, NULL, 'Product received for repair. Advance collected: Rs. 0.00', '2026-10-31 05:37:59', '2026-10-31 05:37:59'),
(87, 27, 68, 'delivered', 'received', 'delivered', 0.00, NULL, 'Item delivered. Remarks: Hiace', '2026-10-31 05:39:12', '2026-10-31 05:39:12'),
(88, 27, 68, 'a4_saved', NULL, 'delivered', 41000.00, NULL, 'A4 Invoice saved live from screen. Bill: Rs. 41,000.00 (Invoice - Ledger Reflected)', '2026-10-31 05:42:59', '2026-10-31 05:42:59'),
(89, 28, 68, 'created', NULL, 'received', 0.00, NULL, 'Product received for repair. Advance collected: Rs. 0.00', '2026-10-31 05:53:17', '2026-10-31 05:53:17'),
(90, 28, 68, 'status_updated', 'received', 'completed', NULL, NULL, 'Status changed from received to completed', '2026-10-31 05:54:33', '2026-10-31 05:54:33'),
(91, 28, 68, 'a4_saved', NULL, 'completed', 44000.00, NULL, 'A4 Invoice saved live from screen. Bill: Rs. 44,000.00 (Invoice - Ledger Reflected)', '2026-10-31 05:56:13', '2026-10-31 05:56:13'),
(92, 29, 68, 'created', NULL, 'received', 0.00, NULL, 'Product received for repair. Advance collected: Rs. 0.00', '2026-10-31 06:16:40', '2026-10-31 06:16:40'),
(93, 29, 68, 'delivered', 'received', 'delivered', 0.00, NULL, 'Item delivered to customer. Final Bill: Rs. 0.00', '2026-10-31 06:16:50', '2026-10-31 06:16:50'),
(94, 18, 68, 'payment_received', NULL, NULL, 17500.00, NULL, 'Payment of Rs. 17,500.00 received via Receipt Voucher RVID-005', '2026-10-31 07:51:39', '2026-10-31 07:51:39'),
(95, 21, 68, 'a4_saved', NULL, 'delivered', 216700.00, NULL, 'A4 Invoice saved live from screen. Bill: Rs. 216,700.00 (Invoice - Ledger Reflected)', '2026-10-31 08:09:53', '2026-10-31 08:09:53'),
(96, 29, 67, 'updated', 'delivered', 'delivered', 0.00, NULL, 'Repair job intake details updated.', '2026-10-31 08:18:09', '2026-10-31 08:18:09'),
(97, 24, 68, 'a4_saved', NULL, 'delivered', 6000.00, NULL, 'A4 Invoice saved live from screen. Bill: Rs. 6,000.00 (Invoice - Ledger Reflected)', '2026-10-31 08:18:27', '2026-10-31 08:18:27'),
(98, 27, 68, 'updated', 'delivered', 'delivered', 33000.00, 2, 'Repair job intake details updated.', '2026-10-31 08:29:26', '2026-10-31 08:29:26'),
(99, 27, 68, 'a4_saved', NULL, 'delivered', 41000.00, NULL, 'A4 Invoice saved live from screen. Bill: Rs. 41,000.00 (Invoice - Ledger Reflected)', '2026-10-31 08:31:16', '2026-10-31 08:31:16'),
(100, 27, 68, 'updated', 'delivered', 'delivered', 0.00, NULL, 'Repair job intake details updated.', '2026-10-31 08:32:02', '2026-10-31 08:32:02'),
(101, 27, 68, 'payment_received', NULL, NULL, 33000.00, NULL, 'Payment of Rs. 33,000.00 received via Receipt Voucher RVID-006', '2026-10-31 08:33:53', '2026-10-31 08:33:53'),
(102, 29, 67, 'a4_saved', NULL, 'delivered', 5000.00, NULL, 'A4 Invoice saved live from screen. Bill: Rs. 5,000.00 (Invoice - Ledger Reflected)', '2026-10-31 08:57:50', '2026-10-31 08:57:50'),
(103, 17, 68, 'payment_received', NULL, NULL, 20500.00, NULL, 'Payment of Rs. 20,500.00 received via Receipt Voucher RVID-007', '2026-10-31 10:34:40', '2026-10-31 10:34:40'),
(104, 30, 67, 'created', NULL, 'received', 0.00, NULL, 'Product received for repair. Advance collected: Rs. 0.00', '2026-10-31 11:35:14', '2026-10-31 11:35:14'),
(105, 12, 68, 'status_updated', 'received', 'completed', NULL, NULL, 'Status changed from received to completed', '2026-10-31 11:48:21', '2026-10-31 11:48:21'),
(106, 12, 68, 'a4_saved', NULL, 'completed', 25000.00, NULL, 'A4 Invoice saved live from screen. Bill: Rs. 25,000.00 (Invoice - Ledger Reflected)', '2026-10-31 11:49:59', '2026-10-31 11:49:59');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(2, 'Branch Manager', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(3, 'Branch', 'web', '2026-09-11 10:46:24', '2026-09-11 10:46:24'),
(4, 'STORE', 'web', '2026-09-29 01:43:47', '2026-09-29 01:43:47'),
(5, 'branch worker', 'web', '2026-10-19 17:44:00', '2026-10-19 17:44:00');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 1),
(17, 1),
(18, 1),
(19, 1),
(20, 1),
(21, 1),
(22, 1),
(23, 1),
(24, 1),
(25, 1),
(26, 1),
(27, 1),
(28, 1),
(29, 1),
(30, 1),
(31, 1),
(32, 1),
(33, 1),
(34, 1),
(35, 1),
(36, 1),
(37, 1),
(38, 1),
(39, 1),
(40, 1),
(41, 1),
(42, 1),
(43, 1),
(44, 1),
(45, 1),
(46, 1),
(47, 1),
(48, 1),
(49, 1),
(50, 1),
(51, 1),
(52, 1),
(53, 1),
(54, 1),
(55, 1),
(56, 1),
(57, 1),
(58, 1),
(59, 1),
(60, 1),
(61, 1),
(62, 1),
(63, 1),
(64, 1),
(65, 1),
(66, 1),
(67, 1),
(68, 1),
(69, 1),
(70, 1),
(71, 1),
(72, 1),
(73, 1),
(74, 1),
(75, 1),
(76, 1),
(77, 1),
(78, 1),
(79, 1),
(80, 1),
(81, 1),
(82, 1),
(83, 1),
(84, 1),
(85, 1),
(86, 1),
(87, 1),
(88, 1),
(89, 1),
(90, 1),
(91, 1),
(92, 1),
(93, 1),
(94, 1),
(95, 1),
(96, 1),
(97, 1),
(98, 1),
(99, 1),
(100, 1),
(101, 1),
(102, 1),
(103, 1),
(104, 1),
(105, 1),
(106, 1),
(107, 1),
(108, 1),
(109, 1),
(110, 1),
(111, 1),
(112, 1),
(113, 1),
(114, 1),
(115, 1),
(116, 1),
(117, 1),
(118, 1),
(119, 1),
(120, 1),
(121, 1),
(122, 1),
(123, 1),
(124, 1),
(125, 1),
(126, 1),
(127, 1),
(128, 1),
(129, 1),
(130, 1),
(131, 1),
(132, 1),
(133, 1),
(134, 1),
(135, 1),
(136, 1),
(137, 1),
(138, 1),
(139, 1),
(140, 1),
(141, 1),
(142, 1),
(143, 1),
(144, 1),
(145, 1),
(146, 1),
(147, 1),
(148, 1),
(149, 1),
(150, 1),
(151, 1),
(152, 1),
(153, 1),
(154, 1),
(155, 1),
(156, 1),
(157, 1),
(158, 1),
(159, 1),
(160, 1),
(161, 1),
(162, 1),
(163, 1),
(164, 1),
(165, 1),
(166, 1),
(167, 1),
(168, 1),
(169, 1),
(170, 1),
(171, 1),
(172, 1),
(173, 1),
(174, 1),
(175, 1),
(176, 1),
(177, 1),
(178, 1),
(179, 1),
(180, 1),
(181, 1),
(182, 1),
(183, 1),
(184, 1),
(185, 1),
(186, 1),
(187, 1),
(188, 1),
(189, 1),
(190, 1),
(191, 1),
(192, 1),
(193, 1),
(194, 1),
(195, 1),
(196, 1),
(197, 1),
(198, 1),
(199, 1),
(200, 1),
(201, 1),
(202, 1),
(203, 1),
(204, 1),
(205, 1),
(206, 1),
(207, 1),
(208, 1),
(209, 1),
(210, 1),
(211, 1),
(212, 1),
(213, 1),
(214, 1),
(215, 1),
(216, 1),
(217, 1),
(218, 1),
(219, 1),
(220, 1),
(221, 1),
(222, 1),
(223, 1),
(224, 1),
(225, 1),
(226, 1),
(227, 1),
(228, 1),
(229, 1),
(230, 1),
(231, 1),
(232, 1),
(233, 1),
(234, 1),
(235, 1),
(236, 1),
(237, 1),
(238, 1),
(239, 1),
(240, 1),
(241, 1),
(242, 1),
(243, 1),
(244, 1),
(245, 1),
(246, 1),
(247, 1),
(248, 1),
(249, 1),
(250, 1),
(251, 1),
(252, 1),
(253, 1),
(254, 1),
(255, 1),
(256, 1),
(257, 1),
(258, 1),
(259, 1),
(260, 1),
(261, 1),
(262, 1),
(263, 1),
(264, 1),
(265, 1),
(266, 1),
(267, 1),
(268, 1),
(269, 1),
(270, 1),
(271, 1),
(272, 1),
(273, 1),
(274, 1),
(275, 1),
(276, 1),
(277, 1),
(278, 1),
(279, 1),
(280, 1),
(281, 1),
(282, 1),
(283, 1),
(284, 1),
(285, 1),
(286, 1),
(287, 1),
(288, 1),
(289, 1),
(290, 1),
(291, 1),
(292, 1),
(293, 1),
(294, 1),
(295, 1),
(1, 2),
(2, 2),
(3, 2),
(4, 2),
(5, 2),
(6, 2),
(7, 2),
(8, 2),
(9, 2),
(10, 2),
(11, 2),
(12, 2),
(21, 2),
(22, 2),
(23, 2),
(24, 2),
(25, 2),
(26, 2),
(27, 2),
(28, 2),
(29, 2),
(30, 2),
(31, 2),
(32, 2),
(33, 2),
(34, 2),
(35, 2),
(36, 2),
(41, 2),
(42, 2),
(43, 2),
(44, 2),
(53, 2),
(54, 2),
(55, 2),
(56, 2),
(57, 2),
(58, 2),
(59, 2),
(60, 2),
(61, 2),
(65, 2),
(66, 2),
(67, 2),
(68, 2),
(69, 2),
(70, 2),
(71, 2),
(72, 2),
(73, 2),
(74, 2),
(75, 2),
(76, 2),
(81, 2),
(82, 2),
(83, 2),
(84, 2),
(85, 2),
(86, 2),
(87, 2),
(88, 2),
(89, 2),
(90, 2),
(91, 2),
(92, 2),
(93, 2),
(94, 2),
(95, 2),
(96, 2),
(97, 2),
(98, 2),
(99, 2),
(100, 2),
(109, 2),
(110, 2),
(111, 2),
(112, 2),
(113, 2),
(114, 2),
(115, 2),
(116, 2),
(117, 2),
(118, 2),
(119, 2),
(120, 2),
(125, 2),
(126, 2),
(127, 2),
(128, 2),
(133, 2),
(134, 2),
(135, 2),
(136, 2),
(137, 2),
(138, 2),
(139, 2),
(140, 2),
(141, 2),
(142, 2),
(143, 2),
(144, 2),
(145, 2),
(146, 2),
(147, 2),
(148, 2),
(153, 2),
(154, 2),
(155, 2),
(156, 2),
(157, 2),
(158, 2),
(159, 2),
(160, 2),
(161, 2),
(162, 2),
(163, 2),
(164, 2),
(177, 2),
(178, 2),
(179, 2),
(180, 2),
(181, 2),
(182, 2),
(183, 2),
(184, 2),
(185, 2),
(186, 2),
(187, 2),
(188, 2),
(189, 2),
(190, 2),
(191, 2),
(192, 2),
(197, 2),
(198, 2),
(199, 2),
(200, 2),
(205, 2),
(206, 2),
(207, 2),
(208, 2),
(221, 2),
(222, 2),
(223, 2),
(224, 2),
(229, 2),
(230, 2),
(231, 2),
(232, 2),
(273, 2),
(274, 2),
(275, 2),
(276, 2),
(296, 2),
(297, 2),
(298, 2),
(1, 3),
(5, 3),
(7, 3),
(9, 3),
(10, 3),
(11, 3),
(17, 3),
(21, 3),
(25, 3),
(29, 3),
(33, 3),
(37, 3),
(41, 3),
(45, 3),
(46, 3),
(47, 3),
(53, 3),
(65, 3),
(66, 3),
(73, 3),
(77, 3),
(81, 3),
(82, 3),
(85, 3),
(86, 3),
(87, 3),
(89, 3),
(90, 3),
(93, 3),
(94, 3),
(95, 3),
(97, 3),
(113, 3),
(114, 3),
(117, 3),
(118, 3),
(133, 3),
(141, 3),
(173, 3),
(177, 3),
(13, 4),
(14, 4),
(15, 4),
(16, 4),
(57, 4),
(58, 4),
(59, 4),
(61, 4),
(1, 5),
(2, 5),
(3, 5),
(4, 5),
(5, 5),
(6, 5),
(7, 5),
(8, 5),
(9, 5),
(10, 5),
(11, 5),
(12, 5),
(21, 5),
(22, 5),
(23, 5),
(24, 5),
(25, 5),
(26, 5),
(27, 5),
(28, 5),
(29, 5),
(30, 5),
(31, 5),
(32, 5),
(33, 5),
(34, 5),
(35, 5),
(36, 5),
(53, 5),
(54, 5),
(55, 5),
(56, 5),
(57, 5),
(58, 5),
(59, 5),
(60, 5),
(61, 5),
(62, 5),
(63, 5),
(64, 5),
(65, 5),
(66, 5),
(67, 5),
(68, 5),
(69, 5),
(70, 5),
(71, 5),
(72, 5),
(73, 5),
(74, 5),
(75, 5),
(76, 5),
(81, 5),
(82, 5),
(83, 5),
(84, 5),
(85, 5),
(86, 5),
(87, 5),
(88, 5),
(89, 5),
(90, 5),
(91, 5),
(92, 5),
(93, 5),
(94, 5),
(95, 5),
(96, 5),
(97, 5),
(98, 5),
(99, 5),
(100, 5),
(109, 5),
(110, 5),
(111, 5),
(112, 5),
(113, 5),
(114, 5),
(115, 5),
(116, 5),
(117, 5),
(118, 5),
(119, 5),
(120, 5),
(125, 5),
(126, 5),
(127, 5),
(128, 5),
(133, 5),
(134, 5),
(135, 5),
(136, 5),
(137, 5),
(138, 5),
(139, 5),
(140, 5),
(141, 5),
(142, 5),
(143, 5),
(144, 5),
(149, 5),
(150, 5),
(151, 5),
(152, 5),
(153, 5),
(154, 5),
(155, 5),
(156, 5),
(161, 5),
(162, 5),
(163, 5),
(164, 5),
(169, 5),
(170, 5),
(171, 5),
(172, 5),
(177, 5),
(178, 5),
(179, 5),
(180, 5),
(197, 5),
(198, 5),
(199, 5),
(200, 5),
(201, 5),
(202, 5),
(203, 5),
(204, 5),
(221, 5),
(222, 5),
(223, 5),
(224, 5),
(229, 5),
(230, 5),
(231, 5),
(232, 5),
(273, 5),
(274, 5),
(275, 5),
(276, 5),
(288, 5),
(289, 5),
(290, 5),
(291, 5),
(292, 5),
(293, 5),
(294, 5),
(295, 5),
(296, 5),
(297, 5),
(298, 5);

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_synced` tinyint NOT NULL DEFAULT '0',
  `invoice_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_id` bigint UNSIGNED DEFAULT NULL,
  `walkin_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_title` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'Invoice',
  `sale_status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `order_status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `is_booking` tinyint(1) NOT NULL DEFAULT '0',
  `credit_days` int DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `estimated_delivery_date` date DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `delivery_source` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_remarks` text COLLATE utf8mb4_unicode_ci,
  `total_items` decimal(12,2) DEFAULT NULL,
  `total_bill_amount` decimal(12,2) DEFAULT NULL,
  `total_extradiscount` decimal(12,2) DEFAULT NULL,
  `tax_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_net` decimal(12,2) DEFAULT NULL,
  `cash` decimal(12,2) DEFAULT NULL,
  `payment_details` longtext COLLATE utf8mb4_unicode_ci,
  `card` decimal(12,2) DEFAULT NULL,
  `change` decimal(12,2) DEFAULT NULL,
  `total_amount_Words` text COLLATE utf8mb4_unicode_ci,
  `terms_and_conditions` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `uuid`, `is_synced`, `invoice_no`, `customer_id`, `walkin_name`, `reference`, `invoice_title`, `sale_status`, `order_status`, `is_booking`, `credit_days`, `due_date`, `estimated_delivery_date`, `delivery_date`, `delivery_source`, `delivery_remarks`, `total_items`, `total_bill_amount`, `total_extradiscount`, `tax_percent`, `tax_amount`, `total_net`, `cash`, `payment_details`, `card`, `change`, `total_amount_Words`, `terms_and_conditions`, `created_at`, `updated_at`) VALUES
(1, '943a9c1d-3b2e-4533-8396-0815928c1c52', 0, 'INV-0001', 2, NULL, 'HF INDUCTION HEATER 60KW AB WITH CONVAYER', 'Invoice', 'posted', 'pending', 1, NULL, '2026-11-03', '2026-11-03', NULL, NULL, NULL, 2.00, 750000.00, 0.00, 0.00, 0.00, 750000.00, 400000.00, '[{\"account_id\":2,\"amount\":400000}]', NULL, -350000.00, NULL, 'DELIVERY TIME: 10-15 WORKING DAYS', '2026-10-16 19:00:00', '2026-10-17 12:44:49'),
(2, '186435e4-c083-4c97-a5df-484169d3be8f', 0, 'INV-0002', 12, NULL, 'Induction Heater 60KW AB LTZ', 'Invoice', 'posted', 'delivered', 1, NULL, '2026-10-24', '2026-10-24', '2026-10-24', 'NABEEL RIKSHAW', '', 1.00, 580000.00, 0.00, 0.00, 0.00, 580000.00, 100000.00, '[{\"account_id\":3,\"amount\":100000}]', NULL, -480000.00, NULL, '1. Payment terms as agreed. 2. Warranty 1 year. 3. Goods once sold will not be returned without authorization.', '2026-08-21 19:00:00', '2026-10-24 10:21:20'),
(3, 'a7f8be07-a125-43e2-b72d-74f5cf788c96', 0, 'INV-0003', 14, NULL, 'HF Induction Heater 25kw-A and 80kw AB PO NO. LDR-27000994', 'Invoice', 'posted', 'pending', 1, NULL, '2026-11-05', '2026-11-05', NULL, NULL, NULL, 2.00, 1040000.00, 0.00, 18.00, 187200.00, 1227200.00, 0.00, NULL, NULL, -1227200.00, NULL, '1. Payment terms: 50% Advance with confirmed Order. 2. Warranty 1 year.', '2026-10-12 19:00:00', '2026-10-24 18:52:36'),
(4, '3cbf091c-969a-4f1d-a753-88104f94d2ec', 0, 'INV-0004', 15, NULL, 'Induction Heater LTZ-40KW', 'Invoice', 'posted', 'pending', 1, NULL, '2026-11-10', '2026-11-10', NULL, NULL, NULL, 1.00, 400000.00, 0.00, 0.00, 0.00, 400000.00, 0.00, NULL, NULL, -400000.00, NULL, '1. Payment terms as agreed. 2. Warranty 1 year. 3. Goods once sold will not be returned without authorization.', '2026-10-25 19:00:00', '2026-10-26 08:40:14'),
(5, 'ec1c942d-f09a-4c6f-9de6-74d7c9559a5c', 0, 'INV-0005', 16, NULL, 'Induction Heater LTZ-60kw', 'Invoice', 'posted', 'pending', 1, NULL, '2026-11-10', '2026-11-10', NULL, NULL, NULL, 1.00, 580000.00, 0.00, 0.00, 0.00, 580000.00, 0.00, NULL, NULL, -580000.00, NULL, '1. Payment terms as agreed. 2. Warranty 1 year. 3. Goods once sold will not be returned without authorization.', '2026-10-25 19:00:00', '2026-10-26 12:00:04'),
(6, '7e71147c-f620-4c15-927b-bc7b953fbf5c', 0, 'INV-0006', 20, NULL, 'LTZ-60KW-AB', 'Invoice', 'posted', 'pending', 1, NULL, '2026-11-11', '2026-11-11', NULL, NULL, NULL, 1.00, 530000.00, 0.00, 0.00, 0.00, 530000.00, 0.00, NULL, NULL, -530000.00, NULL, '1. Payment terms as agreed. 2. Warranty 1 year. 3. Goods once sold will not be returned without authorization.', '2026-10-26 19:00:00', '2026-10-26 20:45:12'),
(7, '4c886087-31a8-4e15-80e6-19cf6a2e2d40', 0, 'INV-0007', 20, NULL, 'LTZ-60KW-AB', 'Invoice', 'posted', 'pending', 1, NULL, '2026-11-11', '2026-11-11', NULL, NULL, NULL, 1.00, 530000.00, 0.00, 0.00, 0.00, 530000.00, 0.00, NULL, NULL, -530000.00, NULL, '1. Payment terms as agreed. 2. Warranty 1 year. 3. Goods once sold will not be returned without authorization.', '2026-10-26 19:00:00', '2026-10-26 20:47:12'),
(8, 'cc97245a-3a84-4b74-b240-314d3a4cd896', 0, 'INV-0008', 20, NULL, 'LTZ-80KW-AB', 'Invoice', 'posted', 'pending', 1, NULL, '2026-11-11', '2026-11-11', NULL, NULL, NULL, 1.00, 770000.00, 0.00, 0.00, 0.00, 770000.00, 0.00, NULL, NULL, -770000.00, NULL, '1. Payment terms as agreed. 2. Warranty 1 year. 3. Goods once sold will not be returned without authorization.', '2026-10-26 19:00:00', '2026-10-26 20:56:29'),
(9, 'd665230b-bd7e-4af2-8a4d-4bef3b1725ea', 0, 'INV-0009', 4, NULL, 'Induction Coils (New)', 'Invoice', 'posted', 'delivered', 1, NULL, '2026-11-12', '2026-11-12', '2026-10-28', 'Self Collection', '', 2.00, 11000.00, 0.00, 0.00, 0.00, 11000.00, 0.00, NULL, NULL, -11000.00, NULL, NULL, '2026-10-27 19:00:00', '2026-10-28 18:14:59'),
(10, '77710b63-d131-4af5-afaa-507fd1d9861f', 0, 'INV-0010', 27, NULL, 'AIR COOLED INDUCTION HEATER 80KW FOR CHEMICAL HEATING', 'Invoice', 'posted', 'pending', 1, NULL, '2026-11-12', '2026-11-12', NULL, NULL, NULL, 4.00, 950000.00, 0.00, 0.00, 0.00, 950000.00, 350000.00, NULL, NULL, -600000.00, NULL, '1. Payment terms as agreed. 2. Warranty 1 year. 3. Goods once sold will not be returned without authorization.', '2026-10-27 19:00:00', '2026-10-29 10:44:16'),
(11, 'c6f51db5-9ecf-49b4-9b44-7404bb790a58', 0, 'INV-0011', 29, NULL, NULL, 'Invoice', 'posted', 'delivered', 1, NULL, '2026-11-13', '2026-11-13', '2026-10-28', 'hiace', '', 3.00, 52500.00, 0.00, 0.00, 0.00, 52500.00, 0.00, NULL, NULL, -52500.00, NULL, NULL, '2026-10-28 19:00:00', '2026-10-30 12:23:27'),
(12, 'e3ad74a4-da7c-4bcf-bfc1-ab8366fb06d4', 0, 'INV-0012', 7, NULL, NULL, 'Invoice', 'posted', 'delivered', 1, NULL, '2026-09-14', '2026-09-14', '2026-09-14', 'LTE team', '', 3.00, 640000.00, 0.00, 0.00, 0.00, 640000.00, 0.00, NULL, NULL, -640000.00, NULL, '1. Payment terms as agreed. 2. Warranty 1 year. 3. Goods once sold will not be returned without authorization.', '2026-08-31 19:00:00', '2026-10-28 20:07:20'),
(13, 'e8f552e7-c83c-4581-b2c4-66582cff7a2d', 0, 'INV-0013', 34, NULL, 'Portable Induction Heater with Interchangeable Coils', 'Invoice', 'posted', 'pending', 1, NULL, '2026-11-14', '2026-11-14', NULL, NULL, NULL, 3.00, 1260000.00, 0.00, 18.00, 226800.00, 1486800.00, 400000.00, '[{\"account_id\":3,\"amount\":400000}]', NULL, -1086800.00, NULL, '1. Payment terms as agreed. 2. Warranty 1 year. 3. Goods once sold will not be returned without authorization.', '2026-10-29 19:00:00', '2026-10-30 19:05:03'),
(14, '9b04450b-62b7-4733-b039-9796cdbc6f62', 0, 'INV-0014', 37, NULL, NULL, 'Invoice', 'posted', 'pending', 1, NULL, '2026-11-15', '2026-11-15', NULL, NULL, NULL, 1.00, 2.00, 0.00, 0.00, 0.00, 2.00, 0.00, NULL, NULL, -2.00, NULL, '1. Payment terms as agreed. 2. Warranty 1 year. 3. Goods once sold will not be returned without authorization.', '2026-10-30 19:00:00', '2026-10-31 11:26:45'),
(15, '82c9a314-aba1-4a69-9838-1b92a085d8f6', 0, 'INV-0015', 39, NULL, NULL, 'Invoice', 'posted', 'pending', 1, NULL, '2026-11-15', '2026-11-15', NULL, NULL, NULL, 1.00, 550000.00, 0.00, 0.00, 0.00, 550000.00, 50000.00, '[{\"account_id\":2,\"amount\":50000}]', NULL, -500000.00, NULL, '1. Payment terms as agreed. 2. Warranty 1 year. 3. Goods once sold will not be returned without authorization.', '2026-10-30 19:00:00', '2026-10-31 09:41:20');

-- --------------------------------------------------------

--
-- Table structure for table `sales_officers`
--

CREATE TABLE `sales_officers` (
  `id` bigint UNSIGNED NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_urdu` text COLLATE utf8mb4_unicode_ci,
  `mobile` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sale_items`
--

CREATE TABLE `sale_items` (
  `id` bigint UNSIGNED NOT NULL,
  `is_manual` tinyint(1) NOT NULL DEFAULT '0',
  `sale_id` bigint UNSIGNED NOT NULL,
  `warehouse_id` bigint UNSIGNED DEFAULT NULL,
  `product_id` bigint UNSIGNED DEFAULT NULL,
  `vendor_id` bigint UNSIGNED DEFAULT NULL,
  `size_mode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `technical_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `serial_no` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `brand_id` bigint UNSIGNED DEFAULT NULL,
  `category_id` bigint UNSIGNED DEFAULT NULL,
  `sub_category_id` bigint UNSIGNED DEFAULT NULL,
  `unit_id` bigint UNSIGNED DEFAULT NULL,
  `color` text COLLATE utf8mb4_unicode_ci,
  `technical_specs` text COLLATE utf8mb4_unicode_ci,
  `technical_remarks` text COLLATE utf8mb4_unicode_ci,
  `stock` decimal(12,2) NOT NULL DEFAULT '0.00',
  `price_level` decimal(12,2) NOT NULL DEFAULT '0.00',
  `price` decimal(12,2) DEFAULT '0.00',
  `purchase_price` decimal(12,2) DEFAULT NULL,
  `price_per_piece` decimal(12,2) NOT NULL DEFAULT '0.00',
  `price_per_m2` decimal(12,2) NOT NULL DEFAULT '0.00',
  `qty` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `total_pieces` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `loose_pieces` int NOT NULL DEFAULT '0',
  `retail_price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `discount_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `tax_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total` decimal(12,2) DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sale_items`
--

INSERT INTO `sale_items` (`id`, `is_manual`, `sale_id`, `warehouse_id`, `product_id`, `vendor_id`, `size_mode`, `product_name`, `technical_name`, `model`, `serial_no`, `brand_id`, `category_id`, `sub_category_id`, `unit_id`, `color`, `technical_specs`, `technical_remarks`, `stock`, `price_level`, `price`, `purchase_price`, `price_per_piece`, `price_per_m2`, `qty`, `total_pieces`, `loose_pieces`, `retail_price`, `discount_percent`, `discount_amount`, `tax_percent`, `tax_amount`, `total`, `created_at`, `updated_at`) VALUES
(1, 0, 1, 1, 7, NULL, 'by_pieces', 'HF INDUCTION HEATER 60KW-AB', NULL, 'WATER-COOLED', NULL, 2, NULL, NULL, NULL, 'eyJuYW1lIjoiSEYgSU5EVUNUSU9OIEhFQVRFUiA2MEtXLUFCIiwic2l6ZSI6IjI0XC83IDEwMCUiLCJjb2xvciI6IldBVEVSLUNPT0xFRCIsInN0b2NrIjowLCJzYWxlX3ByaWNlIjoiNTgwMDAwIiwid2hvbGVzYWxlX3ByaWNlIjoiMCIsIndlaWdodF9wZXJfcGllY2UiOjAsInB1cmNoX3ByaWNlIjoiMCIsImFsZXJ0IjoiMCIsImJhcmNvZGUiOiI5NDM0MTIiLCJjb252X2ZhY3RvciI6MSwiaXNfYmFzZV92YXJpYW50IjoiMSIsInVuaXQiOiJQY3MiLCJjdXJyZW50X3N0b2NrIjowfQ==', NULL, NULL, 0.00, 0.00, 600000.00, NULL, 0.00, 0.00, 1.0000, 1.0000, 0, 0.00, 1.67, 10000.00, 0.00, 0.00, 590000.00, '2026-10-17 12:43:00', '2026-10-17 12:43:00'),
(2, 0, 1, 1, 13, NULL, 'by_pieces', 'Conveyer for Sciessor Forging', NULL, '1-PHASE 220V AC  50Hz/60Hz', NULL, 1, NULL, NULL, NULL, 'eyJuYW1lIjoiQ29udmV5ZXIgZm9yIFNjaWVzc29yIEZvcmdpbmciLCJzaXplIjoiLSIsImNvbG9yIjoiLSIsInN0b2NrIjowLCJzYWxlX3ByaWNlIjoiMTcwMDAwIiwid2hvbGVzYWxlX3ByaWNlIjoiMCIsIndlaWdodF9wZXJfcGllY2UiOjAsInB1cmNoX3ByaWNlIjoiMCIsImFsZXJ0IjoiMCIsImJhcmNvZGUiOiI4NTE0MzgiLCJjb252X2ZhY3RvciI6MSwiaXNfYmFzZV92YXJpYW50IjoiMSIsInVuaXQiOiJQY3MiLCJjdXJyZW50X3N0b2NrIjowfQ==', NULL, NULL, 0.00, 0.00, 170000.00, NULL, 0.00, 0.00, 1.0000, 1.0000, 0, 0.00, 5.88, 10000.00, 0.00, 0.00, 160000.00, '2026-10-17 12:43:00', '2026-10-17 12:43:00'),
(3, 0, 2, 1, 7, NULL, 'by_pieces', 'HF INDUCTION HEATER 60KW-AB', 'HF INDUCTION HEATER 60KW-AB', 'LTZ-60KW', '1013', 2, NULL, NULL, NULL, '01 NEW COIL', 'S-S-240- 3 CORES', NULL, 0.00, 0.00, 580000.00, NULL, 0.00, 0.00, 1.0000, 1.0000, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 580000.00, '2026-10-24 09:52:46', '2026-10-24 10:21:20'),
(4, 0, 3, 1, 3, NULL, 'by_pieces', 'HF INDUCTION HEATER 25KW-A', NULL, 'WATER-COOLED', NULL, 2, NULL, NULL, NULL, 'eyJuYW1lIjoiSEYgSU5EVUNUSU9OIEhFQVRFUiAyNUtXLUEiLCJzaXplIjoiMjRcLzcgMTAwJSIsImNvbG9yIjoiV0FURVItQ09PTEVEIiwic3RvY2siOjAsInNhbGVfcHJpY2UiOiIyNjAwMDAiLCJ3aG9sZXNhbGVfcHJpY2UiOiIwIiwid2VpZ2h0X3Blcl9waWVjZSI6MCwicHVyY2hfcHJpY2UiOiIwIiwiYWxlcnQiOiIwIiwiYmFyY29kZSI6Ijk3MjIxMCIsImNvbnZfZmFjdG9yIjoxLCJpc19iYXNlX3ZhcmlhbnQiOiIxIiwidW5pdCI6IlBjcyIsImN1cnJlbnRfc3RvY2siOjB9', NULL, NULL, 0.00, 0.00, 290000.00, NULL, 0.00, 0.00, 1.0000, 1.0000, 0, 0.00, 0.00, 0.00, 18.00, 52200.00, 290000.00, '2026-10-24 18:52:00', '2026-10-24 18:52:00'),
(5, 0, 3, 1, 8, NULL, 'by_pieces', 'HF INDUCTION HEATER 80KW-AB', NULL, 'WATER-COOLED', NULL, 2, NULL, NULL, NULL, 'eyJuYW1lIjoiSEYgSU5EVUNUSU9OIEhFQVRFUiA4MEtXLUFCIiwic2l6ZSI6IjI0XC83IDEwMCUiLCJjb2xvciI6IldBVEVSLUNPT0xFRCIsInN0b2NrIjowLCJzYWxlX3ByaWNlIjoiNzUwMDAwIiwid2hvbGVzYWxlX3ByaWNlIjoiMCIsIndlaWdodF9wZXJfcGllY2UiOjAsInB1cmNoX3ByaWNlIjoiMCIsImFsZXJ0IjoiMCIsImJhcmNvZGUiOiIzMTY3NTIiLCJjb252X2ZhY3RvciI6MSwiaXNfYmFzZV92YXJpYW50IjoiMSIsInVuaXQiOiJQY3MiLCJjdXJyZW50X3N0b2NrIjowfQ==', NULL, NULL, 0.00, 0.00, 750000.00, NULL, 0.00, 0.00, 1.0000, 1.0000, 0, 0.00, 0.00, 0.00, 18.00, 135000.00, 750000.00, '2026-10-24 18:52:00', '2026-10-24 18:52:00'),
(6, 0, 4, 1, 6, NULL, 'by_pieces', 'HF INDUCTION HEATER 40KW-AB', NULL, 'LTZ-40kw', NULL, 2, NULL, NULL, NULL, 'eyJuYW1lIjoiSEYgSU5EVUNUSU9OIEhFQVRFUiA0MEtXLUFCIiwic2l6ZSI6IjI0XC83IDEwMCUiLCJjb2xvciI6IldBVEVSLUNPT0xFRCIsInN0b2NrIjowLCJzYWxlX3ByaWNlIjoiNDUwMDAwIiwid2hvbGVzYWxlX3ByaWNlIjoiMCIsIndlaWdodF9wZXJfcGllY2UiOjAsInB1cmNoX3ByaWNlIjoiMCIsImFsZXJ0IjoiMCIsImJhcmNvZGUiOiIxODcwOTkiLCJjb252X2ZhY3RvciI6MSwiaXNfYmFzZV92YXJpYW50IjoiMSIsInVuaXQiOiJQY3MiLCJjdXJyZW50X3N0b2NrIjowfQ==', NULL, NULL, 0.00, 0.00, 450000.00, NULL, 0.00, 0.00, 1.0000, 1.0000, 0, 0.00, 11.11, 50000.00, 0.00, 0.00, 400000.00, '2026-10-26 08:39:14', '2026-10-26 08:39:14'),
(7, 0, 5, 1, 7, NULL, 'by_pieces', 'HF INDUCTION HEATER 60KW-AB', NULL, 'LTZ-60KW', NULL, 2, NULL, NULL, NULL, 'eyJuYW1lIjoiSEYgSU5EVUNUSU9OIEhFQVRFUiA2MEtXLUFCIiwic2l6ZSI6IjI0XC83IDEwMCUiLCJjb2xvciI6IldBVEVSLUNPT0xFRCIsInN0b2NrIjowLCJzYWxlX3ByaWNlIjoiNTgwMDAwIiwid2hvbGVzYWxlX3ByaWNlIjoiMCIsIndlaWdodF9wZXJfcGllY2UiOjAsInB1cmNoX3ByaWNlIjoiMCIsImFsZXJ0IjoiMCIsImJhcmNvZGUiOiI5NDM0MTIiLCJjb252X2ZhY3RvciI6MSwiaXNfYmFzZV92YXJpYW50IjoiMSIsInVuaXQiOiJQY3MiLCJjdXJyZW50X3N0b2NrIjowfQ==', NULL, NULL, 0.00, 0.00, 580000.00, NULL, 0.00, 0.00, 1.0000, 1.0000, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 580000.00, '2026-10-26 11:54:42', '2026-10-26 11:54:42'),
(8, 0, 6, 1, 7, NULL, 'by_pieces', 'HF INDUCTION HEATER 60KW-AB', NULL, 'LTZ-60KW-AB', NULL, 2, NULL, NULL, NULL, 'eyJuYW1lIjoiSEYgSU5EVUNUSU9OIEhFQVRFUiA2MEtXLUFCIiwic2l6ZSI6IjI0XC83IDEwMCUiLCJjb2xvciI6IldBVEVSLUNPT0xFRCIsInN0b2NrIjowLCJzYWxlX3ByaWNlIjoiNTgwMDAwIiwid2hvbGVzYWxlX3ByaWNlIjoiMCIsIndlaWdodF9wZXJfcGllY2UiOjAsInB1cmNoX3ByaWNlIjoiMCIsImFsZXJ0IjoiMCIsImJhcmNvZGUiOiI5NDM0MTIiLCJjb252X2ZhY3RvciI6MSwiaXNfYmFzZV92YXJpYW50IjoiMSIsInVuaXQiOiJQY3MiLCJjdXJyZW50X3N0b2NrIjowfQ==', NULL, NULL, 0.00, 0.00, 580000.00, NULL, 0.00, 0.00, 1.0000, 1.0000, 0, 0.00, 8.62, 50000.00, 0.00, 0.00, 530000.00, '2026-10-26 20:45:01', '2026-10-26 20:45:01'),
(9, 0, 7, 1, 7, NULL, 'by_pieces', 'HF INDUCTION HEATER 60KW-AB', NULL, 'LTZ-60KW-AB', NULL, 2, NULL, NULL, NULL, 'eyJuYW1lIjoiSEYgSU5EVUNUSU9OIEhFQVRFUiA2MEtXLUFCIiwic2l6ZSI6IjI0XC83IDEwMCUiLCJjb2xvciI6IldBVEVSLUNPT0xFRCIsInN0b2NrIjowLCJzYWxlX3ByaWNlIjoiNTgwMDAwIiwid2hvbGVzYWxlX3ByaWNlIjoiMCIsIndlaWdodF9wZXJfcGllY2UiOjAsInB1cmNoX3ByaWNlIjoiMCIsImFsZXJ0IjoiMCIsImJhcmNvZGUiOiI5NDM0MTIiLCJjb252X2ZhY3RvciI6MSwiaXNfYmFzZV92YXJpYW50IjoiMSIsInVuaXQiOiJQY3MiLCJjdXJyZW50X3N0b2NrIjowfQ==', NULL, NULL, 0.00, 0.00, 580000.00, NULL, 0.00, 0.00, 1.0000, 1.0000, 0, 0.00, 8.62, 50000.00, 0.00, 0.00, 530000.00, '2026-10-26 20:46:58', '2026-10-26 20:46:58'),
(10, 0, 8, 1, 8, NULL, 'by_pieces', 'HF INDUCTION HEATER 80KW-AB', NULL, 'LTZ-80KW-AB', NULL, 2, NULL, NULL, NULL, 'eyJuYW1lIjoiSEYgSU5EVUNUSU9OIEhFQVRFUiA4MEtXLUFCIiwic2l6ZSI6IjI0XC83IDEwMCUiLCJjb2xvciI6IldBVEVSLUNPT0xFRCIsInN0b2NrIjowLCJzYWxlX3ByaWNlIjoiNzUwMDAwIiwid2hvbGVzYWxlX3ByaWNlIjoiMCIsIndlaWdodF9wZXJfcGllY2UiOjAsInB1cmNoX3ByaWNlIjoiMCIsImFsZXJ0IjoiMCIsImJhcmNvZGUiOiIzMTY3NTIiLCJjb252X2ZhY3RvciI6MSwiaXNfYmFzZV92YXJpYW50IjoiMSIsInVuaXQiOiJQY3MiLCJjdXJyZW50X3N0b2NrIjowfQ==', NULL, NULL, 0.00, 0.00, 820000.00, NULL, 0.00, 0.00, 1.0000, 1.0000, 0, 0.00, 6.10, 50000.00, 0.00, 0.00, 770000.00, '2026-10-26 20:55:49', '2026-10-26 20:55:49'),
(12, 0, 9, 1, 23, NULL, 'by_pieces', 'Induction Coil', 'Induction Coil', NULL, NULL, 5, NULL, NULL, NULL, '-', 'ID 40MM AND ID 50MM', NULL, 0.00, 0.00, 5500.00, NULL, 0.00, 0.00, 2.0000, 2.0000, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 11000.00, '2026-10-28 06:53:14', '2026-10-28 18:14:59'),
(13, 0, 10, 1, 27, NULL, 'by_pieces', 'AIR-COOLED INDUCTION HEATER 80KW', NULL, '80KW', NULL, 2, NULL, NULL, NULL, 'eyJuYW1lIjoiQUlSLUNPT0xFRCBJTkRVQ1RJT04gSEVBVEVSIDgwS1ciLCJzaXplIjoiMjRcLzcgMTAwJSIsImNvbG9yIjoiQUlSIENPT0xFRCIsInN0b2NrIjowLCJzYWxlX3ByaWNlIjoiMCIsIndob2xlc2FsZV9wcmljZSI6IjAiLCJ3ZWlnaHRfcGVyX3BpZWNlIjowLCJwdXJjaF9wcmljZSI6IjAiLCJhbGVydCI6IjAiLCJiYXJjb2RlIjoiNzIzNTA1IiwiY29udl9mYWN0b3IiOjEsImlzX2Jhc2VfdmFyaWFudCI6IjEiLCJ1bml0IjoiUGNzIiwiY3VycmVudF9zdG9jayI6MH0=', NULL, NULL, 0.00, 0.00, 430000.00, NULL, 0.00, 0.00, 1.0000, 1.0000, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 430000.00, '2026-10-28 12:40:44', '2026-10-28 12:40:44'),
(14, 0, 10, 1, 28, NULL, 'by_pieces', 'Heat Proof Wire Coil', '50MM , 650 METER', 'Coil', NULL, 5, NULL, NULL, NULL, 'eyJuYW1lIjoiSGVhdCBQcm9vZiBXaXJlIENvaWwiLCJzaXplIjoiLSIsImNvbG9yIjoiLSIsInN0b2NrIjowLCJzYWxlX3ByaWNlIjoiMCIsIndob2xlc2FsZV9wcmljZSI6IjAiLCJ3ZWlnaHRfcGVyX3BpZWNlIjowLCJwdXJjaF9wcmljZSI6IjAiLCJhbGVydCI6IjAiLCJiYXJjb2RlIjoiNDU4NTAyIiwiY29udl9mYWN0b3IiOjEsImlzX2Jhc2VfdmFyaWFudCI6IjEiLCJ1bml0IjoiUGNzIiwiY3VycmVudF9zdG9jayI6MH0=', NULL, NULL, 0.00, 0.00, 430000.00, NULL, 0.00, 0.00, 1.0000, 1.0000, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 430000.00, '2026-10-28 12:40:44', '2026-10-28 12:40:44'),
(15, 0, 10, 1, 30, NULL, 'by_pieces', 'Insulation/Other Materials', 'Ceramic wool,glass tape,fabric, other accessoreis', 'other', NULL, 7, NULL, NULL, NULL, 'eyJuYW1lIjoiSW5zdWxhdGlvblwvT3RoZXIgTWF0ZXJpYWxzIiwic2l6ZSI6Ii0iLCJjb2xvciI6Ii0iLCJzdG9jayI6MCwic2FsZV9wcmljZSI6IjAiLCJ3aG9sZXNhbGVfcHJpY2UiOiIwIiwid2VpZ2h0X3Blcl9waWVjZSI6MCwicHVyY2hfcHJpY2UiOiIwIiwiYWxlcnQiOiIwIiwiYmFyY29kZSI6Ijg1MjI2OSIsImNvbnZfZmFjdG9yIjoxLCJpc19iYXNlX3ZhcmlhbnQiOiIxIiwidW5pdCI6IlBjcyIsImN1cnJlbnRfc3RvY2siOjB9', NULL, NULL, 0.00, 0.00, 60000.00, NULL, 0.00, 0.00, 1.0000, 1.0000, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 60000.00, '2026-10-28 12:40:44', '2026-10-28 12:40:44'),
(16, 0, 10, 1, 29, NULL, 'by_pieces', 'Installation Charges', NULL, 'Installation', NULL, 6, NULL, NULL, NULL, 'eyJuYW1lIjoiSW5zdGFsbGF0aW9uIENoYXJnZXMiLCJzaXplIjoiLSIsImNvbG9yIjoiLSIsInN0b2NrIjowLCJzYWxlX3ByaWNlIjoiMCIsIndob2xlc2FsZV9wcmljZSI6IjAiLCJ3ZWlnaHRfcGVyX3BpZWNlIjowLCJwdXJjaF9wcmljZSI6IjAiLCJhbGVydCI6IjAiLCJiYXJjb2RlIjoiMzYyNTM0IiwiY29udl9mYWN0b3IiOjEsImlzX2Jhc2VfdmFyaWFudCI6IjEiLCJ1bml0IjoiUGNzIiwiY3VycmVudF9zdG9jayI6MH0=', NULL, NULL, 0.00, 0.00, 30000.00, NULL, 0.00, 0.00, 1.0000, 1.0000, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 30000.00, '2026-10-28 12:40:44', '2026-10-28 12:40:44'),
(29, 0, 12, 1, 26, NULL, 'by_pieces', 'AIR-COOLED INDUCTION HEATER 60KW', 'AIR-COOLED INDUCTION HEATER 60KW', 'AIR COOLED', NULL, 2, NULL, NULL, NULL, 'AIR COOLED', NULL, NULL, 0.00, 0.00, 450000.00, NULL, 0.00, 0.00, 1.0000, 1.0000, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 450000.00, '2026-10-28 20:07:14', '2026-10-28 20:07:14'),
(30, 0, 12, 1, 28, NULL, 'by_pieces', 'Heat Proof Wire Coil', 'Heat Proof Wire Coil', 'Coil', NULL, 5, NULL, NULL, NULL, '-', NULL, NULL, 0.00, 0.00, 170000.00, NULL, 0.00, 0.00, 1.0000, 1.0000, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 170000.00, '2026-10-28 20:07:14', '2026-10-28 20:07:14'),
(31, 0, 12, 1, 29, NULL, 'by_pieces', 'Installation Charges', 'Installation Charges', 'Installation', NULL, 6, NULL, NULL, NULL, '-', NULL, NULL, 0.00, 0.00, 20000.00, NULL, 0.00, 0.00, 1.0000, 1.0000, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 20000.00, '2026-10-28 20:07:14', '2026-10-28 20:07:14'),
(38, 0, 11, 1, 23, NULL, 'by_pieces', 'Induction Coil', 'for LTZ-80KW machine for LTZ-80KW machine for LTZ-80KW machine for LTZ-80KW machine', NULL, NULL, 5, NULL, NULL, NULL, '-', '9\" * 12\"', NULL, 0.00, 0.00, 19000.00, NULL, 0.00, 0.00, 1.0000, 1.0000, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 19000.00, '2026-10-30 08:39:26', '2026-10-30 08:39:26'),
(39, 0, 11, 1, 23, NULL, 'by_pieces', 'Induction Coil', 'for LTZ-80kw machine', NULL, NULL, 5, NULL, NULL, NULL, '-', '14\" * 3\"', NULL, 0.00, 0.00, 18500.00, NULL, 0.00, 0.00, 1.0000, 1.0000, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 18500.00, '2026-10-30 08:39:26', '2026-10-30 08:39:26'),
(40, 0, 11, 1, 23, NULL, 'by_pieces', 'Induction Coil', 'for 60kw machine (china)', NULL, NULL, 5, NULL, NULL, NULL, '-', '9\" * 3\"', NULL, 0.00, 0.00, 15000.00, NULL, 0.00, 0.00, 1.0000, 1.0000, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 15000.00, '2026-10-30 08:39:26', '2026-10-30 08:39:26'),
(47, 0, 13, 1, 24, NULL, 'by_pieces', 'AIR-COOLED INDUCTION HEATER 25KW', 'Portable Induction Heater, with Manual Arm Adjustment ,', '25kw', NULL, 2, NULL, NULL, NULL, 'eyJuYW1lIjoiQUlSLUNPT0xFRCBJTkRVQ1RJT04gSEVBVEVSIDI1S1ciLCJzaXplIjoiMjRcLzcgMTAwJSIsImNvbG9yIjoiQUlSIENPT0xFRCIsInN0b2NrIjowLCJzYWxlX3ByaWNlIjoiMCIsIndob2xlc2FsZV9wcmljZSI6IjAiLCJ3ZWlnaHRfcGVyX3BpZWNlIjowLCJwdXJjaF9wcmljZSI6IjAiLCJhbGVydCI6IjAiLCJiYXJjb2RlIjoiNjM4MTYzIiwiY29udl9mYWN0b3IiOjEsImlzX2Jhc2VfdmFyaWFudCI6IjEiLCJ1bml0IjoiUGNzIiwiY3VycmVudF9zdG9jayI6MH0=', NULL, NULL, 0.00, 0.00, 900000.00, NULL, 0.00, 0.00, 1.0000, 1.0000, 0, 0.00, 0.00, 0.00, 18.00, 162000.00, 900000.00, '2026-10-30 19:04:47', '2026-10-30 19:04:47'),
(48, 0, 13, 1, 23, NULL, 'by_pieces', 'Induction Coil', 'Interchangable Coils, Sizes: 1 Ft x 1 Ft', 'Air Cooled', NULL, NULL, NULL, NULL, NULL, 'eyJuYW1lIjoiSW5kdWN0aW9uIENvaWwiLCJzaXplIjoiLSIsImNvbG9yIjoiLSIsInN0b2NrIjowLCJzYWxlX3ByaWNlIjoiMCIsIndob2xlc2FsZV9wcmljZSI6IjAiLCJ3ZWlnaHRfcGVyX3BpZWNlIjowLCJwdXJjaF9wcmljZSI6IjAiLCJhbGVydCI6IjAiLCJiYXJjb2RlIjoiMjYwODg3IiwiY29udl9mYWN0b3IiOjEsImlzX2Jhc2VfdmFyaWFudCI6IjEiLCJ1bml0IjoiUGNzIiwiY3VycmVudF9zdG9jayI6MH0=', NULL, NULL, 0.00, 0.00, 160000.00, NULL, 0.00, 0.00, 1.0000, 1.0000, 0, 0.00, 0.00, 0.00, 18.00, 28800.00, 160000.00, '2026-10-30 19:04:47', '2026-10-30 19:04:47'),
(49, 0, 13, 1, 23, NULL, 'by_pieces', 'Induction Coil', 'Interchangable Coils, Sizes: 1 Ft x 3 Ft', 'Air Cooled', NULL, NULL, NULL, NULL, NULL, 'eyJuYW1lIjoiSW5kdWN0aW9uIENvaWwiLCJzaXplIjoiLSIsImNvbG9yIjoiLSIsInN0b2NrIjowLCJzYWxlX3ByaWNlIjoiMCIsIndob2xlc2FsZV9wcmljZSI6IjAiLCJ3ZWlnaHRfcGVyX3BpZWNlIjowLCJwdXJjaF9wcmljZSI6IjAiLCJhbGVydCI6IjAiLCJiYXJjb2RlIjoiMjYwODg3IiwiY29udl9mYWN0b3IiOjEsImlzX2Jhc2VfdmFyaWFudCI6IjEiLCJ1bml0IjoiUGNzIiwiY3VycmVudF9zdG9jayI6MH0=', NULL, NULL, 0.00, 0.00, 200000.00, NULL, 0.00, 0.00, 1.0000, 1.0000, 0, 0.00, 0.00, 0.00, 18.00, 36000.00, 200000.00, '2026-10-30 19:04:47', '2026-10-30 19:04:47'),
(58, 0, 15, 1, 7, NULL, 'by_pieces', 'HF INDUCTION HEATER 60KW-AB', NULL, 'LTZ-60kw', NULL, 2, NULL, NULL, NULL, 'eyJuYW1lIjoiSEYgSU5EVUNUSU9OIEhFQVRFUiA2MEtXLUFCIiwic2l6ZSI6IjI0XC83IDEwMCUiLCJjb2xvciI6IldBVEVSLUNPT0xFRCIsInN0b2NrIjowLCJzYWxlX3ByaWNlIjoiNTgwMDAwIiwid2hvbGVzYWxlX3ByaWNlIjoiMCIsIndlaWdodF9wZXJfcGllY2UiOjAsInB1cmNoX3ByaWNlIjoiMCIsImFsZXJ0IjoiMCIsImJhcmNvZGUiOiI5NDM0MTIiLCJjb252X2ZhY3RvciI6MSwiaXNfYmFzZV92YXJpYW50IjoiMSIsInVuaXQiOiJQY3MiLCJjdXJyZW50X3N0b2NrIjowfQ==', NULL, NULL, 0.00, 0.00, 580000.00, NULL, 0.00, 0.00, 1.0000, 1.0000, 0, 0.00, 5.17, 30000.00, 0.00, 0.00, 550000.00, '2026-10-31 09:41:20', '2026-10-31 09:41:20'),
(59, 0, 14, 1, 2, NULL, 'by_pieces', 'HF INDUCTION HEATER 18KW', 'Test Equipment Title , \r\nTest Equipment Title , \r\nTest Equipment Title , \r\nTest Equipment Title ,', 'LTZ', NULL, 1, NULL, NULL, NULL, 'eyJuYW1lIjoiSEYgSU5EVUNUSU9OIEhFQVRFUiAxOEtXIiwic2l6ZSI6IjI0XC83IDEwMCUiLCJjb2xvciI6IldBVEVSLUNPT0xFRCIsInN0b2NrIjowLCJzYWxlX3ByaWNlIjoiMTkwMDAwIiwid2hvbGVzYWxlX3ByaWNlIjoiMCIsIndlaWdodF9wZXJfcGllY2UiOjAsInB1cmNoX3ByaWNlIjoiMCIsImFsZXJ0IjoiMCIsImJhcmNvZGUiOiI1NjkzNTMiLCJjb252X2ZhY3RvciI6MSwiaXNfYmFzZV92YXJpYW50IjoiMSIsInVuaXQiOiJQY3MiLCJjdXJyZW50X3N0b2NrIjowfQ==', 'Test Technical specifications\r\nTest Technical specifications\r\nTest Technical specifications\r\nTest Technical specifications', NULL, 0.00, 0.00, 2.00, NULL, 0.00, 0.00, 1.0000, 1.0000, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 2.00, '2026-10-31 11:26:45', '2026-10-31 11:26:45');

-- --------------------------------------------------------

--
-- Table structure for table `sale_returns`
--

CREATE TABLE `sale_returns` (
  `id` bigint UNSIGNED NOT NULL,
  `sale_id` bigint UNSIGNED DEFAULT NULL,
  `return_invoice` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` bigint UNSIGNED DEFAULT NULL,
  `warehouse_id` bigint UNSIGNED NOT NULL,
  `return_date` date NOT NULL,
  `bill_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `item_discount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `extra_discount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `net_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `due_adjusted` decimal(15,2) NOT NULL DEFAULT '0.00',
  `refundable_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `paid` decimal(15,2) NOT NULL DEFAULT '0.00',
  `balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'posted',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sale_return_items`
--

CREATE TABLE `sale_return_items` (
  `id` bigint UNSIGNED NOT NULL,
  `sale_return_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED DEFAULT NULL,
  `is_manual` tinyint(1) NOT NULL DEFAULT '0',
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vendor_id` bigint UNSIGNED DEFAULT NULL,
  `purchase_price` decimal(15,2) DEFAULT NULL,
  `color` text COLLATE utf8mb4_unicode_ci,
  `warehouse_id` bigint UNSIGNED NOT NULL,
  `qty` decimal(15,2) NOT NULL COMMENT 'Total pieces returned',
  `boxes` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Box quantity (can be decimal like 1.2)',
  `loose_pieces` int NOT NULL DEFAULT '0' COMMENT 'Loose pieces',
  `price` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Price per piece',
  `item_discount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pc',
  `line_total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint UNSIGNED NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'string',
  `group` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`, `type`, `group`, `label`, `description`, `created_at`, `updated_at`) VALUES
(1, 'company_logo', 'uploads/settings/company_logo_1791976860.jpg', 'image', 'company', 'Company Logo', 'Logo displayed at the top of receipts and invoices', '2026-10-14 11:21:00', '2026-10-14 11:21:00'),
(2, 'web_site_logo', 'uploads/settings/company_logo_1791976860.jpg', 'string', 'website', 'Site Logo', 'Website Logo', '2026-10-14 11:21:00', '2026-10-14 11:21:00'),
(3, 'company_stamp', 'uploads/settings/company_stamp_1792241792.jpeg', 'image', 'company', 'Company Stamp', 'Official digital company stamp', '2026-10-17 12:55:43', '2026-10-17 12:56:32'),
(4, 'company_name', 'Logic Tech Engineering', 'string', 'company', 'Company Name', NULL, '2026-10-19 18:24:08', '2026-10-19 18:34:37'),
(5, 'company_phone', '92 336 1500082', 'string', 'company', 'Company Phone', NULL, '2026-10-19 18:24:08', '2026-10-19 18:24:08'),
(6, 'company_address', '01-km Sharaqpur Road Sheikhupura, Punjab, Pakistan', 'string', 'company', 'Company Address', NULL, '2026-10-19 18:24:08', '2026-10-19 18:24:08'),
(7, 'company_email', 'info@logictech.com.pk', 'string', 'company', 'Company Email', NULL, '2026-10-19 18:24:08', '2026-10-19 18:24:08'),
(8, 'company_ntn', '5561761-4', 'string', 'company', 'Company Ntn', NULL, '2026-10-19 18:24:08', '2026-10-19 18:24:08'),
(9, 'company_strn', '3277876171582', 'string', 'company', 'Company Strn', NULL, '2026-10-19 18:24:08', '2026-10-19 18:24:08'),
(10, 'currency_symbol', 'PKR', 'string', 'company', 'Currency Symbol', NULL, '2026-10-19 18:24:08', '2026-10-19 18:24:08'),
(11, 'web_contact_address', '01-km Sharaqpur Road Sheikhupura, Punjab, Pakistan', 'string', 'website', 'Web Contact Address', NULL, '2026-10-19 18:24:08', '2026-10-19 18:24:08'),
(12, 'web_site_name', 'Logic Tech Engineering', 'string', 'website', 'Web Site Name', NULL, '2026-10-19 18:24:08', '2026-10-19 18:34:37'),
(13, 'web_contact_phone', '92 336 1500082', 'string', 'website', 'Web Contact Phone', NULL, '2026-10-19 18:24:08', '2026-10-19 18:24:08'),
(14, 'web_contact_email', 'info@logictech.com.pk', 'string', 'website', 'Web Contact Email', NULL, '2026-10-19 18:24:08', '2026-10-19 18:24:08');

-- --------------------------------------------------------

--
-- Table structure for table `stocks`
--

CREATE TABLE `stocks` (
  `id` bigint UNSIGNED NOT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `warehouse_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `qty` int NOT NULL DEFAULT '0',
  `reserved_qty` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_adjustments`
--

CREATE TABLE `stock_adjustments` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `warehouse_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `variant_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `variant_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('add','subtract','set') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'add',
  `qty` decimal(12,2) NOT NULL DEFAULT '0.00',
  `old_stock` decimal(12,2) NOT NULL DEFAULT '0.00',
  `new_stock` decimal(12,2) NOT NULL DEFAULT '0.00',
  `reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_movements`
--

CREATE TABLE `stock_movements` (
  `id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `type` enum('in','out','assembly_in','assembly_out','adjustment') COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty` decimal(12,3) NOT NULL,
  `ref_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ref_id` bigint UNSIGNED DEFAULT NULL,
  `ref_uuid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_auto_pluck` tinyint NOT NULL DEFAULT '0',
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stock_movements`
--

INSERT INTO `stock_movements` (`id`, `product_id`, `type`, `qty`, `ref_type`, `ref_id`, `ref_uuid`, `is_auto_pluck`, `note`, `created_at`, `updated_at`) VALUES
(1, 7, 'out', -1.000, 'sale', 1, NULL, 0, 'Sale Posted #INV-0001', '2026-10-17 12:44:49', '2026-10-17 12:44:49'),
(2, 13, 'out', -1.000, 'sale', 1, NULL, 0, 'Sale Posted #INV-0001', '2026-10-17 12:44:49', '2026-10-17 12:44:49'),
(7, 21, 'adjustment', 18.000, 'INIT', NULL, NULL, 0, 'Initial Stock', '2026-10-19 06:45:57', '2026-10-19 06:45:57'),
(9, 14, 'out', 2.000, 'MATERIAL_USAGE', 1, NULL, 0, 'Usage #MU-0001 | Purpose: Production Consumption', '2026-10-19 06:59:29', '2026-10-19 06:59:29'),
(10, 15, 'out', 4.000, 'MATERIAL_USAGE', 1, NULL, 0, 'Usage #MU-0001 | Purpose: Production Consumption', '2026-10-19 06:59:29', '2026-10-19 06:59:29'),
(11, 16, 'out', 2.000, 'MATERIAL_USAGE', 1, NULL, 0, 'Usage #MU-0001 | Purpose: Production Consumption', '2026-10-19 06:59:29', '2026-10-19 06:59:29'),
(12, 18, 'out', 3.000, 'MATERIAL_USAGE', 1, NULL, 0, 'Usage #MU-0001 | Purpose: Production Consumption', '2026-10-19 06:59:29', '2026-10-19 06:59:29'),
(13, 22, 'adjustment', 8.000, 'INIT', NULL, NULL, 0, 'Initial Stock', '2026-10-19 08:39:43', '2026-10-19 08:39:43'),
(20, 14, 'out', 5.000, 'MATERIAL_USAGE', 3, NULL, 0, 'Usage #MU-0003 | Purpose: Production Consumption', '2026-10-21 05:47:18', '2026-10-21 05:47:18'),
(21, 15, 'out', 7.000, 'MATERIAL_USAGE', 3, NULL, 0, 'Usage #MU-0003 | Purpose: Production Consumption', '2026-10-21 05:47:18', '2026-10-21 05:47:18'),
(22, 16, 'out', 5.000, 'MATERIAL_USAGE', 3, NULL, 0, 'Usage #MU-0003 | Purpose: Production Consumption', '2026-10-21 05:47:18', '2026-10-21 05:47:18'),
(23, 17, 'out', 3.000, 'MATERIAL_USAGE', 3, NULL, 0, 'Usage #MU-0003 | Purpose: Production Consumption', '2026-10-21 05:47:18', '2026-10-21 05:47:18'),
(24, 21, 'out', 2.000, 'MATERIAL_USAGE', 3, NULL, 0, 'Usage #MU-0003 | Purpose: Production Consumption', '2026-10-21 05:47:18', '2026-10-21 05:47:18'),
(25, 18, 'out', 3.000, 'MATERIAL_USAGE', 3, NULL, 0, 'Usage #MU-0003 | Purpose: Production Consumption', '2026-10-21 05:47:18', '2026-10-21 05:47:18'),
(26, 15, 'out', 4.000, 'MATERIAL_USAGE', 4, NULL, 0, 'Usage #MU-0004 | Purpose: Production Consumption', '2026-10-21 07:20:18', '2026-10-21 07:20:18'),
(27, 14, 'out', 1.000, 'MATERIAL_USAGE', 2, NULL, 0, 'Usage #MU-0002 (Edited) | Purpose: Production Consumption', '2026-10-21 12:35:52', '2026-10-21 12:35:52'),
(28, 16, 'out', 1.000, 'MATERIAL_USAGE', 2, NULL, 0, 'Usage #MU-0002 (Edited) | Purpose: Production Consumption', '2026-10-21 12:35:52', '2026-10-21 12:35:52'),
(29, 15, 'out', 4.000, 'MATERIAL_USAGE', 2, NULL, 0, 'Usage #MU-0002 (Edited) | Purpose: Production Consumption', '2026-10-21 12:35:52', '2026-10-21 12:35:52'),
(30, 17, 'out', 2.000, 'MATERIAL_USAGE', 2, NULL, 0, 'Usage #MU-0002 (Edited) | Purpose: Production Consumption', '2026-10-21 12:35:52', '2026-10-21 12:35:52'),
(31, 18, 'out', 1.000, 'MATERIAL_USAGE', 2, NULL, 0, 'Usage #MU-0002 (Edited) | Purpose: Production Consumption', '2026-10-21 12:35:52', '2026-10-21 12:35:52'),
(32, 21, 'out', 1.000, 'MATERIAL_USAGE', 2, NULL, 0, 'Usage #MU-0002 (Edited) | Purpose: Production Consumption', '2026-10-21 12:35:52', '2026-10-21 12:35:52'),
(33, 15, 'in', 4.000, 'MATERIAL_USAGE_REVERSAL', 4, NULL, 0, 'Reversal of Material Usage #MU-0004', '2026-10-21 12:49:14', '2026-10-21 12:49:14'),
(34, 17, 'in', 6.000, 'PURCHASE', 2, NULL, 0, 'Purchase Confirmed', '2026-10-22 06:47:25', '2026-10-22 06:47:25'),
(35, 17, 'out', 1.000, 'MATERIAL_USAGE', 5, NULL, 0, 'Usage #MU-0004 | Purpose: Production Consumption', '2026-10-23 11:30:29', '2026-10-23 11:30:29'),
(36, 17, 'in', 1.000, 'MATERIAL_USAGE_REVERSAL', 5, NULL, 0, 'Reversal of Material Usage #MU-0004', '2026-10-23 12:01:19', '2026-10-23 12:01:19'),
(37, 14, 'in', 10.000, 'PURCHASE', 1, NULL, 0, 'Purchase Confirmed', '2026-10-23 12:04:01', '2026-10-23 12:04:01'),
(38, 15, 'in', 20.000, 'PURCHASE', 1, NULL, 0, 'Purchase Confirmed', '2026-10-23 12:04:01', '2026-10-23 12:04:01'),
(39, 16, 'in', 10.000, 'PURCHASE', 1, NULL, 0, 'Purchase Confirmed', '2026-10-23 12:04:01', '2026-10-23 12:04:01'),
(40, 18, 'in', 20.000, 'PURCHASE', 1, NULL, 0, 'Purchase Confirmed', '2026-10-23 12:04:01', '2026-10-23 12:04:01'),
(41, 14, 'out', 2.000, 'MATERIAL_USAGE', 6, NULL, 0, 'Usage #MU-0004 | Purpose: Production Consumption', '2026-10-24 06:29:06', '2026-10-24 06:29:06'),
(42, 15, 'out', 2.000, 'MATERIAL_USAGE', 6, NULL, 0, 'Usage #MU-0004 | Purpose: Production Consumption', '2026-10-24 06:29:06', '2026-10-24 06:29:06'),
(43, 16, 'out', 2.000, 'MATERIAL_USAGE', 6, NULL, 0, 'Usage #MU-0004 | Purpose: Production Consumption', '2026-10-24 06:29:06', '2026-10-24 06:29:06'),
(44, 17, 'out', 1.000, 'MATERIAL_USAGE', 6, NULL, 0, 'Usage #MU-0004 | Purpose: Production Consumption', '2026-10-24 06:29:06', '2026-10-24 06:29:06'),
(45, 21, 'out', 1.000, 'MATERIAL_USAGE', 6, NULL, 0, 'Usage #MU-0004 | Purpose: Production Consumption', '2026-10-24 06:29:06', '2026-10-24 06:29:06'),
(46, 18, 'out', 1.000, 'MATERIAL_USAGE', 6, NULL, 0, 'Usage #MU-0004 | Purpose: Production Consumption', '2026-10-24 06:29:06', '2026-10-24 06:29:06'),
(47, 7, 'out', -1.000, 'sale', 2, NULL, 0, 'Sale Posted #INV-0002', '2026-10-24 10:06:26', '2026-10-24 10:06:26'),
(48, 3, 'out', -1.000, 'sale', 3, NULL, 0, 'Sale Posted #INV-0003', '2026-10-24 18:52:36', '2026-10-24 18:52:36'),
(49, 8, 'out', -1.000, 'sale', 3, NULL, 0, 'Sale Posted #INV-0003', '2026-10-24 18:52:36', '2026-10-24 18:52:36'),
(50, 6, 'out', -1.000, 'sale', 4, NULL, 0, 'Sale Posted #INV-0004', '2026-10-26 08:40:14', '2026-10-26 08:40:14'),
(51, 7, 'out', -1.000, 'sale', 5, NULL, 0, 'Sale Posted #INV-0005', '2026-10-26 12:00:04', '2026-10-26 12:00:04'),
(52, 7, 'out', -1.000, 'sale', 6, NULL, 0, 'Sale Posted #INV-0006', '2026-10-26 20:45:12', '2026-10-26 20:45:12'),
(53, 7, 'out', -1.000, 'sale', 7, NULL, 0, 'Sale Posted #INV-0007', '2026-10-26 20:47:12', '2026-10-26 20:47:12'),
(54, 8, 'out', -1.000, 'sale', 8, NULL, 0, 'Sale Posted #INV-0008', '2026-10-26 20:56:29', '2026-10-26 20:56:29'),
(55, 15, 'out', 3.000, 'MATERIAL_USAGE', 7, NULL, 0, 'Usage #MU-0007 | Purpose: Production Consumption', '2026-10-27 09:16:59', '2026-10-27 09:16:59'),
(56, 18, 'out', 1.000, 'MATERIAL_USAGE', 7, NULL, 0, 'Usage #MU-0007 | Purpose: Production Consumption', '2026-10-27 09:16:59', '2026-10-27 09:16:59'),
(58, 23, 'in', 2.000, 'sale_in', 9, NULL, 0, 'Sale In #INV-0009', '2026-10-28 06:53:14', '2026-10-28 06:53:14'),
(59, 23, 'out', -2.000, 'sale', 9, NULL, 0, 'Sale Posted #INV-0009', '2026-10-28 06:53:47', '2026-10-28 06:53:47'),
(60, 27, 'out', -1.000, 'sale', 10, NULL, 0, 'Sale Posted #INV-0010', '2026-10-28 12:41:09', '2026-10-28 12:41:09'),
(61, 28, 'out', -1.000, 'sale', 10, NULL, 0, 'Sale Posted #INV-0010', '2026-10-28 12:41:09', '2026-10-28 12:41:09'),
(62, 30, 'out', -1.000, 'sale', 10, NULL, 0, 'Sale Posted #INV-0010', '2026-10-28 12:41:09', '2026-10-28 12:41:09'),
(63, 29, 'out', -1.000, 'sale', 10, NULL, 0, 'Sale Posted #INV-0010', '2026-10-28 12:41:09', '2026-10-28 12:41:09'),
(67, 23, 'in', 1.000, 'sale_in', 11, NULL, 0, 'Sale In #INV-0011', '2026-10-28 19:39:51', '2026-10-28 19:39:51'),
(68, 23, 'in', 1.000, 'sale_in', 11, NULL, 0, 'Sale In #INV-0011', '2026-10-28 19:39:51', '2026-10-28 19:39:51'),
(69, 23, 'in', 1.000, 'sale_in', 11, NULL, 0, 'Sale In #INV-0011', '2026-10-28 19:39:51', '2026-10-28 19:39:51'),
(73, 23, 'in', 1.000, 'sale_in', 11, NULL, 0, 'Sale In #INV-0011', '2026-10-28 19:43:18', '2026-10-28 19:43:18'),
(74, 23, 'in', 1.000, 'sale_in', 11, NULL, 0, 'Sale In #INV-0011', '2026-10-28 19:43:18', '2026-10-28 19:43:18'),
(75, 23, 'in', 1.000, 'sale_in', 11, NULL, 0, 'Sale In #INV-0011', '2026-10-28 19:43:18', '2026-10-28 19:43:18'),
(82, 26, 'in', 1.000, 'sale_in', 12, NULL, 0, 'Sale In #INV-0012', '2026-10-28 20:07:14', '2026-10-28 20:07:14'),
(83, 28, 'in', 1.000, 'sale_in', 12, NULL, 0, 'Sale In #INV-0012', '2026-10-28 20:07:14', '2026-10-28 20:07:14'),
(84, 29, 'in', 1.000, 'sale_in', 12, NULL, 0, 'Sale In #INV-0012', '2026-10-28 20:07:14', '2026-10-28 20:07:14'),
(85, 26, 'out', -1.000, 'sale', 12, NULL, 0, 'Sale Posted #INV-0012', '2026-10-28 20:07:20', '2026-10-28 20:07:20'),
(86, 28, 'out', -1.000, 'sale', 12, NULL, 0, 'Sale Posted #INV-0012', '2026-10-28 20:07:20', '2026-10-28 20:07:20'),
(87, 29, 'out', -1.000, 'sale', 12, NULL, 0, 'Sale Posted #INV-0012', '2026-10-28 20:07:20', '2026-10-28 20:07:20'),
(88, 23, 'in', 1.000, 'sale_in', 11, NULL, 0, 'Sale In #INV-0011', '2026-10-30 07:22:28', '2026-10-30 07:22:28'),
(89, 23, 'in', 1.000, 'sale_in', 11, NULL, 0, 'Sale In #INV-0011', '2026-10-30 07:22:28', '2026-10-30 07:22:28'),
(90, 23, 'in', 1.000, 'sale_in', 11, NULL, 0, 'Sale In #INV-0011', '2026-10-30 07:22:28', '2026-10-30 07:22:28'),
(94, 23, 'in', 1.000, 'sale_in', 11, NULL, 0, 'Sale In #INV-0011', '2026-10-30 07:22:30', '2026-10-30 07:22:30'),
(95, 23, 'in', 1.000, 'sale_in', 11, NULL, 0, 'Sale In #INV-0011', '2026-10-30 07:22:30', '2026-10-30 07:22:30'),
(96, 23, 'in', 1.000, 'sale_in', 11, NULL, 0, 'Sale In #INV-0011', '2026-10-30 07:22:30', '2026-10-30 07:22:30'),
(100, 23, 'in', 1.000, 'sale_in', 11, NULL, 0, 'Sale In #INV-0011', '2026-10-30 08:39:26', '2026-10-30 08:39:26'),
(101, 23, 'in', 1.000, 'sale_in', 11, NULL, 0, 'Sale In #INV-0011', '2026-10-30 08:39:26', '2026-10-30 08:39:26'),
(102, 23, 'in', 1.000, 'sale_in', 11, NULL, 0, 'Sale In #INV-0011', '2026-10-30 08:39:26', '2026-10-30 08:39:26'),
(106, 23, 'out', -1.000, 'sale', 11, NULL, 0, 'Sale Posted #INV-0011', '2026-10-30 12:23:27', '2026-10-30 12:23:27'),
(107, 23, 'out', -1.000, 'sale', 11, NULL, 0, 'Sale Posted #INV-0011', '2026-10-30 12:23:27', '2026-10-30 12:23:27'),
(108, 23, 'out', -1.000, 'sale', 11, NULL, 0, 'Sale Posted #INV-0011', '2026-10-30 12:23:27', '2026-10-30 12:23:27'),
(109, 24, 'in', 1.000, 'sale_in', 13, NULL, 0, 'Sale In #INV-0013', '2026-10-30 19:02:29', '2026-10-30 19:02:29'),
(110, 23, 'in', 1.000, 'sale_in', 13, NULL, 0, 'Sale In #INV-0013', '2026-10-30 19:02:29', '2026-10-30 19:02:29'),
(111, 23, 'in', 1.000, 'sale_in', 13, NULL, 0, 'Sale In #INV-0013', '2026-10-30 19:02:29', '2026-10-30 19:02:29'),
(115, 24, 'in', 1.000, 'sale_in', 13, NULL, 0, 'Sale In #INV-0013', '2026-10-30 19:04:47', '2026-10-30 19:04:47'),
(116, 23, 'in', 1.000, 'sale_in', 13, NULL, 0, 'Sale In #INV-0013', '2026-10-30 19:04:47', '2026-10-30 19:04:47'),
(117, 23, 'in', 1.000, 'sale_in', 13, NULL, 0, 'Sale In #INV-0013', '2026-10-30 19:04:47', '2026-10-30 19:04:47'),
(118, 24, 'out', -1.000, 'sale', 13, NULL, 0, 'Sale Posted #INV-0013', '2026-10-30 19:05:03', '2026-10-30 19:05:03'),
(119, 23, 'out', -1.000, 'sale', 13, NULL, 0, 'Sale Posted #INV-0013', '2026-10-30 19:05:03', '2026-10-30 19:05:03'),
(120, 23, 'out', -1.000, 'sale', 13, NULL, 0, 'Sale Posted #INV-0013', '2026-10-30 19:05:03', '2026-10-30 19:05:03'),
(122, 2, 'in', 1.000, 'sale_in', 14, NULL, 0, 'Sale In #INV-0014', '2026-10-30 19:11:57', '2026-10-30 19:11:57'),
(123, 2, 'in', 1.000, 'sale_in', 14, NULL, 0, 'Sale In #INV-0014', '2026-10-30 19:56:29', '2026-10-30 19:56:29'),
(125, 2, 'in', 1.000, 'sale_in', 14, NULL, 0, 'Sale In #INV-0014', '2026-10-30 19:56:47', '2026-10-30 19:56:47'),
(127, 2, 'in', 2.000, 'sale_in', 14, NULL, 0, 'Sale In #INV-0014', '2026-10-30 19:59:02', '2026-10-30 19:59:02'),
(129, 2, 'in', 2.000, 'sale_in', 14, NULL, 0, 'Sale In #INV-0014', '2026-10-30 20:02:20', '2026-10-30 20:02:20'),
(131, 7, 'in', 1.000, 'sale_in', 15, NULL, 0, 'Sale In #INV-0015', '2026-10-31 09:35:50', '2026-10-31 09:35:50'),
(133, 7, 'in', 1.000, 'sale_in', 15, NULL, 0, 'Sale In #INV-0015', '2026-10-31 09:38:03', '2026-10-31 09:38:03'),
(135, 7, 'in', 1.000, 'sale_in', 15, NULL, 0, 'Sale In #INV-0015', '2026-10-31 09:40:56', '2026-10-31 09:40:56'),
(137, 7, 'in', 1.000, 'sale_in', 15, NULL, 0, 'Sale In #INV-0015', '2026-10-31 09:41:20', '2026-10-31 09:41:20'),
(138, 7, 'out', -1.000, 'sale', 15, NULL, 0, 'Sale Posted #INV-0015', '2026-10-31 09:41:20', '2026-10-31 09:41:20'),
(139, 2, 'in', 1.000, 'sale_in', 14, NULL, 0, 'Sale In #INV-0014', '2026-10-31 11:26:45', '2026-10-31 11:26:45'),
(140, 2, 'out', -1.000, 'sale', 14, NULL, 0, 'Sale Posted #INV-0014', '2026-10-31 11:26:45', '2026-10-31 11:26:45');

-- --------------------------------------------------------

--
-- Table structure for table `stock_transfers`
--

CREATE TABLE `stock_transfers` (
  `id` bigint UNSIGNED NOT NULL,
  `from_warehouse_id` bigint UNSIGNED NOT NULL,
  `to_warehouse_id` bigint UNSIGNED DEFAULT NULL,
  `to_shop` tinyint(1) NOT NULL DEFAULT '0',
  `product_id` bigint UNSIGNED NOT NULL,
  `quantity` int NOT NULL DEFAULT '0',
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subcategories`
--

CREATE TABLE `subcategories` (
  `id` bigint UNSIGNED NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subcategories`
--

INSERT INTO `subcategories` (`id`, `name`, `category_id`, `created_at`, `updated_at`) VALUES
(1, 'Water-Cooled', 1, '2026-10-17 11:46:06', '2026-10-17 11:46:06'),
(2, 'Air-Cooled', 1, '2026-10-17 11:46:35', '2026-10-17 11:46:35'),
(3, 'Manual', 1, '2026-10-17 12:35:52', '2026-10-17 12:35:52'),
(4, 'R&D', 1, '2026-10-19 06:43:02', '2026-10-19 06:43:02'),
(5, 'Copper Coil', 2, '2026-10-28 06:45:26', '2026-10-28 06:45:26');

-- --------------------------------------------------------

--
-- Table structure for table `system_notifications`
--

CREATE TABLE `system_notifications` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'info',
  `source_id` bigint UNSIGNED DEFAULT NULL,
  `source_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` bigint UNSIGNED NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'string',
  `group` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transports`
--

CREATE TABLE `transports` (
  `id` bigint UNSIGNED NOT NULL,
  `admin_or_user_id` bigint UNSIGNED DEFAULT NULL,
  `company_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_ur` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `address_ur` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `id` bigint UNSIGNED NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `can_approve_returns` tinyint(1) NOT NULL DEFAULT '0',
  `can_approve_past_deadline_returns` tinyint(1) NOT NULL DEFAULT '0',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `usertype` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'admin',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `can_approve_returns`, `can_approve_past_deadline_returns`, `email_verified_at`, `usertype`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'superadmin@example.com', 1, 1, NULL, 'admin', '$2y$10$ymQ.18r4yQ5yioFWOWf3S.f9T6moWgBwj.ecWnm4g25.qp5v0ZuYG', 'Q4nliQRZ5lWo9e1EWQNsvYCBo5hG1uvXKL5dTVmfUT7XxsEV6XwgNSXJ8ZJz', '2026-09-11 10:46:24', '2026-09-18 11:39:12'),
(2, 'Branch Manager Admin', 'admin@admin.com', 0, 0, NULL, 'admin', '$2y$10$dt8T7tpUddqFHDxwYexs5.W8YWYFqy4IMzpSiydZqTNtfSbitBCJG', 's83aJqyfidZiz1mCtOXv4owGKH9La38JsSTd4po0b3SyPVQcnYOYBFLbEwN3', '2026-09-11 10:53:05', '2026-09-11 10:53:05'),
(3, 'Waleed', 'waleed@admin.com', 0, 0, NULL, 'admin', '$2y$10$zoXKDAOLC7TmES1uoQEv0u40N123jxfD7xov.2CL5M0G1kS4PzvVK', NULL, '2026-09-19 00:20:09', '2026-09-19 00:20:09'),
(24, 'Qamar', 'qamar_1790352396_585@logictech.local', 0, 0, NULL, 'admin', '$2y$10$02bAUvO/8gw8DCXB/0DjhehPw18mP2it2K5PQuZCEpUTTwgyKolVW', NULL, '2026-09-25 16:06:37', '2026-09-29 01:48:59'),
(25, 'Umer', 'umer_1790352462_284@logictech.local', 0, 0, NULL, 'admin', '$2y$10$I.f8.eSxrJKoHUyjBrqsT.xOXkYZBimuq1RsVkWWgWLMuG6yq2SkW', NULL, '2026-09-25 16:07:42', '2026-09-25 16:07:42'),
(42, 'UMER', 'umer_1791367321_146@logictech.local', 0, 0, NULL, 'admin', '$2y$10$fg.FGfo37sUXBkv4A8sIhuskxwQ98lSrGGCL7Dvn4HRvc/ovMXPZ2', NULL, '2026-10-07 10:02:01', '2026-10-07 10:02:01'),
(43, 'QAMAR', 'qamar_1791367339_156@logictech.local', 0, 0, NULL, 'admin', '$2y$10$IaHKj.ZbhALQy8UwFUJP3Ox7OQZbH99tb3Ihaud92c.hnW7TNfUme', NULL, '2026-10-07 10:02:19', '2026-10-07 10:02:19'),
(44, 'ATIF', 'atif_1791367394_599@logictech.local', 0, 0, NULL, 'admin', '$2y$10$YDxsqARbChlPzj5AtmYmbenA62SzDFT249xTUACqTMjWofTqUXVq6', NULL, '2026-10-07 10:03:14', '2026-10-07 10:03:14'),
(45, 'ARSLAN', 'arslan_1791367420_365@logictech.local', 0, 0, NULL, 'admin', '$2y$10$sHAWP5i4uGLaA0uT7G/OIuNZpN5Usnv8u0GyEArSsqtIYBoxFyatG', NULL, '2026-10-07 10:03:40', '2026-10-07 10:03:40'),
(46, 'SHAHZADA', 'shahzada_1791367456_497@logictech.local', 0, 0, NULL, 'admin', '$2y$10$gjBJrrrQqAsacg5XhC75LeK7X5cAzZHGUbxDTrdKpU05p2x7uemnW', NULL, '2026-10-07 10:04:16', '2026-10-07 10:04:16'),
(47, 'SHUJAT SHAH', 'shujatshah_1791367514_833@logictech.local', 0, 0, NULL, 'admin', '$2y$10$x0N0l4ToYBZY9WeiNBRhfeTskPeV5O1KMvRzOhlkX04IrkSPt6s3m', NULL, '2026-10-07 10:05:14', '2026-10-07 10:05:14'),
(48, 'SHAZIM', 'shazim_1791367537_815@logictech.local', 0, 0, NULL, 'admin', '$2y$10$v0RhguYeo29QlXxkm.jjpevEghMa.l1D48yR6auNbHZHRPfApcFh2', NULL, '2026-10-07 10:05:37', '2026-10-07 10:05:37'),
(49, 'ZAKI', 'zaki_1791376531_481@logictech.local', 0, 0, NULL, 'admin', '$2y$10$JCIbzqESAx04tqIXOWcLB.f1h0Z4ZFFIXne2oB0DgLs09dcQUqUzK', NULL, '2026-10-07 12:35:31', '2026-10-07 12:35:31'),
(50, 'MOHSIN OFFC', 'mohsinoffc_1791376687_738@logictech.local', 0, 0, NULL, 'admin', '$2y$10$07d3wxq4Vn04FhAQpCW1v.3uMXipe7Q0//HIit6mNC1..SnAiVwFO', NULL, '2026-10-07 12:38:07', '2026-10-07 12:38:07'),
(51, 'ABDUL REHAMN', 'abdulrehamn_1791376715_745@logictech.local', 0, 0, NULL, 'admin', '$2y$10$aWhQE5UCFhC9ExccsnhuxueTI9D6C9RcNGExj19pNSGtHjO2GZuBG', NULL, '2026-10-07 12:38:36', '2026-10-07 12:38:36'),
(52, 'AHMAD UMAIR AC', 'ahmadumairac_1791376746_583@logictech.local', 0, 0, NULL, 'admin', '$2y$10$jWdcQ3SPCy8aMRt.Rs8rKeQmpoUXUs6DtPZrFxktX4vPVoRkAMqQm', NULL, '2026-10-07 12:39:06', '2026-10-07 12:39:06'),
(53, 'YASEEN', 'yaseen_1791376769_640@logictech.local', 0, 0, NULL, 'admin', '$2y$10$4FjdQ9k/PpOcItF.hyLBC.WBnfJVw2GedNmlfu/RZDBQ98jMZRjUK', NULL, '2026-10-07 12:39:29', '2026-10-07 12:39:29'),
(54, 'SHAMEER', 'shameer_1791376787_498@logictech.local', 0, 0, NULL, 'admin', '$2y$10$5n08GLHzRlJUXDPpOGdzG.94/LeBaOpw6wwjUv3YKW/RzzzcYc5lO', NULL, '2026-10-07 12:39:47', '2026-10-07 12:39:47'),
(55, 'ARSLAN ASLAM', 'arslanaslam_1791376813_296@logictech.local', 0, 0, NULL, 'admin', '$2y$10$spemoZRMTFvTXy8u4LXPvuhrG.Qrh5widIHRrY3jrhk/XlobasI.6', NULL, '2026-10-07 12:40:13', '2026-10-07 12:40:13'),
(56, 'MALIK AMIR', 'malikamir_1791376966_402@logictech.local', 0, 0, NULL, 'admin', '$2y$10$qB.eL3ear4ILzMFP56VyoOjq82Lm13K1R3vfs2KE4iyqxTMSdm2sy', NULL, '2026-10-07 12:42:46', '2026-10-07 12:42:46'),
(57, 'BUILDING RENT', 'buildingrent_1791376986_920@logictech.local', 0, 0, NULL, 'admin', '$2y$10$ppkWm28wQSLNq54ScAAcQOMovhVfaGbeXBTyVF5gHv2qPsb1uxtsK', NULL, '2026-10-07 12:43:06', '2026-10-07 12:43:06'),
(58, 'UMAR SULTAN', 'umarsultan_1792396492_105@logictech.local', 0, 0, NULL, 'admin', '$2y$10$5q9guQjkq9IwtYvLDWZQy.5NKeVtUB05rRWNN2zW3hOQumP/cyNJ.', NULL, '2026-10-19 07:54:52', '2026-10-19 07:54:52'),
(59, 'QAMAR SULTAN', 'qamarsultan_1792396571_812@logictech.local', 0, 0, NULL, 'admin', '$2y$10$.JofiRs8CpWZg/YC8M6WweFG976XxCGIVS7reN6ko2MXzb2s4Tpai', NULL, '2026-10-19 07:56:11', '2026-10-19 07:56:11'),
(60, 'ATIF ALI', 'atifali_1792396617_435@logictech.local', 0, 0, NULL, 'admin', '$2y$10$0oINB8xP0BD4KDg3q94lu.3b4.y7ZIUnq9TrRgUOWRG1Xujdunc5W', NULL, '2026-10-19 07:56:57', '2026-10-19 07:56:57'),
(61, 'ARSLAN BHATTI', 'arslanbhatti_1792396675_834@logictech.local', 0, 0, NULL, 'admin', '$2y$10$NBt.Ovgy4Btz5sGmzraikO.AEU0aWmlRAMIHVGQaL/8qLFKzcNdhC', NULL, '2026-10-19 07:57:55', '2026-10-19 07:57:55'),
(62, 'SHAHZADA', 'shahzada_1792397099_687@logictech.local', 0, 0, NULL, 'admin', '$2y$10$aqBiiYYeUdpEuNVdvSSrcennciWDzg8kIxG4JSYQZWt7.c4Wxh6Qa', NULL, '2026-10-19 08:04:59', '2026-10-19 08:04:59'),
(63, 'SHUJAT SHAH', 'shujatshah_1792397160_296@logictech.local', 0, 0, NULL, 'admin', '$2y$10$mdrN.bkXMQP/zOGXpNMf4eVq2mJaKKRIKcCbe7f0TZQh0g67OVJ02', NULL, '2026-10-19 08:06:01', '2026-10-19 08:06:01'),
(64, 'SHAZIM ALI', 'shazimali_1792397215_200@logictech.local', 0, 0, NULL, 'admin', '$2y$10$g3Fwf3ydSkycNDPxU75dTOnNPB9lOlWVdgKoF8TuEwJrD5MmqQcKS', NULL, '2026-10-19 08:06:55', '2026-10-19 08:06:55'),
(65, 'ZAKI', 'zaki_1792397242_301@logictech.local', 0, 0, NULL, 'admin', '$2y$10$QN8NENlfu7RyrZYDAzDm9eC5dRGYMEbItQ6Uf5Wo4W1qqmIN18JR.', NULL, '2026-10-19 08:07:22', '2026-10-19 08:07:22'),
(66, 'MOHSIN OFFC', 'abdurrehman_1792397324_612@logictech.local', 0, 0, NULL, 'admin', '$2y$10$vezM9fdjr8dqEaAHdbLnQ.HdxSh0Yu/BqO/MkwGvfspPalvRZCwNu', NULL, '2026-10-19 08:08:44', '2026-10-19 08:11:48'),
(67, 'Asad', 'asad@logictech.com.pk', 0, 0, NULL, 'admin', '$2y$10$CFRBXMpNSYN2p7fa3PbJi.NqYGw1fPktyE1P85NCz.LhOAXNAk49i', 'HmyVtfY0YXCK74iJfZhSMLbNHiCa3CXH1nPLrtyKxe6fOOTE2l4ZWIiJ3coK', '2026-10-19 17:42:57', '2026-10-19 17:42:57'),
(68, 'Attiq', 'attiq@logictech.com.pk', 0, 0, NULL, 'admin', '$2y$10$oF7fUDS77AuxOhHB/EUhOObz7rjnnL/jHeEJL/hf7Li10ljGBtQNa', 'T2mxP24AkU3LhrSby3mtvp7aSUDy6QKwQWUv6p251qgp6kiW5aK5apol8b6F', '2026-10-19 17:54:36', '2026-10-19 17:54:36');

-- --------------------------------------------------------

--
-- Table structure for table `vendors`
--

CREATE TABLE `vendors` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('local','international') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'local',
  `currency` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PKR',
  `country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_person` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ntn_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `strn_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `credit_limit` decimal(15,2) NOT NULL DEFAULT '0.00',
  `lead_time_days` int NOT NULL DEFAULT '7',
  `payment_terms` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_details` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `opening_balance` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vendors`
--

INSERT INTO `vendors` (`id`, `name`, `type`, `currency`, `country`, `contact_person`, `email`, `phone`, `mobile`, `ntn_number`, `strn_number`, `credit_limit`, `lead_time_days`, `payment_terms`, `bank_details`, `is_active`, `address`, `opening_balance`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'CHINA', 'local', 'PKR', NULL, NULL, NULL, '0', NULL, NULL, NULL, 0.00, 7, NULL, NULL, 1, 'CHINA', '0', '2026-10-17 13:05:05', '2026-10-17 13:05:05', NULL),
(2, 'MHM Enterprices', 'local', 'PKR', NULL, NULL, NULL, '04237630032', NULL, NULL, NULL, 0.00, 7, NULL, NULL, 1, 'brandreth road lhr', '0', '2026-10-19 07:11:46', '2026-10-19 07:11:46', NULL),
(3, 'EPS kHALIL UR REHMAN', 'local', 'PKR', NULL, NULL, NULL, '03224799007', NULL, NULL, NULL, 0.00, 7, NULL, NULL, 1, 'BRANTH ROAD LAHORE', '0', '2026-10-24 06:56:28', '2026-10-24 06:56:28', NULL),
(4, 'Fahad Electric Company Faizan', 'local', 'PKR', NULL, NULL, NULL, '03246630990', NULL, NULL, NULL, 0.00, 7, NULL, NULL, 1, 'BRANTH ROAD LAHORE', '0', '2026-10-24 07:00:15', '2026-10-24 07:00:15', NULL),
(5, 'Chouhan Plastic Stretch Roll', 'local', 'PKR', NULL, NULL, NULL, '03225335194', NULL, NULL, NULL, 0.00, 7, NULL, NULL, 1, 'Near Brandreth Road', '15000', '2026-10-27 09:56:03', '2026-10-27 09:56:03', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vendor_bilties`
--

CREATE TABLE `vendor_bilties` (
  `id` bigint UNSIGNED NOT NULL,
  `vendor_id` bigint UNSIGNED NOT NULL,
  `purchase_id` bigint UNSIGNED DEFAULT NULL,
  `bilty_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicle_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transporter_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vendor_ledgers`
--

CREATE TABLE `vendor_ledgers` (
  `id` bigint UNSIGNED NOT NULL,
  `admin_or_user_id` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `vendor_id` bigint UNSIGNED NOT NULL,
  `opening_balance` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `previous_balance` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `closing_balance` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vendor_ledgers`
--

INSERT INTO `vendor_ledgers` (`id`, `admin_or_user_id`, `vendor_id`, `opening_balance`, `previous_balance`, `closing_balance`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '67', 1, '0', '0', '0', '2026-10-17 13:05:05', '2026-10-22 06:47:25', NULL),
(2, '2', 2, '0', '0', '19270', '2026-10-19 07:11:46', '2026-10-19 07:14:32', NULL),
(3, '67', 3, '116340', '116340', '168920', '2026-10-24 06:56:28', '2026-10-31 10:29:46', NULL),
(4, '67', 4, '0', '0', '100000', '2026-10-24 07:00:15', '2026-10-24 07:17:47', NULL),
(5, '67', 5, '15000', '15000', '34250', '2026-10-27 09:56:04', '2026-10-28 11:16:53', NULL),
(6, '1', 3, '0', '116340', '16340', '2026-10-28 09:46:55', '2026-10-28 09:46:55', NULL),
(7, '1', 4, '0', '100000', '0', '2026-10-28 09:46:55', '2026-10-28 09:46:55', NULL),
(8, '67', 3, '0', '16340', '116340', '2026-10-29 10:15:02', '2026-10-29 10:15:02', NULL),
(9, '67', 4, '0', '0', '100000', '2026-10-29 10:15:02', '2026-10-29 10:15:02', NULL),
(10, '67', 3, '0', '116340', '16340', '2026-10-29 10:15:02', '2026-10-29 10:15:02', NULL),
(11, '67', 4, '0', '100000', '0', '2026-10-29 10:16:42', '2026-10-29 10:16:42', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vendor_payments`
--

CREATE TABLE `vendor_payments` (
  `id` bigint UNSIGNED NOT NULL,
  `vendor_id` bigint UNSIGNED NOT NULL,
  `admin_or_user_id` bigint UNSIGNED NOT NULL,
  `payment_date` date DEFAULT NULL,
  `amount` decimal(12,2) DEFAULT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vouchers`
--

CREATE TABLE `vouchers` (
  `id` bigint UNSIGNED NOT NULL,
  `voucher_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sales_officer` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `person` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sub_head` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `narration` text COLLATE utf8mb4_unicode_ci,
  `amount` decimal(15,2) NOT NULL,
  `status` enum('draft','posted') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `voucher_details`
--

CREATE TABLE `voucher_details` (
  `id` bigint UNSIGNED NOT NULL,
  `voucher_master_id` bigint UNSIGNED NOT NULL,
  `account_id` bigint UNSIGNED NOT NULL,
  `debit` decimal(15,2) NOT NULL DEFAULT '0.00',
  `credit` decimal(15,2) NOT NULL DEFAULT '0.00',
  `narration` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `party_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `party_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `voucher_details`
--

INSERT INTO `voucher_details` (`id`, `voucher_master_id`, `account_id`, `debit`, `credit`, `narration`, `party_type`, `party_id`, `created_at`, `updated_at`) VALUES
(1, 1, 5, 750000.00, 0.00, 'Sale Invoice #INV-0001', NULL, NULL, '2026-10-17 12:44:49', '2026-10-17 12:44:49'),
(2, 1, 6, 0.00, 750000.00, 'Sale Invoice #INV-0001', NULL, NULL, '2026-10-17 12:44:49', '2026-10-17 12:44:49'),
(3, 2, 2, 400000.00, 0.00, 'Payment received from Invoice #INV-0001', NULL, NULL, '2026-10-17 12:44:49', '2026-10-17 12:44:49'),
(4, 2, 5, 0.00, 400000.00, 'Payment for Invoice #INV-0001', NULL, NULL, '2026-10-17 12:44:49', '2026-10-17 12:44:49'),
(7, 4, 3, 5000.00, 0.00, 'Payment to 2', NULL, NULL, '2026-10-19 06:05:38', '2026-10-19 06:05:38'),
(8, 4, 2, 0.00, 5000.00, 'ATM WITHDRAW', NULL, NULL, '2026-10-19 06:05:38', '2026-10-19 06:05:38'),
(11, 6, 7, 19270.00, 0.00, 'Purchase Invoice #PUR-003', NULL, NULL, '2026-10-19 07:14:33', '2026-10-19 07:14:33'),
(12, 6, 1, 0.00, 19270.00, 'Payable to Vendor MHM Enterprices', NULL, NULL, '2026-10-19 07:14:33', '2026-10-19 07:14:33'),
(13, 8, 7, 0.00, 0.00, 'Purchase Invoice #PUR-002', NULL, NULL, '2026-10-22 06:47:25', '2026-10-22 06:47:25'),
(14, 8, 1, 0.00, 0.00, 'Payable to Vendor CHINA', NULL, NULL, '2026-10-22 06:47:25', '2026-10-22 06:47:25'),
(15, 9, 7, 0.00, 0.00, 'Purchase Invoice #PUR-001', NULL, NULL, '2026-10-23 12:04:01', '2026-10-23 12:04:01'),
(16, 9, 1, 0.00, 0.00, 'Payable to Vendor CHINA', NULL, NULL, '2026-10-23 12:04:01', '2026-10-23 12:04:01'),
(17, 10, 7, 91110.00, 0.00, 'Purchase Invoice #PUR-004', NULL, NULL, '2026-10-24 07:16:10', '2026-10-24 07:16:10'),
(18, 10, 1, 0.00, 91110.00, 'Payable to Vendor EPS kHALIL UR REHMAN', NULL, NULL, '2026-10-24 07:16:10', '2026-10-24 07:16:10'),
(19, 11, 7, 100000.00, 0.00, 'Purchase Invoice #PUR-005', NULL, NULL, '2026-10-24 07:17:47', '2026-10-24 07:17:47'),
(20, 11, 1, 0.00, 100000.00, 'Payable to Vendor Fahad Electric Company Faizan', NULL, NULL, '2026-10-24 07:17:47', '2026-10-24 07:17:47'),
(21, 12, 5, 580000.00, 0.00, 'Sale Invoice #INV-0002', NULL, NULL, '2026-10-24 10:06:26', '2026-10-24 10:06:26'),
(22, 12, 6, 0.00, 580000.00, 'Sale Invoice #INV-0002', NULL, NULL, '2026-10-24 10:06:26', '2026-10-24 10:06:26'),
(23, 13, 3, 100000.00, 0.00, 'Payment received from Invoice #INV-0002', NULL, NULL, '2026-10-24 10:06:26', '2026-10-24 10:06:26'),
(24, 13, 5, 0.00, 100000.00, 'Payment for Invoice #INV-0002', NULL, NULL, '2026-10-24 10:06:26', '2026-10-24 10:06:26'),
(25, 14, 5, 1227200.00, 0.00, 'Sale Invoice #INV-0003', NULL, NULL, '2026-10-24 18:52:36', '2026-10-24 18:52:36'),
(26, 14, 6, 0.00, 1227200.00, 'Sale Invoice #INV-0003', NULL, NULL, '2026-10-24 18:52:36', '2026-10-24 18:52:36'),
(35, 16, 5, 400000.00, 0.00, 'Sale Invoice #INV-0004', NULL, NULL, '2026-10-26 08:40:14', '2026-10-26 08:40:14'),
(36, 16, 6, 0.00, 400000.00, 'Sale Invoice #INV-0004', NULL, NULL, '2026-10-26 08:40:14', '2026-10-26 08:40:14'),
(41, 17, 5, 580000.00, 0.00, 'Sale Invoice #INV-0005', NULL, NULL, '2026-10-26 12:00:04', '2026-10-26 12:00:04'),
(42, 17, 6, 0.00, 580000.00, 'Sale Invoice #INV-0005', NULL, NULL, '2026-10-26 12:00:04', '2026-10-26 12:00:04'),
(43, 18, 5, 47000.00, 0.00, 'Repair Invoice #REP-00014 - IGBT modules', NULL, NULL, '2026-10-26 12:58:20', '2026-10-26 12:58:20'),
(44, 18, 6, 0.00, 47000.00, 'Repair Invoice #REP-00014 - IGBT modules', NULL, NULL, '2026-10-26 12:58:20', '2026-10-26 12:58:20'),
(45, 19, 7, 25230.00, 0.00, 'Purchase Invoice #PUR-006', NULL, NULL, '2026-10-26 13:35:31', '2026-10-26 13:35:31'),
(46, 19, 1, 0.00, 25230.00, 'Payable to Vendor EPS kHALIL UR REHMAN', NULL, NULL, '2026-10-26 13:35:31', '2026-10-26 13:35:31'),
(47, 20, 5, 26000.00, 0.00, 'Repair Invoice #REP-00003 - CT repair (Rewinding)', NULL, NULL, '2026-10-26 19:43:13', '2026-10-26 19:43:13'),
(48, 20, 6, 0.00, 26000.00, 'Repair Invoice #REP-00003 - CT repair (Rewinding)', NULL, NULL, '2026-10-26 19:43:13', '2026-10-26 19:43:13'),
(49, 7, 5, 28500.00, 0.00, 'Repair Invoice #REP-00004 - IGBT', NULL, NULL, '2026-10-26 19:47:56', '2026-10-26 19:47:56'),
(50, 7, 6, 0.00, 28500.00, 'Repair Invoice #REP-00004 - IGBT', NULL, NULL, '2026-10-26 19:47:56', '2026-10-26 19:47:56'),
(51, 21, 5, 14000.00, 0.00, 'Repair Invoice #REP-00015 - Induction Heater B-unit Repair \ni. Primary repair\nii. Secondary repair', NULL, NULL, '2026-10-26 20:31:14', '2026-10-26 20:31:14'),
(52, 21, 6, 0.00, 14000.00, 'Repair Invoice #REP-00015 - Induction Heater B-unit Repair \ni. Primary repair\nii. Secondary repair', NULL, NULL, '2026-10-26 20:31:14', '2026-10-26 20:31:14'),
(53, 22, 5, 530000.00, 0.00, 'Sale Invoice #INV-0006', NULL, NULL, '2026-10-26 20:45:12', '2026-10-26 20:45:12'),
(54, 22, 6, 0.00, 530000.00, 'Sale Invoice #INV-0006', NULL, NULL, '2026-10-26 20:45:12', '2026-10-26 20:45:12'),
(55, 23, 5, 530000.00, 0.00, 'Sale Invoice #INV-0007', NULL, NULL, '2026-10-26 20:47:12', '2026-10-26 20:47:12'),
(56, 23, 6, 0.00, 530000.00, 'Sale Invoice #INV-0007', NULL, NULL, '2026-10-26 20:47:12', '2026-10-26 20:47:12'),
(57, 24, 5, 770000.00, 0.00, 'Sale Invoice #INV-0008', NULL, NULL, '2026-10-26 20:56:29', '2026-10-26 20:56:29'),
(58, 24, 6, 0.00, 770000.00, 'Sale Invoice #INV-0008', NULL, NULL, '2026-10-26 20:56:29', '2026-10-26 20:56:29'),
(59, 25, 5, 11500.00, 0.00, 'Repair Invoice #REP-00016 - B-unit Water wiring (New)', NULL, NULL, '2026-10-26 21:07:43', '2026-10-26 21:07:43'),
(60, 25, 6, 0.00, 11500.00, 'Repair Invoice #REP-00016 - B-unit Water wiring (New)', NULL, NULL, '2026-10-26 21:07:43', '2026-10-26 21:07:43'),
(61, 26, 5, 20500.00, 0.00, 'Repair Invoice #REP-00017 - Transformer Rewind (New)', NULL, NULL, '2026-10-26 21:15:08', '2026-10-26 21:15:08'),
(62, 26, 6, 0.00, 20500.00, 'Repair Invoice #REP-00017 - Transformer Rewind (New)', NULL, NULL, '2026-10-26 21:15:08', '2026-10-26 21:15:08'),
(63, 27, 5, 17500.00, 0.00, 'Repair Invoice #REP-00018 - New Driver Board', NULL, NULL, '2026-10-26 21:24:32', '2026-10-26 21:24:32'),
(64, 27, 6, 0.00, 17500.00, 'Repair Invoice #REP-00018 - New Driver Board', NULL, NULL, '2026-10-26 21:24:32', '2026-10-26 21:24:32'),
(69, 30, 5, 11000.00, 0.00, 'Sale Invoice #INV-0009', NULL, NULL, '2026-10-28 06:53:47', '2026-10-28 06:53:47'),
(70, 30, 6, 0.00, 11000.00, 'Sale Invoice #INV-0009', NULL, NULL, '2026-10-28 06:53:47', '2026-10-28 06:53:47'),
(75, 15, 5, 220000.00, 0.00, 'Repair Invoice #REP-00007 - Induction Heater upgrade to LTZ-40kw', NULL, NULL, '2026-10-28 07:26:31', '2026-10-28 07:26:31'),
(76, 15, 6, 0.00, 220000.00, 'Repair Invoice #REP-00007 - Induction Heater upgrade to LTZ-40kw', NULL, NULL, '2026-10-28 07:26:31', '2026-10-28 07:26:31'),
(77, 28, 5, 21500.00, 0.00, 'Repair Invoice #REP-00019 - IGBT', NULL, NULL, '2026-10-28 08:23:38', '2026-10-28 08:23:38'),
(78, 28, 6, 0.00, 21500.00, 'Repair Invoice #REP-00019 - IGBT', NULL, NULL, '2026-10-28 08:23:38', '2026-10-28 08:23:38'),
(82, 32, 7, 19250.00, 0.00, 'Purchase Invoice #PUR-007', NULL, NULL, '2026-10-28 11:16:53', '2026-10-28 11:16:53'),
(83, 32, 1, 0.00, 19250.00, 'Payable to Vendor Chouhan Plastic Stretch Roll', NULL, NULL, '2026-10-28 11:16:53', '2026-10-28 11:16:53'),
(84, 33, 5, 950000.00, 0.00, 'Sale Invoice #INV-0010', NULL, NULL, '2026-10-28 12:41:09', '2026-10-28 12:41:09'),
(85, 33, 6, 0.00, 950000.00, 'Sale Invoice #INV-0010', NULL, NULL, '2026-10-28 12:41:09', '2026-10-28 12:41:09'),
(94, 38, 5, 640000.00, 0.00, 'Sale Invoice #INV-0012', NULL, NULL, '2026-10-28 20:07:20', '2026-10-28 20:07:20'),
(95, 38, 6, 0.00, 640000.00, 'Sale Invoice #INV-0012', NULL, NULL, '2026-10-28 20:07:20', '2026-10-28 20:07:20'),
(96, 39, 1, 100000.00, 0.00, 'Payment to vendor', 'App\\Models\\Vendor', 3, '2026-10-29 10:15:02', '2026-10-29 10:15:02'),
(97, 39, 4, 0.00, 100000.00, 'venders payments from LTE Meezan', NULL, NULL, '2026-10-29 10:15:02', '2026-10-29 10:15:02'),
(98, 40, 1, 100000.00, 0.00, 'Payment to vendor', 'App\\Models\\Vendor', 4, '2026-10-29 10:16:42', '2026-10-29 10:16:42'),
(99, 40, 2, 0.00, 100000.00, 'vender payments from LTE Meezan', NULL, NULL, '2026-10-29 10:16:42', '2026-10-29 10:16:42'),
(100, 41, 2, 350000.00, 0.00, 'Receipt', NULL, NULL, '2026-10-29 10:44:16', '2026-10-29 10:44:16'),
(101, 41, 5, 0.00, 350000.00, 'Receipt from customer', NULL, NULL, '2026-10-29 10:44:16', '2026-10-29 10:44:16'),
(112, 46, 2, 400000.00, 0.00, 'Receipt', NULL, NULL, '2026-10-30 10:34:18', '2026-10-30 10:34:18'),
(113, 46, 5, 0.00, 400000.00, 'Receipt from customer', NULL, NULL, '2026-10-30 10:34:18', '2026-10-30 10:34:18'),
(114, 47, 5, 42000.00, 0.00, 'Repair Invoice #REP-00022 - IGBT', NULL, NULL, '2026-10-30 11:40:55', '2026-10-30 11:40:55'),
(115, 47, 6, 0.00, 42000.00, 'Repair Invoice #REP-00022 - IGBT', NULL, NULL, '2026-10-30 11:40:56', '2026-10-30 11:40:56'),
(116, 48, 2, 42000.00, 0.00, 'Receipt', NULL, NULL, '2026-10-30 12:04:44', '2026-10-30 12:04:44'),
(117, 48, 5, 0.00, 42000.00, 'Receipt from customer', NULL, NULL, '2026-10-30 12:04:44', '2026-10-30 12:04:44'),
(118, 49, 5, 52500.00, 0.00, 'Sale Invoice #INV-0011', NULL, NULL, '2026-10-30 12:23:27', '2026-10-30 12:23:27'),
(119, 49, 6, 0.00, 52500.00, 'Sale Invoice #INV-0011', NULL, NULL, '2026-10-30 12:23:27', '2026-10-30 12:23:27'),
(124, 52, 5, 1486800.00, 0.00, 'Sale Invoice #INV-0013', NULL, NULL, '2026-10-30 19:05:03', '2026-10-30 19:05:03'),
(125, 52, 6, 0.00, 1486800.00, 'Sale Invoice #INV-0013', NULL, NULL, '2026-10-30 19:05:03', '2026-10-30 19:05:03'),
(126, 53, 3, 400000.00, 0.00, 'Payment received from Invoice #INV-0013', NULL, NULL, '2026-10-30 19:05:03', '2026-10-30 19:05:03'),
(127, 53, 5, 0.00, 400000.00, 'Payment for Invoice #INV-0013', NULL, NULL, '2026-10-30 19:05:03', '2026-10-30 19:05:03'),
(128, 54, 5, 43000.00, 0.00, 'Repair Invoice #REP-00025 - Driver Board repair', NULL, NULL, '2026-10-30 19:07:06', '2026-10-30 19:07:06'),
(129, 54, 6, 0.00, 43000.00, 'Repair Invoice #REP-00025 - Driver Board repair', NULL, NULL, '2026-10-30 19:07:06', '2026-10-30 19:07:06'),
(136, 56, 5, 4000.00, 0.00, 'Repair Invoice #REP-00026 - TEST DRIVER BOARD', NULL, NULL, '2026-10-30 19:45:24', '2026-10-30 19:45:24'),
(137, 56, 6, 0.00, 4000.00, 'Repair Invoice #REP-00026 - TEST DRIVER BOARD', NULL, NULL, '2026-10-30 19:45:24', '2026-10-30 19:45:24'),
(148, 62, 5, 44000.00, 0.00, 'Repair Invoice #REP-00028 - IGBT', NULL, NULL, '2026-10-31 05:56:13', '2026-10-31 05:56:13'),
(149, 62, 6, 0.00, 44000.00, 'Repair Invoice #REP-00028 - IGBT', NULL, NULL, '2026-10-31 05:56:13', '2026-10-31 05:56:13'),
(150, 63, 2, 17500.00, 0.00, 'Receipt', NULL, NULL, '2026-10-31 07:51:39', '2026-10-31 07:51:39'),
(151, 63, 5, 0.00, 17500.00, 'Receipt from customer', NULL, NULL, '2026-10-31 07:51:39', '2026-10-31 07:51:39'),
(152, 42, 5, 216700.00, 0.00, 'Repair Invoice #REP-00021 - Main Control Board', NULL, NULL, '2026-10-31 08:09:53', '2026-10-31 08:09:53'),
(153, 42, 6, 0.00, 216700.00, 'Repair Invoice #REP-00021 - Main Control Board', NULL, NULL, '2026-10-31 08:09:53', '2026-10-31 08:09:53'),
(154, 64, 5, 6000.00, 0.00, 'Repair Invoice #REP-00024 - New Driver Boards', NULL, NULL, '2026-10-31 08:18:27', '2026-10-31 08:18:27'),
(155, 64, 6, 0.00, 6000.00, 'Repair Invoice #REP-00024 - New Driver Boards', NULL, NULL, '2026-10-31 08:18:27', '2026-10-31 08:18:27'),
(156, 61, 5, 41000.00, 0.00, 'Repair Invoice #REP-00027 - IGBT modules', NULL, NULL, '2026-10-31 08:31:16', '2026-10-31 08:31:16'),
(157, 61, 6, 0.00, 41000.00, 'Repair Invoice #REP-00027 - IGBT modules', NULL, NULL, '2026-10-31 08:31:16', '2026-10-31 08:31:16'),
(158, 65, 2, 33000.00, 0.00, 'Receipt', NULL, NULL, '2026-10-31 08:33:53', '2026-10-31 08:33:53'),
(159, 65, 5, 0.00, 33000.00, 'Receipt from customer', NULL, NULL, '2026-10-31 08:33:53', '2026-10-31 08:33:53'),
(160, 66, 5, 5000.00, 0.00, 'Repair Invoice #REP-00029 - Induction 40KW FOR REPAIR (Fault : OVER VOLTAGE)', NULL, NULL, '2026-10-31 08:57:50', '2026-10-31 08:57:50'),
(161, 66, 6, 0.00, 5000.00, 'Repair Invoice #REP-00029 - Induction 40KW FOR REPAIR (Fault : OVER VOLTAGE)', NULL, NULL, '2026-10-31 08:57:50', '2026-10-31 08:57:50'),
(170, 71, 5, 550000.00, 0.00, 'Sale Invoice #INV-0015', NULL, NULL, '2026-10-31 09:41:20', '2026-10-31 09:41:20'),
(171, 71, 6, 0.00, 550000.00, 'Sale Invoice #INV-0015', NULL, NULL, '2026-10-31 09:41:20', '2026-10-31 09:41:20'),
(172, 72, 2, 50000.00, 0.00, 'Payment received from Invoice #INV-0015', NULL, NULL, '2026-10-31 09:41:20', '2026-10-31 09:41:20'),
(173, 72, 5, 0.00, 50000.00, 'Payment for Invoice #INV-0015', NULL, NULL, '2026-10-31 09:41:20', '2026-10-31 09:41:20'),
(174, 73, 7, 52580.00, 0.00, 'Purchase Invoice #PUR-008', NULL, NULL, '2026-10-31 10:29:46', '2026-10-31 10:29:46'),
(175, 73, 1, 0.00, 52580.00, 'Payable to Vendor EPS kHALIL UR REHMAN', NULL, NULL, '2026-10-31 10:29:46', '2026-10-31 10:29:46'),
(176, 74, 2, 20500.00, 0.00, 'Receipt', NULL, NULL, '2026-10-31 10:34:40', '2026-10-31 10:34:40'),
(177, 74, 5, 0.00, 20500.00, 'Receipt from customer', NULL, NULL, '2026-10-31 10:34:40', '2026-10-31 10:34:40'),
(178, 75, 5, 2.00, 0.00, 'Sale Invoice #INV-0014', NULL, NULL, '2026-10-31 11:26:45', '2026-10-31 11:26:45'),
(179, 75, 6, 0.00, 2.00, 'Sale Invoice #INV-0014', NULL, NULL, '2026-10-31 11:26:45', '2026-10-31 11:26:45'),
(180, 76, 5, 25000.00, 0.00, 'Repair Invoice #REP-00012 - IGBT', NULL, NULL, '2026-10-31 11:49:59', '2026-10-31 11:49:59'),
(181, 76, 6, 0.00, 25000.00, 'Repair Invoice #REP-00012 - IGBT', NULL, NULL, '2026-10-31 11:49:59', '2026-10-31 11:49:59');

-- --------------------------------------------------------

--
-- Table structure for table `voucher_masters`
--

CREATE TABLE `voucher_masters` (
  `id` bigint UNSIGNED NOT NULL,
  `voucher_type` enum('receipt','payment','expense','journal','contra') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('draft','posted','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `voucher_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `fiscal_year` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `party_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `party_id` bigint UNSIGNED DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `total_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `posted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `voucher_masters`
--

INSERT INTO `voucher_masters` (`id`, `voucher_type`, `status`, `voucher_no`, `date`, `fiscal_year`, `party_type`, `party_id`, `remarks`, `total_amount`, `created_by`, `posted_at`, `created_at`, `updated_at`) VALUES
(1, 'journal', 'posted', 'JV-2026-0001', '2026-10-17', NULL, 'App\\Models\\Customer', 2, 'Sale Invoice #INV-0001', 750000.00, 2, '2026-10-17 12:44:49', '2026-10-17 12:44:49', '2026-10-17 12:44:49'),
(2, 'receipt', 'posted', 'RE-2026-0001', '2026-10-17', '2025-2026', 'App\\Models\\Customer', 2, 'Advance Receipt for Sale Invoice #INV-0001. Total: 400000', 400000.00, 2, NULL, '2026-10-17 12:44:49', '2026-10-17 12:44:49'),
(4, 'payment', 'posted', 'PA-2026-0001', '2026-10-19', '2025-2026', 'App\\Models\\Account', 3, 'ATM WITHDRAW (Ref: PVID-0001)', 5000.00, 2, NULL, '2026-10-19 06:05:38', '2026-10-19 06:05:38'),
(6, 'journal', 'posted', 'JV-2026-0004', '2026-10-16', '2025-2026', 'App\\Models\\Vendor', 2, 'Purchase Voucher #PUR-003', 19270.00, 2, NULL, '2026-10-19 07:14:33', '2026-10-19 07:14:33'),
(7, 'journal', 'posted', 'JV-2026-0005', '2026-10-21', NULL, 'App\\Models\\Customer', 4, 'Repair Invoice #REP-00004', 28500.00, 2, '2026-10-19 12:38:52', '2026-10-19 12:38:52', '2026-10-26 19:47:56'),
(8, 'journal', 'posted', 'JV-2026-0006', '2026-10-18', '2025-2026', 'App\\Models\\Vendor', 1, 'Purchase Voucher #PUR-002', 0.00, 67, NULL, '2026-10-22 06:47:25', '2026-10-22 06:47:25'),
(9, 'journal', 'posted', 'JV-2026-0007', '2026-10-17', '2025-2026', 'App\\Models\\Vendor', 1, 'Purchase Voucher #PUR-001', 0.00, 67, NULL, '2026-10-23 12:04:01', '2026-10-23 12:04:01'),
(10, 'journal', 'posted', 'JV-2026-0008', '2026-10-12', '2025-2026', 'App\\Models\\Vendor', 3, 'Purchase Voucher #PUR-004', 91110.00, 67, NULL, '2026-10-24 07:16:10', '2026-10-24 07:16:10'),
(11, 'journal', 'posted', 'JV-2026-0009', '2026-10-20', '2025-2026', 'App\\Models\\Vendor', 4, 'Purchase Voucher #PUR-005', 100000.00, 67, NULL, '2026-10-24 07:17:47', '2026-10-24 07:17:47'),
(12, 'journal', 'posted', 'JV-2026-0010', '2026-08-22', NULL, 'App\\Models\\Customer', 12, 'Sale Invoice #INV-0002', 580000.00, 67, '2026-10-24 10:06:26', '2026-10-24 10:06:26', '2026-10-24 10:06:26'),
(13, 'receipt', 'posted', 'RE-2026-0002', '2026-10-24', '2025-2026', 'App\\Models\\Customer', 12, 'Advance Receipt for Sale Invoice #INV-0002. Total: 100000', 100000.00, 67, NULL, '2026-10-24 10:06:26', '2026-10-24 10:06:26'),
(14, 'journal', 'posted', 'JV-2026-0011', '2026-10-13', NULL, 'App\\Models\\Customer', 14, 'Sale Invoice #INV-0003', 1227200.00, 67, '2026-10-24 18:52:36', '2026-10-24 18:52:36', '2026-10-24 18:52:36'),
(15, 'journal', 'posted', 'JV-2026-0012', '2026-10-26', NULL, 'App\\Models\\Customer', 8, 'Repair Invoice #REP-00007', 220000.00, 68, '2026-10-26 07:42:20', '2026-10-26 07:42:20', '2026-10-26 09:26:55'),
(16, 'journal', 'posted', 'JV-2026-0013', '2026-10-26', NULL, 'App\\Models\\Customer', 15, 'Sale Invoice #INV-0004', 400000.00, 68, '2026-10-26 08:40:14', '2026-10-26 08:40:14', '2026-10-26 08:40:14'),
(17, 'journal', 'posted', 'JV-2026-0014', '2026-10-26', NULL, 'App\\Models\\Customer', 16, 'Sale Invoice #INV-0005', 580000.00, 68, '2026-10-26 12:00:04', '2026-10-26 12:00:04', '2026-10-26 12:00:04'),
(18, 'journal', 'posted', 'JV-2026-0015', '2026-10-26', NULL, 'App\\Models\\Customer', 17, 'Repair Invoice #REP-00014', 47000.00, 68, '2026-10-26 12:58:20', '2026-10-26 12:58:20', '2026-10-26 12:58:20'),
(19, 'journal', 'posted', 'JV-2026-0016', '2026-10-26', '2025-2026', 'App\\Models\\Vendor', 3, 'Purchase Voucher #PUR-006', 25230.00, 67, NULL, '2026-10-26 13:35:31', '2026-10-26 13:35:31'),
(20, 'journal', 'posted', 'JV-2026-0017', '2026-10-19', NULL, 'App\\Models\\Customer', 4, 'Repair Invoice #REP-00003', 26000.00, 68, '2026-10-26 19:43:13', '2026-10-26 19:43:13', '2026-10-26 19:43:13'),
(21, 'journal', 'posted', 'JV-2026-0018', '2026-10-27', NULL, 'App\\Models\\Customer', 19, 'Repair Invoice #REP-00015', 14000.00, 68, '2026-10-26 20:31:14', '2026-10-26 20:31:14', '2026-10-26 20:31:14'),
(22, 'journal', 'posted', 'JV-2026-0019', '2026-10-27', NULL, 'App\\Models\\Customer', 20, 'Sale Invoice #INV-0006', 530000.00, 68, '2026-10-26 20:45:12', '2026-10-26 20:45:12', '2026-10-26 20:45:12'),
(23, 'journal', 'posted', 'JV-2026-0020', '2026-10-27', NULL, 'App\\Models\\Customer', 20, 'Sale Invoice #INV-0007', 530000.00, 68, '2026-10-26 20:47:12', '2026-10-26 20:47:12', '2026-10-26 20:47:12'),
(24, 'journal', 'posted', 'JV-2026-0021', '2026-10-27', NULL, 'App\\Models\\Customer', 20, 'Sale Invoice #INV-0008', 770000.00, 68, '2026-10-26 20:56:29', '2026-10-26 20:56:29', '2026-10-26 20:56:29'),
(25, 'journal', 'posted', 'JV-2026-0022', '2026-10-15', NULL, 'App\\Models\\Customer', 21, 'Repair Invoice #REP-00016', 11500.00, 68, '2026-10-26 21:07:43', '2026-10-26 21:07:43', '2026-10-26 21:07:43'),
(26, 'journal', 'posted', 'JV-2026-0023', '2026-10-27', NULL, 'App\\Models\\Customer', 22, 'Repair Invoice #REP-00017', 20500.00, 68, '2026-10-26 21:15:08', '2026-10-26 21:15:08', '2026-10-26 21:15:08'),
(27, 'journal', 'posted', 'JV-2026-0024', '2026-10-27', NULL, 'App\\Models\\Customer', 23, 'Repair Invoice #REP-00018', 17500.00, 68, '2026-10-26 21:24:32', '2026-10-26 21:24:32', '2026-10-26 21:24:32'),
(28, 'journal', 'posted', 'JV-2026-0025', '2026-10-27', NULL, 'App\\Models\\Customer', 19, 'Repair Invoice #REP-00019', 21500.00, 68, '2026-10-26 21:29:27', '2026-10-26 21:29:27', '2026-10-26 21:29:27'),
(30, 'journal', 'posted', 'JV-2026-0026', '2026-10-28', NULL, 'App\\Models\\Customer', 4, 'Sale Invoice #INV-0009', 11000.00, 67, '2026-10-28 06:53:47', '2026-10-28 06:53:47', '2026-10-28 06:53:47'),
(32, 'journal', 'posted', 'JV-2026-0027', '2026-10-28', '2025-2026', 'App\\Models\\Vendor', 5, 'Purchase Voucher #PUR-007', 19250.00, 67, NULL, '2026-10-28 11:16:53', '2026-10-28 11:16:53'),
(33, 'journal', 'posted', 'JV-2026-0028', '2026-10-28', NULL, 'App\\Models\\Customer', 27, 'Sale Invoice #INV-0010', 950000.00, 67, '2026-10-28 12:41:09', '2026-10-28 12:41:09', '2026-10-28 12:41:09'),
(38, 'journal', 'posted', 'JV-2026-0030', '2026-09-01', NULL, 'App\\Models\\Customer', 7, 'Sale Invoice #INV-0012', 640000.00, 68, '2026-10-28 20:07:20', '2026-10-28 20:07:20', '2026-10-28 20:07:20'),
(39, 'payment', 'posted', 'PA-2026-0002', '2026-10-27', '2025-2026', 'App\\Models\\Vendor', 3, 'venders payments from LTE Meezan (Ref: PVID-0002)', 100000.00, 67, NULL, '2026-10-29 10:15:02', '2026-10-29 10:15:02'),
(40, 'payment', 'posted', 'PA-2026-0003', '2026-10-29', '2025-2026', 'App\\Models\\Vendor', 4, 'vender payments from LTE Meezan (Ref: PVID-0003)', 100000.00, 67, NULL, '2026-10-29 10:16:42', '2026-10-29 10:16:42'),
(41, 'receipt', 'posted', 'RE-2026-0003', '2026-10-27', '2025-2026', 'App\\Models\\Customer', 27, 'Advance Payment OFT Recieved (Ref: RVID-002) | Invoices: INV-0010', 350000.00, 67, NULL, '2026-10-29 10:44:16', '2026-10-29 10:44:16'),
(42, 'journal', 'posted', 'JV-2026-0031', '2026-10-31', NULL, 'App\\Models\\Customer', 32, 'Repair Invoice #REP-00021', 216700.00, 68, '2026-10-29 19:43:49', '2026-10-29 19:43:49', '2026-10-31 08:09:53'),
(46, 'receipt', 'posted', 'RE-2026-0004', '2026-10-30', '2025-2026', 'App\\Models\\Customer', 34, 'Receipt from NATIONAL DEVELOPMENT COMPLEX/CENTRE (NDC) (Bal: 1,486,800 Dr) (Ref: RVID-003) | Invoices: INV-0013', 400000.00, 67, NULL, '2026-10-30 10:34:18', '2026-10-30 10:34:18'),
(47, 'journal', 'posted', 'JV-2026-0033', '2026-10-30', NULL, 'App\\Models\\Customer', 30, 'Repair Invoice #REP-00022', 42000.00, 68, '2026-10-30 11:40:55', '2026-10-30 11:40:55', '2026-10-30 11:40:55'),
(48, 'receipt', 'posted', 'RE-2026-0005', '2026-10-30', '2025-2026', 'App\\Models\\Customer', 30, 'Receipt from Khaleel ur Rehman (Bal: 106,000 Dr) (Ref: RVID-004) | Invoices: REP-00022', 42000.00, 68, NULL, '2026-10-30 12:04:44', '2026-10-30 12:04:44'),
(49, 'journal', 'posted', 'JV-2026-0034', '2026-10-29', NULL, 'App\\Models\\Customer', 29, 'Sale Invoice #INV-0011', 52500.00, 68, '2026-10-30 12:23:27', '2026-10-30 12:23:27', '2026-10-30 12:23:27'),
(52, 'journal', 'posted', 'JV-2026-0035', '2026-10-30', NULL, 'App\\Models\\Customer', 34, 'Sale Invoice #INV-0013', 1486800.00, 67, '2026-10-30 19:05:03', '2026-10-30 19:05:03', '2026-10-30 19:05:03'),
(53, 'receipt', 'posted', 'RE-2026-0006', '2026-10-31', '2025-2026', 'App\\Models\\Customer', 34, 'Advance Receipt for Sale Invoice #INV-0013. Total: 400000', 400000.00, 67, NULL, '2026-10-30 19:05:03', '2026-10-30 19:05:03'),
(54, 'journal', 'posted', 'JV-2026-0036', '2026-10-31', NULL, 'App\\Models\\Customer', 36, 'Repair Invoice #REP-00025', 43000.00, 68, '2026-10-30 19:07:06', '2026-10-30 19:07:06', '2026-10-30 19:07:06'),
(56, 'journal', 'posted', 'JV-2026-0037', '2026-10-30', NULL, 'App\\Models\\Customer', 38, 'Repair Invoice #REP-00026', 4000.00, 67, '2026-10-30 19:30:35', '2026-10-30 19:30:35', '2026-10-30 19:44:50'),
(61, 'journal', 'posted', 'JV-2026-0039', '2026-06-29', NULL, 'App\\Models\\Customer', 33, 'Repair Invoice #REP-00027', 41000.00, 68, '2026-10-31 05:42:59', '2026-10-31 05:42:59', '2026-10-31 05:42:59'),
(62, 'journal', 'posted', 'JV-2026-0040', '2026-10-31', NULL, 'App\\Models\\Customer', 33, 'Repair Invoice #REP-00028', 44000.00, 68, '2026-10-31 05:56:13', '2026-10-31 05:56:13', '2026-10-31 05:56:13'),
(63, 'receipt', 'posted', 'RE-2026-0007', '2026-10-31', '2025-2026', 'App\\Models\\Customer', 23, 'Receipt from Abdul Moiz (Bal: 17,500 Dr) (Ref: RVID-005) | Invoices: REP-00018', 17500.00, 68, NULL, '2026-10-31 07:51:39', '2026-10-31 07:51:39'),
(64, 'journal', 'posted', 'JV-2026-0041', '2026-10-30', NULL, 'App\\Models\\Customer', 35, 'Repair Invoice #REP-00024', 6000.00, 68, '2026-10-31 08:18:27', '2026-10-31 08:18:27', '2026-10-31 08:18:27'),
(65, 'receipt', 'posted', 'RE-2026-0008', '2026-10-31', '2025-2026', 'App\\Models\\Customer', 33, 'Receipt from Danish Sb Gujranwala (Bal: 85,000 Dr) (Ref: RVID-006) | Invoices: REP-00027', 33000.00, 68, NULL, '2026-10-31 08:33:53', '2026-10-31 08:33:53'),
(66, 'journal', 'posted', 'JV-2026-0042', '2026-10-31', NULL, 'App\\Models\\Customer', 38, 'Repair Invoice #REP-00029', 5000.00, 67, '2026-10-31 08:57:50', '2026-10-31 08:57:50', '2026-10-31 08:57:50'),
(71, 'journal', 'posted', 'JV-2026-0043', '2026-10-31', NULL, 'App\\Models\\Customer', 39, 'Sale Invoice #INV-0015', 550000.00, 68, '2026-10-31 09:41:20', '2026-10-31 09:41:20', '2026-10-31 09:41:20'),
(72, 'receipt', 'posted', 'RE-2026-0009', '2026-10-31', '2025-2026', 'App\\Models\\Customer', 39, 'Advance Receipt for Sale Invoice #INV-0015. Total: 50000', 50000.00, 68, NULL, '2026-10-31 09:41:20', '2026-10-31 09:41:20'),
(73, 'journal', 'posted', 'JV-2026-0044', '2026-10-28', '2025-2026', 'App\\Models\\Vendor', 3, 'Purchase Voucher #PUR-008', 52580.00, 67, NULL, '2026-10-31 10:29:46', '2026-10-31 10:29:46'),
(74, 'receipt', 'posted', 'RE-2026-0010', '2026-10-31', '2025-2026', 'App\\Models\\Customer', 22, 'Receipt from Kashif Sb Gujranwala (Bal: 20,500 Dr) (Ref: RVID-007) | Invoices: REP-00017', 20500.00, 68, NULL, '2026-10-31 10:34:40', '2026-10-31 10:34:40'),
(75, 'journal', 'posted', 'JV-2026-0045', '2026-10-31', NULL, 'App\\Models\\Customer', 37, 'Sale Invoice #INV-0014', 2.00, 67, '2026-10-31 11:26:45', '2026-10-31 11:26:45', '2026-10-31 11:26:45'),
(76, 'journal', 'posted', 'JV-2026-0046', '2026-10-31', NULL, 'App\\Models\\Customer', 13, 'Repair Invoice #REP-00012', 25000.00, 68, '2026-10-31 11:49:59', '2026-10-31 11:49:59', '2026-10-31 11:49:59');

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_stock_onhand`
-- (See below for the actual view)
--
CREATE TABLE `v_stock_onhand` (
`product_id` bigint unsigned
,`onhand_qty` decimal(34,3)
);

-- --------------------------------------------------------

--
-- Table structure for table `warehouses`
--

CREATE TABLE `warehouses` (
  `id` bigint UNSIGNED NOT NULL,
  `branch_id` bigint UNSIGNED DEFAULT NULL,
  `warehouse_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creater_id` bigint UNSIGNED DEFAULT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `warehouses`
--

INSERT INTO `warehouses` (`id`, `branch_id`, `warehouse_name`, `creater_id`, `location`, `remarks`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, NULL, 'Main Warehouse', NULL, 'Main Office', NULL, '2026-10-17 11:47:06', '2026-10-17 11:47:06', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `warehouse_stocks`
--

CREATE TABLE `warehouse_stocks` (
  `id` bigint UNSIGNED NOT NULL,
  `warehouse_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `quantity` int NOT NULL DEFAULT '0',
  `total_pieces` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `warehouse_stocks`
--

INSERT INTO `warehouse_stocks` (`id`, `warehouse_id`, `product_id`, `quantity`, `total_pieces`, `remarks`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 0, 0.0000, 'Finished Goods (Make-to-Order)', '2026-10-17 11:47:06', '2026-10-17 11:47:06'),
(2, 1, 2, 0, 0.0000, 'Finished Goods (Make-to-Order)', '2026-10-17 11:47:53', '2026-10-31 11:26:45'),
(3, 1, 3, -1, -1.0000, 'Finished Goods (Make-to-Order)', '2026-10-17 11:49:22', '2026-10-24 18:52:36'),
(4, 1, 4, 0, 0.0000, 'Finished Goods (Make-to-Order)', '2026-10-17 11:55:38', '2026-10-17 11:55:38'),
(5, 1, 5, 0, 0.0000, 'Finished Goods (Make-to-Order)', '2026-10-17 12:08:18', '2026-10-17 12:08:18'),
(6, 1, 6, -1, -1.0000, 'Finished Goods (Make-to-Order)', '2026-10-17 12:09:28', '2026-10-26 08:40:14'),
(7, 1, 7, -5, -5.0000, 'Finished Goods (Make-to-Order)', '2026-10-17 12:10:10', '2026-10-31 09:41:20'),
(8, 1, 8, -2, -2.0000, 'Finished Goods (Make-to-Order)', '2026-10-17 12:21:44', '2026-10-26 20:56:29'),
(9, 1, 9, 0, 0.0000, 'Finished Goods (Make-to-Order)', '2026-10-17 12:31:54', '2026-10-17 12:31:54'),
(10, 1, 10, 0, 0.0000, 'Finished Goods (Make-to-Order)', '2026-10-17 12:32:52', '2026-10-17 12:32:52'),
(11, 1, 11, 0, 0.0000, 'Finished Goods (Make-to-Order)', '2026-10-17 12:33:29', '2026-10-17 12:33:29'),
(12, 1, 12, 0, 0.0000, 'Finished Goods (Make-to-Order)', '2026-10-17 12:34:15', '2026-10-17 12:34:15'),
(13, 1, 13, 0, 0.0000, 'Finished Goods (Make-to-Order)', '2026-10-17 12:36:43', '2026-10-30 18:57:47'),
(14, 1, 14, 0, 0.0000, 'Issued in Material Usage MU-0004', '2026-10-17 13:08:21', '2026-10-24 06:29:06'),
(15, 1, 15, 0, 0.0000, 'Issued in Material Usage MU-0007', '2026-10-17 13:09:18', '2026-10-27 09:16:59'),
(16, 1, 16, 0, 0.0000, 'Issued in Material Usage MU-0004', '2026-10-17 13:09:49', '2026-10-24 06:29:06'),
(17, 1, 17, 0, 0.0000, 'Issued in Material Usage MU-0004', '2026-10-17 13:10:14', '2026-10-24 06:29:06'),
(18, 1, 18, 11, 11.0000, 'Issued in Material Usage MU-0007', '2026-10-17 13:10:37', '2026-10-27 09:16:59'),
(19, 1, 19, 0, 0.0000, 'Initial Stock', '2026-10-17 13:10:59', '2026-10-17 13:10:59'),
(20, 1, 20, 0, 0.0000, 'Initial Stock', '2026-10-17 13:11:28', '2026-10-17 13:11:28'),
(21, 1, 21, 14, 14.0000, 'Issued in Material Usage MU-0004', '2026-10-19 06:45:57', '2026-10-24 06:29:06'),
(22, 1, 22, 8, 8.0000, 'Initial Stock', '2026-10-19 08:39:43', '2026-10-19 08:39:43'),
(23, 1, 23, 0, 0.0000, 'Finished Goods (Make-to-Order)', '2026-10-28 06:45:52', '2026-10-30 19:05:03'),
(24, 1, 24, -1, -1.0000, 'Finished Goods (Make-to-Order)', '2026-10-28 12:02:53', '2026-10-30 19:05:03'),
(25, 1, 25, 0, 0.0000, 'Finished Goods (Make-to-Order)', '2026-10-28 12:03:28', '2026-10-28 12:03:28'),
(26, 1, 26, -1, -1.0000, 'Finished Goods (Make-to-Order)', '2026-10-28 12:04:17', '2026-10-28 20:07:20'),
(27, 1, 27, -1, -1.0000, 'Finished Goods (Make-to-Order)', '2026-10-28 12:05:59', '2026-10-28 12:41:09'),
(28, 1, 28, -2, -2.0000, 'Finished Goods (Make-to-Order)', '2026-10-28 12:17:57', '2026-10-28 20:07:20'),
(29, 1, 29, 0, 0.0000, 'Finished Goods (Make-to-Order)', '2026-10-28 12:19:22', '2026-10-30 18:57:01'),
(30, 1, 30, -1, -1.0000, 'Finished Goods (Make-to-Order)', '2026-10-28 12:34:31', '2026-10-28 12:41:09');

-- --------------------------------------------------------

--
-- Table structure for table `web_customers`
--

CREATE TABLE `web_customers` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `customer_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wishlists`
--

CREATE TABLE `wishlists` (
  `id` bigint UNSIGNED NOT NULL,
  `web_customer_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `zones`
--

CREATE TABLE `zones` (
  `id` bigint UNSIGNED NOT NULL,
  `zone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `zones`
--

INSERT INTO `zones` (`id`, `zone`, `created_at`, `updated_at`) VALUES
(1, 'SIALKOT', '2026-10-19 19:11:01', '2026-10-19 19:11:01'),
(2, 'LAHORE', '2026-10-19 19:11:15', '2026-10-19 19:11:15'),
(3, 'KARACHI', '2026-10-19 19:11:29', '2026-10-19 19:11:29'),
(4, 'GUJRANWALA', '2026-10-19 19:11:49', '2026-10-19 19:11:49'),
(5, 'SKP LHR ROAD', '2026-10-19 19:12:41', '2026-10-19 19:12:41'),
(6, 'Wazirabad', '2026-10-24 11:46:26', '2026-10-24 11:46:26'),
(7, 'Rawalpindi', '2026-10-26 12:16:36', '2026-10-26 12:16:36'),
(8, 'Daska', '2026-10-26 20:33:48', '2026-10-26 20:33:48'),
(9, 'Muridke', '2026-10-28 13:23:09', '2026-10-28 13:23:09'),
(10, 'Islamabad', '2026-10-28 19:50:54', '2026-10-28 19:50:54');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `accounts_head_id_foreign` (`head_id`);

--
-- Indexes for table `account_heads`
--
ALTER TABLE `account_heads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `account_heads_parent_id_foreign` (`parent_id`);

--
-- Indexes for table `account_histories`
--
ALTER TABLE `account_histories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `account_histories_account_id_foreign` (`account_id`);

--
-- Indexes for table `biometric_devices`
--
ALTER TABLE `biometric_devices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `biometric_devices_is_active_index` (`is_active`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `branches_user_id_unique` (`user_id`);

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `coupons_code_unique` (`code`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customers_customer_id_unique` (`customer_id`),
  ADD UNIQUE KEY `customers_uuid_unique` (`uuid`);

--
-- Indexes for table `customer_ledgers`
--
ALTER TABLE `customer_ledgers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_ledgers_customer_id_foreign` (`customer_id`);

--
-- Indexes for table `customer_payments`
--
ALTER TABLE `customer_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_payments_customer_id_foreign` (`customer_id`);

--
-- Indexes for table `day_closings`
--
ALTER TABLE `day_closings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `drawer_transactions`
--
ALTER TABLE `drawer_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `drawer_transactions_day_closing_id_foreign` (`day_closing_id`),
  ADD KEY `drawer_transactions_returned_in_closing_id_foreign` (`returned_in_closing_id`);

--
-- Indexes for table `ecommerce_orders`
--
ALTER TABLE `ecommerce_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ecommerce_orders_order_number_unique` (`order_number`),
  ADD KEY `ecommerce_orders_web_customer_id_foreign` (`web_customer_id`),
  ADD KEY `ecommerce_orders_coupon_id_foreign` (`coupon_id`);

--
-- Indexes for table `ecommerce_order_items`
--
ALTER TABLE `ecommerce_order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ecommerce_order_items_ecommerce_order_id_foreign` (`ecommerce_order_id`),
  ADD KEY `ecommerce_order_items_product_id_foreign` (`product_id`);

--
-- Indexes for table `employee_salary_structures`
--
ALTER TABLE `employee_salary_structures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_salary_structures_assigned_by_foreign` (`assigned_by`),
  ADD KEY `employee_salary_structures_employee_id_is_active_index` (`employee_id`,`is_active`),
  ADD KEY `employee_salary_structures_salary_structure_id_is_active_index` (`salary_structure_id`,`is_active`),
  ADD KEY `employee_salary_structures_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `expense_categories`
--
ALTER TABLE `expense_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expense_vouchers`
--
ALTER TABLE `expense_vouchers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `grn_items`
--
ALTER TABLE `grn_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `grn_items_grn_id_foreign` (`grn_id`),
  ADD KEY `grn_items_purchase_item_id_foreign` (`purchase_item_id`),
  ADD KEY `grn_items_product_id_foreign` (`product_id`),
  ADD KEY `grn_items_warehouse_id_foreign` (`warehouse_id`);

--
-- Indexes for table `grn_receipts`
--
ALTER TABLE `grn_receipts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `grn_receipts_grn_number_unique` (`grn_number`),
  ADD KEY `grn_receipts_purchase_id_foreign` (`purchase_id`),
  ADD KEY `grn_receipts_received_by_foreign` (`received_by`),
  ADD KEY `grn_receipts_store_incharge_id_foreign` (`store_incharge_id`);

--
-- Indexes for table `hr_attendances`
--
ALTER TABLE `hr_attendances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hr_attendances_employee_id_foreign` (`employee_id`);

--
-- Indexes for table `hr_departments`
--
ALTER TABLE `hr_departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hr_designations`
--
ALTER TABLE `hr_designations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hr_designations_name_unique` (`name`);

--
-- Indexes for table `hr_employees`
--
ALTER TABLE `hr_employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hr_employees_email_unique` (`email`),
  ADD KEY `hr_employees_user_id_foreign` (`user_id`),
  ADD KEY `hr_employees_department_id_foreign` (`department_id`),
  ADD KEY `hr_employees_designation_id_foreign` (`designation_id`),
  ADD KEY `hr_employees_shift_id_foreign` (`shift_id`),
  ADD KEY `hr_employees_biometric_device_id_foreign` (`biometric_device_id`),
  ADD KEY `hr_employees_device_user_id_index` (`device_user_id`);

--
-- Indexes for table `hr_employee_documents`
--
ALTER TABLE `hr_employee_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hr_employee_documents_employee_id_foreign` (`employee_id`);

--
-- Indexes for table `hr_holidays`
--
ALTER TABLE `hr_holidays`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hr_holidays_date_unique` (`date`);

--
-- Indexes for table `hr_leaves`
--
ALTER TABLE `hr_leaves`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hr_leaves_employee_id_foreign` (`employee_id`);

--
-- Indexes for table `hr_loans`
--
ALTER TABLE `hr_loans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hr_loans_employee_id_foreign` (`employee_id`);

--
-- Indexes for table `hr_loan_payments`
--
ALTER TABLE `hr_loan_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hr_loan_payments_loan_id_foreign` (`loan_id`);

--
-- Indexes for table `hr_loan_scheduled_deductions`
--
ALTER TABLE `hr_loan_scheduled_deductions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hr_loan_scheduled_deductions_loan_id_foreign` (`loan_id`);

--
-- Indexes for table `hr_payrolls`
--
ALTER TABLE `hr_payrolls`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hr_payrolls_employee_id_foreign` (`employee_id`),
  ADD KEY `hr_payrolls_reviewed_by_foreign` (`reviewed_by`),
  ADD KEY `hr_payrolls_account_id_foreign` (`account_id`);

--
-- Indexes for table `hr_payroll_details`
--
ALTER TABLE `hr_payroll_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hr_payroll_details_payroll_id_foreign` (`payroll_id`);

--
-- Indexes for table `hr_salary_structures`
--
ALTER TABLE `hr_salary_structures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hr_salary_structures_employee_id_index` (`employee_id`),
  ADD KEY `hr_salary_structures_parent_structure_id_foreign` (`parent_structure_id`);

--
-- Indexes for table `hr_settings`
--
ALTER TABLE `hr_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hr_settings_key_unique` (`key`);

--
-- Indexes for table `hr_shifts`
--
ALTER TABLE `hr_shifts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `import_shipment_costs`
--
ALTER TABLE `import_shipment_costs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `import_shipment_costs_purchase_id_foreign` (`purchase_id`);

--
-- Indexes for table `invoice_series`
--
ALTER TABLE `invoice_series`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_series_prefix_unique` (`prefix`);

--
-- Indexes for table `inward_gatepasses`
--
ALTER TABLE `inward_gatepasses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inward_gatepass_items`
--
ALTER TABLE `inward_gatepass_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inward_gatepass_items_inward_gatepass_id_foreign` (`inward_gatepass_id`);

--
-- Indexes for table `journal_entries`
--
ALTER TABLE `journal_entries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `journal_entries_source_type_source_id_index` (`source_type`,`source_id`),
  ADD KEY `journal_entries_account_id_entry_date_index` (`account_id`,`entry_date`),
  ADD KEY `journal_entries_entry_date_index` (`entry_date`),
  ADD KEY `journal_entries_party_type_party_id_index` (`party_type`,`party_id`);

--
-- Indexes for table `material_usages`
--
ALTER TABLE `material_usages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `material_usages_usage_no_unique` (`usage_no`),
  ADD KEY `material_usages_warehouse_id_foreign` (`warehouse_id`),
  ADD KEY `material_usages_user_id_foreign` (`user_id`),
  ADD KEY `material_usages_date_warehouse_id_index` (`date`,`warehouse_id`);

--
-- Indexes for table `material_usage_items`
--
ALTER TABLE `material_usage_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `material_usage_items_product_id_foreign` (`product_id`),
  ADD KEY `material_usage_items_material_usage_id_product_id_index` (`material_usage_id`,`product_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `modules_name_unique` (`name`);

--
-- Indexes for table `narrations`
--
ALTER TABLE `narrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indexes for table `package_types`
--
ALTER TABLE `package_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `package_types_name_unique` (`name`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payment_vouchers`
--
ALTER TABLE `payment_vouchers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `products_brand_id_foreign` (`brand_id`),
  ADD KEY `products_item_type_index` (`item_type`);

--
-- Indexes for table `product_bookings`
--
ALTER TABLE `product_bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_discounts`
--
ALTER TABLE `product_discounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_discounts_product_id_foreign` (`product_id`);

--
-- Indexes for table `product_web_images`
--
ALTER TABLE `product_web_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_web_images_product_id_foreign` (`product_id`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchases_pr_id_foreign` (`pr_id`),
  ADD KEY `purchases_created_by_foreign` (`created_by`);

--
-- Indexes for table `purchase_items`
--
ALTER TABLE `purchase_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_items_product_id_index` (`product_id`);

--
-- Indexes for table `purchase_requisitions`
--
ALTER TABLE `purchase_requisitions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `purchase_requisitions_pr_number_unique` (`pr_number`),
  ADD KEY `purchase_requisitions_branch_id_foreign` (`branch_id`),
  ADD KEY `purchase_requisitions_warehouse_id_foreign` (`warehouse_id`),
  ADD KEY `purchase_requisitions_requested_by_foreign` (`requested_by`),
  ADD KEY `purchase_requisitions_approved_by_foreign` (`approved_by`);

--
-- Indexes for table `purchase_requisition_items`
--
ALTER TABLE `purchase_requisition_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_requisition_items_pr_id_foreign` (`pr_id`),
  ADD KEY `purchase_requisition_items_product_id_foreign` (`product_id`);

--
-- Indexes for table `purchase_returns`
--
ALTER TABLE `purchase_returns`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `purchase_returns_return_invoice_unique` (`return_invoice`),
  ADD KEY `purchase_returns_vendor_id_foreign` (`vendor_id`),
  ADD KEY `purchase_returns_warehouse_id_foreign` (`warehouse_id`),
  ADD KEY `purchase_returns_purchase_id_foreign` (`purchase_id`);

--
-- Indexes for table `purchase_return_items`
--
ALTER TABLE `purchase_return_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_return_items_purchase_return_id_foreign` (`purchase_return_id`),
  ADD KEY `purchase_return_items_product_id_foreign` (`product_id`);

--
-- Indexes for table `receipts_vouchers`
--
ALTER TABLE `receipts_vouchers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `repair_orders`
--
ALTER TABLE `repair_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `repair_orders_repair_no_unique` (`repair_no`),
  ADD KEY `repair_orders_customer_id_index` (`customer_id`),
  ADD KEY `repair_orders_product_id_index` (`product_id`),
  ADD KEY `repair_orders_advance_account_id_index` (`advance_account_id`),
  ADD KEY `repair_orders_final_account_id_index` (`final_account_id`),
  ADD KEY `repair_orders_status_index` (`status`),
  ADD KEY `repair_orders_received_by_index` (`received_by`),
  ADD KEY `repair_orders_delivered_by_index` (`delivered_by`);

--
-- Indexes for table `repair_order_logs`
--
ALTER TABLE `repair_order_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `repair_order_logs_repair_order_id_index` (`repair_order_id`),
  ADD KEY `repair_order_logs_user_id_index` (`user_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sales_invoice_no_unique` (`invoice_no`),
  ADD UNIQUE KEY `sales_uuid_unique` (`uuid`),
  ADD KEY `sales_due_date_index` (`due_date`);

--
-- Indexes for table `sales_officers`
--
ALTER TABLE `sales_officers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_items_sale_id_foreign` (`sale_id`);

--
-- Indexes for table `sale_returns`
--
ALTER TABLE `sale_returns`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sale_returns_return_invoice_unique` (`return_invoice`),
  ADD KEY `sale_returns_sale_id_foreign` (`sale_id`),
  ADD KEY `sale_returns_customer_id_foreign` (`customer_id`),
  ADD KEY `sale_returns_warehouse_id_foreign` (`warehouse_id`);

--
-- Indexes for table `sale_return_items`
--
ALTER TABLE `sale_return_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_return_items_sale_return_id_foreign` (`sale_return_id`),
  ADD KEY `sale_return_items_product_id_foreign` (`product_id`),
  ADD KEY `sale_return_items_warehouse_id_foreign` (`warehouse_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_key_unique` (`key`);

--
-- Indexes for table `stocks`
--
ALTER TABLE `stocks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `stocks_unique_triplet` (`branch_id`,`warehouse_id`,`product_id`);

--
-- Indexes for table `stock_adjustments`
--
ALTER TABLE `stock_adjustments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_adjustments_user_id_foreign` (`user_id`),
  ADD KEY `stock_adjustments_warehouse_id_foreign` (`warehouse_id`),
  ADD KEY `stock_adjustments_product_id_foreign` (`product_id`);

--
-- Indexes for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sm_product_created` (`product_id`,`created_at`),
  ADD KEY `stock_movements_ref_uuid_index` (`ref_uuid`);

--
-- Indexes for table `stock_transfers`
--
ALTER TABLE `stock_transfers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_transfers_from_warehouse_id_foreign` (`from_warehouse_id`),
  ADD KEY `stock_transfers_to_warehouse_id_foreign` (`to_warehouse_id`),
  ADD KEY `stock_transfers_product_id_foreign` (`product_id`);

--
-- Indexes for table `subcategories`
--
ALTER TABLE `subcategories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subcategories_category_id_foreign` (`category_id`);

--
-- Indexes for table `system_notifications`
--
ALTER TABLE `system_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `system_notifications_user_id_is_read_index` (`user_id`,`is_read`),
  ADD KEY `system_notifications_type_created_at_index` (`type`,`created_at`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `system_settings_key_unique` (`key`);

--
-- Indexes for table `transports`
--
ALTER TABLE `transports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `vendors`
--
ALTER TABLE `vendors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vendor_bilties`
--
ALTER TABLE `vendor_bilties`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_bilties_vendor_id_foreign` (`vendor_id`),
  ADD KEY `vendor_bilties_purchase_id_foreign` (`purchase_id`);

--
-- Indexes for table `vendor_ledgers`
--
ALTER TABLE `vendor_ledgers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_ledgers_vendor_id_foreign` (`vendor_id`);

--
-- Indexes for table `vendor_payments`
--
ALTER TABLE `vendor_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_payments_vendor_id_foreign` (`vendor_id`);

--
-- Indexes for table `vouchers`
--
ALTER TABLE `vouchers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `voucher_details`
--
ALTER TABLE `voucher_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `voucher_details_voucher_master_id_foreign` (`voucher_master_id`),
  ADD KEY `voucher_details_account_id_foreign` (`account_id`);

--
-- Indexes for table `voucher_masters`
--
ALTER TABLE `voucher_masters`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `voucher_masters_voucher_no_unique` (`voucher_no`),
  ADD KEY `voucher_masters_party_type_party_id_index` (`party_type`,`party_id`),
  ADD KEY `voucher_masters_voucher_type_index` (`voucher_type`),
  ADD KEY `voucher_masters_status_index` (`status`);

--
-- Indexes for table `warehouses`
--
ALTER TABLE `warehouses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `warehouses_creater_id_index` (`creater_id`);

--
-- Indexes for table `warehouse_stocks`
--
ALTER TABLE `warehouse_stocks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `warehouse_stocks_warehouse_id_foreign` (`warehouse_id`),
  ADD KEY `warehouse_stocks_product_id_foreign` (`product_id`);

--
-- Indexes for table `web_customers`
--
ALTER TABLE `web_customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `web_customers_email_unique` (`email`);

--
-- Indexes for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `wishlists_web_customer_id_product_id_unique` (`web_customer_id`,`product_id`),
  ADD KEY `wishlists_product_id_foreign` (`product_id`);

--
-- Indexes for table `zones`
--
ALTER TABLE `zones`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `account_heads`
--
ALTER TABLE `account_heads`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `account_histories`
--
ALTER TABLE `account_histories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `biometric_devices`
--
ALTER TABLE `biometric_devices`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `customer_ledgers`
--
ALTER TABLE `customer_ledgers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `customer_payments`
--
ALTER TABLE `customer_payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `day_closings`
--
ALTER TABLE `day_closings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `drawer_transactions`
--
ALTER TABLE `drawer_transactions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ecommerce_orders`
--
ALTER TABLE `ecommerce_orders`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ecommerce_order_items`
--
ALTER TABLE `ecommerce_order_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_salary_structures`
--
ALTER TABLE `employee_salary_structures`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expense_categories`
--
ALTER TABLE `expense_categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `expense_vouchers`
--
ALTER TABLE `expense_vouchers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grn_items`
--
ALTER TABLE `grn_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grn_receipts`
--
ALTER TABLE `grn_receipts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_attendances`
--
ALTER TABLE `hr_attendances`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_departments`
--
ALTER TABLE `hr_departments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `hr_designations`
--
ALTER TABLE `hr_designations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `hr_employees`
--
ALTER TABLE `hr_employees`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `hr_employee_documents`
--
ALTER TABLE `hr_employee_documents`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_holidays`
--
ALTER TABLE `hr_holidays`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_leaves`
--
ALTER TABLE `hr_leaves`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_loans`
--
ALTER TABLE `hr_loans`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_loan_payments`
--
ALTER TABLE `hr_loan_payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_loan_scheduled_deductions`
--
ALTER TABLE `hr_loan_scheduled_deductions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_payrolls`
--
ALTER TABLE `hr_payrolls`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_payroll_details`
--
ALTER TABLE `hr_payroll_details`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_salary_structures`
--
ALTER TABLE `hr_salary_structures`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `hr_settings`
--
ALTER TABLE `hr_settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_shifts`
--
ALTER TABLE `hr_shifts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `import_shipment_costs`
--
ALTER TABLE `import_shipment_costs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoice_series`
--
ALTER TABLE `invoice_series`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `inward_gatepasses`
--
ALTER TABLE `inward_gatepasses`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inward_gatepass_items`
--
ALTER TABLE `inward_gatepass_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `journal_entries`
--
ALTER TABLE `journal_entries`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=196;

--
-- AUTO_INCREMENT for table `material_usages`
--
ALTER TABLE `material_usages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `material_usage_items`
--
ALTER TABLE `material_usage_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=200;

--
-- AUTO_INCREMENT for table `modules`
--
ALTER TABLE `modules`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `narrations`
--
ALTER TABLE `narrations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `package_types`
--
ALTER TABLE `package_types`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_vouchers`
--
ALTER TABLE `payment_vouchers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=308;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `product_bookings`
--
ALTER TABLE `product_bookings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_discounts`
--
ALTER TABLE `product_discounts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_web_images`
--
ALTER TABLE `product_web_images`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `purchase_items`
--
ALTER TABLE `purchase_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `purchase_requisitions`
--
ALTER TABLE `purchase_requisitions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_requisition_items`
--
ALTER TABLE `purchase_requisition_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_returns`
--
ALTER TABLE `purchase_returns`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_return_items`
--
ALTER TABLE `purchase_return_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `receipts_vouchers`
--
ALTER TABLE `receipts_vouchers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `repair_orders`
--
ALTER TABLE `repair_orders`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `repair_order_logs`
--
ALTER TABLE `repair_order_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `sales_officers`
--
ALTER TABLE `sales_officers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sale_items`
--
ALTER TABLE `sale_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `sale_returns`
--
ALTER TABLE `sale_returns`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sale_return_items`
--
ALTER TABLE `sale_return_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `stocks`
--
ALTER TABLE `stocks`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_adjustments`
--
ALTER TABLE `stock_adjustments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_movements`
--
ALTER TABLE `stock_movements`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=141;

--
-- AUTO_INCREMENT for table `stock_transfers`
--
ALTER TABLE `stock_transfers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subcategories`
--
ALTER TABLE `subcategories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `system_notifications`
--
ALTER TABLE `system_notifications`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transports`
--
ALTER TABLE `transports`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `vendors`
--
ALTER TABLE `vendors`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `vendor_bilties`
--
ALTER TABLE `vendor_bilties`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vendor_ledgers`
--
ALTER TABLE `vendor_ledgers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `vendor_payments`
--
ALTER TABLE `vendor_payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vouchers`
--
ALTER TABLE `vouchers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `voucher_details`
--
ALTER TABLE `voucher_details`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=182;

--
-- AUTO_INCREMENT for table `voucher_masters`
--
ALTER TABLE `voucher_masters`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `warehouses`
--
ALTER TABLE `warehouses`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `warehouse_stocks`
--
ALTER TABLE `warehouse_stocks`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `web_customers`
--
ALTER TABLE `web_customers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wishlists`
--
ALTER TABLE `wishlists`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `zones`
--
ALTER TABLE `zones`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

-- --------------------------------------------------------

--
-- Structure for view `v_stock_onhand`
--
DROP TABLE IF EXISTS `v_stock_onhand`;

CREATE ALGORITHM=UNDEFINED DEFINER=`binsult1_logictech`@`localhost` SQL SECURITY DEFINER VIEW `v_stock_onhand`  AS SELECT `stock_movements`.`product_id` AS `product_id`, round(coalesce(sum((case when (`stock_movements`.`type` in ('in','assembly_in')) then abs(`stock_movements`.`qty`) when (`stock_movements`.`type` in ('out','assembly_out')) then -(abs(`stock_movements`.`qty`)) when (`stock_movements`.`type` = 'adjustment') then `stock_movements`.`qty` else 0 end)),0),3) AS `onhand_qty` FROM `stock_movements` GROUP BY `stock_movements`.`product_id` ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accounts`
--
ALTER TABLE `accounts`
  ADD CONSTRAINT `accounts_head_id_foreign` FOREIGN KEY (`head_id`) REFERENCES `account_heads` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `account_heads`
--
ALTER TABLE `account_heads`
  ADD CONSTRAINT `account_heads_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `account_heads` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `account_histories`
--
ALTER TABLE `account_histories`
  ADD CONSTRAINT `account_histories_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `customer_ledgers`
--
ALTER TABLE `customer_ledgers`
  ADD CONSTRAINT `customer_ledgers_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `customer_payments`
--
ALTER TABLE `customer_payments`
  ADD CONSTRAINT `customer_payments_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `drawer_transactions`
--
ALTER TABLE `drawer_transactions`
  ADD CONSTRAINT `drawer_transactions_day_closing_id_foreign` FOREIGN KEY (`day_closing_id`) REFERENCES `day_closings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `drawer_transactions_returned_in_closing_id_foreign` FOREIGN KEY (`returned_in_closing_id`) REFERENCES `day_closings` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ecommerce_orders`
--
ALTER TABLE `ecommerce_orders`
  ADD CONSTRAINT `ecommerce_orders_coupon_id_foreign` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ecommerce_orders_web_customer_id_foreign` FOREIGN KEY (`web_customer_id`) REFERENCES `web_customers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ecommerce_order_items`
--
ALTER TABLE `ecommerce_order_items`
  ADD CONSTRAINT `ecommerce_order_items_ecommerce_order_id_foreign` FOREIGN KEY (`ecommerce_order_id`) REFERENCES `ecommerce_orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ecommerce_order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `employee_salary_structures`
--
ALTER TABLE `employee_salary_structures`
  ADD CONSTRAINT `employee_salary_structures_assigned_by_foreign` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `employee_salary_structures_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `hr_employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_salary_structures_salary_structure_id_foreign` FOREIGN KEY (`salary_structure_id`) REFERENCES `hr_salary_structures` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_salary_structures_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `grn_items`
--
ALTER TABLE `grn_items`
  ADD CONSTRAINT `grn_items_grn_id_foreign` FOREIGN KEY (`grn_id`) REFERENCES `grn_receipts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `grn_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `grn_items_purchase_item_id_foreign` FOREIGN KEY (`purchase_item_id`) REFERENCES `purchase_items` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `grn_items_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`);

--
-- Constraints for table `grn_receipts`
--
ALTER TABLE `grn_receipts`
  ADD CONSTRAINT `grn_receipts_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `grn_receipts_received_by_foreign` FOREIGN KEY (`received_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `grn_receipts_store_incharge_id_foreign` FOREIGN KEY (`store_incharge_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `hr_attendances`
--
ALTER TABLE `hr_attendances`
  ADD CONSTRAINT `hr_attendances_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `hr_employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hr_employees`
--
ALTER TABLE `hr_employees`
  ADD CONSTRAINT `hr_employees_biometric_device_id_foreign` FOREIGN KEY (`biometric_device_id`) REFERENCES `biometric_devices` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hr_employees_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `hr_departments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hr_employees_designation_id_foreign` FOREIGN KEY (`designation_id`) REFERENCES `hr_designations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hr_employees_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `hr_shifts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hr_employees_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `hr_employee_documents`
--
ALTER TABLE `hr_employee_documents`
  ADD CONSTRAINT `hr_employee_documents_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `hr_employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hr_leaves`
--
ALTER TABLE `hr_leaves`
  ADD CONSTRAINT `hr_leaves_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `hr_employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hr_loans`
--
ALTER TABLE `hr_loans`
  ADD CONSTRAINT `hr_loans_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `hr_employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hr_loan_payments`
--
ALTER TABLE `hr_loan_payments`
  ADD CONSTRAINT `hr_loan_payments_loan_id_foreign` FOREIGN KEY (`loan_id`) REFERENCES `hr_loans` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hr_loan_scheduled_deductions`
--
ALTER TABLE `hr_loan_scheduled_deductions`
  ADD CONSTRAINT `hr_loan_scheduled_deductions_loan_id_foreign` FOREIGN KEY (`loan_id`) REFERENCES `hr_loans` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hr_payrolls`
--
ALTER TABLE `hr_payrolls`
  ADD CONSTRAINT `hr_payrolls_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hr_payrolls_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `hr_employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hr_payrolls_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `hr_payroll_details`
--
ALTER TABLE `hr_payroll_details`
  ADD CONSTRAINT `hr_payroll_details_payroll_id_foreign` FOREIGN KEY (`payroll_id`) REFERENCES `hr_payrolls` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hr_salary_structures`
--
ALTER TABLE `hr_salary_structures`
  ADD CONSTRAINT `hr_salary_structures_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `hr_employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hr_salary_structures_parent_structure_id_foreign` FOREIGN KEY (`parent_structure_id`) REFERENCES `hr_salary_structures` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `import_shipment_costs`
--
ALTER TABLE `import_shipment_costs`
  ADD CONSTRAINT `import_shipment_costs_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inward_gatepass_items`
--
ALTER TABLE `inward_gatepass_items`
  ADD CONSTRAINT `inward_gatepass_items_inward_gatepass_id_foreign` FOREIGN KEY (`inward_gatepass_id`) REFERENCES `inward_gatepasses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `journal_entries`
--
ALTER TABLE `journal_entries`
  ADD CONSTRAINT `journal_entries_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `material_usages`
--
ALTER TABLE `material_usages`
  ADD CONSTRAINT `material_usages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `material_usages_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `material_usage_items`
--
ALTER TABLE `material_usage_items`
  ADD CONSTRAINT `material_usage_items_material_usage_id_foreign` FOREIGN KEY (`material_usage_id`) REFERENCES `material_usages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `material_usage_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `product_discounts`
--
ALTER TABLE `product_discounts`
  ADD CONSTRAINT `product_discounts_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_web_images`
--
ALTER TABLE `product_web_images`
  ADD CONSTRAINT `product_web_images_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchases`
--
ALTER TABLE `purchases`
  ADD CONSTRAINT `purchases_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `purchases_pr_id_foreign` FOREIGN KEY (`pr_id`) REFERENCES `purchase_requisitions` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `purchase_requisitions`
--
ALTER TABLE `purchase_requisitions`
  ADD CONSTRAINT `purchase_requisitions_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `purchase_requisitions_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_requisitions_requested_by_foreign` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `purchase_requisitions_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_requisition_items`
--
ALTER TABLE `purchase_requisition_items`
  ADD CONSTRAINT `purchase_requisition_items_pr_id_foreign` FOREIGN KEY (`pr_id`) REFERENCES `purchase_requisitions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_requisition_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_returns`
--
ALTER TABLE `purchase_returns`
  ADD CONSTRAINT `purchase_returns_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `purchase_returns_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_returns_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `purchase_return_items`
--
ALTER TABLE `purchase_return_items`
  ADD CONSTRAINT `purchase_return_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_return_items_purchase_return_id_foreign` FOREIGN KEY (`purchase_return_id`) REFERENCES `purchase_returns` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `repair_order_logs`
--
ALTER TABLE `repair_order_logs`
  ADD CONSTRAINT `repair_order_logs_repair_order_id_foreign` FOREIGN KEY (`repair_order_id`) REFERENCES `repair_orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD CONSTRAINT `sale_items_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sale_returns`
--
ALTER TABLE `sale_returns`
  ADD CONSTRAINT `sale_returns_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sale_returns_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sale_returns_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sale_return_items`
--
ALTER TABLE `sale_return_items`
  ADD CONSTRAINT `sale_return_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sale_return_items_sale_return_id_foreign` FOREIGN KEY (`sale_return_id`) REFERENCES `sale_returns` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sale_return_items_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stock_adjustments`
--
ALTER TABLE `stock_adjustments`
  ADD CONSTRAINT `stock_adjustments_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_adjustments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `stock_adjustments_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD CONSTRAINT `stock_movements_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stock_transfers`
--
ALTER TABLE `stock_transfers`
  ADD CONSTRAINT `stock_transfers_from_warehouse_id_foreign` FOREIGN KEY (`from_warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_transfers_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_transfers_to_warehouse_id_foreign` FOREIGN KEY (`to_warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subcategories`
--
ALTER TABLE `subcategories`
  ADD CONSTRAINT `subcategories_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `system_notifications`
--
ALTER TABLE `system_notifications`
  ADD CONSTRAINT `system_notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vendor_bilties`
--
ALTER TABLE `vendor_bilties`
  ADD CONSTRAINT `vendor_bilties_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `vendor_bilties_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vendor_ledgers`
--
ALTER TABLE `vendor_ledgers`
  ADD CONSTRAINT `vendor_ledgers_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vendor_payments`
--
ALTER TABLE `vendor_payments`
  ADD CONSTRAINT `vendor_payments_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `voucher_details`
--
ALTER TABLE `voucher_details`
  ADD CONSTRAINT `voucher_details_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `voucher_details_voucher_master_id_foreign` FOREIGN KEY (`voucher_master_id`) REFERENCES `voucher_masters` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `warehouse_stocks`
--
ALTER TABLE `warehouse_stocks`
  ADD CONSTRAINT `warehouse_stocks_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `warehouse_stocks_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD CONSTRAINT `wishlists_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlists_web_customer_id_foreign` FOREIGN KEY (`web_customer_id`) REFERENCES `web_customers` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
