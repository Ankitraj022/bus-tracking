-- Bus Tracking System Database Setup
-- Run this script in phpMyAdmin to create the database and tables

-- Create database
CREATE DATABASE IF NOT EXISTS bus_tracking_db;
USE bus_tracking_db;

-- Create buses table
CREATE TABLE IF NOT EXISTS buses (
    id VARCHAR(20) PRIMARY KEY,
    bus_number VARCHAR(20) NOT NULL,
    route_id VARCHAR(20),
    capacity INT DEFAULT 50,
    current_lat DECIMAL(10, 8),
    current_lng DECIMAL(11, 8),
    speed DECIMAL(5, 2) DEFAULT 0.00,
    occupancy INT DEFAULT 0,
    status ENUM('active', 'inactive', 'maintenance') DEFAULT 'active',
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create routes table
CREATE TABLE IF NOT EXISTS routes (
    id VARCHAR(20) PRIMARY KEY,
    route_name VARCHAR(100) NOT NULL,
    start_location VARCHAR(100) NOT NULL,
    end_location VARCHAR(100) NOT NULL,
    frequency_minutes INT DEFAULT 15,
    estimated_duration_minutes INT DEFAULT 45,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create bus_locations table for tracking history
CREATE TABLE IF NOT EXISTS bus_locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bus_id VARCHAR(20),
    latitude DECIMAL(10, 8) NOT NULL,
    longitude DECIMAL(11, 8) NOT NULL,
    speed DECIMAL(5, 2),
    occupancy INT,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (bus_id) REFERENCES buses(id) ON DELETE CASCADE
);

-- Create users table for admin access
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    role ENUM('admin', 'operator') DEFAULT 'operator',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create stops table
CREATE TABLE IF NOT EXISTS stops (
    id INT AUTO_INCREMENT PRIMARY KEY,
    stop_name VARCHAR(100) NOT NULL,
    latitude DECIMAL(10, 8) NOT NULL,
    longitude DECIMAL(11, 8) NOT NULL,
    route_id VARCHAR(20),
    stop_order INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (route_id) REFERENCES routes(id) ON DELETE SET NULL
);

-- Insert sample data for testing
INSERT INTO routes (id, route_name, start_location, end_location, frequency_minutes, estimated_duration_minutes) VALUES
('PB-12', 'Patna Bus Stand to Airport', 'Patna Bus Stand', 'Patna Airport', 15, 45),
('PB-15', 'Patna Junction to Gandhi Maidan', 'Patna Junction', 'Gandhi Maidan', 10, 30),
('PB-20', 'Patna to Danapur', 'Patna', 'Danapur', 20, 60);

INSERT INTO buses (id, bus_number, route_id, capacity, current_lat, current_lng, speed, occupancy, status) VALUES
('BUS-001', 'PB-001', 'PB-12', 50, 25.5941, 85.1376, 25.5, 35, 'active'),
('BUS-002', 'PB-002', 'PB-15', 45, 25.6129, 85.1415, 20.0, 28, 'active'),
('BUS-003', 'PB-003', 'PB-20', 55, 25.5941, 85.1376, 0.0, 0, 'active');

INSERT INTO stops (stop_name, latitude, longitude, route_id, stop_order) VALUES
('Patna Bus Stand', 25.5941, 85.1376, 'PB-12', 1),
('Patna Junction', 25.6129, 85.1415, 'PB-12', 2),
('Patna Airport', 25.5913, 85.0880, 'PB-12', 3),
('Gandhi Maidan', 25.6129, 85.1415, 'PB-15', 1),
('Danapur', 25.5711, 85.0475, 'PB-20', 2);

-- Insert default admin user (password: admin123)
INSERT INTO users (username, password, email, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@bustracking.com', 'admin');

-- Create indexes for better performance
CREATE INDEX idx_bus_route ON buses(route_id);
CREATE INDEX idx_bus_status ON buses(status);
CREATE INDEX idx_location_bus_time ON bus_locations(bus_id, timestamp);
CREATE INDEX idx_stop_route ON stops(route_id);
