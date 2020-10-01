-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 01, 2020 at 03:59 AM
-- Server version: 10.1.35-MariaDB
-- PHP Version: 7.2.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `scm-db`
--
CREATE DATABASE IF NOT EXISTS `scm-db` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `scm-db`;

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `attempt_login` (IN `username` VARCHAR(24), IN `password` VARCHAR(32))  READS SQL DATA
BEGIN
SELECT (SELECT COUNT(`users`.`id`)
        FROM `users` 
        WHERE `users`.`username` = username AND
        `users`.`password` = password) = 1 AS 'Success';
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `checkout_order` (IN `order_id` INT)  NO SQL
BEGIN
UPDATE `customer_orders` SET `status`='processing' WHERE `customer_orders`.`id` = order_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_new_available_item` (IN `name` VARCHAR(32), IN `description` VARCHAR(1023), IN `price` FLOAT)  MODIFIES SQL DATA
BEGIN
INSERT INTO `available_items`(`name`, `description`, `price`) VALUES (name, description, price);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_new_user` (IN `username` VARCHAR(24), IN `password` VARCHAR(32), IN `role` ENUM('administrator','customer','transportation_associate'), IN `first_name` VARCHAR(24), IN `last_name` VARCHAR(24))  NO SQL
BEGIN
INSERT INTO `users` (`username`, `password`, `role`, `first_name`, `last_name`) VALUES (username, password, role, first_name, last_name);

IF role = 'customer' THEN
	INSERT INTO `customer_info`(`customer_id`) VALUES (LAST_INSERT_ID());
END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `relate_item_and_material` (IN `available_item_id` INT, IN `material_id` INT, IN `quantity` INT)  MODIFIES SQL DATA
    DETERMINISTIC
BEGIN
INSERT INTO `item_material_relations` (`item_id`, `material_id`, `quantity_of_material_required`) VALUES (available_item_id, material_id, quantity);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `relate_item_and_order` (IN `customer_id` INT, IN `available_item_id` INT, IN `quantity` INT)  MODIFIES SQL DATA
BEGIN
DECLARE order_id int;

/* Add an item to a customer's order, creating an order if one does not yet being made */

/* Insert a new custoemr order that is 'being_made' if one does not already exist */
INSERT INTO `customer_orders`(`customer_id`, `status`) (SELECT customer_id, 'being_made' WHERE NOT EXISTS (SELECT * FROM `customer_orders` WHERE `customer_id` = customer_id AND `status` = 'being_made'));

SELECT `customer_orders`.`id` INTO order_id FROM `customer_orders` WHERE `customer_id` = customer_id AND `status` = 'being_made' LIMIT 1;

INSERT INTO `order_item_relations`(`order_id`, `item_id`, `quantity_ordered`) VALUES (order_id, available_item_id, quantity);
                                                                                      
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `select_customer_info` (IN `customer_id` INT)  NO SQL
BEGIN
SELECT `customer_info`.`street_address`, `customer_info`.`city`, `customer_info`.`state`, `customer_info`.`zip_code`, `customer_info`.`card_number` 
FROM `customer_info` WHERE `customer_info`.`customer_id` = customer_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `select_item_materials_info` (IN `item_id` INT)  NO SQL
BEGIN

SELECT `available_items`.`name` AS 'Item Name', `materials`.`name` AS 'Material Name', `item_material_relations`.`quantity_of_material_required` AS 'Material Quantity',
`materials`.`units` AS 'Units'
FROM `available_items` LEFT JOIN `item_material_relations` 
ON (`available_items`.`id` = `item_material_relations`.`item_id`) LEFT JOIN `materials` ON (`item_material_relations`.`material_id` = `materials`.`id`) WHERE `available_items`.`id` = item_id;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `select_item_stock` (IN `item_id` INT)  NO SQL
BEGIN
/* Select the stock of the item associated with item_id */

SELECT `available_items`.`stock` FROM `available_items` WHERE `available_items`.`id` = item_id;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `select_orders_to_be_shipped` ()  NO SQL
BEGIN

/* Select the ids of orders that are ready to be shipped. The order has been through checkout and TODO: the items are in stock */

SELECT `customer_orders`.`id` FROM `customer_orders` WHERE `customer_orders`.`status` = 'processing';
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `select_order_info` (IN `order_id` INT)  READS SQL DATA
    DETERMINISTIC
BEGIN
DECLARE customer_id int;

