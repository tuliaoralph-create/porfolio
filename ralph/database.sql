-- ==========================================================
-- Ralph Tuliao Portfolio Database Schema & Seed Data
-- Database: ralph_portfolio
-- ==========================================================

CREATE DATABASE IF NOT EXISTS `ralph_portfolio` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `ralph_portfolio`;

-- ----------------------------------------------------------
-- Table: profile_info
-- ----------------------------------------------------------
DROP TABLE IF EXISTS `profile_info`;
CREATE TABLE `profile_info` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(100) NOT NULL,
  `title` VARCHAR(150) NOT NULL,
  `headline` VARCHAR(255) NOT NULL,
  `bio` TEXT NOT NULL,
  `avatar` VARCHAR(255) DEFAULT 'assets/images/ralph-tuliao.jpg',
  `email` VARCHAR(100) DEFAULT 'ralph.tuliao@example.com',
  `location` VARCHAR(100) DEFAULT 'Philippines',
  `status` VARCHAR(100) DEFAULT 'Available for new projects',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `profile_info` (`full_name`, `title`, `headline`, `bio`, `avatar`, `email`, `location`, `status`) VALUES
(
  'Ralph Tuliao',
  'Full-Stack Developer & IoT Engineer',
  'Engineering modern web experiences & smart connected hardware solutions.',
  'Hello! I am Ralph Tuliao, a passionate developer combining robust full-stack web engineering with embedded IoT systems. From building intuitive e-commerce platforms like Tapseafood to automated hardware like the Arduino Smart Water Dispenser, I bring ideas to life with clean code, sleek interfaces, and reliable hardware integration.',
  'assets/images/ralph-tuliao.jpg',
  'ralphtuliao.tuliao@gmail.com',
  'Manila, Philippines',
  'Available for Projects & Collaborations'
);

-- ----------------------------------------------------------
-- Table: projects
-- ----------------------------------------------------------
DROP TABLE IF EXISTS `projects`;
CREATE TABLE `projects` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(150) NOT NULL,
  `subtitle` VARCHAR(200) NOT NULL,
  `category` ENUM('web', 'iot', 'software') NOT NULL DEFAULT 'web',
  `description` TEXT NOT NULL,
  `image` VARCHAR(255) NOT NULL,
  `tags` VARCHAR(255) NOT NULL,
  `features` TEXT NOT NULL,
  `tech_stack` VARCHAR(255) NOT NULL,
  `demo_url` VARCHAR(255) DEFAULT '',
  `live_demo_url` VARCHAR(500) DEFAULT '',
  `github_url` VARCHAR(255) DEFAULT '#',
  `featured` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `projects` (`title`, `subtitle`, `category`, `description`, `image`, `tags`, `features`, `tech_stack`, `demo_url`, `live_demo_url`, `github_url`, `featured`) VALUES
(
  'Tapseafood: Online Food Ordering',
  'Full-Featured Seafood E-Commerce & Instant Ordering Platform',
  'web',
  'Tapseafood is a dynamic online food ordering web platform tailored for seafood restaurants. Designed with a vibrant, modern UI, it offers seamless dish exploration, customized gourmet order selection, real-time shopping cart totals, instant checkout processing, and interactive order status tracking.',
  'assets/images/tapseafood.jpg',
  'E-Commerce,Web Development,PHP,MySQL,Interactive UI',
  'Interactive Digital Seafood Menu;Real-Time Cart Calculation;Customer Checkout Pipeline;Dynamic Pricing & Tax Computation;Admin Order Management Dashboard',
  'PHP, MySQL, JavaScript, HTML5/CSS3, Responsive UI/UX',
  'demo-tapseafood.html',
  'demo-tapseafood.html',
  'https://github.com/ralphtuliao/tapseafood-ordering',
  1
),
(
  'Hotel Online Booking',
  'Comprehensive Luxury Hotel Room Reservation & Availability System',
  'web',
  'A high-end hotel booking portal that delivers an effortless guest reservation experience. Features interactive suite previews, dynamic calendar date-selection, occupancy and guest filters, instant rate calculations, room availability verification, and automated confirmation records.',
  'assets/images/hotel-booking.jpg',
  'Hospitality,Booking Engine,Web App,PHP,Database System',
  'Real-Time Suite Availability Engine;Flexible Check-In / Check-Out Date Picker;Multi-Guest & Room Type Selector;Automated Booking Confirmation;Room Gallery & Amenity Explorer',
  'PHP, MySQL, Vanilla JavaScript, CSS3 Glassmorphism, AJAX',
  'demo-hotel.html',
  'demo-hotel.html',
  'https://github.com/ralphtuliao/hotel-online-booking',
  1
),
(
  'Arduino Water Dispenser',
  'Touchless Smart Liquid Dispensing System Powered by Arduino',
  'iot',
  'An intelligent contactless water dispensing station developed using an Arduino microcontroller and ultrasonic sensing. It accurately detects cup placement, automatically triggers a submerged 12V pump via relay, tracks dispensed milliliters on an I2C OLED display, and provides red/green status LED feedback for hygienic, hands-free operation.',
  'assets/images/arduino-dispenser.jpg',
  'IoT,Arduino,Hardware,C++,Automation,Sensors',
  'Touchless Proximity Detection via HC-SR04 Ultrasonic Sensor;Automated Relay-Driven Submersible Water Pump;Real-Time Milliliter & Water Level Tracking on OLED Display;Overfill Safety Cutoff & Status LED Indicators;Clean Acrylic Chamber & Breadboard Prototype Architecture',
  'Arduino UNO, C/C++, HC-SR04 Sensor, 0.96 OLED (I2C), 5V Relay, 12V Pump',
  'demo-arduino.html',
  'demo-arduino.html',
  'https://github.com/ralphtuliao/arduino-smart-water-dispenser',
  1
);

