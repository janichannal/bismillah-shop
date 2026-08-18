-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 18, 2026 at 06:43 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bismillah_shop`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `failed_attempts` int(11) NOT NULL DEFAULT 0,
  `locked_until` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `name`, `email`, `password`, `created_at`, `failed_attempts`, `locked_until`) VALUES
(1, 'jawad Shop Admin', 'admin@bismillahshop.com', '$2y$10$4bo6Idedm9waT5orRgHq8OSIvupWWfzcMwjwMk04EiZ66BTcz7k..', '2026-08-09 06:31:52', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`, `description`, `created_at`) VALUES
(1, 'Mobile Phones', 'New and used smartphones from popular brands', '2026-08-09 06:31:52'),
(2, 'Laptops', 'Laptops for study, work, and gaming', '2026-08-09 06:31:52'),
(3, 'Tablets', 'Tablets for browsing, study, and entertainment', '2026-08-09 06:31:52'),
(4, 'Smart Watches', 'Fitness and smart watches', '2026-08-09 06:31:52'),
(5, 'Headphones', 'Wired and wireless headphones', '2026-08-09 06:31:52'),
(6, 'Accessories', 'Chargers, power banks, cases, and other accessories', '2026-08-09 06:31:52');

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `gallery_id` int(11) NOT NULL,
  `title` varchar(150) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gallery`
--

INSERT INTO `gallery` (`gallery_id`, `title`, `description`, `image`, `created_at`) VALUES
(2, 'Repair Counter', 'Where our technicians handle repairs.', 'img_6a7c7a5f181599.99423492.jpg', '2026-08-09 06:31:52'),
(3, 'Product Display', 'Our mobile and laptop display section.', 'img_6a7c7ac7166c71.01595724.jpg', '2026-08-09 06:31:52'),
(4, 'our shop front view', 'shop fornt veiws', 'img_6a7c79fff24d42.96713944.jpg', '2026-08-09 08:14:20');

-- --------------------------------------------------------

--
-- Table structure for table `gallery_images`
--

CREATE TABLE `gallery_images` (
  `image_id` int(11) NOT NULL,
  `gallery_id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gallery_images`
--

INSERT INTO `gallery_images` (`image_id`, `gallery_id`, `image`, `sort_order`, `created_at`) VALUES
(3, 4, 'img_6a7dbc19242d83.33606281.jpg', 0, '2026-08-13 12:44:09'),
(4, 4, 'img_6a7dbc1926b7e3.92803323.webp', 1, '2026-08-13 12:44:09'),
(6, 2, 'img_6a7dbfc7c857f9.18864654.jpg', 1, '2026-08-13 12:59:51'),
(8, 3, 'img_6a7dc010058bc9.03453441.webp', 1, '2026-08-13 13:01:04'),
(9, 2, 'img_6a7dc262a82634.57282639.png', 2, '2026-08-13 13:10:58');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `subject` varchar(150) DEFAULT NULL,
  `message` text NOT NULL,
  `status` enum('unread','read') NOT NULL DEFAULT 'unread',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`message_id`, `name`, `email`, `phone`, `subject`, `message`, `status`, `created_at`) VALUES
(1, 'Ali Raza', 'ali.raza@example.com', '03001234567', 'Laptop repair inquiry', 'My laptop is not turning on, can you check it?', 'read', '2026-08-09 06:31:52'),
(2, 'Sana Khan', 'sana.khan@example.com', '03111234567', 'Product availability', 'Is the Galaxy A54 currently in stock?', 'read', '2026-08-09 06:31:52'),
(4, 'Jawad channal', 'jawad@gmail.com', '0331234567', 'infinx note 8i battery required', 'is this moblie phone battery availalbe in you shop', 'read', '2026-08-09 08:58:19'),
(8, 'munna', 'munna@gmil.com', '03423434534', 'i want to buy this phone', 'could you give me any discount on this', 'read', '2026-08-10 07:43:13'),
(9, 'Jawad channal', 'jawad11@gmail.com', '03178467537', 'Product Enquiry: Wired Stereo Headphones', 'Hi, I am interested in the Wired Stereo Headphones (Rs. 2,000). Please provide more information.', 'read', '2026-08-13 11:15:03'),
(10, 'Jawad baloch', 'jawad@gmail.com', '0376677657657', 'Product Enquiry: HP Spectre x360 16 2-in-1', 'Hi, I am interested in the HP Spectre x360 16 2-in-1 (Rs. 385,000). Please provide more information.', 'read', '2026-08-14 09:27:56'),
(11, 'ab raheem', 'raheemm@gmail.com', '023456723456', 'Product Enquiry: Amazfit Bip 5', 'Hi, I am interested in the Amazfit Bip 5 (Rs. 25,000). Please provide more information.', 'read', '2026-08-15 15:51:10');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `reset_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`reset_id`, `admin_id`, `token`, `expires_at`, `used`, `created_at`) VALUES
(1, 1, '276c67919fad322084977d91b1a8dc191ab083003945880bb13c70d3f4a1d0d2', '2026-08-16 15:31:36', 0, '2026-08-16 13:01:36'),
(2, 1, '1b51047239e838795e41dc34e1417d2b174cf6832fd8f405d5a8439f1255f3d1', '2026-08-16 15:54:28', 0, '2026-08-16 13:24:28');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `product_name` varchar(150) NOT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `model_number` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `sale_price` decimal(10,2) DEFAULT NULL,
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `condition` enum('New','Used','Refurbished') NOT NULL DEFAULT 'New',
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `category_id`, `product_name`, `brand`, `model_number`, `price`, `sale_price`, `stock_quantity`, `condition`, `description`, `image`, `created_at`, `updated_at`) VALUES
(1, 1, 'Galaxy A54', 'Samsung', 'SM-A546', 54999.00, NULL, 8, 'New', 'Mid-range smartphone with a 120Hz display and 50MP camera.', 'img_6a7ae9ac8c7f91.07719086.png', '2026-08-09 06:31:52', '2026-08-11 09:21:48'),
(2, 1, 'iPhone 12', 'Apple', 'A2172', 89999.00, NULL, 0, 'Used', 'Well-maintained iPhone 12 with 90% battery health.', 'img_6a7c736398ca09.95890791.webp', '2026-08-09 06:31:52', '2026-08-17 06:45:58'),
(3, 2, 'ThinkPad E14', 'Lenovo', 'E14 Gen 3', 124999.00, NULL, 5, 'New', 'Reliable business laptop with Ryzen 5 processor and 16GB RAM.', 'img_6a7c73bfc6bfd6.96277470.jpg', '2026-08-09 06:31:52', '2026-08-12 13:23:11'),
(4, 2, 'MacBook Air M1', 'Apple', 'A2337', 189999.00, NULL, 5, 'Refurbished', 'Refurbished MacBook Air with 8GB RAM and 256GB SSD.', 'img_6a7c740b52a493.53340108.webp', '2026-08-09 06:31:52', '2026-08-14 03:53:59'),
(5, 3, 'Galaxy Tab A9', 'Samsung', 'SM-X110', 34999.00, NULL, 6, 'New', 'Compact tablet, great for study and entertainment.', 'img_6a7c7453e618d3.18737220.jpg', '2026-08-09 06:31:52', '2026-08-12 13:25:39'),
(6, 4, 'Watch GT 3', 'Huawei', 'GT3-46mm', 22999.00, NULL, 10, 'New', 'Fitness smartwatch with two-week battery life.', 'img_6a7c74916229d8.27962653.png', '2026-08-09 06:31:52', '2026-08-12 13:26:41'),
(8, 6, '20000mAh Power Bank', 'Anker', 'A1263', 4999.00, NULL, 15, 'New', 'Fast-charging power bank with dual USB output.', 'img_6a7c756852c2b8.07430083.webp', '2026-08-09 06:31:52', '2026-08-12 13:30:16'),
(9, 2, 'Lenovo IdeaPad 3', 'lenovo', 'IdeaPad 3 15ITL6', 65000.00, NULL, 5, 'New', 'Brand-new Lenovo IdeaPad 3 laptop with a 15.6-inch display, suitable for students, office work, browsing, programming, and everyday use. Features a comfortable keyboard, reliable performance, and modern design.', 'img_6a7831f2b36903.83130283.jpg', '2026-08-09 07:53:22', '2026-08-12 14:07:42'),
(10, 5, 'wireless headphone', 'Audionic', 'GT3-46mm', 2500.00, NULL, 10, 'New', 'wireless headphones and standard for every body', 'img_6a7c76ac90e477.53100974.jpg', '2026-08-12 13:35:40', '2026-08-12 14:01:48'),
(11, 5, 'Wired Stereo Headphones', 'Audionic', 'AH-115', 2000.00, NULL, 23, 'New', 'High-quality wired stereo headphones with clear sound, deep bass, comfortable ear cushions, and an adjustable headband. Features a 3.5mm audio jack and built-in microphone, making them suitable for music, calls, gaming, and online classes. Compatible with smartphones, laptops, tablets, and other devices with a 3.5mm audio port.', 'img_6a7d58652c4438.76491931.jpg', '2026-08-12 14:01:18', '2026-08-14 04:24:55'),
(12, 6, 'Premium 9H Tempered Glass Screen Protector', 'spigen', 'TG iPhone15 PRO', 1200.00, NULL, 21, 'New', 'Ultra clear 9H hardness tempered glass with oleophobic coating to resist fingerprints and smudges. Includes easy install alignment tray and cleaning kit. Compatible with iPhone 15 Pro / Pro Max. Edge to edge coverage with 99.9% transparency.', 'img_6a7ec874a2ce59.84393117.jpg', '2026-08-14 07:49:08', '2026-08-14 07:49:08'),
(13, 6, 'USB C to Lightning Fast Charge Cable – 2 Pack', 'Anker', 'C LIGHT 6FT', 2000.00, NULL, 15, 'New', 'MFi certified 6 foot braided nylon cables supporting PD fast charging (up to 27W) and data transfer (480 Mbps). Durable strain relief connectors tested for 10,000+ bends. Compatible with all Lightning devices.', 'img_6a81c4b3cd9678.94953351.jpg', '2026-08-14 07:51:24', '2026-08-16 14:09:55'),
(14, 1, 'Infinix Note 60 Pro', 'Infinix', 'X678B', 115000.00, NULL, 10, 'New', '6.78\" FHD+ OLED 144Hz display with 4,500 nits peak brightness & Gorilla Glass 7i. Powered by Snapdragon 7s Gen 4 chipset with 12GB RAM. 6,500 mAh battery with 90W wired & 30W wireless charging. 50MP primary + 8MP ultrawide rear camera, 13MP front camera. Features Active Matrix rear display for notifications, dual JBL speakers, IP64 rating, and in-display fingerprint scanner. Available in Mist Titanium, Midnight Black, Fizz Blue, Rose Gold, and Mocha Brown.', 'img_6a81c421870b07.82252847.jpg', '2026-08-14 07:55:51', '2026-08-16 14:07:29'),
(15, 1, 'Infinix Smart 20', 'Infinix', 'X6528', 30999.00, NULL, 15, 'New', '6.78\" IPS LCD with 120Hz refresh rate & 700 nits brightness. Powered by MediaTek Helio G81 Ultimate processor with 4GB RAM. 64GB/128GB storage with microSD expandability. 8MP rear + 8MP selfie camera with 2K video recording. 5,200 mAh battery with 15W charging. Features IP64 dust/splash resistance, side-mounted fingerprint scanner, IR blaster, and dual SIM support. Runs Android 16 with XOS 16. Available in multiple colors.', 'img_6a81c2f75e9902.37754934.jpg', '2026-08-14 07:58:23', '2026-08-16 14:02:31'),
(16, 1, 'Oppo Find X8 Pro', 'Oppo', 'CPH2651', 215000.00, NULL, 7, 'New', '6.78\" LTPO AMOLED 120Hz display with 4,500 nits peak brightness & Gorilla Glass Victus 2. Powered by Mediatek Dimensity 9400 chipset with 12GB/16GB RAM. 5,910 mAh battery with 80W wired & 50W wireless charging. Quad camera setup: 50MP main (Lytia 808) + 50MP ultrawide + 50MP periscope telephoto (3x optical) + 50MP telephoto (6x optical). 32MP selfie camera. AI-powered photography features, IP68 dust/water resistance, and in-display fingerprint scanner. Runs Android 15 with ColorOS 15. Available in Black and White.', 'img_6a81c24c720902.68094852.jpg', '2026-08-14 08:00:44', '2026-08-16 13:59:40'),
(17, 1, 'Oppo A60', 'Oppo', 'CPH2643', 43000.00, NULL, 25, 'New', '6.67\" HD+ IPS LCD 90Hz display with 720 nits brightness. Powered by Qualcomm Snapdragon 680 processor with 6GB/8GB RAM. 128GB storage with microSD expandability. 50MP main + 2MP depth rear camera, 8MP front camera. 5,000 mAh battery with 45W SUPERVOOC fast charging. Features IP54 dust/splash resistance, side-mounted fingerprint scanner, stereo speakers, and 3.5mm headphone jack. Ultra-durable design with military-grade shock resistance. Available in Blue and Purple.', 'img_6a81c1f44b8d70.98153351.jpg', '2026-08-14 08:02:32', '2026-08-16 13:58:12'),
(18, 2, 'HP Spectre x360 16 2-in-1', 'HP', '16-aa0053dx', 385000.00, NULL, 3, 'New', 'Premium 16\" 4K OLED 360° touchscreen with 120Hz refresh rate & Corning Gorilla Glass. Powered by Intel Core Ultra 9 285H processor with 32GB LPDDR5x RAM & 1TB PCIe Gen4 SSD. Intel Arc Graphics. 83Wh battery with 90W fast charging. Features B&O quad speakers, 9MP IR webcam with physical shutter, fingerprint reader, Thunderbolt 4 ports, and Wi-Fi 7. Runs Windows 11 Pro. Sleek gem-cut design in Nightfall Black. Ideal for creative professionals and power users.', 'img_6a81c1804365d0.23890978.jpg', '2026-08-14 09:24:55', '2026-08-16 13:56:16'),
(19, 2, 'HP Victus 15 Gaming Laptop', 'HP', '15-fb2083dx', 195000.00, NULL, 7, 'New', '15.6\" FHD 144Hz IPS display with anti-glare coating. Powered by AMD Ryzen 5 8645HS processor with 16GB DDR5 RAM & 512GB PCIe Gen4 SSD. NVIDIA GeForce RTX 4050 6GB graphics. 70Wh battery with 200W adapter. Features RGB backlit keyboard, B&O dual speakers, 1080p webcam, Gigabit Ethernet, HDMI 2.1, and USB-C with DisplayPort. Mica Silver chassis. Perfect for casual gaming, streaming, and multitasking.', 'img_6a81c0ff3e98c7.75125312.jpg', '2026-08-14 09:30:30', '2026-08-16 13:54:07'),
(20, 4, 'Apple Watch Series 10', 'Apple', 'A3001 (46mm) / A3002 (42mm)', 185000.00, NULL, 7, 'New', 'Next-generation smartwatch with 46mm/42mm Always On Retina LTPO OLED display (2,000 nits brightness). Powered by S10 SiP chip with 64GB storage. Features ECG monitoring, blood oxygen tracking, temperature sensing, sleep apnea notifications, and cycle tracking. Dual frequency GPS, heart rate sensor, and emergency SOS. 18 hour battery life with fast charging (80% in 30 mins). Swim proof (WR50), IP6X dust resistance. Runs watchOS 11 with comprehensive health and fitness features. Available in Jet Black, Rose Gold, Silver Aluminum, and Titanium finishes. Compatible with iPhone models.', 'img_6a81c093308928.12917098.jpg', '2026-08-14 09:41:18', '2026-08-16 13:52:19'),
(21, 4, 'Samsung Galaxy Watch Ultra', 'Samsung', 'SM L705', 165000.00, NULL, 8, 'New', 'Premium 47mm rugged titanium smartwatch with 1.5\" Super AMOLED 480x480 display (3,000 nits peak brightness). Powered by Exynos W1000 processor with 2GB RAM & 32GB storage. 590 mAh battery with 40+ hours normal use and 100 hours in power saving mode. Features advanced BioActive sensor (heart rate, ECG, blood pressure, body composition), dual frequency GPS, and sleep coaching. 10ATM water resistance, MIL STD 810H certified, and IP68 rating. Runs Wear OS 5 with Samsung Health. Ideal for extreme sports, outdoor adventures, and fitness enthusiasts. Available in Titanium Gray, Titanium White, and Titanium Silver.', 'img_6a81c00b718684.30499723.jpg', '2026-08-14 09:43:56', '2026-08-16 13:50:03'),
(22, 4, 'Amazfit Bip 5', 'Amazfit', 'A2236', 25000.00, 23000.00, 25, 'New', 'Affordable 1.91\" large rectangular TFT touchscreen display (320x380 pixels, 60Hz). Powered by Zepp OS 2.0 with 4GB storage. 300 mAh battery with up to 10 days battery life (typical) and 26 days in battery saver mode. Features BioTracker PPG heart rate sensor, SpO2 measurement, sleep tracking, stress monitoring, and 120+ sports modes. Built in GPS for accurate outdoor tracking. 5ATM water resistance. Supports Bluetooth 5.2 and calls via Bluetooth. Lightweight design (46g) with color matched silicone strap. Available in Black, Pink, and Cream. Compatible with Android and iOS devices. Great value for fitness tracking and daily wear.', 'img_6a81bf3835bfe4.39281174.jpg', '2026-08-14 09:58:32', '2026-08-16 13:46:56'),
(23, 3, 'Apple iPad (10th Generation)', 'Apple', 'A2757 (Wi-Fi)', 120000.00, 115000.00, 10, 'New', '10.9\" Liquid Retina IPS display (2360x1640 resolution, 500 nits brightness, True Tone). Powered by A14 Bionic chip with 6 core CPU and 4 core GPU. 64GB storage. 28.6Wh battery with up to 10 hours of web/video playback; 20W USB C fast charging. 12MP rear camera with 4K video and 12MP landscape ultra wide front camera with Center Stage for video calls. Touch ID integrated into top button. Supports Apple Pencil (1st gen & USB C) and Magic Keyboard Folio. Wi Fi 6, Bluetooth 5.2, USB C port. Runs iPadOS 18 with multitasking features and Stage Manager. All screen design with flat edges. Available in Silver, Blue, Pink, and Yellow. Perfect for students, professionals, and everyday media consumption.', 'img_6a81be33ad97b0.56422428.jpg', '2026-08-14 10:00:43', '2026-08-16 13:43:09'),
(24, 3, 'Samsung Galaxy Tab S9 FE', 'Samsung', 'SM X510 (Wi Fi)', 115000.00, 112000.00, 7, 'New', '10.9\" 90Hz TFT LCD display (2304x1440 resolution, Vision Booster technology). Powered by Exynos 1380 octa core processor with 6GB RAM. 128GB storage with microSD expansion up to 1TB. 8,000 mAh battery with 45W Super Fast Charging. 8MP rear camera + 12MP ultra wide front camera with auto framing. IP68 water and dust resistance (first in its class). Includes S Pen (IP68 rated) in the box with low latency and Air Actions. Dual AKG speakers with Dolby Atmos. Armor Aluminum frame. Supports Samsung DeX for desktop like experience. Android 14 with One UI 6.1 and 4 years of OS updates. Available in Gray, Mint, Silver, and Lavender. Ideal for students, creatives, and productivity on the go.', 'img_6a81bdbe898979.85605641.jpg', '2026-08-14 10:02:30', '2026-08-16 13:40:14'),
(25, 5, 'Apple AirPods Pro (2nd Generation) with MagSafe Charging Case (USB‑C)', 'Apple', 'MTJV3TA/A (A3056)', 70000.00, NULL, 7, 'New', 'The Apple AirPods Pro (2nd generation) feature pro‑level Active Noise Cancellation and Transparency mode, along with Adaptive Audio that intelligently blends these settings based on your environment. Powered by the H2 chip, they deliver crisp, high‑fidelity sound with Personalized Spatial Audio and dynamic head tracking for an immersive listening experience. They offer up to 6 hours of listening time per charge and over 30 hours with the MagSafe charging case (USB‑C). Sweat‑ and water‑resistant (IPX4), they are designed for all‑day comfort and reliable performance.', 'img_6a81c85ce2ef65.91708242.jpg', '2026-08-16 14:25:32', '2026-08-16 14:25:32');

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `image_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`image_id`, `product_id`, `image`, `sort_order`, `created_at`) VALUES
(6, 11, 'img_6a7db8dcdfa509.18198306.webp', 1, '2026-08-13 12:30:20'),
(7, 11, 'img_6a7db95e7cb803.78527877.png', 2, '2026-08-13 12:32:30'),
(8, 10, 'img_6a7dba38dad993.88219193.png', 0, '2026-08-13 12:36:08'),
(9, 10, 'img_6a7dba38db9885.49736851.png', 1, '2026-08-13 12:36:08'),
(10, 9, 'img_6a7dba7e783fa3.40876605.jpg', 0, '2026-08-13 12:37:18'),
(11, 9, 'img_6a7dba7e7cd197.82075607.webp', 1, '2026-08-13 12:37:18'),
(12, 1, 'img_6a7dbb06355a20.52613918.webp', 0, '2026-08-13 12:39:34'),
(13, 1, 'img_6a7dbb16a512e9.98920404.jpg', 1, '2026-08-13 12:39:50'),
(15, 2, 'img_6a7dbb83632dc6.08561296.jpg', 1, '2026-08-13 12:41:39'),
(16, 2, 'img_6a7dbb83660738.10595862.jpg', 2, '2026-08-13 12:41:39'),
(17, 4, 'img_6a7e915773a918.85143718.png', 0, '2026-08-14 03:53:59'),
(18, 4, 'img_6a7e91577505f0.79572982.png', 1, '2026-08-14 03:53:59'),
(19, 3, 'img_6a7e94e5e14295.59032875.png', 0, '2026-08-14 04:09:10'),
(20, 3, 'img_6a7e94e607d275.49054203.webp', 1, '2026-08-14 04:09:10'),
(21, 5, 'img_6a7e958f2e4a31.41023134.jpg', 0, '2026-08-14 04:11:59'),
(22, 6, 'img_6a7e95e36079d6.45828738.jpg', 0, '2026-08-14 04:13:23'),
(23, 6, 'img_6a7e95e361b422.32017938.jpg', 1, '2026-08-14 04:13:23'),
(27, 8, 'img_6a7e96c84da912.11184624.webp', 2, '2026-08-14 04:17:12'),
(28, 8, 'img_6a7e97e1e32262.48365990.webp', 3, '2026-08-14 04:21:53'),
(29, 8, 'img_6a7e97e1e439e9.51403203.png', 4, '2026-08-14 04:21:53'),
(30, 5, 'img_6a7e987618e117.32301765.jpg', 1, '2026-08-14 04:24:22'),
(32, 24, 'img_6a81bdbe8d0c56.19467440.jpg', 0, '2026-08-16 13:40:14'),
(33, 24, 'img_6a81bdbe8deaf6.64140997.jpg', 1, '2026-08-16 13:40:14'),
(34, 23, 'img_6a81be33afe316.68239363.jpg', 0, '2026-08-16 13:42:11'),
(35, 23, 'img_6a81be33b10c93.71681077.jpg', 1, '2026-08-16 13:42:11'),
(36, 22, 'img_6a81bf385674b3.87127761.webp', 0, '2026-08-16 13:46:32'),
(37, 22, 'img_6a81bf38573796.49417027.jpg', 1, '2026-08-16 13:46:32'),
(38, 21, 'img_6a81c00b742c96.83868892.jpg', 0, '2026-08-16 13:50:03'),
(39, 21, 'img_6a81c00b753743.83614714.jpg', 1, '2026-08-16 13:50:03'),
(40, 21, 'img_6a81c00b761450.89863230.jpg', 2, '2026-08-16 13:50:03'),
(41, 20, 'img_6a81c093332fd7.07167106.jpg', 0, '2026-08-16 13:52:19'),
(42, 20, 'img_6a81c0933406f7.83451636.jpg', 1, '2026-08-16 13:52:19'),
(43, 20, 'img_6a81c09334c1b8.11473140.jpg', 2, '2026-08-16 13:52:19'),
(44, 19, 'img_6a81c0ff419715.05989789.jpg', 0, '2026-08-16 13:54:07'),
(45, 19, 'img_6a81c0ff42b675.60901293.jpg', 1, '2026-08-16 13:54:07'),
(46, 19, 'img_6a81c0ff436cf8.70340098.jpg', 2, '2026-08-16 13:54:07'),
(47, 18, 'img_6a81c18046bee5.42777928.jpg', 0, '2026-08-16 13:56:16'),
(48, 18, 'img_6a81c18047a452.28040201.jpg', 1, '2026-08-16 13:56:16'),
(49, 18, 'img_6a81c18048c8a7.22032448.jpg', 2, '2026-08-16 13:56:16'),
(50, 17, 'img_6a81c1f44e3fa6.71070394.jpg', 0, '2026-08-16 13:58:12'),
(51, 17, 'img_6a81c1f44efd59.33083760.jpg', 1, '2026-08-16 13:58:12'),
(52, 17, 'img_6a81c1f44f8cb8.42009473.jpg', 2, '2026-08-16 13:58:12'),
(53, 16, 'img_6a81c24c740551.18280347.jpg', 0, '2026-08-16 13:59:40'),
(54, 16, 'img_6a81c24c74e283.76785697.jpg', 1, '2026-08-16 13:59:40'),
(55, 16, 'img_6a81c24c7595e4.68762225.jpg', 2, '2026-08-16 13:59:40'),
(56, 15, 'img_6a81c2f760dec1.86649842.jpg', 0, '2026-08-16 14:02:31'),
(57, 15, 'img_6a81c2f7617d67.15081575.jpg', 1, '2026-08-16 14:02:31'),
(58, 15, 'img_6a81c2f76246f5.23100482.jpg', 2, '2026-08-16 14:02:31'),
(59, 14, 'img_6a81c421896b79.99372283.jpg', 0, '2026-08-16 14:07:29'),
(60, 14, 'img_6a81c4218a18a9.34949138.jpg', 1, '2026-08-16 14:07:29'),
(61, 14, 'img_6a81c4218ac106.06111220.jpg', 2, '2026-08-16 14:07:29'),
(62, 13, 'img_6a81c4b3cfa0a6.91489782.jpg', 0, '2026-08-16 14:09:55'),
(63, 13, 'img_6a81c4b3d04ee0.46789030.jpg', 1, '2026-08-16 14:09:55'),
(64, 12, 'img_6a81c613bf1915.84050630.jpg', 0, '2026-08-16 14:15:47'),
(65, 12, 'img_6a81c613c03fe5.09445892.jpg', 1, '2026-08-16 14:15:47'),
(66, 12, 'img_6a81c613c0cef0.72544987.jpg', 2, '2026-08-16 14:15:47'),
(67, 12, 'img_6a81c613c15cc0.16091412.jpg', 3, '2026-08-16 14:15:47'),
(68, 25, 'img_6a81c85ce507b6.40882137.jpg', 0, '2026-08-16 14:25:32'),
(69, 25, 'img_6a81c85ce5ad67.20382639.jpg', 1, '2026-08-16 14:25:32'),
(70, 25, 'img_6a81c85ce6e875.75679308.jpg', 2, '2026-08-16 14:25:32');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `rating` tinyint(4) NOT NULL,
  `review_text` text DEFAULT NULL,
  `status` enum('pending','approved') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`review_id`, `product_id`, `customer_name`, `rating`, `review_text`, `status`, `created_at`) VALUES
(1, 11, 'jani', 5, 'best services', 'approved', '2026-08-13 11:37:08'),
(2, 10, 'jawad', 4, '', 'approved', '2026-08-14 04:26:47'),
(3, 22, 'Jawad channal', 5, 'zxctvybunimooiukyr', 'approved', '2026-08-15 15:53:53');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `service_id` int(11) NOT NULL,
  `service_name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`service_id`, `service_name`, `description`, `price`, `image`, `created_at`, `updated_at`) VALUES
