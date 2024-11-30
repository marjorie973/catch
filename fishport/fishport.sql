-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 18, 2024 at 05:14 PM
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
-- Database: `fishport`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_broker`
--

CREATE TABLE `tbl_broker` (
  `broker_id` int(8) NOT NULL,
  `lname` varchar(50) NOT NULL,
  `fname` varchar(50) NOT NULL,
  `mname` varchar(50) NOT NULL,
  `phonenum` varchar(30) NOT NULL,
  `address` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_broker`
--

INSERT INTO `tbl_broker` (`broker_id`, `lname`, `fname`, `mname`, `phonenum`, `address`, `username`, `password`) VALUES
(17, 'Broker', 'I', 'Am', '09123456789', 'San Pablo, ZDS', 'admin', 'asd');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_catched_fish`
--

CREATE TABLE `tbl_catched_fish` (
  `catched_fish_id` int(8) NOT NULL,
  `catch_id` int(8) NOT NULL,
  `fish_name` varchar(100) NOT NULL,
  `unit` varchar(100) NOT NULL,
  `price` double NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_catched_fish`
--

INSERT INTO `tbl_catched_fish` (`catched_fish_id`, `catch_id`, `fish_name`, `unit`, `price`, `quantity`) VALUES
(66618778, 10247976, 'Bangus', 'Kilo', 200, 46),
(40222734, 10247976, 'Bulinao', 'Kilo', 50, 8),
(46184496, 21656133, 'Balyena', 'Banyera', 500, 49),
(66251728, 21656133, 'Shark', 'Box', 300, 32);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_catch_report`
--

CREATE TABLE `tbl_catch_report` (
  `catch_id` int(8) NOT NULL,
  `vessel_id` int(8) DEFAULT NULL,
  `depart_date` datetime DEFAULT NULL,
  `return_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_catch_report`
--

INSERT INTO `tbl_catch_report` (`catch_id`, `vessel_id`, `depart_date`, `return_date`) VALUES
(10247976, 67029499, '2024-11-14 10:28:34', '2024-11-14 10:28:34'),
(21656133, 12113271, '2024-11-14 10:30:01', '2024-11-14 10:30:01');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_owner`
--

CREATE TABLE `tbl_owner` (
  `owner_id` int(8) NOT NULL,
  `owner_lname` varchar(50) NOT NULL,
  `owner_fname` varchar(50) NOT NULL,
  `owner_mname` varchar(50) NOT NULL,
  `phonenum` varchar(30) NOT NULL,
  `address` varchar(100) NOT NULL,
  `username` varchar(250) NOT NULL,
  `password` varchar(100) NOT NULL,
  `status` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_owner`
--

INSERT INTO `tbl_owner` (`owner_id`, `owner_lname`, `owner_fname`, `owner_mname`, `phonenum`, `address`, `username`, `password`, `status`) VALUES
(5, 'Account', 'Dummy', 'This', '09123456789', 'San Pablo, ZDS', 'dummy', 'asd', ''),
(455, 'Lapaz', 'Rey Mark', 'Gabate', '09123456789', 'San Pablo, ZDS', 'reymarklapaz', 'asd', '');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_sell`
--

CREATE TABLE `tbl_sell` (
  `sell_id` int(8) NOT NULL,
  `buyer_name` varchar(100) DEFAULT NULL,
  `buyer_address` varchar(100) DEFAULT NULL,
  `buyer_phonenumber` varchar(100) DEFAULT NULL,
  `total_price` double DEFAULT NULL,
  `date_bought` timestamp NULL DEFAULT current_timestamp(),
  `status` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_sell`
--

INSERT INTO `tbl_sell` (`sell_id`, `buyer_name`, `buyer_address`, `buyer_phonenumber`, `total_price`, `date_bought`, `status`) VALUES
(93450742, 'Mark', 'Pagadian', '0912344567', 3800, '2024-11-14 02:31:40', 'Paid');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_sell_fish_list`
--

CREATE TABLE `tbl_sell_fish_list` (
  `sell_fish_list_id` int(8) NOT NULL,
  `catched_fish_id` int(8) NOT NULL,
  `sell_id` int(8) NOT NULL,
  `buy_quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_sell_fish_list`
--

INSERT INTO `tbl_sell_fish_list` (`sell_fish_list_id`, `catched_fish_id`, `sell_id`, `buy_quantity`) VALUES
(81088997, 66618778, 93450742, 4),
(74156048, 40222734, 93450742, 2),
(77476622, 46184496, 93450742, 1),
(77178364, 66251728, 93450742, 8);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_vessel`
--

CREATE TABLE `tbl_vessel` (
  `vessel_id` int(8) NOT NULL,
  `vessel_name` varchar(100) NOT NULL,
  `vessel_origin` varchar(100) NOT NULL,
  `owner_id` int(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_vessel`
--

INSERT INTO `tbl_vessel` (`vessel_id`, `vessel_name`, `vessel_origin`, `owner_id`) VALUES
(12113271, 'Dummy Vessels', 'Dum', 5),
(67029499, 'Mark Vessels', 'Rey', 455);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_broker`
--
ALTER TABLE `tbl_broker`
  ADD PRIMARY KEY (`broker_id`);

--
-- Indexes for table `tbl_catched_fish`
--
ALTER TABLE `tbl_catched_fish`
  ADD KEY `catch_id` (`catch_id`);

--
-- Indexes for table `tbl_catch_report`
--
ALTER TABLE `tbl_catch_report`
  ADD PRIMARY KEY (`catch_id`),
  ADD KEY `vessel_id` (`vessel_id`);

--
-- Indexes for table `tbl_owner`
--
ALTER TABLE `tbl_owner`
  ADD PRIMARY KEY (`owner_id`);

--
-- Indexes for table `tbl_sell_fish_list`
--
ALTER TABLE `tbl_sell_fish_list`
  ADD KEY `catched_fish_id` (`catched_fish_id`),
  ADD KEY `sell_id` (`sell_id`);

--
-- Indexes for table `tbl_vessel`
--
ALTER TABLE `tbl_vessel`
  ADD PRIMARY KEY (`vessel_id`) USING BTREE,
  ADD KEY `ownder_id` (`owner_id`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_catched_fish`
--
ALTER TABLE `tbl_catched_fish`
  ADD CONSTRAINT `tbl_catched_fish_ibfk_1` FOREIGN KEY (`catch_id`) REFERENCES `tbl_catch_report` (`catch_id`);

--
-- Constraints for table `tbl_catch_report`
--
ALTER TABLE `tbl_catch_report`
  ADD CONSTRAINT `tbl_catch_report_ibfk_1` FOREIGN KEY (`vessel_id`) REFERENCES `tbl_vessel` (`vessel_id`);

--
-- Constraints for table `tbl_vessel`
--
ALTER TABLE `tbl_vessel`
  ADD CONSTRAINT `tbl_vessel_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `tbl_owner` (`owner_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
