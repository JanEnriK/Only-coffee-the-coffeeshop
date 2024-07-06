-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 06, 2024 at 03:44 PM
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
  `shopname` varchar(255) NOT NULL,
  `branch` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `contact_no` varchar(11) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `employees_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblcoffeeshop`
--

INSERT INTO `tblcoffeeshop` (`coffeeshopid`, `shopname`, `branch`, `address`, `contact_no`, `email`, `employees_id`) VALUES
(1, 'Only Coffee', 'Legarda Manila ', '2255 Legarda St, Sampaloc, 1008 Metro Manila', '09156351463', 'onlycoffee@gmail.com', NULL);

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
(34, 'Jan', 'Manuel', 'jan@gmail.com', 'admin', '2024-04-23', 'enrique', '$2y$10$woVQnRr/aNbyJaSU6BxFuu03QDnKz34oqzPzYH6mKLzKNk0ZQBRGa'),
(35, 'test', 'test3', 'test@gmail.com', 'guest', '2024-04-23', 'testing', '$2y$10$FO9R0sczJKEUWd.AO7Ga8O0UXDHjL2v9UsKGU6l39ASpA7Z4WB85C'),
(39, 'Juan', 'Luna', 'juan@gmail.com', 'guest', '2024-04-23', 'juan', '$2y$10$uerZ8nU9of.PLLelQqufcec3cz5ubJL.mbCdGGbdFTYfWLx5Y7Cvm'),
(42, 'JP', 'Olarte', 'jp@gmail.com', 'admin', '2024-04-25', 'jp', '$2y$10$VVzxH5W.aTdBNNse4dzwRe4/iMvJ50hS.8rvsx.lztgH8kBv0S0KG'),
(44, 'justin', 'japson', 'justin@gmail.com', 'admin', '2024-04-26', 'justin', '$2y$10$IWQQ7taqWigMODDzty9sne727TSQbKysBdNk2i3ygVRIXjcd.8nfO'),
(66, 'cesar', 'salazar', 'cesarsalad@cesar.com', 'guest', '2024-05-02', 'cesar', '$2y$10$LrmSkroLFaFJH9yTqXjlmeovCwegu.7IPLru8MsuftiM68eh/ZFHS');

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
(24, 'good environment', 'the venue of the shop has a good ambience', '2024-05-02 16:54:06', 66);

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
(3, 'Powdered Sugar', 'Sweetener', 2010, 'grams', 100, 'In Stock'),
(4, 'Arrabica Coffee Bean', 'Coffee Bean', 4990, 'grams', 500, 'In Stock'),
(5, 'Liberica Coffee Bean', 'Coffee Bean', 480, 'grams', 500, 'Low Stock'),
(6, 'Oat Milk', 'Milk', 1000, 'ml', 500, 'In Stock'),
(7, 'Soy Milk', 'Milk', 985, 'ml', 500, 'In Stock'),
(8, 'Pearls', 'Sinker', 1000, 'grams', 500, 'In Stock'),
(9, 'Nata De Coco', 'Sinker', 1000, 'grams', 500, 'In Stock'),
(12, 'Cream', 'Toppings', 995, 'grams', 500, 'In Stock'),
(13, 'Marshmallows', 'Toppings', 1500, 'grams', 500, 'In Stock'),
(14, 'Caramel', 'Flavor', 1000, 'ml', 500, 'In Stock'),
(15, 'Matcha', 'Flavor', 500, 'grams', 500, 'Low Stock'),
(16, 'Oreo Cookies', 'Flavor', 1000, 'grams', 500, 'In Stock'),
(48, 'Brown Sugar', 'Sweetener', 5000, 'grams', 500, 'In Stock');

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
  `reason` varchar(255) NOT NULL,
  `datetime` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblinventoryreport`
--

INSERT INTO `tblinventoryreport` (`inventory_report_id`, `inventory_item`, `inventory_id`, `quantity`, `unit`, `reason`, `datetime`) VALUES
(27, 'testInv1', 46, 40, 'grams', 'added supply for testInv1', '2024-05-03 21:53:37'),
(28, 'testInv2', 47, 20, 'grams', 'added supply for testInv2', '2024-05-03 21:53:51'),
(29, 'testInv1', 46, -30, 'grams', 'inventory deduct for preparing 3 testProd', '2024-05-03 21:54:54'),
(30, 'testInv2', 47, -15, 'grams', 'inventory deduct for preparing 3 testProd', '2024-05-03 21:54:54'),
(31, 'testInv1', 46, -11, 'grams', 'spoiled', '2024-05-03 22:12:31'),
(32, 'testInv1', 46, -9, 'grams', 'broken', '2024-05-03 22:13:13'),
(33, 'testInv1', 46, 1, 'grams', 'added supply for testInv1', '2024-05-03 22:14:01'),
(34, 'testInv1', 46, 9, 'grams', 'added supply for testInv1', '2024-05-03 22:29:33'),
(35, 'testInv1', 46, -1, 'grams', 'spilled', '2024-05-03 22:31:33'),
(36, 'testInv1', 46, 1, 'grams', 'added supply for testInv1', '2024-05-03 22:35:43'),
(37, 'testInv1', 46, -1, 'grams', 'spoiler', '2024-05-03 22:39:05'),
(38, 'testInv1', 46, 1, 'grams', 'added supply for testInv1', '2024-05-03 22:48:10'),
(39, 'Cream', 12, -5, 'grams', 'inventory deduct for preparing 1 Vanilla Cream Frappe', '2024-05-06 22:32:29'),
(40, 'Liberica Coffee Bean', 5, -10, 'grams', 'inventory deduct for preparing 1 Vanilla Cream Frappe', '2024-05-06 22:32:29'),
(41, 'Soy Milk', 7, -15, 'ml', 'inventory deduct for preparing 1 Vanilla Cream Frappe', '2024-05-06 22:32:29'),
(42, 'Arrabica Coffee Bean', 4, -10, 'grams', 'inventory deduct for preparing 1 Espresso Machiato', '2024-05-16 11:04:44'),
(43, 'Matcha', 15, -5, 'grams', 'inventory deduct for preparing 1 Espresso Machiato', '2024-05-16 11:04:44'),
(44, 'Liberica Coffee Bean', 5, -20, 'grams', 'accidentally spilled', '2024-05-19 23:51:10'),
(45, 'Liberica Coffee Bean', 5, -30, 'grams', 'another spillage', '2024-05-19 23:53:46'),
(46, 'Liberica Coffee Bean', 5, 80, 'grams', 'added supply for Liberica Coffee Bean', '2024-05-19 23:55:40'),
(47, 'Brown Sugar', 48, 5000, 'grams', 'New Inventory initial supply for Brown Sugar', '2024-05-20 10:20:30'),
(48, '123', 49, 123213, '123', 'New Inventory initial supply for 123', '2024-05-20 12:31:14'),
(49, 'Powdered Sugar', 3, 3, 'grams', 'added supply for Powdered Sugar', '2024-06-04 21:34:56'),
(50, 'Powdered Sugar', 3, 7, 'grams', 'added supply for Powdered Sugar', '2024-06-04 21:35:14');

-- --------------------------------------------------------

--
-- Table structure for table `tblorderitem`
--

CREATE TABLE `tblorderitem` (
  `orderitem_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT NULL,
  `status` enum('active','completed','ended') NOT NULL,
  `orderid` int(11) DEFAULT NULL,
  `productid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblorderitem`
