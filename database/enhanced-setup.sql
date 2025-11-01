-- Enhanced Database Schema for Rumah Daisy Cantik
-- Database: u289291769_websiterdc
-- User: u289291769_websiterdc  
-- Password: Kanibal123!!!

-- Drop existing tables if they exist (for clean setup)
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `activity_log`;
DROP TABLE IF EXISTS `room_amenities`;
DROP TABLE IF EXISTS `room_images`;
DROP TABLE IF EXISTS `rooms`;
DROP TABLE IF EXISTS `room_types`;
DROP TABLE IF EXISTS `admin_users`;
DROP TABLE IF EXISTS `pages`;
DROP TABLE IF EXISTS `buttons`;
DROP TABLE IF EXISTS `parallax`;
DROP TABLE IF EXISTS `popup`;
DROP TABLE IF EXISTS `accommodations`;
DROP TABLE IF EXISTS `images`;
SET FOREIGN_KEY_CHECKS = 1;

-- Create Images table (enhanced)
CREATE TABLE `images` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `src` varchar(500) NOT NULL,
  `alt` varchar(200) NOT NULL,
  `type` enum('hero', 'gallery', 'thumbnail', 'parallax', 'popup', 'room', 'villa', 'general') DEFAULT 'general',
  `category` varchar(50) NULL,
  `width` int NULL,
  `height` int NULL,
  `lazy` boolean DEFAULT FALSE,
  `responsive` boolean DEFAULT TRUE,
  `description` text NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Room Types table
