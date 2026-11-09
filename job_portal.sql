-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 31, 2025 at 06:37 PM
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
-- Database: `job_portal`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `unit_rate` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `user_id`, `password`, `unit_rate`) VALUES
(1, 'admin', '1234', 0);

-- --------------------------------------------------------

--
-- Table structure for table `blogs`
--

CREATE TABLE `blogs` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `excerpt` text DEFAULT NULL,
  `image_path` varchar(500) DEFAULT NULL,
  `author` varchar(100) NOT NULL,
  `status` enum('draft','published') NOT NULL DEFAULT 'draft',
  `slug` varchar(255) NOT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `blogs`
--

INSERT INTO `blogs` (`id`, `title`, `content`, `excerpt`, `image_path`, `author`, `status`, `slug`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(1, 'Dharashiv Job Alert', 'Urgent recruitment', 'Urgent recruitment...', 'uploads/blogs/1760254691_slider-image-2-1920x900.jpg', 'Suraj Shinde', 'published', 'dharashiv-job-alert-1760254691', NULL, NULL, '2025-10-12 07:38:11', '2025-10-12 07:38:11'),
(2, 'Mastering Resume Writing in 2025', 'Learn how to craft a resume that stands out in the AI-driven hiring world. Includes ATS optimization tips and layout tricks.', 'Learn how to craft a resume that stands out in the AI-driven hiring world. Includes ATS optimization tips and layout tricks....', 'uploads/blogs/1760274452_maxresdefault.jpg', 'Priya Sharma', 'published', 'mastering-resume-writing-in-2025-1760274452', NULL, NULL, '2025-10-12 13:07:32', '2025-10-12 13:07:32');

-- --------------------------------------------------------

--
-- Table structure for table `job_seekers`
--

CREATE TABLE `job_seekers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_picture` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `core_industry` varchar(10) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `subcategory` varchar(50) DEFAULT NULL,
  `selected_skills` text DEFAULT NULL,
  `project_management_exp` enum('yes','no') DEFAULT NULL,
  `project_management_skills` text DEFAULT NULL,
  `cross_industry_exp` enum('yes','no') DEFAULT NULL,
  `us_experience` text DEFAULT NULL,
  `india_experience` text DEFAULT NULL,
  `certificates` text DEFAULT NULL,
  `cover_photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `job_seekers`
--

INSERT INTO `job_seekers` (`id`, `user_id`, `fullname`, `email`, `username`, `password`, `created_at`, `profile_picture`, `phone`, `bio`, `core_industry`, `category`, `subcategory`, `selected_skills`, `project_management_exp`, `project_management_skills`, `cross_industry_exp`, `us_experience`, `india_experience`, `certificates`, `cover_photo`) VALUES
(22, 0, 'Ajay Rathod', 'rathodajay7624@gmail.com', 'Ajay', '$2y$10$hIGFvOGJvHUj8wJs7IqaFOzclDNAA9.h8.L32P3KDvSGK9wTENaYe', '2025-10-21 11:57:14', 'uploads/profiles/profile_68f77619599c4.png', '9607170795', 'Admin', 'CS', '', '', '[\"Web Development (Frontend, Backend, Full-Stack)\",\"Mobile App Development (Android, iOS, Cross-platform)\",\"Desktop \\/ Enterprise Software Development\",\"Embedded Systems \\/ IoT Development\",\"C, C++, Java\",\"Python, R\",\"JavaScript (Node.js, React, Angular, Vue)\",\"PHP, Ruby, Go, Rust\",\"Swift, Kotlin, Dart\\/Flutter\",\".NET \\/ C#\",\"Node.js\",\"TensorFlow\"]', 'yes', 'still working on it ', 'no', '[{\"employer\":\"\",\"from\":\"\",\"to\":\"\",\"state\":\"\",\"city\":\"\"}]', '[{\"employer\":\"\",\"from\":\"\",\"to\":\"\",\"state\":\"\",\"city\":\"\"}]', '[\"uploads\\/certificates\\/cert_68f776195a610.png\"]', 'uploads/covers/cover_22_68f776598b791.jpg'),
(23, 0, 'snt', 'saumitra0@gmail.com', 'SNT23Oct', '$2y$10$3HxMhharAmJztoMRSOFc1ufwtCz5xaeYMcLP.SX6bLVbqJeJMlsUG', '2025-10-22 18:45:07', '', '', '', 'Mechanical', 'mech_specializations', 'mech_design_dev', '[\"Ruby\",\"Bash \\/ Shell\",\"PowerShell\",\"Xamarin\",\"Flutter\",\"CAD \\/ 3D Modelling\",\"Reverse Engineering\"]', 'no', '', 'no', '[{\"employer\":\"\",\"from\":\"\",\"to\":\"\",\"state\":\"\",\"city\":\"\"}]', '[{\"employer\":\"\",\"from\":\"\",\"to\":\"\",\"state\":\"\",\"city\":\"\"}]', '[]', NULL),
(24, 0, 'Ajay Rathod', '1234@gmail.com', 'Ajay Rathod', '$2y$10$ULA3EeDovvFr1jKlH8jWn.hMQsiVapHQ3aaZpDf8ZT75BwiRTJKIq', '2025-10-28 07:01:38', 'uploads/profiles/profile_69006cb005aa8.jpg', '09607170795', 'i am a jr software developer', 'IT', '', '', '[\"C\",\"C++\",\"C#\",\"Java\",\"Python\",\"Web Development (Frontend, Backend, Full-Stack)\",\"Mobile App Development (Android, iOS, Cross-platform)\",\"Embedded Systems \\/ IoT Development\"]', 'no', '', 'no', '[{\"employer\":\"\",\"from\":\"\",\"to\":\"\",\"state\":\"\",\"city\":\"\"}]', '[{\"employer\":\"\",\"from\":\"\",\"to\":\"\",\"state\":\"\",\"city\":\"\"}]', '[]', 'uploads/covers/cover_24_69006ce3b4513.jpg'),
(25, 0, 'Pruthviraj Kapse', 'kapsepruthviraj732@gmail.com', 'Pruthviraj', '$2y$10$VXmWw0C2.RG3pKNQHcfc3e0VekFjUa0V4WCkE57uoj9RXd73x2DIa', '2025-10-31 09:02:38', 'uploads/profiles/profile_69047bb5d9c0f.jpg', '9529361465', 'bla bla', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/covers/cover_25_69047bcaf1e9a.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `lending`
--

CREATE TABLE `lending` (
  `id` int(10) UNSIGNED NOT NULL,
  `UserId` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `date_of_lending` date NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `status` enum('pending','received') NOT NULL,
  `current_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lending`
--

INSERT INTO `lending` (`id`, `UserId`, `name`, `date_of_lending`, `amount`, `description`, `status`, `current_time`) VALUES
(4, 2, 'Sohel', '2025-10-27', 80.00, 'te gadit tikit dil hot chutya c ', 'pending', '2025-10-27 02:10:17'),
(5, 2, 'cafe Dharashiv', '2025-10-27', 300.00, 'chutyaa aapa ', 'pending', '2025-10-27 02:11:00');

-- --------------------------------------------------------

--
-- Table structure for table `ods`
--

CREATE TABLE `ods` (
  `id` int(255) NOT NULL,
  `ots` int(255) NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `ods`
--

INSERT INTO `ods` (`id`, `ots`, `user_id`, `uuid`, `status`, `date`) VALUES
(1, 872152, 'warehouse', '679cb6ccd601b', 'unverified', '2025-01-31 11:41:00'),
(2, 928584, 'warehouse', '679cb7b64a97f', 'unverified', '2025-01-31 11:44:54'),
(3, 847400, 'warehouse', '679cb7fc285df', 'unverified', '2025-01-31 11:46:04'),
(4, 703550, 'warehouse', '679cb82bc71b9', 'unverified', '2025-01-31 11:46:51'),
(5, 766132, 'warehouse', '679cba179f251', 'unverified', '2025-01-31 11:55:03'),
(6, 781229, 'warehouse', '679dc81cc3393', 'unverified', '2025-02-01 07:07:08'),
(7, 642179, 'warehouse', '67a1d89e0b1b3', 'unverified', '2025-02-04 09:06:38'),
(8, 668729, 'warehouse', '67a358c2259b7', 'unverified', '2025-02-05 12:25:38'),
(9, 181227, 'warehouse', '67a4e5beb9009', 'unverified', '2025-02-06 16:39:26'),
(10, 800366, 'warehouse', '67beeb8134d4d', 'unverified', '2025-02-26 10:22:57'),
(11, 404987, 'warehouse', '67c13f19c22af', 'unverified', '2025-02-28 04:44:09'),
(12, 316152, 'process', '67c92f301a1af', 'verified', '2025-03-06 05:14:24'),
(13, 879022, 'process', '67cd3543eb707', 'verified', '2025-03-09 06:29:23'),
(14, 501670, 'customer', '67cd4b994e6a3', 'unverified', '2025-03-09 08:04:41'),
(15, 181880, 'customer', '67d9dbc8b58c3', 'unverified', '2025-03-18 20:47:04'),
(16, 678082, 'process', '67d9dbfb6cf14', 'verified', '2025-03-18 20:47:55'),
(17, 983125, 'customer', '67f0e24f739c8', 'unverified', '2025-04-05 07:57:03'),
(18, 508523, 'customer', '67f11a1273006', 'unverified', '2025-04-05 11:54:58'),
(19, 755187, 'customer', '67f11b2077e23', 'unverified', '2025-04-05 11:59:28'),
(20, 458818, 'demo', '67f124d56b5e2', 'verified', '2025-04-05 12:40:53'),
(21, 306632, 'warehouse', '67f401550b46f', 'unverified', '2025-04-07 16:46:13'),
(22, 905638, 'customer', '67f412dd7f75b', 'unverified', '2025-04-07 18:01:01'),
(23, 375030, 'employee1', '67f413919a77d', 'verified', '2025-04-07 18:04:01'),
(24, 560450, 'customer', '67fa355547718', 'unverified', '2025-04-12 09:41:41'),
(25, 210218, 'sur', '67fa3d6f125ca', 'verified', '2025-04-12 10:16:15'),
(26, 161957, 'warehouse', '681110050ef17', 'unverified', '2025-04-29 17:44:37'),
(27, 391672, 'warehouse', '6830224cad1a0', 'unverified', '2025-05-23 07:22:52'),
(28, 262487, 'warehouse', '68303e2f0dcb2', 'verified', '2025-05-23 09:21:51'),
(29, 467360, 'warehouse', '68306cd1aba96', 'verified', '2025-05-23 12:40:49'),
(30, 258621, 'warehouse', '68306d3497b91', 'verified', '2025-05-23 12:42:28'),
(31, 374044, 'customer', '683ae0180a977', 'unverified', '2025-05-31 10:55:20'),
(32, 698761, 'customer', '683c1a87e89d3', 'unverified', '2025-06-01 09:16:55'),
(33, 516599, 'warehouse', '685a86207f1c9', 'verified', '2025-06-24 11:04:00'),
(34, 238906, 'customer', '688a61f134d0c', 'verified', '2025-07-30 18:18:25'),
(35, 458451, 'customer', '688c4ab9cc8d4', 'verified', '2025-08-01 05:03:53'),
(36, 805900, 'customer', '68be6b931ce7b', 'verified', '2025-09-08 05:37:23'),
(37, 884794, 'admin', '68c160ff2f54c', 'unverified', '2025-09-10 11:29:03'),
(38, 775317, 'admin', '68c165099b0a0', 'unverified', '2025-09-10 11:46:17'),
(39, 464601, 'admin', '68c17299f0e43', 'unverified', '2025-09-10 12:44:09'),
(40, 686851, 'admin', '68c172b5b45f8', 'unverified', '2025-09-10 12:44:37'),
(41, 179251, 'admin', '68c4cb5bbd248', 'unverified', '2025-09-13 01:39:39'),
(42, 559810, 'admin', '68c4d57bd98ff', 'unverified', '2025-09-13 02:22:51'),
(43, 366568, 'admin', '68c55c4fc2d20', 'unverified', '2025-09-13 11:58:07'),
(44, 829055, 'admin', '68c7af811a6cd', 'unverified', '2025-09-15 06:17:37'),
(45, 862303, 'admin', '68d1282fec7cf', 'unverified', '2025-09-22 10:42:55'),
(46, 588472, 'admin', '68d1f28061aa7', 'unverified', '2025-09-23 01:06:08'),
(47, 185766, 'admin', '68d1fd74b4321', 'unverified', '2025-09-23 01:52:52'),
(48, 687473, 'admin', '68d232928f206', 'unverified', '2025-09-23 05:39:30'),
(0, 624746, 'admin', '68d2408d230a9', 'unverified', '2025-09-23 06:39:09'),
(0, 702022, 'admin', '68d2415ed6cc0', 'unverified', '2025-09-23 06:42:38'),
(0, 668129, 'admin', '68d242f6d9480', 'unverified', '2025-09-23 06:49:26'),
(0, 648382, 'admin', '68d243620e8ac', 'unverified', '2025-09-23 06:51:14'),
(0, 381400, 'admin', '68d27e7906d17', 'unverified', '2025-09-23 11:03:21'),
(0, 520192, 'Admin', '68d27edb4e859', 'unverified', '2025-09-23 11:04:59'),
(0, 547669, 'admin', '68d3aa78ab4ff', 'unverified', '2025-09-24 08:23:20'),
(0, 453149, 'admin', '68d5541d51c81', 'verified', '2025-09-25 14:39:25'),
(0, 569740, 'admin', '68d5553b12eff', 'verified', '2025-09-25 14:44:11'),
(0, 286162, 'admin', '68d6481a7dc0b', 'verified', '2025-09-26 08:00:26'),
(0, 812569, 'admin', '68d7bf0d24d87', 'verified', '2025-09-27 10:40:13'),
(0, 520314, 'admin', '68eb18d47575f', 'verified', '2025-10-12 02:56:20'),
(0, 380758, 'admin', '68eb1dbb0816a', 'verified', '2025-10-12 03:17:15'),
(0, 127315, 'admin', '68eb2171d8496', 'verified', '2025-10-12 03:33:05'),
(0, 216134, 'admin', '68eb21ba798a4', 'verified', '2025-10-12 03:34:18'),
(0, 410599, 'admin', '68eb22c446b9d', 'verified', '2025-10-12 03:38:44'),
(0, 149672, 'admin', '68eb59e725c78', 'verified', '2025-10-12 07:33:59'),
(0, 624251, 'admin', '68eb5c9a9437a', 'verified', '2025-10-12 07:45:30'),
(0, 966332, 'admin', '68eba089ac0bc', 'verified', '2025-10-12 12:35:21'),
(0, 371342, 'admin', '68eba77692642', 'verified', '2025-10-12 13:04:54'),
(0, 260093, 'admin', '68ebe439e5de0', 'verified', '2025-10-12 17:24:09'),
(0, 185409, 'admin', '68ee7e0583343', 'verified', '2025-10-14 16:44:53'),
(0, 296054, 'admin', '68f09ce0720be', 'verified', '2025-10-16 07:21:04'),
(0, 914048, 'admin', '68f5bd2ac0f6c', 'verified', '2025-10-20 04:40:10'),
(0, 113358, 'admin', '68f5cb8272ac0', 'verified', '2025-10-20 05:41:22'),
(0, 848702, 'admin', '68f747491904a', 'verified', '2025-10-21 08:41:45'),
(0, 963964, 'admin', '68f925e2349a2', 'verified', '2025-10-22 18:43:46'),
(0, 423139, 'admin', '68fe7c15e45a7', 'verified', '2025-10-26 19:52:53');

-- --------------------------------------------------------

--
-- Table structure for table `portfolios`
--

CREATE TABLE `portfolios` (
  `id` int(11) NOT NULL,
  `profile_type` enum('1','2','3') NOT NULL COMMENT '1=US to India, 2=US exp in India, 3=US student to India',
  `full_name` varchar(255) NOT NULL,
  `current_location` varchar(100) NOT NULL,
  `visa_status` varchar(50) DEFAULT NULL,
  `return_purpose` varchar(255) NOT NULL,
  `linkedin_profile` varchar(500) DEFAULT NULL,
  `technical_strengths` text NOT NULL,
  `personal_strengths` text NOT NULL,
  `resume_path` varchar(500) DEFAULT NULL,
  `portfolio_path` varchar(500) DEFAULT NULL,
  `total_experience` decimal(4,1) NOT NULL,
  `current_designation` varchar(255) NOT NULL,
  `location_preference` text NOT NULL,
  `willing_relocate` enum('Yes','No') NOT NULL,
  `languages_spoken` text NOT NULL,
  `email` varchar(255) NOT NULL,
  `country_code` varchar(10) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `functions_desired` varchar(255) NOT NULL,
  `current_salary` decimal(12,2) NOT NULL,
  `salary_currency` enum('USD','INR') NOT NULL,
  `availability` varchar(50) NOT NULL,
  `citizenship_status` varchar(100) NOT NULL,
  `additional_info` text DEFAULT NULL,
  `status` enum('pending','completed','under_review','approved','rejected') NOT NULL DEFAULT 'pending',
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `portfolios`
--

INSERT INTO `portfolios` (`id`, `profile_type`, `full_name`, `current_location`, `visa_status`, `return_purpose`, `linkedin_profile`, `technical_strengths`, `personal_strengths`, `resume_path`, `portfolio_path`, `total_experience`, `current_designation`, `location_preference`, `willing_relocate`, `languages_spoken`, `email`, `country_code`, `phone_number`, `functions_desired`, `current_salary`, `salary_currency`, `availability`, `citizenship_status`, `additional_info`, `status`, `submitted_at`, `updated_at`, `user_id`) VALUES
(131, '1', 'Ajay Balu Rathod', 'India', 'H1', 'Immigration issues related to visa', 'www.codecrave.site/linkdin', 'software developer', 'nothing', 'uploads/portfolios/68f77844901c1_Ajay_Rathod_Resume.pdf', NULL, 2.0, 'Jr. Software Engineer', '[\"No preference\",\"Andhra Pradesh\",\"Karnataka\",\"Tamil Nadu\",\"Telangana\",\"Maharashtra\"]', 'No', 'HINDI, MARATHI, ENGLISH', 'rathodajay7624@gmail.com', '91', '9607170795', 'Team member', 10000.00, 'INR', 'Within 2 weeks', 'NRI (Indian Citizen)', 'nothing ', 'completed', '2025-10-21 12:05:30', '2025-10-21 12:10:44', 22),
(132, '2', 'Saumitra Thakkar', 'US', 'J1', 'others', 'www.linkedin/saumitra.thakkar', 'strong on technicalities', 'strong muscles', 'uploads/portfolios/68f92746f0c33_Near_Miss_Poster__2_.pdf', NULL, 7.0, 'partner', '[\"Telangana\"]', 'Yes', 'marathi hindi', '', '91', '7738777888', 'Project manager', 500000.00, 'USD', 'Immediate', 'NRI (Indian Citizen)', 'no', 'rejected', '2025-10-22 18:47:25', '2025-10-26 19:53:14', 23),
(252, '', 'ajay rathod', 'US', 'F1', 'Laid off', 'www.codecrave.site/linkdin', 'lgglg', 'sddffksdf', NULL, NULL, 4.0, 'STUDENT', '[\"Andhra Pradesh\",\"Karnataka\",\"Tamil Nadu\"]', 'Yes', 'hindi', 'random@gmail.com', '91', '9607170795', 'Individual contributor', 8.00, 'USD', 'Within 2 weeks', 'NRI (Indian Citizen)', '', '', '2025-10-31 08:22:37', '2025-10-31 08:22:37', 22),
(262, '2', 'Elon Musk', 'India', 'J1', 'Immigration issues related to visa', 'df;ldfk', 'errtkert', 'rkrtjjelk', NULL, NULL, 0.0, 'ldkgerlk', '[]', '', '', 'vdff', 'f', 'dfg', 'Project manager', 0.00, '', '', 'GC Holder', '', '', '2025-10-31 08:23:50', '2025-10-31 08:23:50', 22),
(269, '3', 'fl;gljdfgl', 'India', 'H1', 'Immigration issues related to visa', 'flfbgjdflvj', 'flgkdfgldk', 'fdkdflgk', NULL, NULL, 4.0, 'fglkdfl', '[\"Karnataka\"]', 'Yes', 'HINDI', 'dldkfjdflf', 'fdflkjkfdl', 'ssdjflsdj', 'Team member', 959.00, 'USD', 'Within 2 weeks', 'PIO', 'fjsdsfkj', '', '2025-10-31 08:25:58', '2025-10-31 08:25:58', 22),
(275, '2', 'ajay rathod', 'India', 'Dependent', 'Laid off', 'fldjgl', '', '', NULL, NULL, 0.0, '', '[]', '', '', 'niranjan@gmail.com', 'f', 'dfjsdfk', '', 0.00, '', '', 'PIO', '', '', '2025-10-31 08:56:46', '2025-10-31 08:56:46', 22);

-- --------------------------------------------------------

--
-- Table structure for table `portfolio_certificates`
--

CREATE TABLE `portfolio_certificates` (
  `id` int(11) NOT NULL,
  `portfolio_id` int(11) NOT NULL,
  `certificate_name` varchar(255) NOT NULL,
  `certificate_path` varchar(500) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `portfolio_education`
--

CREATE TABLE `portfolio_education` (
  `id` int(11) NOT NULL,
  `portfolio_id` int(11) NOT NULL,
  `degree_type` varchar(100) NOT NULL,
  `graduation_year` int(11) NOT NULL,
  `university_name` varchar(255) NOT NULL,
  `specialization` varchar(255) DEFAULT NULL,
  `certificate_path` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `portfolio_education`
--

INSERT INTO `portfolio_education` (`id`, `portfolio_id`, `degree_type`, `graduation_year`, `university_name`, `specialization`, `certificate_path`) VALUES
(132, 131, 'Bachelor\'s', 2026, 'BAMU', 'PHP DEVELOPER', NULL),
(205, 132, 'Bachelor\'s', 2020, 'mumbai', 'CS', NULL),
(210, 252, 'Bachelor\'s', 456, '465', '6546', NULL),
(214, 269, 'Master\'s', 5465, '546', '5', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `repayments`
--

CREATE TABLE `repayments` (
  `id` int(11) NOT NULL,
  `UserId` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `repayment_date` date NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` varchar(500) DEFAULT NULL,
  `status` enum('received','pending','deleted') NOT NULL DEFAULT 'received',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `repayments`
--

INSERT INTO `repayments` (`id`, `UserId`, `name`, `repayment_date`, `amount`, `description`, `status`, `created_at`) VALUES
(1, 2, 'rohan chothave', '0000-00-00', 1500.00, 'e', 'pending', '2025-10-26 10:34:24'),
(3, 2, 'ajay', '2025-10-27', 800.00, 'chargar sathi', 'pending', '2025-10-27 02:05:09');

-- --------------------------------------------------------

--
-- Table structure for table `tblcategory`
--

CREATE TABLE `tblcategory` (
  `CategoryId` int(11) NOT NULL,
  `CategoryName` varchar(255) DEFAULT NULL,
  `Mode` enum('expense','income') NOT NULL DEFAULT 'expense',
  `UserId` int(11) NOT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblcategory`
--

INSERT INTO `tblcategory` (`CategoryId`, `CategoryName`, `Mode`, `UserId`, `CreatedAt`) VALUES
(1, 'Food', 'expense', 1, '2025-10-26 09:16:56'),
(2, 'Entertainment', 'expense', 1, '2025-10-26 09:16:56'),
(3, 'Salary', 'income', 1, '2025-10-26 09:16:56'),
(4, 'Mama', 'income', 2, '2025-10-26 09:25:01'),
(5, 'room rent ', 'expense', 2, '2025-10-26 09:33:26'),
(6, 'needs', 'expense', 2, '2025-10-27 01:59:04');

-- --------------------------------------------------------

--
-- Table structure for table `tblexpense`
--

CREATE TABLE `tblexpense` (
  `ID` int(11) NOT NULL,
  `UserId` int(11) NOT NULL,
  `ExpenseDate` date DEFAULT NULL,
  `CategoryId` int(11) NOT NULL,
  `category` varchar(255) DEFAULT NULL,
  `ExpenseCost` decimal(10,2) DEFAULT NULL,
  `Description` varchar(300) DEFAULT NULL,
  `NoteDate` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblexpense`
--

INSERT INTO `tblexpense` (`ID`, `UserId`, `ExpenseDate`, `CategoryId`, `category`, `ExpenseCost`, `Description`, `NoteDate`) VALUES
(1, 2, '2025-10-26', 5, 'room rent ', 1500.00, 'for september month  ', '2025-10-26 09:33:59'),
(2, 2, '2025-10-27', 6, 'needs', 250.00, 'charging cable ', '2025-10-27 01:59:21');

-- --------------------------------------------------------

--
-- Table structure for table `tblincome`
--

CREATE TABLE `tblincome` (
  `ID` int(11) NOT NULL,
  `UserId` int(11) NOT NULL,
  `IncomeDate` date DEFAULT NULL,
  `CategoryId` int(11) NOT NULL,
  `category` varchar(255) DEFAULT NULL,
  `IncomeAmount` decimal(10,2) DEFAULT NULL,
  `Description` varchar(300) DEFAULT NULL,
  `NoteDate` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblincome`
--

INSERT INTO `tblincome` (`ID`, `UserId`, `IncomeDate`, `CategoryId`, `category`, `IncomeAmount`, `Description`, `NoteDate`) VALUES
(1, 2, '2025-10-26', 4, 'Mama', 2500.00, 'mami che piase mama ne patavle aahet he ', '2025-10-26 09:26:06'),
(2, 2, '2025-10-26', 4, 'Mama', 1.00, 's', '2025-10-26 09:26:35'),
(3, 2, '2025-10-26', 4, 'Mama', 1.00, '1', '2025-10-26 09:31:47');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(191) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `password` varchar(255) NOT NULL,
  `verification_code` varchar(12) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `verification_code`, `created_at`) VALUES
(1, 'User', 'user@gmail.com', '9245657856', '$2y$10$JkvQ00olAxMBVQBUJ6kZp.rNtv0v5K7OChUeVvR04uAq8ZEFWDC2.', '4ebebb3c3d07', '2025-10-26 14:46:55'),
(2, 'Pruthviraj Kapase', 'kapasepruthviraj731@gmail.com', '09529361465', '$2y$10$geQA.dZ/gPel3nO49T/v1et6uPJ3ZonWz1tvgBrtDLAF69TXHALGy', '26c65899ac8d', '2025-10-26 14:50:52');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blogs`
--
ALTER TABLE `blogs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `job_seekers`
--
ALTER TABLE `job_seekers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `lending`
--
ALTER TABLE `lending`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_lending_user` (`UserId`);

--
-- Indexes for table `portfolios`
--
ALTER TABLE `portfolios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_portfolios_email` (`email`),
  ADD KEY `idx_portfolios_status` (`status`),
  ADD KEY `idx_portfolios_submitted` (`submitted_at`),
  ADD KEY `idx_user_status` (`status`),
  ADD KEY `fk_portfolios_user_id` (`user_id`);

--
-- Indexes for table `portfolio_certificates`
--
ALTER TABLE `portfolio_certificates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_certificates_portfolio_id` (`portfolio_id`);

--
-- Indexes for table `portfolio_education`
--
ALTER TABLE `portfolio_education`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_education_portfolio_id` (`portfolio_id`);

--
-- Indexes for table `repayments`
--
ALTER TABLE `repayments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_userid` (`UserId`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_repayment_date` (`repayment_date`);

--
-- Indexes for table `tblcategory`
--
ALTER TABLE `tblcategory`
  ADD PRIMARY KEY (`CategoryId`),
  ADD KEY `idx_UserId` (`UserId`);

--
-- Indexes for table `tblexpense`
--
ALTER TABLE `tblexpense`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `idx_expense_user_date` (`UserId`,`ExpenseDate`),
  ADD KEY `idx_expense_user_category` (`UserId`,`CategoryId`),
  ADD KEY `fk_expense_category` (`CategoryId`);

--
-- Indexes for table `tblincome`
--
ALTER TABLE `tblincome`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `idx_income_user_date` (`UserId`,`IncomeDate`),
  ADD KEY `idx_income_user_category` (`UserId`,`CategoryId`),
  ADD KEY `fk_income_category` (`CategoryId`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blogs`
--
ALTER TABLE `blogs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `job_seekers`
--
ALTER TABLE `job_seekers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `lending`
--
ALTER TABLE `lending`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `portfolios`
--
ALTER TABLE `portfolios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=276;

--
-- AUTO_INCREMENT for table `portfolio_certificates`
--
ALTER TABLE `portfolio_certificates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `portfolio_education`
--
ALTER TABLE `portfolio_education`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=215;

--
-- AUTO_INCREMENT for table `repayments`
--
ALTER TABLE `repayments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tblcategory`
--
ALTER TABLE `tblcategory`
  MODIFY `CategoryId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tblexpense`
--
ALTER TABLE `tblexpense`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tblincome`
--
ALTER TABLE `tblincome`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `lending`
--
ALTER TABLE `lending`
  ADD CONSTRAINT `fk_lending_user` FOREIGN KEY (`UserId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `portfolios`
--
ALTER TABLE `portfolios`
  ADD CONSTRAINT `fk_portfolios_user_id` FOREIGN KEY (`user_id`) REFERENCES `job_seekers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `portfolio_certificates`
--
ALTER TABLE `portfolio_certificates`
  ADD CONSTRAINT `portfolio_certificates_ibfk_1` FOREIGN KEY (`portfolio_id`) REFERENCES `portfolios` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `portfolio_education`
--
ALTER TABLE `portfolio_education`
  ADD CONSTRAINT `portfolio_education_ibfk_1` FOREIGN KEY (`portfolio_id`) REFERENCES `portfolios` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `repayments`
--
ALTER TABLE `repayments`
  ADD CONSTRAINT `fk_repayments_user` FOREIGN KEY (`UserId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tblcategory`
--
ALTER TABLE `tblcategory`
  ADD CONSTRAINT `fk_category_user` FOREIGN KEY (`UserId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Application alignment: add missing columns used by portfolios feature
-- Safe to run multiple times on MariaDB 10.4+ (IF NOT EXISTS)
ALTER TABLE `portfolios`
  ADD COLUMN IF NOT EXISTS `core_industry` varchar(50) NULL AFTER `citizenship_status`,
  ADD COLUMN IF NOT EXISTS `category` varchar(50) NULL AFTER `core_industry`,
  ADD COLUMN IF NOT EXISTS `subcategory` varchar(50) NULL AFTER `category`,
  ADD COLUMN IF NOT EXISTS `selected_skills` text NULL AFTER `subcategory`,
  ADD COLUMN IF NOT EXISTS `project_management_exp` enum('yes','no') NULL AFTER `selected_skills`,
  ADD COLUMN IF NOT EXISTS `project_management_skills` text NULL AFTER `project_management_exp`,
  ADD COLUMN IF NOT EXISTS `cross_industry_exp` enum('yes','no') NULL AFTER `project_management_skills`,
  ADD COLUMN IF NOT EXISTS `us_experience` text NULL AFTER `cross_industry_exp`,
  ADD COLUMN IF NOT EXISTS `india_experience` text NULL AFTER `us_experience`;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
