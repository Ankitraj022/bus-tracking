<?php
// Database configuration for Bus Tracking System
// Using XAMPP MySQL server

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'bus_tracking_db');

// Create connection
function getConnection() {
    try {
        $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch(PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

// Test connection
function testConnection() {
    try {
        $conn = getConnection();
        echo "Database connection successful!";
        return true;
    } catch(Exception $e) {
        echo "Database connection failed: " . $e->getMessage();
        return false;
    }
}
?>