SELECT `customer_orders`.`customer_id` INTO customer_id FROM `customer_orders` WHERE `customer_orders`.`id` = order_id;

SELECT `available_items`.`name` AS 'item',
`order_item_relations`.`quantity_ordered` AS 'QTY',
ROUND(`available_items`.`price`, 2) AS 'Price' 
FROM `order_item_relations` LEFT JOIN `available_items` 
ON (`order_item_relations`.`item_id` = `available_items`.`id`)
WHERE `order_item_relations`.`order_id` = order_id;

CALL select_shipping_info(customer_id);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `select_shipping_info` (IN `user_id` INT)  READS SQL DATA
    DETERMINISTIC
BEGIN
SELECT `users`.`first_name`, `users`.`last_name`, `customer_info`.`street_address`, `customer_info`.`city`, `customer_info`.`state`, `customer_info`.`zip_code` 
FROM `users` LEFT JOIN `customer_info` ON (`users`.`id` = `customer_info`.`customer_id`) WHERE `users`.`id` = user_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_available_item` (IN `item_id` INT, IN `name` VARCHAR(32), IN `description` VARCHAR(1024), IN `price` FLOAT, IN `stock` INT)  NO SQL
BEGIN
UPDATE `available_items` SET `name`=name,`description`=description,`price`=price,`stock`=stock WHERE `available_items`.`id` = item_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_available_item_stock` (IN `item_id` INT, IN `stock` INT)  MODIFIES SQL DATA
    DETERMINISTIC
    COMMENT 'Used to update to an item''s stock'
BEGIN

UPDATE `available_items` SET `stock`=stock WHERE `available_items`.`id` = item_id;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_customer_info` (IN `customer_id` INT, IN `street_address` VARCHAR(64), IN `city` VARCHAR(32), IN `state` CHAR(2), IN `zip_code` INT(5), IN `card_number` INT(24))  NO SQL
BEGIN
UPDATE `customer_info` SET `street_address`=street_address,`city`=city,`state`=state,`zip_code`=zip_code,`card_number`=card_number WHERE `customer_info`.`customer_id` = customer_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_materials` (IN `material_id` INT, IN `name` VARCHAR(32), IN `quantity` INT, IN `units` VARCHAR(24))  MODIFIES SQL DATA
BEGIN
UPDATE `materials` SET `name`=name, `quantity`=quantity, `units`=units WHERE `materials`.`id` = material_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_material_quantity` (IN `material_id` INT, IN `quantity` INT)  MODIFIES SQL DATA
BEGIN
UPDATE `materials` SET `quantity`=quantity WHERE `materials`.`id` = material_id;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `available_items`
--

CREATE TABLE `available_items` (
  `id` int(11) NOT NULL,
  `name` varchar(32) NOT NULL,
  `description` varchar(1024) NOT NULL,
  `price` float NOT NULL,
  `stock` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `available_items`
--

INSERT INTO `available_items` (`id`, `name`, `description`, `price`, `stock`) VALUES
(1, 'Oak Wood Chair', 'An Oak Wood Chair is the best kind of chair.', 29.97, 100),
(2, 'Maple Wood Chair', 'Maple is better than oak.', 29.98, 20),
(3, 'Oak Wood Rocking Chair', 'It rocks.', 34.99, 5),
(4, 'Maple Stool', 'A wonderful stool made of maple.', 19.99, 0);

-- --------------------------------------------------------

--
-- Table structure for table `customer_info`
--

CREATE TABLE `customer_info` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `street_address` varchar(64) NOT NULL,
  `city` varchar(32) NOT NULL,
  `state` char(2) NOT NULL,
  `zip_code` int(5) NOT NULL,
  `card_number` bigint(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `customer_info`
--

INSERT INTO `customer_info` (`id`, `customer_id`, `street_address`, `city`, `state`, `zip_code`, `card_number`) VALUES
(1, 2, '123 Street Rd.', 'Saint Paul', 'MN', 55112, 9123456789123456),
(2, 4, '987 MyStreet Ave.', 'Minneapolis', 'MN', 55111, 1111111111111111),
(3, 7, 'ABC St.', 'Roseville', 'MN', 55113, 1000000000000000);

-- --------------------------------------------------------

--
-- Table structure for table `customer_orders`
--

CREATE TABLE `customer_orders` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `status` enum('being_made','processing','shipped','delivered') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `customer_orders`
--

INSERT INTO `customer_orders` (`id`, `customer_id`, `status`) VALUES
(1, 2, 'processing'),
(2, 4, 'being_made'),
(4, 7, 'being_made');

-- --------------------------------------------------------

--
-- Table structure for table `item_material_relations`
--

CREATE TABLE `item_material_relations` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `material_id` int(11) NOT NULL,
  `quantity_of_material_required` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `item_material_relations`
--

INSERT INTO `item_material_relations` (`id`, `item_id`, `material_id`, `quantity_of_material_required`) VALUES
(1, 2, 3, 1),
(2, 2, 2, 10),
(3, 1, 1, 1),
(4, 1, 2, 10),
(5, 3, 1, 2),
(6, 3, 2, 15);

-- --------------------------------------------------------

--
-- Table structure for table `materials`
--

CREATE TABLE `materials` (
  `id` int(11) NOT NULL,
  `name` varchar(32) NOT NULL,
  `quantity` int(11) NOT NULL,
  `units` varchar(24) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `materials`
--

INSERT INTO `materials` (`id`, `name`, `quantity`, `units`) VALUES
(1, 'Oak Wood', 100, 'Logs'),
(2, 'Nails', 33, 'Nails'),
(3, 'Maple Wood', 50, 'Logs');

-- --------------------------------------------------------

--
-- Table structure for table `order_item_relations`
--

CREATE TABLE `order_item_relations` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity_ordered` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `order_item_relations`
--