--

INSERT INTO `tblorderitem` (`orderitem_id`, `quantity`, `status`, `orderid`, `productid`) VALUES
(9, 3, 'completed', 110, 17),
(15, 1, 'completed', 121, 7),
(16, 2, 'completed', 121, 17),
(17, 1, 'completed', 121, 21),
(18, 1, 'completed', 122, 11),
(19, 1, 'completed', 122, 15),
(20, 1, 'completed', 123, 17),
(21, 1, 'completed', 123, 15),
(22, 1, 'completed', 124, 15),
(23, 1, 'completed', 124, 11),
(24, 1, 'completed', 124, 16),
(25, 1, 'completed', 125, 7),
(26, 1, 'completed', 125, 21),
(27, 1, 'completed', 125, 17),
(49, 1, 'completed', 127, 10),
(50, 1, 'completed', 127, 11),
(51, 1, 'completed', 128, 10),
(52, 1, 'completed', 128, 11),
(53, 1, 'completed', 129, 73),
(54, 1, 'completed', 130, 73),
(55, 3, 'completed', 131, 73),
(56, 3, 'completed', 132, 73),
(57, 2, 'ended', 126, 15),
(58, 1, 'completed', 135, 10),
(59, 1, 'completed', 133, 11),
(60, 1, 'active', 134, 16);

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
  `order_status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblorders`
