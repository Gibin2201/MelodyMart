-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 18, 2026 at 08:21 AM
-- Server version: 8.4.7
-- PHP Version: 8.5.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_orchestraone`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_adminlogin`
--

DROP TABLE IF EXISTS `tbl_adminlogin`;
CREATE TABLE IF NOT EXISTS `tbl_adminlogin` (
  `loginid` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`loginid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_adminlogin`
--

INSERT INTO `tbl_adminlogin` (`loginid`, `username`, `password`) VALUES
(1, 'admin', '4330');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_cart`
--

DROP TABLE IF EXISTS `tbl_cart`;
CREATE TABLE IF NOT EXISTS `tbl_cart` (
  `cart_id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `totalamount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `customer_id` int NOT NULL,
  `cart_date` date NOT NULL,
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'cart',
  `cartmaster_id` int DEFAULT NULL,
  PRIMARY KEY (`cart_id`),
  KEY `idx_cart_product` (`product_id`),
  KEY `idx_cart_customer` (`customer_id`),
  KEY `idx_cart_cartmaster` (`cartmaster_id`),
  KEY `idx_cart_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_cart`
--

INSERT INTO `tbl_cart` (`cart_id`, `product_id`, `quantity`, `totalamount`, `customer_id`, `cart_date`, `status`, `cartmaster_id`) VALUES
(4, 7, 2, 80.00, 1, '2025-09-15', 'paid', 3),
(5, 6, 1, 30.00, 1, '2025-09-15', 'paid', 3),
(6, 5, 2, 60.00, 1, '2025-09-15', 'paid', 4),
(8, 7, 2, 80.00, 1, '2025-09-25', 'paid', 5),
(9, 5, 2, 60.00, 2, '2025-09-29', 'paid', 6),
(10, 8, 1, 1234.00, 1, '2025-10-06', 'paid', 7),
(11, 6, 1, 30.00, 1, '2025-10-08', 'paid', 8),
(12, 5, 1, 30.00, 1, '2025-10-08', 'paid', 10),
(13, 9, 1, 1234.00, 5, '2025-10-09', 'paid', 9),
(16, 5, 3, 90.00, 1, '2025-10-10', 'paid', 12),
(17, 7, 2, 80.00, 1, '2025-10-10', 'paid', 12),
(18, 5, 1, 30.00, 1, '2025-10-10', 'cart', NULL),
(22, 13, 1, 89000.00, 7, '2026-10-22', 'paid', 13),
(24, 6, 1, 30.00, 7, '2026-10-22', 'paid', 14),
(25, 9, 1, 1234.00, 7, '2026-10-24', 'paid', 15),
(26, 7, 1, 40.00, 7, '2026-10-24', 'paid', 15),
(27, 13, 1, 89000.00, 7, '2026-11-03', 'paid', 15),
(29, 14, 1, 15000.00, 7, '2026-11-03', 'paid', 16),
(30, 14, 3, 45000.00, 7, '2026-11-03', 'paid', 17),
(31, 5, 1, 30.00, 7, '2026-11-03', 'paid', 18),
(32, 13, 1, 89000.00, 8, '2026-11-03', 'paid', 19),
(33, 11, 1, 58990.00, 7, '2026-11-18', 'cart', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_cartmaster`
--

DROP TABLE IF EXISTS `tbl_cartmaster`;
CREATE TABLE IF NOT EXISTS `tbl_cartmaster` (
  `cartmaster_id` int NOT NULL AUTO_INCREMENT,
  `grandtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `customer_id` int NOT NULL,
  `cmstatus` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'paid',
  PRIMARY KEY (`cartmaster_id`),
  KEY `idx_cartmaster_customer` (`customer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_cartmaster`
--

INSERT INTO `tbl_cartmaster` (`cartmaster_id`, `grandtotal`, `customer_id`, `cmstatus`) VALUES
(3, 110.00, 1, 'Paid'),
(4, 60.00, 1, 'Paid'),
(5, 80.00, 1, 'Paid'),
(6, 60.00, 2, 'Paid'),
(7, 1234.00, 1, 'Paid'),
(8, 30.00, 1, 'Paid'),
(9, 1234.00, 5, 'Paid'),
(10, 30.00, 1, 'Paid'),
(12, 170.00, 1, 'Paid'),
(13, 89000.00, 7, 'Paid'),
(14, 30.00, 7, 'Paid'),
(15, 90274.00, 7, 'Paid'),
(16, 15000.00, 7, 'Paid'),
(17, 45000.00, 7, 'Paid'),
(18, 30.00, 7, 'Paid'),
(19, 89000.00, 8, 'Paid');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_category`
--

DROP TABLE IF EXISTS `tbl_category`;
CREATE TABLE IF NOT EXISTS `tbl_category` (
  `category_id` int NOT NULL AUTO_INCREMENT,
  `category_name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `category_image` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_category`
--

INSERT INTO `tbl_category` (`category_id`, `category_name`, `category_image`) VALUES
(7, 'String Instruments', 'string.webp'),
(8, 'Wind Instruments', 'wind.webp'),
(9, 'Keyboard Instruments', 'key.webp'),
(10, 'Percussion', 'percussion.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_customer`
--

DROP TABLE IF EXISTS `tbl_customer`;
CREATE TABLE IF NOT EXISTS `tbl_customer` (
  `customer_id` int NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `location_id` int NOT NULL,
  `phone_no` bigint NOT NULL,
  `email` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `reg_date` date NOT NULL,
  PRIMARY KEY (`customer_id`),
  UNIQUE KEY `unique_customer_username` (`username`),
  KEY `idx_customer_location` (`location_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_customer`
--

INSERT INTO `tbl_customer` (`customer_id`, `customer_name`, `location_id`, `phone_no`, `email`, `username`, `password`, `reg_date`) VALUES
(1, 'Bissy', 4, 555555550, '', 'bissy12', 'bissy1234', '2025-07-31'),
(2, 'ar', 4, 2147483647, 'ar@gmail.com', 'ar', 'ar', '2025-08-26'),
(3, 'Bestinsaji', 4, 9695949392, 'bestinsaji@gmail.com', 'bestin', 'bestin@1234', '2025-09-29'),
(5, 'Adhi', 5, 9876543200, 'adhi@123', 'adhi123', 'adhi@123', '2025-10-09'),
(6, 'Test', 4, 9587461235, 'test@gmIL.COM', 'test', 'test@123', '2026-10-15'),
(7, 'Gibin Thomas', 7, 7558870208, 'gibinthomas2201@gmail.com', 'gibin', 'Gibin@2003', '2026-10-22'),
(8, 'Ehsan', 4, 7559097394, 'ehsannn@gmail.com', 'ehsan', 'Ehsan@123', '2026-11-03');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_deliveryaddress`
--

DROP TABLE IF EXISTS `tbl_deliveryaddress`;
CREATE TABLE IF NOT EXISTS `tbl_deliveryaddress` (
  `deliveryaddressid` int NOT NULL AUTO_INCREMENT,
  `personname` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `mobno` bigint NOT NULL,
  `address` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `pincode` int NOT NULL,
  `cartmasterid` int NOT NULL,
  `cartmaster_id` int GENERATED ALWAYS AS (`cartmasterid`) VIRTUAL,
  PRIMARY KEY (`deliveryaddressid`),
  KEY `idx_delivery_cartmaster` (`cartmasterid`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_deliveryaddress`
--

INSERT INTO `tbl_deliveryaddress` (`deliveryaddressid`, `personname`, `mobno`, `address`, `pincode`, `cartmasterid`) VALUES
(2, 'Gibin Thomas', 7558870208, 'Kallampillil (H) Chakkampuzha (p.o)', 686574, 13),
(3, 'Gibin Thomas', 7558870208, 'Kallampillil (H) Chakkampuzha (p.o)', 686574, 14),
(4, 'Gibin Thomas', 7558870208, 'Kallampillil (H) Chakkampuzha (p.o)', 686574, 15),
(5, 'Gibin Thomas', 7558870208, 'Kallampillil (H) Chakkampuzha (p.o)', 686574, 16),
(6, 'Gibin Thomas', 7558870208, 'Kallampillil (H) Chakkampuzha (p.o)', 686574, 17),
(7, 'Gibin Thomas', 7558870208, 'Kallampillil (H) Chakkampuzha (p.o)', 686574, 18),
(8, 'Ehsan', 7559097394, 'Thelampuram', 685585, 19);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_district`
--

DROP TABLE IF EXISTS `tbl_district`;
CREATE TABLE IF NOT EXISTS `tbl_district` (
  `district_id` int NOT NULL AUTO_INCREMENT,
  `district_name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`district_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_district`
--

INSERT INTO `tbl_district` (`district_id`, `district_name`) VALUES
(4, 'Idukki'),
(5, 'Kottayam'),
(6, 'Ernakulam'),
(7, 'Alappuzha'),
(8, 'palakad'),
(10, 'Kollam');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_location`
--

DROP TABLE IF EXISTS `tbl_location`;
CREATE TABLE IF NOT EXISTS `tbl_location` (
  `location_id` int NOT NULL AUTO_INCREMENT,
  `location_name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `district_id` int NOT NULL,
  PRIMARY KEY (`location_id`),
  KEY `idx_location_district` (`district_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_location`
--

INSERT INTO `tbl_location` (`location_id`, `location_name`, `district_id`) VALUES
(1, 'Kaloor', 1),
(4, 'thodupuzha', 4),
(5, 'Kochi', 6),
(7, 'Ettumanoor', 5),
(8, 'Cherthala', 7),
(10, 'Arthunkal', 7),
(11, 'Perinad ', 10),
(12, 'Muvattupuzha', 6);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_payment`
--

DROP TABLE IF EXISTS `tbl_payment`;
CREATE TABLE IF NOT EXISTS `tbl_payment` (
  `payment_id` int NOT NULL AUTO_INCREMENT,
  `cartmaster_id` int NOT NULL,
  `payment_date` date NOT NULL,
  `pamount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `customer_id` int NOT NULL,
  `pstatus` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `commission` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`payment_id`),
  KEY `idx_payment_cartmaster` (`cartmaster_id`),
  KEY `idx_payment_customer` (`customer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_payment`
--

INSERT INTO `tbl_payment` (`payment_id`, `cartmaster_id`, `payment_date`, `pamount`, `customer_id`, `pstatus`, `commission`) VALUES
(3, 3, '2025-09-15', 110.00, 1, 'Paid', 11.00),
(4, 4, '2025-09-15', 60.00, 1, 'Paid', 6.00),
(5, 5, '2025-09-25', 80.00, 1, 'Paid', 8.00),
(6, 6, '2025-09-29', 60.00, 2, 'Paid', 6.00),
(7, 7, '2025-10-06', 1234.00, 1, 'Paid', 123.00),
(8, 8, '2025-10-08', 30.00, 1, 'Paid', 3.00),
(9, 9, '2025-10-09', 1234.00, 5, 'Paid', 123.00),
(10, 10, '2025-10-10', 30.00, 1, 'Paid', 3.00),
(12, 12, '2025-10-10', 170.00, 1, 'Paid', 17.00),
(13, 13, '2026-10-22', 89000.00, 7, 'Paid', 8900.00),
(14, 14, '2026-10-22', 30.00, 7, 'Paid', 3.00),
(15, 15, '2026-11-03', 90274.00, 7, 'Paid', 9027.00),
(16, 16, '2026-11-03', 15000.00, 7, 'Paid', 1500.00),
(17, 17, '2026-11-03', 45000.00, 7, 'Paid', 4500.00),
(18, 18, '2026-11-03', 30.00, 7, 'Paid', 3.00),
(19, 19, '2026-11-03', 89000.00, 8, 'Paid', 8900.00);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_product`
--

DROP TABLE IF EXISTS `tbl_product`;
CREATE TABLE IF NOT EXISTS `tbl_product` (
  `product_id` int NOT NULL AUTO_INCREMENT,
  `product_name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `subcat` int NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `image` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `stock` int NOT NULL,
  `pstatus` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'available',
  `brand` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `subcategory_id` int GENERATED ALWAYS AS (`subcat`) VIRTUAL,
  PRIMARY KEY (`product_id`),
  KEY `idx_product_subcat` (`subcat`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_product`
--

INSERT INTO `tbl_product` (`product_id`, `product_name`, `subcat`, `amount`, `image`, `stock`, `pstatus`, `brand`, `description`) VALUES
(4, 'gee', 4, 5000.00, 'download.webp', 5, 'available', 'zooo', NULL),
(5, 'Yamaha APX600', 6, 30.00, 'gui1.jpg', 6, 'available', 'Yamaha', NULL),
(6, 'Yamaha V10G (Natural) Acoustic Violin', 7, 30.00, 'vio.jpg', 8, 'available', 'Vio', NULL),
(7, 'Stagg Levante Bb Clarinet LV-CL4100', 3, 40.00, 'clari.jpeg', 7, 'available', 'Stagg', NULL),
(8, 'bbb1', 6, 1234.00, 'pho.jpg', 201, 'available', 'cvcvq', NULL),
(9, 'A', 6, 1234.00, 'pho.jpg', 18, 'available', 'asdf', NULL),
(10, 'Fender Player Stratocaster', 6, 64999.00, 'Guitar.jpg', 12, 'available', 'Fender', 'Classic Stratocaster tone with alder body and player series Alnico 5 single-coil pickups.'),
(11, 'Yamaha P-125 Digital Stage Piano', 9, 58990.00, 'piano.jpeg', 8, 'available', 'Yamaha', 'Pure CF sound engine with 88 weighted GHS keys for authentic grand piano touch.'),
(12, 'Yamaha YTR-2330 Standard Bb Trumpet', 5, 42500.00, 'Trumpet.jpg', 6, 'available', 'Yamaha', 'Durable yellow brass bell with balanced weight distribution for students and performers.'),
(13, 'Roland V-Drums Acoustic Design Kit', 10, 89000.00, 'percussion.jpg', 12, 'available', 'Roland', 'Full-sized wooden drum shells with advanced multi-sensor mesh trigger pads.'),
(14, 'yamha piano', 9, 15000.00, 'Screenshot 2026-11-03 121157.png', 6, 'available', 'MelodyMart', 'wooden');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_subcategory`
--

DROP TABLE IF EXISTS `tbl_subcategory`;
CREATE TABLE IF NOT EXISTS `tbl_subcategory` (
  `subcategory_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci NOT NULL,
  `category_id` int NOT NULL,
  PRIMARY KEY (`subcategory_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_subcategory`
--

INSERT INTO `tbl_subcategory` (`subcategory_id`, `name`, `image`, `description`, `category_id`) VALUES
(3, 'Clarinet', 'Clarinet.jpg', ' A clarinet is a single-reed woodwind musical instrument with a cylindrical bore and a flared bell.', 8),
(4, 'Tuba', 'tuba.jpg', ' The tuba is the largest and lowest-sounding instrument in the brass family', 8),
(5, 'Trumpet', 'Trumpet.jpg', ' A trumpet is a metal wind instrument in the brass family that produces sound when a player buzzes their lips into a cup-shaped mouthpiece.', 8),
(6, 'Guitar', 'guitar.webp', 'Acoustic, classical, and electric guitars crafted with premium tonewoods.', 7),
(7, 'Violin', 'violin.webp', 'Handcrafted orchestral violins, violas, and cellos with rich acoustic resonance.', 7),
(8, 'Bassoon', 'bassoon.webp', 'Orchestral double-reed woodwinds and brass instruments for concert bands.', 8),
(9, 'Piano & Keyboards', 'piano.jpeg', 'Digital pianos, acoustic grand pianos, synthesizers, and stage controllers.', 9),
(10, 'Drums & Percussion', 'percussion.jpg', 'Acoustic drum sets, cymbals, electronic drum kits, and orchestral percussion.', 10);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_warranty`
--

DROP TABLE IF EXISTS `tbl_warranty`;
CREATE TABLE IF NOT EXISTS `tbl_warranty` (
  `warranty_id` int NOT NULL AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `cart_id` int NOT NULL,
  `applydate` date NOT NULL,
  `wimg` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `issuedesc` varchar(500) COLLATE utf8mb4_general_ci NOT NULL,
  `prod_img` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `wstatus` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `responsedate` date DEFAULT NULL,
  PRIMARY KEY (`warranty_id`),
  KEY `idx_warranty_cart` (`cart_id`),
  KEY `idx_warranty_customer` (`customer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_warranty`
--

INSERT INTO `tbl_warranty` (`warranty_id`, `customer_id`, `cart_id`, `applydate`, `wimg`, `issuedesc`, `prod_img`, `wstatus`, `responsedate`) VALUES
(3, 1, 4, '2025-09-18', 'key1.jpg', 'jjj', 'shop.webp', 'rejected', NULL),
(5, 2, 9, '2025-09-29', 'pho.jpg', 'kk', 'ss.jpg', 'rejected', NULL),
(6, 1, 10, '2025-10-06', 'ss.jpg', 'asdf', 'chash.jpg', 'accepted', '2025-10-23'),
(7, 5, 13, '2025-10-09', 'pho.jpg', 'broken', 'chash.jpg', 'accepted', '2025-10-16'),
(8, 7, 31, '2026-11-03', '1793688986_wcard_Screenshot2026-11-03121157.png', '54yhe56', '1793688986_defect_Screenshot2026-11-03121157.png', 'requested', NULL),
(9, 7, 31, '2026-11-03', '1793689083_wcard_Screenshot2026-11-03121157.png', 'bbs', '1793689083_defect_WhatsAppImage2026-10-08at11.59.4', 'requested', NULL);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_subcategory`
--
ALTER TABLE `tbl_subcategory`
  ADD CONSTRAINT `tbl_subcategory_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `tbl_category` (`category_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