(1, 'Laptop Repair', 'Diagnosis and repair for hardware and software issues.', 1500.00, 'img_6a7c77dfe3a2c9.48319421.jpg', '2026-08-09 06:31:52', '2026-08-12 13:40:47'),
(2, 'Screen Replacement', 'Mobile and laptop screen replacement with genuine parts.', 3500.00, 'img_6a7c7825ab5684.61923443.jpg', '2026-08-09 06:31:52', '2026-08-12 13:41:57'),
(3, 'Windows Installation', 'Fresh Windows installation with essential software setup.', 900.00, 'img_6a7d4f6128df13.27347833.jpg', '2026-08-09 06:31:52', '2026-08-13 05:00:17'),
(4, 'Data Recovery', 'Recovery of lost or deleted data from damaged drives.', 2500.00, 'img_6a7c7875327a60.58440349.jpg', '2026-08-09 06:31:52', '2026-08-12 13:43:17'),
(5, 'Mobile Repair', 'General mobile phone repair and maintenance.', 1000.00, 'img_6a7c78a799c744.61689287.jpg', '2026-08-09 06:31:52', '2026-08-12 13:44:07'),
(7, 'Battery Replacement', 'Genuine battery replacement for all phone and laptop models. Restore full charge capacity.', 700.00, 'img_6a7eb76be705c9.42473498.jpg', '2026-08-14 06:36:27', '2026-08-14 06:36:27'),
(8, 'Charging Port Repair', 'Fix broken, loose, or faulty charging ports for all devices. Restore reliable power connection.', 500.00, 'img_6a7eb862b141e9.70095659.jpg', '2026-08-14 06:40:34', '2026-08-14 06:40:34'),
(9, 'Screen Protector Installation', 'Precision-cut tempered glass or film protector applied bubble‑free for maximum protection.', 300.00, 'img_6a7eb9c4f22865.52147482.jpg', '2026-08-14 06:46:28', '2026-08-14 06:46:28');

