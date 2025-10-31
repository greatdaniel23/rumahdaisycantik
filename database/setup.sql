-- Database: u289291769_websiterdc
-- User: u289291769_websiterdc  
-- Password: Kanibal123!!!

-- Create Images table
CREATE TABLE `images` (
  `id` varchar(50) NOT NULL PRIMARY KEY,
  `src` varchar(500) NOT NULL,
  `alt` varchar(200) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Accommodations table
CREATE TABLE `accommodations` (
  `id` varchar(50) NOT NULL PRIMARY KEY,
  `image_src` varchar(500) NOT NULL,
  `image_alt` varchar(200) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `button_text` varchar(50) NOT NULL,
  `button_link` varchar(500) NOT NULL,
  `sort_order` int DEFAULT 0,
  `is_active` boolean DEFAULT TRUE,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Popup table (single row for popup settings)
CREATE TABLE `popup` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `enabled` boolean DEFAULT FALSE,
  `title` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `image` varchar(500) NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Parallax table (single row for parallax settings)
CREATE TABLE `parallax` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `background_image` varchar(500) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Buttons table
CREATE TABLE `buttons` (
  `id` varchar(50) NOT NULL PRIMARY KEY,
  `text` varchar(100) NOT NULL,
  `link` varchar(500) NOT NULL,
  `is_active` boolean DEFAULT TRUE,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Pages table
CREATE TABLE `pages` (
  `id` varchar(50) NOT NULL PRIMARY KEY,
  `title` varchar(100) NOT NULL,
  `content` longtext NOT NULL,
  `is_active` boolean DEFAULT TRUE,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Admin Users table for authentication
CREATE TABLE `admin_users` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `username` varchar(50) NOT NULL UNIQUE,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(100) NULL,
  `is_active` boolean DEFAULT TRUE,
  `last_login` timestamp NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Activity Log table for tracking changes
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

-- Insert initial data based on current content.json

-- Insert images
INSERT INTO `images` (`id`, `src`, `alt`) VALUES
('logo-header', 'images/logo.png', 'Logo'),
('welcome-pool-view', 'images/DSC07061.JPG', 'Rumah Daisy Cantik Pool View'),
('logo-footer', 'images/logo.png', 'Logo');

-- Insert accommodations
INSERT INTO `accommodations` (`id`, `image_src`, `image_alt`, `title`, `description`, `button_text`, `button_link`, `sort_order`) VALUES
('package-1', 'images/DSC07061.JPG', 'Luxury Villa with Private Pool', 'Luxury Villa', 'Experience the ultimate comfort in our luxury villas with a stunning private pool and jungle view.', 'Book This Villa', 'https://booking.com/villa-1', 1),
('package-2', 'images/DSC07061.JPG', 'Family Room with Pool Access', 'Family Room', 'Spacious rooms perfect for families, with direct access to our main pool and lounge area.', 'View Details', '#', 2),
('package-3', 'images/DSC07061.JPG', 'Poolside Room with Lounge', 'Poolside Room', 'Cozy room with a beautiful poolside view and a comfortable lounge area.', 'View Details', '#', 3);

-- Insert popup settings
INSERT INTO `popup` (`enabled`, `title`, `message`, `image`) VALUES
(TRUE, 'Special Offer!', 'Enjoy a complimentary breakfast for bookings made this week.', 'images/DSC07061.JPG');

-- Insert parallax settings
INSERT INTO `parallax` (`background_image`) VALUES
('https://images.unsplash.com/photo-1506748686214-e9df14d4d9d0');

-- Insert buttons
INSERT INTO `buttons` (`id`, `text`, `link`) VALUES
('cta-book-now', 'Reserve Now!', 'https://new-booking-link.com/'),
('hero-ask-us', 'Ask Us', 'https://wa.me/6282221193425'),
('hero-whatsapp', 'WhatsApp', 'https://wa.me/6282221193425'),
('search-availability-btn', 'Search Availability', ''),
('footer-subscribe-send', 'Send', '');

-- Insert pages
INSERT INTO `pages` (`id`, `title`, `content`) VALUES
('about', 'About Us', '<h1>About Rumah Daisy Cantik</h1><p>This is the about page content. It can be edited from the admin panel.</p>'),
('villas', 'Our Villas', '<h1>Our Villas</h1><p>This is the villas page content. It can be edited from the admin panel.</p>'),
('contact', 'Contact Us', '<h1>Contact Us</h1><p>This is the contact page content. It can be edited from the admin panel.</p>'),
('offers', 'Special Offers', '<h1>Special Offers</h1><p>This is the offers page content. It can be edited from the admin panel.</p>');

-- Insert default admin user (password: 'password' - will be hashed in PHP)
-- Note: This will be handled by the PHP script for proper password hashing