--

INSERT INTO `tblorders` (`order_id`, `order_type`, `order_datetime`, `quantity`, `base_coffee_id`, `customer_id`, `order_number`, `order_status`) VALUES
(108, 'take-out', '2024-04-29 21:08:28', 1, 11, 34, 101, 'payed'),
(109, 'take-out', '2024-04-29 21:08:28', 1, 16, 34, 101, 'payed'),
(110, 'take-out', '2024-04-29 21:08:47', 1, 15, 34, 101, 'payed'),
(111, 'take-out', '2024-04-29 21:14:59', 1, 7, 34, 102, 'payed'),
(112, 'take-out', '2024-04-29 21:14:59', 1, 11, 34, 102, 'payed'),
(113, 'take-out', '2024-04-29 21:15:30', 1, 70, 34, 103, 'payed'),
(114, 'take-out', '2024-04-29 21:15:30', 1, 17, 34, 103, 'payed'),
(115, 'take-out', '2024-04-29 22:07:30', 1, 7, 34, 104, 'payed'),
(116, 'take-out', '2024-04-29 22:07:45', 1, 11, 34, 105, 'payed'),
(117, 'take-out', '2024-04-29 23:38:58', 1, 7, 34, 106, 'payed'),
(118, 'take-out', '2024-04-29 23:39:10', 1, 11, 34, 107, 'payed'),
(122, 'take-out', '2024-05-01 16:14:56', 3, 17, 34, 110, 'payed'),
(123, 'take-out', '2024-05-01 20:59:13', 1, 7, 44, 111, 'payed'),
(124, 'take-out', '2024-05-01 20:59:13', 3, 15, 44, 111, 'payed'),
(125, 'take-out', '2024-05-01 21:17:05', 1, 7, 44, 112, 'payed'),
(126, 'take-out', '2024-05-01 21:22:04', 2, 17, 44, 113, 'payed'),
(127, 'take-out', '2024-05-01 21:22:04', 1, 15, 44, 113, 'payed'),
(128, 'take-out', '2024-05-02 12:12:08', 2, 11, 34, 114, 'payed'),
(129, 'take-out', '2024-05-02 12:12:08', 1, 15, 34, 114, 'payed'),
(130, 'take-out', '2024-05-02 12:12:09', 2, 16, 34, 114, 'payed'),
(131, 'take-out', '2024-05-02 12:12:27', 1, 7, 34, 115, 'payed'),
(132, 'take-out', '2024-05-02 12:12:34', 1, 16, 34, 116, 'payed'),
(133, 'take-out', '2024-05-02 12:12:44', 1, 21, 34, 117, 'payed'),
(134, 'take-out', '2024-05-02 12:12:58', 1, 7, 34, 118, 'payed'),
(135, 'take-out', '2024-05-02 12:26:18', 1, 7, 34, 119, 'payed'),
(136, 'take-out', '2024-05-02 12:48:21', 1, 15, 34, 120, 'payed'),
(137, 'take-out', '2024-05-02 12:48:21', 1, 16, 34, 120, 'payed'),
(141, 'take-out', '2024-05-02 13:38:02', 1, 7, 39, 121, 'payed'),
(142, 'take-out', '2024-05-02 13:38:02', 2, 17, 39, 121, 'payed'),
(143, 'take-out', '2024-05-02 13:38:02', 1, 21, 39, 121, 'payed'),
(144, 'take-out', '2024-05-02 13:38:23', 1, 11, 39, 122, 'payed'),
(145, 'take-out', '2024-05-02 13:38:23', 1, 15, 39, 122, 'payed'),
(146, 'take-out', '2024-05-02 13:38:38', 1, 17, 39, 123, 'payed'),
(147, 'take-out', '2024-05-02 13:38:38', 1, 15, 39, 123, 'payed'),
(148, 'take-out', '2024-05-02 13:39:21', 1, 15, 35, 124, 'payed'),
(149, 'take-out', '2024-05-02 13:39:21', 1, 11, 35, 124, 'payed'),
(150, 'take-out', '2024-05-02 13:39:21', 1, 16, 35, 124, 'payed'),
(151, 'take-out', '2024-05-02 16:55:20', 1, 7, 66, 125, 'payed'),
(152, 'take-out', '2024-05-02 16:55:20', 1, 21, 66, 125, 'payed'),
(153, 'take-out', '2024-05-02 16:55:20', 1, 17, 66, 125, 'payed'),
(180, 'take-out', '2024-05-03 11:06:36', 2, 15, 34, 126, 'payed'),
(181, 'take-out', '2024-05-03 11:48:37', 1, 10, 34, 127, 'payed'),
(182, 'take-out', '2024-05-03 11:48:37', 1, 11, 34, 127, 'payed'),
(183, 'take-out', '2024-05-03 11:52:06', 1, 10, 34, 128, 'payed'),
(184, 'take-out', '2024-05-03 11:52:07', 1, 11, 34, 128, 'payed'),
(185, 'take-out', '2024-05-03 20:52:14', 1, 73, 1, 129, 'payed'),
(186, 'take-out', '2024-05-03 21:30:40', 1, 73, 1, 130, 'payed'),
(187, 'take-out', '2024-05-03 21:49:13', 3, 73, 1, 131, 'payed'),
(188, 'take-out', '2024-05-03 21:54:21', 3, 73, 1, 132, 'payed'),
(189, 'take-out', '2024-05-04 20:40:54', 1, 11, 1, 133, 'payed'),
(190, 'take-out', '2024-05-04 20:55:35', 1, 16, 1, 134, 'payed'),
(191, 'take-out', '2024-05-06 22:23:07', 1, 10, 39, 135, 'payed');

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
  `reference_no` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblpayment`
--

INSERT INTO `tblpayment` (`paymentID`, `order_datetime`, `amountpayed`, `paymenttype`, `customerid`, `orderNumber`, `reference_no`) VALUES
(182, '2024-05-02 09:23:04', 260.00, 'cash', 44, 113, NULL),
(183, '2024-05-02 09:35:31', 117.00, 'online', 44, 112, '12312312'),
(184, '2024-05-02 09:39:23', 90.00, 'cash', 34, 107, NULL),
(185, '2024-05-02 10:05:27', 65.00, 'cash', 34, 106, NULL),
(186, '2024-05-02 11:00:45', 365.00, 'cash', 44, 111, NULL),
(187, '2024-05-02 12:02:42', 351.00, 'online', 34, 110, 'qweqwe'),
(188, '2024-05-02 12:04:57', 100.00, 'cash', 34, 105, NULL),
(189, '2024-05-02 12:07:00', 117.00, 'cash', 34, 104, NULL),
(190, '2024-05-02 12:07:41', 117.00, 'online', 34, 103, '1231'),
(191, '2024-05-02 12:07:53', 230.00, 'cash', 34, 102, NULL),
(192, '2024-05-02 12:08:05', 500.00, 'online', 34, 101, '2352342342'),
(193, '2024-05-02 12:20:12', 130.00, 'online', 34, 118, '123123123'),
(194, '2024-05-02 12:20:24', 65.00, 'cash', 34, 117, NULL),
(195, '2024-05-02 12:20:38', 200.00, 'cash', 34, 116, NULL),
(196, '2024-05-02 12:20:46', 130.00, 'cash', 34, 115, NULL),
(197, '2024-05-02 12:21:06', 720.00, 'online', 34, 114, '1231231123223'),
(198, '2024-05-02 12:35:36', 130.00, 'cash', 34, 119, NULL),
(199, '2024-05-02 12:48:45', 400.00, 'cash', 34, 120, NULL),
(200, '2024-05-02 13:05:54', 200.00, 'cash', 34, 121, NULL),
(201, '2024-05-02 13:09:22', 200.00, 'cash', 34, 121, NULL),
(202, '2024-05-02 13:10:24', 265.00, 'cash', 34, 122, NULL),
(203, '2024-05-02 13:39:51', 468.00, 'online', 39, 121, '#wdf22341'),
(204, '2024-05-02 13:40:00', 300.00, 'cash', 39, 122, NULL),
(205, '2024-05-02 13:40:09', 330.00, 'cash', 39, 123, NULL),
(206, '2024-05-02 13:40:26', 250.00, 'online', 35, 124, '123dfgsd'),
(207, '2024-05-02 16:57:10', 351.00, 'online', 66, 125, '#wdf24y524'),
(219, '2024-05-03 11:48:37', 300.00, 'cash', 34, 127, NULL),
(220, '2024-05-03 11:52:07', 300.00, 'online', 34, 128, 'drtfgh'),
(221, '2024-05-03 20:52:14', 50.00, 'cash', 1, 129, NULL),
(222, '2024-05-03 21:30:40', 90.00, 'cash', 1, 130, NULL),
(223, '2024-05-03 21:49:13', 300.00, 'cash', 1, 131, NULL),
(224, '2024-05-03 21:54:21', 300.00, 'online', 1, 132, 'w34e5r6t7yui'),
(225, '2024-05-04 08:49:06', 400.00, 'cash', 34, 126, NULL),
(226, '2024-05-06 22:28:38', 200.00, 'cash', 39, 135, NULL),
(227, '2024-05-14 22:57:20', 100.00, 'online', 1, 133, '1231'),
(228, '2024-05-14 22:57:36', 200.00, 'cash', 1, 134, NULL);

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
(7, 'Salted Caramel Cold Breww', 'a salted caramel coffee that is brewed colddss', 130.00, 'coffee-3727673_640.jpg', 50, 'Available', 'brewed'),
(10, 'Vanilla Cream Frappe', 'A coffee that is frapped with vanilla cream', 200.00, 'stock-of-mix-a-cup-coffee-latte-more-motive-top-view-foodgraphy-generative-ai-photo.jpg', 48, 'Available', 'frappe'),
(11, 'Iced Americano', 'A coffee that is americanized with ice', 100.00, 'stock-of-mix-a-cup-coffee-latte-more-motive-top-view-foodgraphy-generative-ai-photo.jpg', 24, 'Available', 'frappe'),
(15, 'Iced White Chocolate mocha', 'A white chocolate flavored coffee with ice and mocha', 200.00, 'stock-of-mix-a-cup-coffee-latte-more-motive-top-view-foodgraphy-generative-ai-photo.jpg', 100, 'Available', 'espresso'),
(16, 'Espresso Machiato', 'A expressed coffee with macchiato', 200.00, 'stock-of-mix-a-cup-coffee-latte-more-motive-top-view-foodgraphy-generative-ai-photo.jpg', 100, 'Available', 'espresso'),
(17, 'Iced caffe latte', 'a coffee with ice and latted', 130.00, 'stock-of-mix-a-cup-coffee-latte-more-motive-top-view-foodgraphy-generative-ai-photo.jpg', 100, 'Available', 'latte'),
(21, 'Iced Special Cappuccinoo', 'a coffee with ice and cappucinized but its special', 130.00, 'stock-of-mix-a-cup-coffee-latte-more-motive-top-view-foodgraphy-generative-ai-photo.jpg', 50, 'Available', 'cappuccino');

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
(146, 17, 4, 5),
(147, 17, 6, 10),
(148, 7, 14, 5),
(149, 7, 4, 10),
(150, 7, 6, 20),
(151, 10, 12, 5),
(152, 10, 5, 10),
(153, 10, 7, 15),
(154, 11, 5, 20),
(155, 15, 13, 5),
(156, 15, 14, 10),
(157, 15, 4, 10),
(158, 16, 4, 10),
(159, 16, 15, 5),
(163, 21, 4, 10),
(164, 21, 14, 12),
(165, 21, 6, 20);

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
(196, '2024-04-26 23:31:32', 'jp@gmail.com has deleted a employee.', 42);

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
  ADD PRIMARY KEY (`coffeeshopid`),
  ADD KEY `employees_id` (`employees_id`);

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
  MODIFY `employeeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `tblfeedback`
--
ALTER TABLE `tblfeedback`
  MODIFY `feedbackid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `tblinventory`
--
ALTER TABLE `tblinventory`
  MODIFY `inventory_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `tblinventoryreport`
--
ALTER TABLE `tblinventoryreport`
  MODIFY `inventory_report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `tblorderitem`
--
ALTER TABLE `tblorderitem`
  MODIFY `orderitem_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `tblorders`
--
ALTER TABLE `tblorders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=192;

--
-- AUTO_INCREMENT for table `tblpayment`
--
ALTER TABLE `tblpayment`
  MODIFY `paymentID` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=229;

--
-- AUTO_INCREMENT for table `tblproducts`
--
ALTER TABLE `tblproducts`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `tblproducts_inventory`
--
ALTER TABLE `tblproducts_inventory`
  MODIFY `productsInventory_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=168;

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
  MODIFY `logid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=197;

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
