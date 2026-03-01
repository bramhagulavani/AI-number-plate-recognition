-- Sample database and data for the ANPR project
-- Run with: mysql -u root -p < sample_data.sql

CREATE DATABASE IF NOT EXISTS `number_plate_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `number_plate_db`;

-- Users table (used for registration/login/profile)
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `mobile` VARCHAR(20) DEFAULT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `address` TEXT DEFAULT NULL,
  `pincode` VARCHAR(10) DEFAULT NULL,
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- NOTE: The `password` column in this dump contains plain text for easy import and inspection.
-- For production use you MUST store password hashes created by PHP's password_hash().
-- Example to convert a plain password to a hash using PHP CLI:
-- php -r "echo password_hash('password123', PASSWORD_BCRYPT) . PHP_EOL;"

INSERT INTO `users` (name, mobile, email, address, pincode, password)
VALUES
('Bramha Gulavani', '9371146064', 'admin@example.com', 'Admin Office, City', '400001', 'password123'),
('Alice Johnson', '9123456780', 'alice@example.com', '123 Maple St', '400002', 'alicepass'),
('Bob Smith', '9198765432', 'bob@example.com', '456 Oak Ave', '400003', 'bobpass'),
('Carol Lee', '9187654321', 'carol@example.com', '789 Pine Rd', '400004', 'carolpass'),
('David Kim', '9176543210', 'david@example.com', '101 Elm St', '400005', 'davidpass');

-- Vehicle records table (referenced by display.php)
CREATE TABLE IF NOT EXISTS `vehicle_records` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `number_plate` VARCHAR(50) NOT NULL,
  `image_path` VARCHAR(255) DEFAULT NULL,
  `location` VARCHAR(255) DEFAULT NULL,
  `entry_time` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `vehicle_records` (number_plate, image_path, location, entry_time)
VALUES
('MH12AB1234', 'public/assets/images/realtime.jpg', 'Gate A', '2026-02-25 08:12:00'),
('MH14CD5678', 'public/assets/images/1.png', 'Gate B', '2026-02-25 09:05:00'),
('MH20EF9012', 'public/assets/images/22.png', 'Gate A', '2026-02-25 10:20:00'),
('MH31GH3456', 'public/assets/images/3.png', 'Parking Lot', '2026-02-25 11:45:00'),
('MH43IJ7890', 'public/assets/images/mustang.png', 'Gate C', '2026-02-25 12:30:00');

-- Optional: You can replace plain passwords with bcrypt hashes using PHP and then run UPDATE statements, e.g.:
-- UPDATE users SET password = '<bcrypt-hash>' WHERE email = 'alice@example.com';

-- End of sample data
