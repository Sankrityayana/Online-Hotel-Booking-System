-- Online Hotel Booking System Database Schema
-- Created: 2025-12-29

CREATE DATABASE IF NOT EXISTS hotel_booking_db;
USE hotel_booking_db;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    user_type ENUM('customer', 'admin', 'hotel_manager') DEFAULT 'customer',
    profile_picture VARCHAR(255) DEFAULT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_user_type (user_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Hotels table
CREATE TABLE IF NOT EXISTS hotels (
    id INT PRIMARY KEY AUTO_INCREMENT,
    hotel_name VARCHAR(200) NOT NULL,
    description TEXT,
    address TEXT NOT NULL,
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100),
    country VARCHAR(100) NOT NULL,
    postal_code VARCHAR(20),
    phone VARCHAR(20),
    email VARCHAR(100),
    star_rating INT DEFAULT 3,
    image VARCHAR(255),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_city (city),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Room types
CREATE TABLE IF NOT EXISTS room_types (
    id INT PRIMARY KEY AUTO_INCREMENT,
    type_name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Rooms table
CREATE TABLE IF NOT EXISTS rooms (
    id INT PRIMARY KEY AUTO_INCREMENT,
    hotel_id INT NOT NULL,
    room_type_id INT NOT NULL,
    room_number VARCHAR(50) NOT NULL,
    floor INT,
    price_per_night DECIMAL(10, 2) NOT NULL,
    capacity INT DEFAULT 2,
    bed_type VARCHAR(50),
    size_sqft INT,
    description TEXT,
    image VARCHAR(255),
    status ENUM('available', 'occupied', 'maintenance') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (hotel_id) REFERENCES hotels(id) ON DELETE CASCADE,
    FOREIGN KEY (room_type_id) REFERENCES room_types(id) ON DELETE CASCADE,
    INDEX idx_hotel (hotel_id),
    INDEX idx_status (status),
    INDEX idx_price (price_per_night)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Amenities
CREATE TABLE IF NOT EXISTS amenities (
    id INT PRIMARY KEY AUTO_INCREMENT,
    amenity_name VARCHAR(100) NOT NULL,
    icon VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Room amenities junction table
CREATE TABLE IF NOT EXISTS room_amenities (
    room_id INT NOT NULL,
    amenity_id INT NOT NULL,
    PRIMARY KEY (room_id, amenity_id),
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
    FOREIGN KEY (amenity_id) REFERENCES amenities(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Bookings table
CREATE TABLE IF NOT EXISTS bookings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    room_id INT NOT NULL,
    hotel_id INT NOT NULL,
    check_in_date DATE NOT NULL,
    check_out_date DATE NOT NULL,
    num_guests INT DEFAULT 1,
    total_amount DECIMAL(10, 2) NOT NULL,
    booking_status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    payment_status ENUM('pending', 'paid', 'refunded') DEFAULT 'pending',
    payment_method VARCHAR(50),
    special_requests TEXT,
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
    FOREIGN KEY (hotel_id) REFERENCES hotels(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_dates (check_in_date, check_out_date),
    INDEX idx_status (booking_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Reviews table
CREATE TABLE IF NOT EXISTS reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    hotel_id INT NOT NULL,
    booking_id INT,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    review_title VARCHAR(200),
    review_text TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (hotel_id) REFERENCES hotels(id) ON DELETE CASCADE,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE SET NULL,
    INDEX idx_hotel (hotel_id),
    INDEX idx_rating (rating)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Payments table
CREATE TABLE IF NOT EXISTS payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    booking_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    transaction_id VARCHAR(100),
    payment_status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    INDEX idx_booking (booking_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Activity logs
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    action VARCHAR(100) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin user
INSERT INTO users (full_name, email, password, user_type) VALUES
('Admin', 'admin@hotelbooking.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
-- Default password: password

-- Insert room types
INSERT INTO room_types (type_name, description) VALUES
('Standard', 'Basic comfortable room with essential amenities'),
('Deluxe', 'Spacious room with premium amenities and better view'),
('Suite', 'Luxury suite with separate living area and premium features'),
('Executive', 'High-end room with business facilities'),
('Family Room', 'Large room suitable for families with multiple beds'),
('Penthouse', 'Top floor luxury suite with exceptional amenities');

-- Insert amenities
INSERT INTO amenities (amenity_name, icon) VALUES
('WiFi', '📶'),
('Air Conditioning', '❄️'),
('TV', '📺'),
('Mini Bar', '🍷'),
('Room Service', '🛎️'),
('Safe', '🔒'),
('Coffee Maker', '☕'),
('Balcony', '🏞️'),
('Ocean View', '🌊'),
('City View', '🏙️'),
('Jacuzzi', '🛁'),
('Work Desk', '💼');

-- Insert sample hotels
INSERT INTO hotels (hotel_name, description, address, city, state, country, postal_code, phone, email, star_rating, status) VALUES
('Grand Palace Hotel', 'Luxurious 5-star hotel in the heart of the city with world-class amenities and service.', '123 Main Street', 'New York', 'NY', 'USA', '10001', '+1-555-0100', 'info@grandpalace.com', 5, 'active'),
('Ocean View Resort', 'Beautiful beachfront resort with stunning ocean views and premium facilities.', '456 Beach Road', 'Miami', 'FL', 'USA', '33101', '+1-555-0200', 'reservations@oceanview.com', 4, 'active'),
('Mountain Lodge', 'Cozy mountain retreat perfect for nature lovers and adventure seekers.', '789 Mountain Path', 'Denver', 'CO', 'USA', '80201', '+1-555-0300', 'contact@mountainlodge.com', 3, 'active');

-- Insert sample rooms for Grand Palace Hotel (id=1)
INSERT INTO rooms (hotel_id, room_type_id, room_number, floor, price_per_night, capacity, bed_type, size_sqft, description, status) VALUES
(1, 1, '101', 1, 150.00, 2, 'Queen', 300, 'Comfortable standard room with city view', 'available'),
(1, 2, '201', 2, 250.00, 2, 'King', 450, 'Deluxe room with premium amenities', 'available'),
(1, 3, '301', 3, 450.00, 4, 'King + Sofa Bed', 700, 'Luxury suite with living area', 'available'),
(1, 1, '102', 1, 150.00, 2, 'Queen', 300, 'Standard room with modern decor', 'available');

-- Insert sample rooms for Ocean View Resort (id=2)
INSERT INTO rooms (hotel_id, room_type_id, room_number, floor, price_per_night, capacity, bed_type, size_sqft, description, status) VALUES
(2, 2, 'B101', 1, 300.00, 2, 'King', 500, 'Beachfront deluxe room with ocean view', 'available'),
(2, 3, 'B201', 2, 500.00, 4, 'King + 2 Queens', 800, 'Family suite with balcony', 'available'),
(2, 1, 'B102', 1, 200.00, 2, 'Queen', 350, 'Standard room near beach', 'available');

-- Insert sample rooms for Mountain Lodge (id=3)
INSERT INTO rooms (hotel_id, room_type_id, room_number, floor, price_per_night, capacity, bed_type, size_sqft, description, status) VALUES
(3, 1, 'M101', 1, 120.00, 2, 'Queen', 280, 'Cozy mountain view room', 'available'),
(3, 5, 'M201', 2, 220.00, 6, '2 Queens + Sofa', 600, 'Spacious family room with fireplace', 'available');

-- Sample customer
INSERT INTO users (full_name, email, password, phone, address, user_type) VALUES
('John Smith', 'customer@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+1-555-1234', '123 Customer St, New York, NY', 'customer');
-- Default password: password