-- --------------------------------------------------------

--
-- Table structure for table `service_images`
--

CREATE TABLE `service_images` (
  `image_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `service_images`
--

INSERT INTO `service_images` (`image_id`, `service_id`, `image`, `sort_order`, `created_at`) VALUES
(1, 3, 'img_6a7d623d614714.30391474.jpg', 0, '2026-08-13 06:20:45'),
(2, 3, 'img_6a7d623d623fb1.84924769.jpg', 1, '2026-08-13 06:20:45'),
(3, 1, 'img_6a7e9979788d39.44337808.jpg', 0, '2026-08-14 04:28:41'),
(4, 1, 'img_6a7e9979797260.67588103.jpg', 1, '2026-08-14 04:28:41'),
(5, 1, 'img_6a7e99797a37d8.23784452.png', 2, '2026-08-14 04:28:41'),
(6, 7, 'img_6a7eb76bf1b866.51706464.jpg', 0, '2026-08-14 06:36:27'),
(7, 8, 'img_6a7eb862b3b9b5.54848636.jpg', 0, '2026-08-14 06:40:34'),
(8, 8, 'img_6a7eb862b553d8.07509586.jpg', 1, '2026-08-14 06:40:34'),
(9, 8, 'img_6a7eb862b60ff6.14126621.jpg', 2, '2026-08-14 06:40:34'),
(10, 9, 'img_6a7eb9c50046d4.00642856.jpg', 0, '2026-08-14 06:46:29'),
(11, 9, 'img_6a7eb9c500ed43.50154081.jpg', 1, '2026-08-14 06:46:29'),
(12, 9, 'img_6a7eb9c501b082.73841597.jpg', 2, '2026-08-14 06:46:29'),
(13, 4, 'img_6a7ebab70563e1.32512050.jpg', 0, '2026-08-14 06:50:31'),
(14, 4, 'img_6a7ebab705efe3.79864375.jpg', 1, '2026-08-14 06:50:31'),
(15, 2, 'img_6a7ebbe58d88f1.20127370.jpg', 0, '2026-08-14 06:55:33'),
(16, 2, 'img_6a7ebbe58e5032.99841157.jpg', 1, '2026-08-14 06:55:33'),
(17, 2, 'img_6a7ebbe58f0c84.98049390.jpg', 2, '2026-08-14 06:55:33');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `setting_id` int(11) NOT NULL,
  `logo_image` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`setting_id`, `logo_image`, `updated_at`) VALUES
(1, 'img_6a7c71ff1685c7.17053139.png', '2026-08-12 13:15:43'),
(2, NULL, '2026-08-12 12:41:48');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`gallery_id`);

--
-- Indexes for table `gallery_images`
--
ALTER TABLE `gallery_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `fk_gallery_images_gallery` (`gallery_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`reset_id`),
  ADD KEY `fk_reset_admin` (`admin_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `fk_product_category` (`category_id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `fk_product_images_product` (`product_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `fk_reviews_product` (`product_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`service_id`);

--
-- Indexes for table `service_images`
--
ALTER TABLE `service_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `fk_service_images_service` (`service_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`setting_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `gallery_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `gallery_images`
--
ALTER TABLE `gallery_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `reset_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `service_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `service_images`
--
ALTER TABLE `service_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `setting_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `gallery_images`
--
ALTER TABLE `gallery_images`
  ADD CONSTRAINT `fk_gallery_images_gallery` FOREIGN KEY (`gallery_id`) REFERENCES `gallery` (`gallery_id`) ON DELETE CASCADE;

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `fk_reset_admin` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`admin_id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_product_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE CASCADE;

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `fk_product_images_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_reviews_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `service_images`
--
ALTER TABLE `service_images`
  ADD CONSTRAINT `fk_service_images_service` FOREIGN KEY (`service_id`) REFERENCES `services` (`service_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
