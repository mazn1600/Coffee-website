-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: May 05, 2025 at 05:36 PM
-- Server version: 8.0.40
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `coffee_shop`
--

-- --------------------------------------------------------

--
-- Table structure for table `ordered_items`
--

CREATE TABLE `ordered_items` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `unit_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `ordered_items`
--

INSERT INTO `ordered_items` (`id`, `order_id`, `product_id`, `quantity`, `unit_price`) VALUES
(1, 1, 1, 1, 65.00),
(2, 2, 3, 1, 70.00),
(3, 3, 3, 1, 70.00),
(4, 4, 3, 1, 70.00),
(5, 5, 2, 1, 299.00),
(6, 6, 1, 1, 65.00),
(7, 7, 1, 1, 65.00),
(8, 7, 5, 1, 55.00);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int NOT NULL,
  `user_id` int NOT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `shipping_address` text,
  `payment_method` varchar(50) DEFAULT NULL,
  `status` enum('pending','paid','shipped','delivered','cancelled') DEFAULT 'pending',
  `order_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `total_price`, `shipping_address`, `payment_method`, `status`, `order_date`) VALUES
(1, 6, 65.00, 'dammam', 'cash', 'pending', '2025-05-05 15:09:36'),
(2, 6, 70.00, '', 'cash', 'pending', '2025-05-05 15:09:58'),
(3, 6, 70.00, '', 'cash', 'pending', '2025-05-05 15:31:25'),
(4, 6, 70.00, '', 'cash', 'pending', '2025-05-05 15:32:55'),
(5, 6, 299.00, '', 'cash', 'shipped', '2025-05-05 15:34:33'),
(6, 6, 65.00, '', 'cash', 'paid', '2025-05-05 15:36:07'),
(7, 6, 120.00, 'dammam', 'cash', 'pending', '2025-05-05 16:08:31');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL,
  `stock` int NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `type` enum('beans','machine') NOT NULL,
  `extra_info` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `name`, `description`, `price`, `stock`, `image`, `type`, `extra_info`) VALUES
(1, 'محصول سبعة جرام | كولومبيا ويلا', 'قهوة كولومبيا ويلا من سبعة جرام تتميز بنكهات فواكه لذيذة وطعم متوازن. هذه القهوة تأتي من منطقة ويلا في كولومبيا، المشهورة بتربتها الخصبة والمناخ المثالي لزراعة القهوة. إليك بعض صفاتها:\r\n\r\nالنكهة: تجمع بين الحمضية الطفيفة والحلاوة الطبيعية، مع لمسة من الفواكه الاستوائية مثل البرتقال والخوخ.\r\n\r\nالجسم: متوسطة الجسم، مما يجعلها خفيفة ولكن غنية في نفس الوقت.\r\n\r\nالروائح: تتميز برائحة الزهور الطازجة مع لمسة من الفواكه المنعشة.\r\n\r\nالنكهة النهائية: طويلة ومع توازن جيد بين الحموضة والحلاوة، مما يترك تأثيراً لذيذاً على الحنك.\r\n\r\nهذه القهوة مثالية لعشاق القهوة التي تحتوي على توازن بين الحموضة والحلاوة وتقدم تجربة مميزة في كل رشفة.', 65.00, 97, 'uploads/1746408404_7grams.png', 'beans', NULL),
(2, 'v60 | أدوات القهوة المقطرة ', 'أدوات V60 لتحضير القهوة المقطرة تشمل:\r\n\r\nV60 (الوعاء المخروطي): الأداة الرئيسية لتحضير القهوة.\r\n\r\nمرشحات ورقية: تستخدم لتصفية القهوة واحتجاز الشوائب.\r\n\r\nمقياس القهوة: لقياس الكمية الدقيقة من القهوة المطحونة.\r\n\r\nغلاية خاصة: غلاية ذات رأس طويل (غلاية الكاني) لتوزيع الماء بدقة.\r\n\r\nمطفأة القهوة: لضبط الطحن المناسب حسب حجم الفلتر.\r\n\r\nمقياس حرارة الماء: لضبط درجة حرارة الماء أثناء التحضير.\r\n\r\nتُستخدم هذه الأدوات بشكل متكامل لتحضير قهوة مقطرة بأعلى جودة.', 299.00, 22, 'uploads/1746408584_v60kit.png', 'machine', NULL),
(3, 'ميزان الكتروني ', 'الميزان الإلكتروني يُستخدم لقياس الكمية الدقيقة من القهوة والماء، مما يساعد في تحضير قهوة متوازنة. يتميز بالدقة العالية وسهولة الاستخدام، ويعتبر أداة أساسية في تحضير القهوة المختصة.', 70.00, 37, 'uploads/1746408683_meyzan.png', 'machine', NULL),
(4, 'محصول صواع | بن كولومبيا', 'محصول صواع | بن كولومبيا: قهوة كولومبية مختصة ذات نكهة غنية، حموضة معتدلة، وروائح فواكه خفيفة.\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n', 49.00, 50, 'uploads/1746408753_9aw3.png', 'beans', NULL),
(5, 'محصول ادهم | كولومبيا', 'محصول أدهم | كولومبيا: قهوة كولومبية ذات طعم متوازن، حموضة معتدلة، ونكهة مميزة من الفواكه والمكسرات.\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n', 55.00, 19, 'uploads/1746408880_adhmbeans.png', 'beans', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('customer','admin') DEFAULT 'customer'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `role`) VALUES
(6, 'MAZEN ALSALEM', 'mazn@mail.com', '$2y$10$gN9ZT8ieHX1Bt/dHh.edA.serlUJx04Q7w.PFReX.9y5hkH26J0U6', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ordered_items`
--
ALTER TABLE `ordered_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

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
-- AUTO_INCREMENT for table `ordered_items`
--
ALTER TABLE `ordered_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ordered_items`
--
ALTER TABLE `ordered_items`
  ADD CONSTRAINT `ordered_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ordered_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
