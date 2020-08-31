-- phpMyAdmin SQL Dump
-- version 4.8.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 31, 2020 at 02:33 PM
-- Server version: 10.1.32-MariaDB
-- PHP Version: 7.2.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `personal`
--

-- --------------------------------------------------------

--
-- Table structure for table `p_event`
--

CREATE TABLE `p_event` (
  `e_id` int(11) NOT NULL,
  `e_subject` varchar(255) DEFAULT NULL,
  `e_start_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `e_end_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `e_create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `e_update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `e_description` longtext,
  `e_type` varchar(255) DEFAULT NULL,
  `e_cron` varchar(255) DEFAULT NULL,
  `e_recurring` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `p_expense`
--

CREATE TABLE `p_expense` (
  `e_id` int(11) NOT NULL,
  `s_id` int(11) DEFAULT NULL COMMENT 'session',
  `pt_id` int(11) DEFAULT NULL COMMENT 'payment_type',
  `et_id` int(11) DEFAULT NULL,
  `e_amount` decimal(7,2) DEFAULT NULL,
  `e_claimable` int(11) DEFAULT NULL,
  `e_refundable` int(11) DEFAULT NULL,
  `e_remark` longtext,
  `e_create_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `e_update_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `e_receipt_date` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `p_expense_type`
--

CREATE TABLE `p_expense_type` (
  `et_id` int(11) NOT NULL,
  `et_label` varchar(255) DEFAULT NULL,
  `et_color` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `p_payment_type`
--

CREATE TABLE `p_payment_type` (
  `pt_id` int(11) NOT NULL,
  `pt_label` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `p_session`
--

CREATE TABLE `p_session` (
  `s_id` int(11) NOT NULL,
  `s_label` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `p_task`
--

CREATE TABLE `p_task` (
  `t_id` int(11) NOT NULL,
  `t_subject` varchar(255) DEFAULT NULL,
  `t_description` longtext,
  `t_priority` int(11) DEFAULT NULL,
  `t_create_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `t_due_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `t_stat` int(11) DEFAULT NULL,
  `tk_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `p_task_list`
--

CREATE TABLE `p_task_list` (
  `tk_id` int(11) NOT NULL,
  `tk_name` varchar(255) DEFAULT NULL,
  `tk_description` mediumtext,
  `tk_color` varchar(255) DEFAULT NULL,
  `tk_create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `p_event`
--
ALTER TABLE `p_event`
  ADD PRIMARY KEY (`e_id`);

--
-- Indexes for table `p_expense`
--
ALTER TABLE `p_expense`
  ADD PRIMARY KEY (`e_id`);

--
-- Indexes for table `p_expense_type`
--
ALTER TABLE `p_expense_type`
  ADD PRIMARY KEY (`et_id`);

--
-- Indexes for table `p_payment_type`
--
ALTER TABLE `p_payment_type`
  ADD PRIMARY KEY (`pt_id`);

--
-- Indexes for table `p_session`
--
ALTER TABLE `p_session`
  ADD PRIMARY KEY (`s_id`);

--
-- Indexes for table `p_task`
--
ALTER TABLE `p_task`
  ADD PRIMARY KEY (`t_id`);

--
-- Indexes for table `p_task_list`
--
ALTER TABLE `p_task_list`
  ADD PRIMARY KEY (`tk_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `p_event`
--
ALTER TABLE `p_event`
  MODIFY `e_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `p_expense`
--
ALTER TABLE `p_expense`
  MODIFY `e_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1044;

--
-- AUTO_INCREMENT for table `p_expense_type`
--
ALTER TABLE `p_expense_type`
  MODIFY `et_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `p_payment_type`
--
ALTER TABLE `p_payment_type`
  MODIFY `pt_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `p_session`
--
ALTER TABLE `p_session`
  MODIFY `s_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `p_task`
--
ALTER TABLE `p_task`
  MODIFY `t_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `p_task_list`
--
ALTER TABLE `p_task_list`
  MODIFY `tk_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
