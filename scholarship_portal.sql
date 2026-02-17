-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 17, 2026 at 02:19 PM
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
-- Database: `scholarship_portal`
--

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `application_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `scholarship_id` int(11) NOT NULL,
  `application_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','approved','rejected','under_review') DEFAULT 'pending',
  `applied_percentage` decimal(5,2) DEFAULT NULL,
  `applied_degree` varchar(50) DEFAULT NULL,
  `additional_info` text DEFAULT NULL,
  `admin_notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`application_id`, `student_id`, `scholarship_id`, `application_date`, `status`, `applied_percentage`, `applied_degree`, `additional_info`, `admin_notes`) VALUES
(1, 1, 1, '2026-01-17 13:16:52', 'pending', 85.00, 'Bachelor', NULL, NULL),
(2, 1, 2, '2026-01-17 13:16:52', 'approved', 78.00, 'Bachelor', NULL, NULL),
(3, 1, 3, '2026-01-17 13:16:52', 'rejected', 70.00, 'Bachelor', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `education`
--

CREATE TABLE `education` (
  `edu_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `degree` varchar(50) DEFAULT NULL,
  `institute` varchar(100) DEFAULT NULL,
  `marks` varchar(20) DEFAULT NULL,
  `grade` varchar(5) DEFAULT NULL,
  `percentage` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `education`
--

INSERT INTO `education` (`edu_id`, `user_id`, `degree`, `institute`, `marks`, `grade`, `percentage`, `year`, `country`, `created_at`) VALUES
(1, 1, 'Matric', 'allied school ', '1023', 'A', 98, 2021, 'Pakistan', '2026-01-14 17:21:15'),
(2, 1, 'Intermediate', 'kips college', '870', 'B', 78, 2023, 'Pakistan', '2026-01-14 17:22:09'),
(3, 1, 'Bachelor', 'UAF', '1990', 'A', 91, 2026, 'Pakistan', '2026-01-14 17:22:32');

-- --------------------------------------------------------

--
-- Table structure for table `requirements`
--

CREATE TABLE `requirements` (
  `req_id` int(11) NOT NULL,
  `scholarship_id` int(11) DEFAULT NULL,
  `min_percentage` int(11) DEFAULT NULL,
  `degree` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `scholarships`
--

CREATE TABLE `scholarships` (
  `scholarship_id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `institute` varchar(200) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `degree` varchar(50) DEFAULT NULL,
  `min_percentage` decimal(5,2) DEFAULT NULL,
  `benefits` text DEFAULT NULL,
  `deadline` date DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scholarships`
--

INSERT INTO `scholarships` (`scholarship_id`, `name`, `institute`, `country`, `degree`, `min_percentage`, `benefits`, `deadline`, `description`, `created_at`) VALUES
(1, 'Merit Scholarship', 'ABC University', 'Pakistan', 'Bachelor', 70.00, 'Full Tuition + Monthly Stipend', '2024-12-31', NULL, '2026-01-17 12:15:03'),
(2, 'Need-Based Scholarship', 'XYZ College', 'USA', 'Master', 65.00, 'Partial Tuition Coverage', '2027-11-30', NULL, '2026-01-17 12:15:03'),
(3, 'STEM Scholarship', 'Tech Institute', 'UK', 'PhD', 75.00, 'Full Scholarship + Research Grant', '2024-10-15', NULL, '2026-01-17 12:15:03'),
(4, 'Test Engineering Scholarship', 'University of Test', 'Pakistan', 'Bachelor', 70.00, 'Full tuition fee, Monthly stipend', '2026-12-31', NULL, '2026-01-17 12:38:23');

-- --------------------------------------------------------

--
-- Table structure for table `scholarships_new`
--

CREATE TABLE `scholarships_new` (
  `scholarship_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `institute` varchar(100) NOT NULL,
  `country` varchar(50) DEFAULT NULL,
  `degree_required` varchar(50) DEFAULT NULL,
  `min_percentage` decimal(5,2) DEFAULT NULL,
  `benefits` text DEFAULT NULL,
  `deadline` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scholarships_new`
--

INSERT INTO `scholarships_new` (`scholarship_id`, `name`, `institute`, `country`, `degree_required`, `min_percentage`, `benefits`, `deadline`, `created_at`) VALUES
(1, 'Merit Scholarship', 'University of Punjab', 'Pakistan', 'Bachelor', 80.00, 'Full tuition fee waiver, monthly stipend', '2024-06-30', '2026-01-16 19:16:22'),
(2, 'Need-Based Scholarship', 'LUMS', 'Pakistan', 'Master', 70.00, '50% tuition fee waiver, hostel facility', '2024-07-15', '2026-01-16 19:16:22');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `cnic` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` enum('admin','student','reviewer') DEFAULT 'student',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `cnic`, `password`, `user_type`, `created_at`) VALUES
(1, 'Admin User', 'admin@test.com', '99999-8888888-7', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '2026-01-17 12:15:03'),
(2, 'Test Student', 'student@test.com', '12345-6789012-3', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', '2026-01-17 12:15:03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`application_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `scholarship_id` (`scholarship_id`);

--
-- Indexes for table `education`
--
ALTER TABLE `education`
  ADD PRIMARY KEY (`edu_id`);

--
-- Indexes for table `requirements`
--
ALTER TABLE `requirements`
  ADD PRIMARY KEY (`req_id`),
  ADD KEY `scholarship_id` (`scholarship_id`);

--
-- Indexes for table `scholarships`
--
ALTER TABLE `scholarships`
  ADD PRIMARY KEY (`scholarship_id`);

--
-- Indexes for table `scholarships_new`
--
ALTER TABLE `scholarships_new`
  ADD PRIMARY KEY (`scholarship_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `application_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `education`
--
ALTER TABLE `education`
  MODIFY `edu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `requirements`
--
ALTER TABLE `requirements`
  MODIFY `req_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `scholarships`
--
ALTER TABLE `scholarships`
  MODIFY `scholarship_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `scholarships_new`
--
ALTER TABLE `scholarships_new`
  MODIFY `scholarship_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `applications_ibfk_2` FOREIGN KEY (`scholarship_id`) REFERENCES `scholarships` (`scholarship_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
