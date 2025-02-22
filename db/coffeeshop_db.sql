-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 12, 2024 at 03:35 PM
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
-- Database: `coffeeshop_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(100) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblcart`
--

CREATE TABLE `tblcart` (
  `cartID` int(11) NOT NULL,
  `quantity` int(11) DEFAULT NULL,
  `order_datetime` datetime DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `customerid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblcartitem`
--

CREATE TABLE `tblcartitem` (
  `cartitemID` int(11) NOT NULL,
  `quantity` int(11) DEFAULT NULL,
  `order_datetime` datetime DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `productid` int(11) DEFAULT NULL,
  `cartid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblcategory_inventory`
--

CREATE TABLE `tblcategory_inventory` (
  `categoryInventory_id` int(11) NOT NULL,
  `inventory_category` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblcategory_inventory`
--

INSERT INTO `tblcategory_inventory` (`categoryInventory_id`, `inventory_category`) VALUES
(1, 'Sweetener'),
(2, 'Coffee Bean'),
(3, 'Milk'),
(4, 'Sinker'),
(5, 'Disposable'),
(6, 'Toppings'),
(7, 'Flavor');

-- --------------------------------------------------------

--
-- Table structure for table `tblcategory_product`
--

CREATE TABLE `tblcategory_product` (
  `categoryProduct_id` int(11) NOT NULL,
  `category` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblcategory_product`
--

INSERT INTO `tblcategory_product` (`categoryProduct_id`, `category`) VALUES
(1, 'americano'),
(2, 'brewed'),
(3, 'frappe'),
(4, 'espresso'),
(5, 'latte'),
(6, 'cappuccino'),
(20, 'milk based');

-- --------------------------------------------------------

--
-- Table structure for table `tblcoffeeshop`
--

CREATE TABLE `tblcoffeeshop` (
  `coffeeshopid` int(11) NOT NULL,
  `logo` varchar(255) NOT NULL,
  `shopname` varchar(255) NOT NULL,
  `date_established` date DEFAULT current_timestamp(),
  `tagline` text DEFAULT NULL,
  `vision` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '["Vision1", "Vision2", "Vision3"]',
  `branch` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `contact_no` varchar(11) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `VAT` decimal(5,2) NOT NULL,
  `weekday_start_time` time NOT NULL,
  `weekday_end_time` time NOT NULL,
  `weekend_start_time` time NOT NULL,
  `weekend_end_time` time NOT NULL,
  `employees_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblcoffeeshop`
--

INSERT INTO `tblcoffeeshop` (`coffeeshopid`, `logo`, `shopname`, `date_established`, `tagline`, `vision`, `branch`, `address`, `contact_no`, `email`, `VAT`, `weekday_start_time`, `weekday_end_time`, `weekend_start_time`, `weekend_end_time`, `employees_id`) VALUES
(1, 'images.png', 'Only Coffee', '2023-07-02', 'Step into \'Only Coffee\', where every brew is a study buddy and every sip fuels your university journey. Embrace the aroma of academia as you indulge in our meticulously crafted blends, the perfect companions for your academic pursuits. Join us at the intersection of caffeine and campus life, where \'Only Coffee\' means endless possibilities', '[\"Serve the Best Tasting Coffee.\",\"Home of Caffeine Addict Students.\",\"Provide Fast Service.\"]', 'Legarda Manila ', '2255 Legarda St, Sampaloc, 1008 Metro Manila', '09156351463', 'onlycoffee@gmail.com', 15.99, '08:00:00', '20:00:00', '14:00:00', '20:00:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tblcustomers`
--

CREATE TABLE `tblcustomers` (
  `customerid` int(11) NOT NULL,
  `customername` varchar(255) NOT NULL,
  `contactnumber` varchar(13) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblcustomers`
--

INSERT INTO `tblcustomers` (`customerid`, `customername`, `contactnumber`, `email`, `address`) VALUES
(2, 'Edie shing', '09123123123', 'edi@gmail.com', 'doon lang'),
(3, 'Mang kanor', '09222222222', 'Testemail@mailinator.com', 'testaddress'),
(4, 'Megan old', '09222222222', 'Testemail14@mailinator.com', 'testaddress'),
(5, 'Andrew E', '09222222222', 'Testemail@mailinator.com', 'testaddress');

-- --------------------------------------------------------

--
-- Table structure for table `tblemployees`
--

CREATE TABLE `tblemployees` (
  `employeeID` int(11) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `email` varchar(50) NOT NULL,
  `position` varchar(255) NOT NULL DEFAULT 'guest',
  `hiredate` date NOT NULL DEFAULT current_timestamp(),
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblemployees`
--

INSERT INTO `tblemployees` (`employeeID`, `firstname`, `lastname`, `email`, `position`, `hiredate`, `username`, `password`) VALUES
(1, 'Super', 'Admin', 'superadmin@gmail.com', 'admin', '2024-04-01', 'superadmin', '$2y$10$ExJqrs6/0hYlS7mTyFwbN.ja1XeJAb78OZExDw5UxO2PAk91YL2yu'),
(34, 'Jan', 'Manuel', 'manuel.janenrique@ue.edu.ph', 'admin', '2024-04-23', 'enrique', '$2y$10$Gfn/r1DlFMRZ3.46xbYTcuGN38wrY4EdxnMEEtemBlY0VqfgKKY3y'),
(35, 'test', 'test3', 'test@gmail.com', 'guest', '2024-04-23', 'testing', '$2y$10$FO9R0sczJKEUWd.AO7Ga8O0UXDHjL2v9UsKGU6l39ASpA7Z4WB85C'),
(39, 'Juan', 'Luna', 'juan@gmail.com', 'guest', '2024-04-23', 'juan', '$2y$10$uerZ8nU9of.PLLelQqufcec3cz5ubJL.mbCdGGbdFTYfWLx5Y7Cvm'),
(42, 'JP', 'Olarte', 'jp@gmail.com', 'admin', '2024-04-25', 'jp', '$2y$10$VVzxH5W.aTdBNNse4dzwRe4/iMvJ50hS.8rvsx.lztgH8kBv0S0KG'),
(44, 'justin', 'japson', 'justin@gmail.com', 'admin', '2024-04-26', 'justin', '$2y$10$IWQQ7taqWigMODDzty9sne727TSQbKysBdNk2i3ygVRIXjcd.8nfO'),
(66, 'cesar', 'salazar', 'cesarsalad@cesar.com', 'guest', '2024-05-02', 'cesar', '$2y$10$LrmSkroLFaFJH9yTqXjlmeovCwegu.7IPLru8MsuftiM68eh/ZFHS'),
(67, '134er', '2342er', 'jan@gmail.com2', 'guest', '2024-07-06', '34234', '$2y$10$E/lUfiEd/ViXeTO7q7oHRuxB.qYPV00yDl.UQnyayYnLLc/nJvKd2'),
(68, 'Robin', 'Manuel', 'jaaan.manuel2002@gmail.com', 'admin', '2024-07-06', 'JanEnriK', '$2y$10$tbBB1UmQis5oMSBBv8RIaON1PK6HY4cRkEpe2f5ULeSfY5awgDr36');

-- --------------------------------------------------------

--
-- Table structure for table `tblfeedback`
--

CREATE TABLE `tblfeedback` (
  `feedbackid` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `feedback_desc` text DEFAULT NULL,
  `feedback_datetime` datetime NOT NULL DEFAULT current_timestamp(),
  `customerid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblfeedback`
--

INSERT INTO `tblfeedback` (`feedbackid`, `title`, `feedback_desc`, `feedback_datetime`, `customerid`) VALUES
(19, 'Good Coffee', 'Coffee served here in only coffee is one of the best', '2024-04-23 14:58:06', 35),
(21, 'Test Title', 'Test feedback body', '2024-04-26 17:46:22', 39),
(22, 'Fast Service', 'The service here in only coffee has one of the fastest service in the coffee industry.', '2024-04-26 20:21:17', 42),
(23, 'Strong coffee', 'Coffee that\'s been served to me is too strong', '2024-04-27 21:06:59', 44),
(24, 'good environment', 'the venue of the shop has a good ambience', '2024-05-02 16:54:06', 66),
(25, 'Quality Ingredients', 'Ingredients used are authentic and rare.', '2024-07-04 22:03:48', 34),
(26, 'Alot of flavor profile', 'The coffee shop offers various product with different flavor profiles.', '2024-07-11 16:28:14', 34);

-- --------------------------------------------------------

--
-- Table structure for table `tblinventory`
--

CREATE TABLE `tblinventory` (
  `inventory_id` int(11) NOT NULL,
  `inventory_item` varchar(255) NOT NULL,
  `item_type` varchar(255) NOT NULL,
  `quantity` float DEFAULT NULL,
  `unit` varchar(10) DEFAULT NULL,
  `reorder_point` float DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblinventory`
--

INSERT INTO `tblinventory` (`inventory_id`, `inventory_item`, `item_type`, `quantity`, `unit`, `reorder_point`, `status`) VALUES
(3, 'Powdered Sugar', 'Sweetener', 1000, 'grams', 100, 'In Stock'),
(7, 'Soy Milk', 'Milk', 965, 'ml', 500, 'In Stock'),
(8, 'Pearls', 'Sinker', 1000, 'grams', 500, 'In Stock'),
(9, 'Nata De Coco', 'Sinker', 2000, 'grams', 500, 'In Stock'),
(12, 'Cream', 'Toppings', 1000, 'grams', 500, 'In Stock'),
(13, 'Marshmallows', 'Toppings', 990, 'grams', 500, 'In Stock'),
(14, 'Caramel', 'Flavor', 1453, 'ml', 500, 'In Stock'),
(15, 'Matcha', 'Flavor', 1490, 'grams', 500, 'In Stock'),
(16, 'Oreo Cookies', 'Flavor', 1000, 'grams', 500, 'In Stock'),
(48, 'Brown Sugar', 'Sweetener', 1000, 'grams', 500, 'In Stock'),
(50, 'Condensed Milk', 'Milk', 1000, 'ml', 500, 'In Stock'),
(51, 'Robusta Coffee Bean', 'Coffee Bean', 1359, 'grams', 500, 'In Stock');

-- --------------------------------------------------------

--
-- Table structure for table `tblinventoryitems`
--

CREATE TABLE `tblinventoryitems` (
  `tblinventoryitems_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `expiration_date` date NOT NULL,
  `inventory_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblinventoryitems`
--

INSERT INTO `tblinventoryitems` (`tblinventoryitems_id`, `quantity`, `expiration_date`, `inventory_id`) VALUES
(1, 999, '2024-12-31', 51),
(23, 360, '2024-07-31', 51),
(25, 1000, '2024-12-31', 3),
(29, 965, '2024-12-31', 7),
(30, 1000, '2024-12-31', 8),
(31, 1000, '2024-12-31', 9),
(32, 1000, '2024-12-31', 9),
(34, 990, '2024-12-31', 13),
(35, 473, '2024-12-31', 14),
(36, 500, '2024-12-31', 15),
(37, 1000, '2024-12-31', 16),
(38, 1000, '2024-12-31', 48),
(39, 1000, '2024-12-31', 50),
(41, 1000, '2024-11-30', 0),
(42, 990, '2024-09-30', 15),
(43, 1000, '2024-07-31', 0),
(44, 1000, '2024-07-31', 0),
(45, 1000, '2024-07-31', 10),
(46, 1000, '2024-12-31', 10),
(48, 1000, '2024-08-10', 10),
(49, 1000, '2024-08-10', 10),
(50, 1000, '2024-08-10', 10),
(51, 1000, '2024-08-10', 10),
(52, 1000, '2024-08-10', 10),
(53, 1000, '2024-08-10', 10),
(54, 1000, '2024-08-10', 10),
(55, 980, '2024-07-31', 14),
(56, 1000, '2024-12-31', 12);

-- --------------------------------------------------------

--
-- Table structure for table `tblinventoryreport`
--

CREATE TABLE `tblinventoryreport` (
  `inventory_report_id` int(11) NOT NULL,
  `inventory_item` varchar(255) NOT NULL,
  `inventory_id` int(11) NOT NULL,
  `quantity` float NOT NULL,
  `unit` varchar(20) NOT NULL,
  `record_type` varchar(255) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `datetime` datetime NOT NULL DEFAULT current_timestamp(),
  `employee_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblinventoryreport`
--

INSERT INTO `tblinventoryreport` (`inventory_report_id`, `inventory_item`, `inventory_id`, `quantity`, `unit`, `record_type`, `reason`, `datetime`, `employee_id`) VALUES
(181, 'Robusta Coffee Bean', 51, -40, 'grams', 'Product Preparation Deduct', 'inventory deduct for preparing 2 Sagada Coffee', '2024-07-12 16:04:26', 34),
(182, 'Matcha', 15, -5, 'grams', 'Product Preparation Deduct', 'inventory deduct for preparing 1 Espresso Machiato', '2024-07-12 16:04:29', 34),
(183, 'Marshmallows', 13, -5, 'grams', 'Product Preparation Deduct', 'inventory deduct for preparing 1 Iced White Chocolate mocha', '2024-07-12 16:04:29', 34),
(184, 'Caramel', 14, -10, 'ml', 'Product Preparation Deduct', 'inventory deduct for preparing 1 Iced White Chocolate mocha', '2024-07-12 16:04:29', 34),
(185, 'Marshmallows', 13, -5, 'grams', 'Product Preparation Deduct', 'inventory deduct for preparing 1 Iced White Chocolate mocha', '2024-07-12 16:04:32', 34),
(186, 'Caramel', 14, -10, 'ml', 'Product Preparation Deduct', 'inventory deduct for preparing 1 Iced White Chocolate mocha', '2024-07-12 16:04:32', 34),
(187, 'Matcha', 15, -5, 'grams', 'Product Preparation Deduct', 'inventory deduct for preparing 1 Espresso Machiato', '2024-07-12 16:04:35', 34);

-- --------------------------------------------------------

--
-- Table structure for table `tblorderitem`
--

CREATE TABLE `tblorderitem` (
  `orderitem_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT NULL,
  `status` enum('active','completed','ended') NOT NULL,
  `date_time` datetime NOT NULL DEFAULT current_timestamp(),
  `orderid` int(11) DEFAULT NULL,
  `productid` int(11) DEFAULT NULL,
  `customerid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblorderitem`
--

INSERT INTO `tblorderitem` (`orderitem_id`, `quantity`, `status`, `date_time`, `orderid`, `productid`, `customerid`) VALUES
(74, 1, 'completed', '2024-07-02 01:28:00', 101, 7, 34),
(75, 2, 'completed', '2024-07-02 01:28:00', 101, 15, 34),
(76, 1, 'completed', '2024-07-02 01:28:00', 101, 17, 34),
(77, 3, 'ended', '2024-07-02 01:28:21', 105, 7, 34),
(78, 1, 'ended', '2024-07-02 01:32:32', 102, 7, 34),
(79, 1, 'ended', '2024-07-02 01:32:32', 102, 15, 34),
(80, 6, 'completed', '2024-07-02 01:32:42', 104, 15, 34),
(81, 1, 'ended', '2024-07-02 01:40:22', 107, 15, 34),
(82, 3, 'ended', '2024-07-02 01:40:22', 107, 17, 34),
(83, 1, 'completed', '2024-07-02 01:40:37', 108, 21, 34),
(84, 1, 'completed', '2024-07-02 01:40:55', 109, 7, 34),
(85, 3, 'completed', '2024-07-02 01:40:55', 109, 15, 34),
(86, 1, 'completed', '2024-07-02 01:40:55', 109, 17, 34),
(87, 1, 'completed', '2024-07-02 01:41:15', 110, 15, 34),
(88, 1, 'completed', '2024-07-02 01:41:15', 110, 17, 34),
(89, 1, 'completed', '2024-07-02 01:41:15', 110, 21, 34),
(93, 3, 'completed', '2024-07-03 21:40:04', 101, 11, 34),
(94, 1, 'completed', '2024-07-04 01:18:03', 101, 11, 34),
(95, 1, 'ended', '2024-07-04 01:21:09', 102, 10, 34),
(96, 1, 'completed', '2024-07-04 01:25:15', 103, 11, 34),
(97, 1, 'completed', '2024-07-04 01:27:26', 104, 17, 34),
(104, 1, 'ended', '2024-07-04 01:53:29', 105, 7, 34),
(106, 1, 'ended', '2024-07-04 01:55:57', 106, 21, 34),
(107, 1, 'completed', '2024-07-04 14:41:40', 107, 11, 34),
(108, 2, 'completed', '2024-07-04 14:58:06', 108, 7, 34),
(109, 2, 'completed', '2024-07-04 15:24:26', 109, 77, 34),
(110, 2, 'ended', '2024-07-04 19:53:23', 110, 77, 34),
(111, 1, 'ended', '2024-07-05 16:16:28', 101, 11, 34),
(112, 1, 'ended', '2024-07-05 16:26:48', 102, 7, 34),
(113, 1, 'ended', '2024-07-06 15:28:49', 101, 7, 34),
(114, 1, 'completed', '2024-07-06 19:11:05', 104, 10, 68),
(115, 1, 'completed', '2024-07-06 19:11:05', 104, 21, 68),
(116, 1, 'ended', '2024-07-08 21:38:02', 101, 15, 34),
(117, 1, 'ended', '2024-07-08 21:38:02', 101, 17, 34),
(118, 1, 'ended', '2024-07-08 21:38:02', 101, 21, 34),
(119, 1, 'completed', '2024-07-08 21:56:27', 102, 7, 34),
(120, 1, 'completed', '2024-07-10 17:47:50', 101, 77, 34),
(121, 1, 'ended', '2024-07-11 23:42:01', 103, 77, 34),
(122, 1, 'completed', '2024-07-12 16:03:32', 102, 15, 34),
(123, 2, 'completed', '2024-07-12 16:03:40', 104, 77, 66),
(124, 1, 'completed', '2024-07-12 16:03:49', 103, 16, 66),
(125, 1, 'completed', '2024-07-12 16:03:49', 103, 15, 66),
(126, 1, 'completed', '2024-07-12 16:03:57', 101, 16, 34),
(127, 1, 'active', '2024-07-12 18:01:18', 105, 16, 34);

-- --------------------------------------------------------

--
-- Table structure for table `tblorders`
--

CREATE TABLE `tblorders` (
  `order_id` int(11) NOT NULL,
  `order_type` varchar(255) NOT NULL,
  `order_datetime` datetime NOT NULL DEFAULT current_timestamp(),
  `quantity` int(11) NOT NULL,
  `base_coffee_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `order_number` int(11) NOT NULL,
  `order_status` varchar(255) NOT NULL,
  `payment_proof` varchar(255) DEFAULT NULL,
  `discount` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblorders`
--

INSERT INTO `tblorders` (`order_id`, `order_type`, `order_datetime`, `quantity`, `base_coffee_id`, `customer_id`, `order_number`, `order_status`, `payment_proof`, `discount`) VALUES
(234, 'take-out', '2024-07-02 01:26:13', 1, 7, 34, 101, 'payed', NULL, 0),
(235, 'take-out', '2024-07-02 01:26:13', 2, 15, 34, 101, 'payed', NULL, 0),
(236, 'take-out', '2024-07-02 01:26:13', 1, 17, 34, 101, 'payed', NULL, 0),
(237, 'take-out', '2024-07-02 01:26:37', 1, 7, 34, 102, 'payed', 'order_142 (3).jpg', 0),
(238, 'take-out', '2024-07-02 01:26:37', 1, 15, 34, 102, 'payed', 'order_142 (3).jpg', 0),
(239, 'take-out', '2024-07-02 01:27:08', 3, 21, 34, 103, 'declined', 'image-Photoroom.png-Photoroom.png', 0),
(240, 'take-out', '2024-07-02 01:27:08', 2, 17, 34, 103, 'declined', 'image-Photoroom.png-Photoroom.png', 0),
(241, 'take-out', '2024-07-02 01:27:23', 6, 15, 34, 104, 'payed', NULL, 0),
(242, 'take-out', '2024-07-02 01:27:42', 3, 7, 34, 105, 'payed', NULL, 0),
(243, 'take-out', '2024-07-02 01:38:19', 2, 17, 34, 106, 'declined', 'order_139.jpg', 0),
(244, 'take-out', '2024-07-02 01:40:22', 1, 15, 34, 107, 'payed', NULL, NULL),
(245, 'take-out', '2024-07-02 01:40:22', 3, 17, 34, 107, 'payed', NULL, NULL),
(246, 'take-out', '2024-07-02 01:40:37', 1, 21, 34, 108, 'payed', NULL, NULL),
(247, 'take-out', '2024-07-02 01:40:55', 1, 7, 34, 109, 'payed', NULL, NULL),
(248, 'take-out', '2024-07-02 01:40:55', 3, 15, 34, 109, 'payed', NULL, NULL),
(249, 'take-out', '2024-07-02 01:40:55', 1, 17, 34, 109, 'payed', NULL, NULL),
(250, 'take-out', '2024-07-02 01:41:15', 1, 15, 34, 110, 'payed', NULL, NULL),
(251, 'take-out', '2024-07-02 01:41:15', 1, 17, 34, 110, 'payed', NULL, NULL),
(252, 'take-out', '2024-07-02 01:41:15', 1, 21, 34, 110, 'payed', NULL, NULL),
(253, 'take-out', '2024-07-02 18:53:13', 1, 17, 34, 111, 'canceled', 'order_142 (2).png', 0),
(254, 'take-out', '2024-07-02 22:29:14', 49, 7, 34, 112, 'declined', 'order_142 (8).jpg', 0.1),
(255, 'take-out', '2024-07-03 21:40:03', 3, 11, 34, 101, 'payed', NULL, NULL),
(256, 'take-out', '2024-07-04 01:04:02', 1, 11, 34, 101, 'payed', NULL, 0),
(257, 'take-out', '2024-07-04 01:19:11', 1, 10, 34, 102, 'payed', 'order_101.jpg', 0),
(258, 'take-out', '2024-07-04 01:21:37', 1, 11, 34, 103, 'payed', NULL, 0),
(259, 'take-out', '2024-07-04 01:27:05', 1, 17, 34, 104, 'payed', 'order_101 (5).jpg', 0),
(263, 'take-out', '2024-07-04 01:53:29', 1, 7, 34, 105, 'payed', NULL, NULL),
(265, 'take-out', '2024-07-04 01:55:57', 1, 21, 34, 106, 'payed', NULL, NULL),
(266, 'take-out', '2024-07-04 14:40:28', 1, 11, 34, 107, 'payed', 'order_101 (10).jpg', 0),
(267, 'take-out', '2024-07-04 14:56:00', 2, 7, 34, 108, 'payed', 'order_101 (10).jpg', 0),
(268, 'take-out', '2024-07-04 15:22:49', 2, 77, 34, 109, 'payed', 'order_101 (10).jpg', 0),
(269, 'take-out', '2024-07-04 19:51:42', 2, 77, 34, 110, 'payed', NULL, 0),
(270, 'take-out', '2024-07-05 16:15:07', 1, 11, 34, 101, 'payed', NULL, 0.1),
(271, 'take-out', '2024-07-05 16:19:59', 1, 7, 34, 102, 'payed', NULL, 0.1),
(272, 'take-out', '2024-07-06 15:25:36', 1, 7, 34, 101, 'payed', NULL, 0),
(273, 'take-out', '2024-07-06 18:31:55', 1, 7, 34, 102, 'canceled', NULL, 0),
(274, 'take-out', '2024-07-06 18:32:15', 1, 10, 34, 103, 'canceled', 'order_101 (10).jpg', 0),
(275, 'take-out', '2024-07-06 19:10:33', 1, 10, 68, 104, 'payed', NULL, 0),
(276, 'take-out', '2024-07-06 19:10:33', 1, 21, 68, 104, 'payed', NULL, 0),
(277, 'take-out', '2024-07-07 21:51:11', 1, 11, 34, 101, 'canceled', 'order_102.jpg', 0),
(278, 'take-out', '2024-07-08 21:38:01', 1, 15, 34, 101, 'payed', NULL, NULL),
(279, 'take-out', '2024-07-08 21:38:01', 1, 17, 34, 101, 'payed', NULL, NULL),
(280, 'take-out', '2024-07-08 21:38:01', 1, 21, 34, 101, 'payed', NULL, NULL),
(281, 'take-out', '2024-07-08 21:51:17', 1, 7, 34, 102, 'payed', NULL, 0),
(282, 'take-out', '2024-07-10 17:47:50', 1, 77, 34, 101, 'payed', NULL, NULL),
(283, 'take-out', '2024-07-11 23:29:49', 1, 15, 34, 101, 'canceled', NULL, 0),
(284, 'take-out', '2024-07-11 23:31:06', 1, 16, 34, 102, 'canceled', NULL, 0),
(285, 'take-out', '2024-07-11 23:42:01', 1, 77, 34, 103, 'payed', NULL, NULL),
(286, 'take-out', '2024-07-12 00:07:01', 1, 16, 34, 101, 'payed', NULL, 0),
(287, 'take-out', '2024-07-12 00:08:44', 1, 15, 34, 102, 'payed', '450488498_2110511382668721_1148435884013927544_n.jpg', 0),
(288, 'take-out', '2024-07-12 16:01:54', 1, 16, 66, 103, 'payed', NULL, 0),
(289, 'take-out', '2024-07-12 16:01:54', 1, 15, 66, 103, 'payed', NULL, 0),
(290, 'take-out', '2024-07-12 16:02:21', 2, 77, 66, 104, 'payed', '449448853_1650418262166229_4143653410016719871_n.jpg', 0),
(291, 'take-out', '2024-07-12 18:01:18', 1, 16, 34, 105, 'payed', NULL, NULL),
(292, 'take-out', '2024-07-12 18:01:39', 1, 15, 34, 106, 'notpayed', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tblpayment`
--

CREATE TABLE `tblpayment` (
  `paymentID` int(100) NOT NULL,
  `order_datetime` datetime NOT NULL DEFAULT current_timestamp(),
  `amountpayed` decimal(10,2) NOT NULL,
  `paymenttype` varchar(50) NOT NULL,
  `customerid` int(11) DEFAULT NULL,
  `orderNumber` int(11) DEFAULT NULL,
  `reference_no` varchar(255) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblpayment`
--

INSERT INTO `tblpayment` (`paymentID`, `order_datetime`, `amountpayed`, `paymenttype`, `customerid`, `orderNumber`, `reference_no`, `employee_id`) VALUES
(272, '2024-07-12 16:03:32', 200.00, 'online', 34, 102, '34rewr332', 34),
(273, '2024-07-12 16:03:40', 300.00, 'online', 66, 104, 'gsdf24523f', 34),
(274, '2024-07-12 16:03:49', 400.00, 'cash', 66, 103, NULL, 34),
(275, '2024-07-12 16:03:57', 200.00, 'online', 34, 101, '12532r4tfwf4', 34),
(276, '2024-07-12 18:01:18', 200.00, 'cash', 34, 105, NULL, 34);

-- --------------------------------------------------------

--
-- Table structure for table `tblproducts`
--

CREATE TABLE `tblproducts` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `SKU` int(11) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblproducts`
--

INSERT INTO `tblproducts` (`product_id`, `product_name`, `product_description`, `price`, `image`, `SKU`, `status`, `category`) VALUES
(7, 'Salted Caramel Cold Brew', 'a salted caramel coffee that is brewed colddss', 130.00, 'stock-of-mix-a-cup-coffee-latte-more-motive-top-view-foodgraphy-generative-ai-photo.jpg', 0, NULL, 'brewed'),
(10, 'Vanilla Cream Frappe', 'A coffee that is frapped with vanilla cream', 200.00, 'coffee-3727673_640.jpg', 0, NULL, 'frappe'),
(11, 'Iced Americano', 'A coffee that is americanized with ice', 100.00, 'stock-of-mix-a-cup-coffee-latte-more-motive-top-view-foodgraphy-generative-ai-photo.jpg', 0, NULL, 'frappe'),
(15, 'Iced White Chocolate mocha', 'A white chocolate flavored coffee with ice and mocha', 200.00, 'coffee-3727673_640.jpg', 145, 'Available', 'espresso'),
(16, 'Espresso Machiato', 'A expressed coffee with macchiato', 200.00, 'stock-of-mix-a-cup-coffee-latte-more-motive-top-view-foodgraphy-generative-ai-photo.jpg', 298, 'Available', 'espresso'),
(17, 'Iced caffe latte', 'a coffee with ice and latted', 130.00, 'coffee-3727673_640.jpg', 0, NULL, 'latte'),
(21, 'Iced Special Cappuccinoo', 'a coffee with ice and cappucinized but its special', 130.00, 'stock-of-mix-a-cup-coffee-latte-more-motive-top-view-foodgraphy-generative-ai-photo.jpg', 0, NULL, 'cappuccino'),
(77, 'Sagada Coffee', 'coffee made in sagada', 150.00, 'stock-of-mix-a-cup-coffee-latte-more-motive-top-view-foodgraphy-generative-ai-photo.jpg', 67, 'Available', 'americano');

-- --------------------------------------------------------

--
-- Table structure for table `tblproducts_inventory`
--

CREATE TABLE `tblproducts_inventory` (
  `productsInventory_id` int(11) NOT NULL,
  `products_id` int(11) NOT NULL,
  `inventory_id` int(11) NOT NULL,
  `quantity` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblproducts_inventory`
--

INSERT INTO `tblproducts_inventory` (`productsInventory_id`, `products_id`, `inventory_id`, `quantity`) VALUES
(155, 15, 13, 5),
(156, 15, 14, 10),
(159, 16, 15, 5),
(169, 77, 51, 20);

-- --------------------------------------------------------

--
-- Table structure for table `tblpromo`
--

CREATE TABLE `tblpromo` (
  `promoid` int(11) NOT NULL,
  `promoname` varchar(255) NOT NULL,
  `promodesc` text DEFAULT NULL,
  `promocode` varchar(20) NOT NULL,
  `value` decimal(10,2) NOT NULL,
  `startdate` date NOT NULL,
  `enddate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblpromo`
--

INSERT INTO `tblpromo` (`promoid`, `promoname`, `promodesc`, `promocode`, `value`, `startdate`, `enddate`) VALUES
(1, '50% off', 'minus 50% off purchases', 'SINKWENTY', 0.50, '2024-01-01', '2024-01-31'),
(2, '10% off', 'minus 10% on all purchases', 'FREEUP', 0.10, '2024-02-11', '2024-02-17');

-- --------------------------------------------------------

--
-- Table structure for table `tbluser`
--

CREATE TABLE `tbluser` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(11) NOT NULL DEFAULT 'guest',
  `date_of_registration` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbluser`
--

INSERT INTO `tbluser` (`id`, `customer_name`, `email`, `username`, `password`, `role`, `date_of_registration`) VALUES
(9, 'Sendo', 'sendo@gmail.com', 'xdo123', '$2y$10$bUqmv08S7XMeHV4DZ2NSDuucG9p7BwM3RtCgpzyHHeC8vTx7dq2am', 'guest', '2024-03-29 12:44:03'),
(10, 'Sendo Galang', 'odnes@gmail.com', 'sendo123', '$2y$10$ks2bC7Ez3Oc1SqICfCbylu1gg/w28jWoNYnDfo0MYDTGpbYfrVjmO', 'admin', '2024-03-29 12:46:02'),
(11, 'Jeffel Madula', 'jeffel@example.com', 'jeffel123', '$2y$10$3CJVRwaRV8SJA5sSAd4gaOMmY9eTc4TP9n4pMh.fMhOmpcdABYHMa', 'admin', '2024-03-29 12:49:51'),
(12, 'Kurby', 'kurby@gmail.com', 'kurby', '$2y$10$68yUATYNr5N94obo7QyQleqhmQQFbP8tZDexM.V23uLfmYTA8QcAG', 'guest', '2024-03-29 12:53:59'),
(13, 'Test', 'test@gmail.com', 'test123', '$2y$10$qca.TQG9r3Swm1ukUB09i.rC5bD0nd8i4sTPuxsJolMMH2gXcijXe', 'guest', '2024-03-29 13:31:17'),
(14, 'Test', 'kurtdiestro@gmail.com', 'test', '$2y$10$sZa1.2aH0aCzEOJyctWICuKMuAEDgVN2Mhu/LHCqDgQdStm2Kwore', 'guest', '2024-03-29 13:32:07'),
(15, 'example', 'example@gmail.com', 'example', '$2y$10$1eMXin60acXoNAEjl4Jn2eCyCjTbXqgUb3f62fOvuScH83DVKWu0u', 'guest', '2024-04-07 14:26:29'),
(16, 'Test', 'sendo1111@gmail.com', 'test', '$2y$10$48rdsHHNsvQzm4oZQC7LrOOycyKfTTcMGCxnJdTsjRSIX6ghQx.i.', 'guest', '2024-04-20 11:05:00');

-- --------------------------------------------------------

--
-- Table structure for table `tbluserlogs`
--

CREATE TABLE `tbluserlogs` (
  `logid` int(11) NOT NULL,
  `log_datetime` datetime NOT NULL,
  `loginfo` varchar(255) NOT NULL,
  `employeeid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbluserlogs`
--

INSERT INTO `tbluserlogs` (`logid`, `log_datetime`, `loginfo`, `employeeid`) VALUES
(154, '2024-04-23 23:04:19', 'jan@gmail.com has edited an employee information.', 34),
(155, '2024-04-23 23:12:50', 'jan@gmail.com has added a new employee.', 34),
(156, '2024-04-23 23:13:14', 'jan@gmail.com has edited an employee information.', 34),
(157, '2024-04-23 23:14:38', 'jan@gmail.com has edited an employee information.', 34),
(158, '2024-04-23 23:15:08', 'jan@gmail.com has added a new employee.', 34),
(159, '2024-04-23 23:15:17', 'jan@gmail.com has deleted a employee.', 34),
(160, '2024-04-23 23:15:19', 'jan@gmail.com has deleted a employee.', 34),
(161, '2024-04-24 16:05:37', 'jan@gmail.com has added a new employee.', 34),
(162, '2024-04-24 16:05:47', 'jan@gmail.com has edited an employee information.', 34),
(163, '2024-04-24 16:05:51', 'jan@gmail.com has edited an employee information.', 34),
(164, '2024-04-24 16:06:07', 'jan@gmail.com has edited an employee information.', 34),
(165, '2024-04-24 16:06:16', 'jan@gmail.com has edited an employee information.', 34),
(166, '2024-04-24 16:06:25', 'jan@gmail.com has edited an employee information.', 34),
(167, '2024-04-24 16:06:32', 'jan@gmail.com has deleted a employee.', 34),
(168, '2024-04-25 14:06:17', 'jan@gmail.com has added a new employee.', 34),
(169, '2024-04-25 14:06:24', 'jan@gmail.com has edited an employee information.', 34),
(170, '2024-04-25 14:06:31', 'jan@gmail.com has edited an employee information.', 34),
(171, '2024-04-25 14:06:35', 'jan@gmail.com has deleted a employee.', 34),
(172, '2024-04-25 14:15:14', 'jan@gmail.com has deleted a employee.', 34),
(173, '2024-04-25 14:19:25', 'jan@gmail.com has edited an employee information.', 34),
(174, '2024-04-25 14:19:33', 'jan@gmail.com has edited an employee information.', 34),
(175, '2024-04-25 14:22:41', 'jan@gmail.com has added a new employee.', 34),
(176, '2024-04-25 14:23:45', 'jan@gmail.com has added a new employee.', 34),
(177, '2024-04-25 14:24:35', 'jan@gmail.com has edited an employee information.', 34),
(178, '2024-04-25 14:49:14', 'jan@gmail.com has edited an employee information.', 34),
(179, '2024-04-25 23:32:37', 'jan@gmail.com has edited an employee information.', 34),
(180, '2024-04-26 19:41:27', 'jan@gmail.com has edited an employee information.', 34),
(181, '2024-04-26 19:41:40', 'jan@gmail.com has edited an employee information.', 34),
(182, '2024-04-26 19:42:01', 'justin@gmail.com has edited an employee information.', 44),
(183, '2024-04-26 23:15:22', 'jp@gmail.com has edited an employee information.', 42),
(184, '2024-04-26 23:17:23', 'jp@gmail.com has edited an employee information.', 42),
(185, '2024-04-26 23:17:27', 'jp@gmail.com has edited an employee information.', 42),
(186, '2024-04-26 23:17:44', 'jp@gmail.com has added a new employee.', 42),
(187, '2024-04-26 23:17:50', 'jp@gmail.com has deleted a employee.', 42),
(188, '2024-04-26 23:20:28', 'jp@gmail.com has added a new employee.', 42),
(189, '2024-04-26 23:25:39', 'jp@gmail.com has edited an employee information.', 42),
(190, '2024-04-26 23:28:34', 'jp@gmail.com has edited an employee information.', 42),
(191, '2024-04-26 23:28:52', 'jp@gmail.com has edited an employee information.', 42),
(192, '2024-04-26 23:31:04', 'jp@gmail.com has added a new employee.', 42),
(193, '2024-04-26 23:31:14', 'jp@gmail.com has edited an employee information.', 42),
(194, '2024-04-26 23:31:23', 'jp@gmail.com has edited an employee information.', 42),
(195, '2024-04-26 23:31:28', 'jp@gmail.com has deleted a employee.', 42),
(196, '2024-04-26 23:31:32', 'jp@gmail.com has deleted a employee.', 42),
(197, '2024-07-06 11:46:38', 'jan@gmail.com has edited an employee information.', 34);

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

CREATE TABLE `testimonials` (
  `id` int(11) NOT NULL,
  `body` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tblcart`
--
ALTER TABLE `tblcart`
  ADD PRIMARY KEY (`cartID`),
  ADD KEY `customerid` (`customerid`);

--
-- Indexes for table `tblcartitem`
--
ALTER TABLE `tblcartitem`
  ADD PRIMARY KEY (`cartitemID`),
  ADD KEY `productid` (`productid`),
  ADD KEY `cartid` (`cartid`);

--
-- Indexes for table `tblcategory_inventory`
--
ALTER TABLE `tblcategory_inventory`
  ADD PRIMARY KEY (`categoryInventory_id`);

--
-- Indexes for table `tblcategory_product`
--
ALTER TABLE `tblcategory_product`
  ADD PRIMARY KEY (`categoryProduct_id`);

--
-- Indexes for table `tblcoffeeshop`
--
ALTER TABLE `tblcoffeeshop`
  ADD PRIMARY KEY (`coffeeshopid`);

--
-- Indexes for table `tblcustomers`
--
ALTER TABLE `tblcustomers`
  ADD PRIMARY KEY (`customerid`);

--
-- Indexes for table `tblemployees`
--
ALTER TABLE `tblemployees`
  ADD PRIMARY KEY (`employeeID`) USING BTREE;

--
-- Indexes for table `tblfeedback`
--
ALTER TABLE `tblfeedback`
  ADD PRIMARY KEY (`feedbackid`),
  ADD KEY `customerid` (`customerid`);

--
-- Indexes for table `tblinventory`
--
ALTER TABLE `tblinventory`
  ADD PRIMARY KEY (`inventory_id`);

--
-- Indexes for table `tblinventoryitems`
--
ALTER TABLE `tblinventoryitems`
  ADD PRIMARY KEY (`tblinventoryitems_id`);

--
-- Indexes for table `tblinventoryreport`
--
ALTER TABLE `tblinventoryreport`
  ADD PRIMARY KEY (`inventory_report_id`);

--
-- Indexes for table `tblorderitem`
--
ALTER TABLE `tblorderitem`
  ADD PRIMARY KEY (`orderitem_id`);

--
-- Indexes for table `tblorders`
--
ALTER TABLE `tblorders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `tblpayment`
--
ALTER TABLE `tblpayment`
  ADD PRIMARY KEY (`paymentID`);

--
-- Indexes for table `tblproducts`
--
ALTER TABLE `tblproducts`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `tblproducts_inventory`
--
ALTER TABLE `tblproducts_inventory`
  ADD PRIMARY KEY (`productsInventory_id`);

--
-- Indexes for table `tblpromo`
--
ALTER TABLE `tblpromo`
  ADD PRIMARY KEY (`promoid`);

--
-- Indexes for table `tbluser`
--
ALTER TABLE `tbluser`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbluserlogs`
--
ALTER TABLE `tbluserlogs`
  ADD PRIMARY KEY (`logid`),
  ADD KEY `employeeid` (`employeeid`);

--
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tblcart`
--
ALTER TABLE `tblcart`
  MODIFY `cartID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblcartitem`
--
ALTER TABLE `tblcartitem`
  MODIFY `cartitemID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblcategory_inventory`
--
ALTER TABLE `tblcategory_inventory`
  MODIFY `categoryInventory_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `tblcategory_product`
--
ALTER TABLE `tblcategory_product`
  MODIFY `categoryProduct_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `tblcoffeeshop`
--
ALTER TABLE `tblcoffeeshop`
  MODIFY `coffeeshopid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tblcustomers`
--
ALTER TABLE `tblcustomers`
  MODIFY `customerid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tblemployees`
--
ALTER TABLE `tblemployees`
  MODIFY `employeeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `tblfeedback`
--
ALTER TABLE `tblfeedback`
  MODIFY `feedbackid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `tblinventory`
--
ALTER TABLE `tblinventory`
  MODIFY `inventory_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `tblinventoryitems`
--
ALTER TABLE `tblinventoryitems`
  MODIFY `tblinventoryitems_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `tblinventoryreport`
--
ALTER TABLE `tblinventoryreport`
  MODIFY `inventory_report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=188;

--
-- AUTO_INCREMENT for table `tblorderitem`
--
ALTER TABLE `tblorderitem`
  MODIFY `orderitem_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=128;

--
-- AUTO_INCREMENT for table `tblorders`
--
ALTER TABLE `tblorders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=293;

--
-- AUTO_INCREMENT for table `tblpayment`
--
ALTER TABLE `tblpayment`
  MODIFY `paymentID` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=277;

--
-- AUTO_INCREMENT for table `tblproducts`
--
ALTER TABLE `tblproducts`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT for table `tblproducts_inventory`
--
ALTER TABLE `tblproducts_inventory`
  MODIFY `productsInventory_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=170;

--
-- AUTO_INCREMENT for table `tblpromo`
--
ALTER TABLE `tblpromo`
  MODIFY `promoid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tbluser`
--
ALTER TABLE `tbluser`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `tbluserlogs`
--
ALTER TABLE `tbluserlogs`
  MODIFY `logid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=198;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tblcart`
--
ALTER TABLE `tblcart`
  ADD CONSTRAINT `tblcart_ibfk_1` FOREIGN KEY (`customerid`) REFERENCES `tblcustomers` (`customerid`);

--
-- Constraints for table `tblcartitem`
--
ALTER TABLE `tblcartitem`
  ADD CONSTRAINT `tblcartitem_ibfk_1` FOREIGN KEY (`productid`) REFERENCES `tblproducts` (`product_id`),
  ADD CONSTRAINT `tblcartitem_ibfk_2` FOREIGN KEY (`cartid`) REFERENCES `tblcart` (`cartID`);

--
-- Constraints for table `tblcoffeeshop`
--
ALTER TABLE `tblcoffeeshop`
  ADD CONSTRAINT `tblcoffeeshop_ibfk_1` FOREIGN KEY (`employees_id`) REFERENCES `tblemployees` (`employeeID`);

--
-- Constraints for table `tblfeedback`
--
ALTER TABLE `tblfeedback`
  ADD CONSTRAINT `FK_tblfeedback_tbluser` FOREIGN KEY (`customerid`) REFERENCES `tblemployees` (`employeeID`);

--
-- Constraints for table `tbluserlogs`
--
ALTER TABLE `tbluserlogs`
  ADD CONSTRAINT `tbluserlogs_ibfk_2` FOREIGN KEY (`employeeid`) REFERENCES `tblemployees` (`employeeID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