CREATE TABLE `room_types` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(100) NOT NULL,
  `description` text NULL,
  `base_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `max_guests` int NOT NULL DEFAULT 2,
  `size_sqm` int NULL,
  `is_active` boolean DEFAULT TRUE,
  `sort_order` int DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Rooms table
CREATE TABLE `rooms` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `room_number` varchar(20) NOT NULL UNIQUE,
  `room_type_id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NULL,
  `floor` int NULL,
  `view_type` enum('pool', 'garden', 'jungle', 'ocean', 'mountain', 'city') NULL,
  `price_per_night` decimal(10,2) NOT NULL,
  `max_guests` int NOT NULL DEFAULT 2,
  `bedrooms` int DEFAULT 1,
  `bathrooms` int DEFAULT 1,
  `size_sqm` int NULL,
  `bed_type` varchar(50) NULL,
  `main_image_id` int NULL,
  `status` enum('available', 'occupied', 'maintenance', 'out_of_order') DEFAULT 'available',
  `is_active` boolean DEFAULT TRUE,
  `check_in_time` time DEFAULT '14:00:00',
  `check_out_time` time DEFAULT '12:00:00',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`room_type_id`) REFERENCES `room_types`(`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`main_image_id`) REFERENCES `images`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Room Images table (many-to-many relationship)
CREATE TABLE `room_images` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `room_id` int NOT NULL,
  `image_id` int NOT NULL,
  `sort_order` int DEFAULT 0,
  `is_primary` boolean DEFAULT FALSE,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`room_id`) REFERENCES `rooms`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`image_id`) REFERENCES `images`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_room_image` (`room_id`, `image_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Room Amenities table
CREATE TABLE `room_amenities` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `room_id` int NOT NULL,
  `amenity_name` varchar(100) NOT NULL,
  `amenity_type` enum('comfort', 'technology', 'bathroom', 'kitchen', 'outdoor', 'service') DEFAULT 'comfort',
  `description` text NULL,
  `icon` varchar(50) NULL,
  `is_highlighted` boolean DEFAULT FALSE,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`room_id`) REFERENCES `rooms`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Accommodations table (enhanced, now more general)
CREATE TABLE `accommodations` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(100) NOT NULL,
  `description` text NULL,
  `type` enum('villa', 'room', 'suite', 'apartment', 'bungalow') NOT NULL,
  `max_guests` int NULL,
  `bedrooms` int NULL,
  `bathrooms` int NULL,
  `price_per_night` decimal(10,2) NULL,
  `image_url` varchar(500) NULL,
  `amenities` json NULL,
  `sort_order` int DEFAULT 0,
  `is_active` boolean DEFAULT TRUE,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Popup table
CREATE TABLE `popup` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `title` varchar(100) NOT NULL,
  `description` text NULL,
  `image_url` varchar(500) NULL,
  `button_text` varchar(50) NULL,
  `button_url` varchar(500) NULL,
  `is_active` boolean DEFAULT TRUE,
  `display_priority` int DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Parallax table
CREATE TABLE `parallax` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `title` varchar(100) NOT NULL,
  `description` text NULL,
  `image_url` varchar(500) NOT NULL,
  `overlay_opacity` decimal(3,2) DEFAULT 0.50,
  `is_active` boolean DEFAULT TRUE,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Buttons table
CREATE TABLE `buttons` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `text` varchar(100) NOT NULL,
  `url` varchar(500) NOT NULL,
  `style` enum('primary', 'secondary', 'outline', 'ghost') DEFAULT 'primary',
  `icon` varchar(50) NULL,
  `target` enum('_self', '_blank') DEFAULT '_self',
  `is_active` boolean DEFAULT TRUE,
  `sort_order` int DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Pages table
CREATE TABLE `pages` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `page_name` varchar(50) NOT NULL UNIQUE,
  `title` varchar(100) NOT NULL,
  `description` text NULL,
  `meta_description` varchar(160) NULL,
  `keywords` json NULL,
  `is_active` boolean DEFAULT TRUE,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Admin Users table
CREATE TABLE `admin_users` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `username` varchar(50) NOT NULL UNIQUE,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(100) NULL,
  `full_name` varchar(100) NULL,
  `role` enum('admin', 'manager', 'editor') DEFAULT 'editor',
  `is_active` boolean DEFAULT TRUE,
  `last_login` timestamp NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Activity Log table
CREATE TABLE `activity_log` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `table_name` varchar(50) NOT NULL,
  `record_id` varchar(50) NOT NULL,
  `action` enum('CREATE', 'UPDATE', 'DELETE') NOT NULL,
  `old_values` json NULL,
  `new_values` json NULL,
  `admin_user_id` int NULL,
  `ip_address` varchar(45) NULL,
  `user_agent` varchar(500) NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`admin_user_id`) REFERENCES `admin_users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert initial data

-- Insert room types
INSERT INTO `room_types` (`name`, `description`, `base_price`, `max_guests`, `size_sqm`, `sort_order`) VALUES
('Deluxe Villa', 'Spacious villa with private pool and jungle view', 250.00, 4, 80, 1),
('Family Room', 'Perfect for families with pool access', 150.00, 6, 45, 2),
('Standard Room', 'Comfortable room with garden view', 100.00, 2, 25, 3),
('Poolside Bungalow', 'Cozy bungalow right by the pool', 180.00, 3, 35, 4);

-- Insert sample rooms
INSERT INTO `rooms` (`room_number`, `room_type_id`, `name`, `description`, `floor`, `view_type`, `price_per_night`, `max_guests`, `bedrooms`, `bathrooms`, `size_sqm`, `bed_type`) VALUES
('V01', 1, 'Villa Daisy', 'Luxury villa with private infinity pool overlooking the jungle', 1, 'jungle', 280.00, 4, 2, 2, 85, 'King + Twin'),
('V02', 1, 'Villa Cantik', 'Elegant villa with garden and pool access', 1, 'garden', 260.00, 4, 2, 2, 80, 'King + Twin'),
('FR01', 2, 'Family Haven', 'Spacious family room with connecting door option', 2, 'pool', 170.00, 6, 2, 2, 50, 'King + Bunk'),
('FR02', 2, 'Family Paradise', 'Large family room with pool view balcony', 2, 'pool', 160.00, 6, 2, 2, 48, 'King + Bunk'),
('SR01', 3, 'Garden Retreat', 'Peaceful standard room with garden view', 1, 'garden', 120.00, 2, 1, 1, 28, 'Queen'),
('SR02', 3, 'Jungle Whisper', 'Quiet room with partial jungle view', 1, 'jungle', 110.00, 2, 1, 1, 25, 'Queen'),
('PB01', 4, 'Poolside Paradise', 'Direct pool access bungalow', 1, 'pool', 200.00, 3, 1, 1, 40, 'King'),
('PB02', 4, 'Pool Breeze', 'Charming poolside bungalow with patio', 1, 'pool', 190.00, 3, 1, 1, 38, 'King');

-- Insert sample images
INSERT INTO `images` (`src`, `alt`, `type`, `category`) VALUES
('images/DSC07061.JPG', 'Main Pool View', 'hero', 'general'),
('images/logo.png', 'Rumah Daisy Cantik Logo', 'general', 'branding'),
('images/villa-deluxe-1.jpg', 'Deluxe Villa Interior', 'room', 'villa'),
('images/villa-deluxe-pool.jpg', 'Villa Private Pool', 'room', 'villa'),
('images/family-room-1.jpg', 'Family Room Layout', 'room', 'family'),
('images/standard-room-1.jpg', 'Standard Room Interior', 'room', 'standard'),
('images/poolside-bungalow.jpg', 'Poolside Bungalow Exterior', 'room', 'bungalow');

-- Insert room amenities
INSERT INTO `room_amenities` (`room_id`, `amenity_name`, `amenity_type`, `description`, `icon`, `is_highlighted`) VALUES
-- Villa Daisy amenities
(1, 'Private Infinity Pool', 'outdoor', 'Exclusive infinity pool with jungle view', 'fas fa-swimming-pool', TRUE),
(1, 'King Size Bed', 'comfort', 'Luxury king size bed with premium linens', 'fas fa-bed', TRUE),
(1, 'Air Conditioning', 'comfort', 'Individual climate control', 'fas fa-snowflake', FALSE),
(1, 'Free WiFi', 'technology', 'High-speed internet access', 'fas fa-wifi', FALSE),
(1, 'Private Bathroom', 'bathroom', 'En-suite bathroom with bathtub', 'fas fa-bath', FALSE),
(1, 'Kitchenette', 'kitchen', 'Mini kitchen with basic appliances', 'fas fa-blender', FALSE),
(1, 'Jungle View Terrace', 'outdoor', 'Private terrace overlooking jungle', 'fas fa-tree', TRUE),

-- Villa Cantik amenities
(2, 'Private Pool Access', 'outdoor', 'Direct access to private pool area', 'fas fa-swimming-pool', TRUE),
(2, 'King Size Bed', 'comfort', 'Luxury king size bed', 'fas fa-bed', TRUE),
(2, 'Air Conditioning', 'comfort', 'Climate control system', 'fas fa-snowflake', FALSE),
(2, 'Free WiFi', 'technology', 'Complimentary internet', 'fas fa-wifi', FALSE),
(2, 'Garden View', 'outdoor', 'Beautiful tropical garden view', 'fas fa-leaf', FALSE),

-- Family Room amenities  
(3, 'Family Layout', 'comfort', 'Designed specifically for families', 'fas fa-users', TRUE),
(3, 'Pool View Balcony', 'outdoor', 'Balcony overlooking main pool', 'fas fa-building', TRUE),
(3, 'Bunk Beds', 'comfort', 'Safe and fun bunk beds for kids', 'fas fa-bed', FALSE),
(3, 'Air Conditioning', 'comfort', 'Cool and comfortable environment', 'fas fa-snowflake', FALSE),
(3, 'Free WiFi', 'technology', 'Stay connected during your stay', 'fas fa-wifi', FALSE);

-- Insert accommodations
INSERT INTO `accommodations` (`name`, `description`, `type`, `max_guests`, `bedrooms`, `bathrooms`, `price_per_night`, `image_url`, `amenities`, `sort_order`) VALUES
('Luxury Villa Experience', 'Ultimate comfort in our luxury villas with stunning private pools and jungle views', 'villa', 4, 2, 2, 270.00, 'images/DSC07061.JPG', '["Private Pool", "Jungle View", "King Bed", "Kitchenette", "WiFi"]', 1),
('Family Paradise', 'Spacious accommodations perfect for families with pool access and kid-friendly amenities', 'room', 6, 2, 2, 165.00, 'images/family-room.jpg', '["Pool Access", "Family Layout", "Bunk Beds", "Balcony", "WiFi"]', 2),
('Garden Retreat', 'Peaceful rooms with beautiful garden views and modern comfort', 'room', 2, 1, 1, 115.00, 'images/garden-room.jpg', '["Garden View", "Queen Bed", "AC", "WiFi", "Private Bath"]', 3);

-- Insert popup
INSERT INTO `popup` (`title`, `description`, `image_url`, `button_text`, `button_url`) VALUES
('Welcome to Paradise!', 'Experience the ultimate tropical getaway at Rumah Daisy Cantik. Book now and save 20% on your first stay!', 'images/DSC07061.JPG', 'Book Now', 'https://booking.com/rumah-daisy-cantik');

-- Insert parallax
INSERT INTO `parallax` (`title`, `description`, `image_url`, `overlay_opacity`) VALUES
('Escape to Paradise', 'Discover your perfect tropical retreat surrounded by lush jungle and crystal-clear pools', 'images/DSC07061.JPG', 0.40);

-- Insert buttons
INSERT INTO `buttons` (`text`, `url`, `style`, `icon`, `target`) VALUES
('Reserve Now', 'https://booking.com/reserve', 'primary', 'fas fa-calendar-check', '_blank'),
('Contact Us', 'https://wa.me/6282221193425', 'secondary', 'fab fa-whatsapp', '_blank'),
('View Gallery', '#gallery', 'outline', 'fas fa-images', '_self'),
('Special Offers', '/offers.html', 'primary', 'fas fa-percent', '_self');

-- Insert pages
INSERT INTO `pages` (`page_name`, `title`, `description`, `meta_description`, `keywords`) VALUES
('home', 'Welcome to Rumah Daisy Cantik', 'Your tropical paradise awaits', 'Luxury accommodation in tropical setting with private pools and jungle views', '["tropical resort", "luxury villa", "private pool", "jungle view", "Indonesia"]'),
('about', 'About Us', 'Learn more about our tropical paradise', 'Discover the story behind Rumah Daisy Cantik resort', '["about us", "resort story", "tropical paradise"]'),
('rooms', 'Our Rooms & Villas', 'Explore our accommodation options', 'Browse our selection of villas, family rooms and bungalows', '["rooms", "villas", "accommodation", "booking"]'),
('contact', 'Contact Us', 'Get in touch with our team', 'Contact Rumah Daisy Cantik for reservations and inquiries', '["contact", "reservations", "booking", "phone"]');

-- Insert default admin user
INSERT INTO `admin_users` (`username`, `password_hash`, `email`, `full_name`, `role`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@rumahdaisycantik.com', 'System Administrator', 'admin');