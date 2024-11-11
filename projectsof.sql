-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 11, 2024 at 09:00 PM
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
-- Database: `projectsof`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `IDcart` int(100) NOT NULL,
  `IDuser` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact`
--

CREATE TABLE `contact` (
  `ContactNumber` int(100) NOT NULL,
  `Fname` varchar(10) NOT NULL,
  `Lname` varchar(10) NOT NULL,
  `mail` varchar(30) NOT NULL,
  `content` varchar(200) NOT NULL,
  `date` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `manager`
--

CREATE TABLE `manager` (
  `username` varchar(10) NOT NULL,
  `IdManager` int(10) NOT NULL,
  `Fname` varchar(10) NOT NULL,
  `Lname` varchar(10) NOT NULL,
  `Mail` varchar(30) NOT NULL,
  `password` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `manager`
--

INSERT INTO `manager` (`username`, `IdManager`, `Fname`, `Lname`, `Mail`, `password`) VALUES
('naiefsh', 1, 'naief', 'shebel', 'naief@gmail.com', '12345'),
('ayasa', 2, 'aya', 'sabea', 'ayasabea17@gmail.com', '23456');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `productCode` int(100) NOT NULL,
  `productName` varchar(30) NOT NULL,
  `price` int(100) NOT NULL,
  `color` varchar(30) NOT NULL,
  `source` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`productCode`, `productName`, `price`, `color`, `source`) VALUES
(1, 'Calvin Klein', 100, 'Oil Green', 'photo\\t1.webp'),
(2, 'Calvin Klein', 110, 'Black', 'photo/t2.webp'),
(3, 'Calvin Klein', 120, 'White', 'photo/t3.webp'),
(4, 'Calvin Klein', 130, 'Blue', 'photo/t4.webp'),
(5, 'Calvin Klein', 150, 'Blue', 'photo/t5.webp'),
(6, 'Jeans', 100, 'blue', 'photo/j1.jpg'),
(7, 'Jeans', 100, 'blue', 'photo/j2.jpg'),
(8, 'Jeans', 100, 'blue', 'photo/j3.jpg'),
(9, 'Jeans', 100, 'blue', 'photo/j4.jpg'),
(10, 'Jeans', 100, 'blue', 'photo/j5.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `proudincart`
--

CREATE TABLE `proudincart` (
  `IDcart` int(100) NOT NULL,
  `prodectCode` int(100) NOT NULL,
  `amount` int(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `username` varchar(10) NOT NULL,
  `Fname` varchar(20) NOT NULL,
  `Lname` varchar(10) NOT NULL,
  `password` int(30) NOT NULL,
  `IDuser` int(30) NOT NULL,
  `Mail` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`username`, `Fname`, `Lname`, `password`, `IDuser`, `Mail`) VALUES
('mosheko', 'moshe', 'kohen', 333, 123, 'moshe@gmail.com'),
('davidsa', 'david', 'salami', 444, 1234, 'david@gmail.com'),
('lalana', 'lala', 'nasrawi', 555, 12345, 'lala@gmail.com');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`IDcart`),
  ADD KEY `IDuser` (`IDuser`);

--
-- Indexes for table `manager`
--
ALTER TABLE `manager`
  ADD PRIMARY KEY (`IdManager`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`productCode`);

--
-- Indexes for table `proudincart`
--
ALTER TABLE `proudincart`
  ADD PRIMARY KEY (`IDcart`,`prodectCode`),
  ADD KEY `prodectCode` (`prodectCode`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`IDuser`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `IDuser` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12346;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`IDuser`) REFERENCES `users` (`IDuser`);

--
-- Constraints for table `proudincart`
--
ALTER TABLE `proudincart`
  ADD CONSTRAINT `proudincart_ibfk_1` FOREIGN KEY (`prodectCode`) REFERENCES `product` (`productCode`),
  ADD CONSTRAINT `proudincart_ibfk_2` FOREIGN KEY (`IDcart`) REFERENCES `cart` (`IDcart`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