INSERT INTO `order_item_relations` (`id`, `order_id`, `item_id`, `quantity_ordered`) VALUES
(1, 1, 1, 2),
(2, 1, 3, 1),
(3, 2, 2, 9),
(4, 1, 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(24) NOT NULL,
  `password` varchar(32) NOT NULL,
  `email` varchar(64) NOT NULL,
  `phone_number` varchar(24) NOT NULL,
  `role` enum('administrator','customer','transportation_associate') NOT NULL,
  `first_name` varchar(24) NOT NULL,
  `last_name` varchar(24) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `phone_number`, `role`, `first_name`, `last_name`) VALUES
(1, 'Kim', 'password', 'yk4510kf@go.minnstate.edu', '', 'administrator', 'Kim', 'Pampusch'),
(2, 'Bob123', 'password', 'bob@gmail.com', '', 'customer', 'Bob', 'OneTwoThree'),
(3, 'TransportTheStuff', 'password', 'transport@gmail.com', '', 'transportation_associate', 'Joe', 'Smith'),
(4, 'BestCustomer', 'password', 'eSmith@yahoo.com', '', 'customer', 'Emily', 'Smith'),
(6, 'transport_person', 'password', 'transporting@gmail.com', '', 'transportation_associate', 'transp', 'ort'),
(7, 'customer123', 'password', 'customer@gmail.com', '', 'customer', 'custom', 'er');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `available_items`
--
ALTER TABLE `available_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer_info`
--
ALTER TABLE `customer_info`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_info_ibfk_1` (`customer_id`);

--
-- Indexes for table `customer_orders`
--
ALTER TABLE `customer_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`customer_id`);

--
-- Indexes for table `item_material_relations`
--
ALTER TABLE `item_material_relations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_material_relations_ibfk_1` (`item_id`),
  ADD KEY `material_id` (`material_id`);

--
-- Indexes for table `materials`
--
ALTER TABLE `materials`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_item_relations`
--
ALTER TABLE `order_item_relations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_item_relations_ibfk_1` (`item_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `available_items`
--
ALTER TABLE `available_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `customer_info`
--
ALTER TABLE `customer_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `customer_orders`
--
ALTER TABLE `customer_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `item_material_relations`
--
ALTER TABLE `item_material_relations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `materials`
--
ALTER TABLE `materials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `order_item_relations`
--
ALTER TABLE `order_item_relations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `customer_info`
--
ALTER TABLE `customer_info`
  ADD CONSTRAINT `customer_info_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `customer_orders`
--
ALTER TABLE `customer_orders`
  ADD CONSTRAINT `customer_orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `item_material_relations`
--
ALTER TABLE `item_material_relations`
  ADD CONSTRAINT `item_material_relations_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `available_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `item_material_relations_ibfk_2` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `order_item_relations`
--
ALTER TABLE `order_item_relations`
  ADD CONSTRAINT `order_item_relations_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `available_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `order_item_relations_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `customer_orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