-- ----------------------------------------------------------
-- Table: skills
-- ----------------------------------------------------------
DROP TABLE IF EXISTS `skills`;
CREATE TABLE `skills` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `category` VARCHAR(50) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `proficiency` INT NOT NULL DEFAULT 85,
  `icon_code` VARCHAR(50) DEFAULT 'code'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `skills` (`category`, `name`, `proficiency`, `icon_code`) VALUES
('Web Frontend', 'HTML5 & Modern CSS3', 95, 'layout'),
('Web Frontend', 'JavaScript (ES6+)', 90, 'code'),
('Web Frontend', 'Responsive UI & Glassmorphism', 92, 'palette'),
('Backend & DB', 'PHP (Object-Oriented & Procedural)', 88, 'server'),
('Backend & DB', 'MySQL / Relational Database Design', 85, 'database'),
('Backend & DB', 'RESTful APIs & AJAX Communication', 84, 'wifi'),
('IoT & Hardware', 'Arduino UNO & C/C++ Programming', 88, 'cpu'),
('IoT & Hardware', 'Sensor Integration (Ultrasonic, IR, Temp)', 86, 'activity'),
('IoT & Hardware', 'Relay Control & DC Motors/Pumps', 82, 'zap'),
('Tools & Dev', 'Git & GitHub Version Control', 85, 'git-branch'),
('Tools & Dev', 'XAMPP & Apache Local Stacks', 90, 'terminal'),
('Tools & Dev', 'Hardware Debugging & Circuit Design', 80, 'tool');

-- ----------------------------------------------------------
-- Table: contact_messages
-- ----------------------------------------------------------
DROP TABLE IF EXISTS `contact_messages`;
CREATE TABLE `contact_messages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(120) NOT NULL,
  `subject` VARCHAR(150) NOT NULL,
  `message` TEXT NOT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `status` ENUM('unread', 'read', 'archived') DEFAULT 'unread'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------
-- Table: users (Authentication)
-- ----------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(120) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` ENUM('visitor', 'admin') DEFAULT 'visitor',
  `is_active` TINYINT(1) DEFAULT 1,
  `last_login` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Optional: seed an admin account (password: Admin@1234)
INSERT INTO `users` (`username`, `email`, `password_hash`, `role`) VALUES
(
  'admin',
  'ralphtuliao.tuliao@gmail.com',
  '$2y$10$eE0m1J8i04tNrvzP6y73UOMfWwGZJ9G7zZqTfT4H5PzX3f7g9a8sC',
  'admin'
);

-- ----------------------------------------------------------
-- Migration: Ensure live_demo_url column and interactive demo files
-- ----------------------------------------------------------
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `live_demo_url` VARCHAR(500) DEFAULT '' AFTER `demo_url`;

-- Set live demo URLs to interactive demo pages
UPDATE `projects` SET `demo_url` = 'demo-tapseafood.html', `live_demo_url` = 'demo-tapseafood.html' WHERE `title` LIKE '%Tapseafood%';
UPDATE `projects` SET `demo_url` = 'demo-hotel.html', `live_demo_url` = 'demo-hotel.html' WHERE `title` LIKE '%Hotel%';
UPDATE `projects` SET `demo_url` = 'demo-arduino.html', `live_demo_url` = 'demo-arduino.html' WHERE `title` LIKE '%Arduino%';
