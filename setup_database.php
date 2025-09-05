<?php
// Database Setup Script for Bus Tracking System
// Run this file to create the database and tables

echo "<h1>🚌 Bus Tracking System - Database Setup</h1>";

// Database connection parameters
$host = 'localhost';
$user = 'root';
$pass = '';

try {
    // Connect to MySQL without selecting a database
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p style='color: green;'>✅ Connected to MySQL server successfully!</p>";
    
    // Create database if it doesn't exist
    $sql = "CREATE DATABASE IF NOT EXISTS bus_tracking_db";
    $pdo->exec($sql);
    echo "<p>✅ Database 'bus_tracking_db' created/verified successfully!</p>";
    
    // Select the database
    $pdo->exec("USE bus_tracking_db");
    
    // Create tables
    $tables = [
        'buses' => "CREATE TABLE IF NOT EXISTS buses (
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
        )",
        
        'routes' => "CREATE TABLE IF NOT EXISTS routes (
            id VARCHAR(20) PRIMARY KEY,
            route_name VARCHAR(100) NOT NULL,
            start_location VARCHAR(100) NOT NULL,
            end_location VARCHAR(100) NOT NULL,
            frequency_minutes INT DEFAULT 15,
            estimated_duration_minutes INT DEFAULT 45,
            status ENUM('active', 'inactive') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        'bus_locations' => "CREATE TABLE IF NOT EXISTS bus_locations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            bus_id VARCHAR(20),
            latitude DECIMAL(10, 8) NOT NULL,
            longitude DECIMAL(11, 8) NOT NULL,
            speed DECIMAL(5, 2),
            occupancy INT,
            timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_bus_time (bus_id, timestamp)
        )",
        
        'users' => "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            email VARCHAR(100),
            role ENUM('admin', 'operator') DEFAULT 'operator',
            status ENUM('active', 'inactive') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        'stops' => "CREATE TABLE IF NOT EXISTS stops (
            id INT AUTO_INCREMENT PRIMARY KEY,
            stop_name VARCHAR(100) NOT NULL,
            latitude DECIMAL(10, 8) NOT NULL,
            longitude DECIMAL(11, 8) NOT NULL,
            route_id VARCHAR(20),
            stop_order INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )"
    ];
    
    foreach ($tables as $tableName => $sql) {
        try {
            $pdo->exec($sql);
            echo "<p>✅ Table '$tableName' created successfully!</p>";
        } catch (Exception $e) {
            echo "<p style='color: orange;'>⚠️ Table '$tableName' already exists or error: " . $e->getMessage() . "</p>";
        }
    }
    
    // Insert sample data
    echo "<h3>Inserting Sample Data...</h3>";
    
    // Sample routes
    $routes = [
        ['PB-12', 'Patna Bus Stand to Airport', 'Patna Bus Stand', 'Patna Airport', 15, 45],
        ['PB-15', 'Patna Junction to Gandhi Maidan', 'Patna Junction', 'Gandhi Maidan', 10, 30],
        ['PB-20', 'Patna to Danapur', 'Patna', 'Danapur', 20, 60]
    ];
    
    foreach ($routes as $route) {
        try {
            $sql = "INSERT IGNORE INTO routes (id, route_name, start_location, end_location, frequency_minutes, estimated_duration_minutes) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($route);
            echo "<p>✅ Route '{$route[1]}' added/verified</p>";
        } catch (Exception $e) {
            echo "<p style='color: orange;'>⚠️ Route '{$route[1]}' already exists</p>";
        }
    }
    
    // Sample buses
    $buses = [
        ['BUS-001', 'PB-001', 'PB-12', 50, 25.5941, 85.1376, 25.5, 35],
        ['BUS-002', 'PB-002', 'PB-15', 45, 25.6129, 85.1415, 20.0, 28],
        ['BUS-003', 'PB-003', 'PB-20', 55, 25.5941, 85.1376, 0.0, 0]
    ];
    
    foreach ($buses as $bus) {
        try {
            $sql = "INSERT IGNORE INTO buses (id, bus_number, route_id, capacity, current_lat, current_lng, speed, occupancy) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($bus);
            echo "<p>✅ Bus '{$bus[0]}' added/verified</p>";
        } catch (Exception $e) {
            echo "<p style='color: orange;'>⚠️ Bus '{$bus[0]}' already exists</p>";
        }
    }
    
    // Sample stops
    $stops = [
        ['Patna Bus Stand', 25.5941, 85.1376, 'PB-12', 1],
        ['Patna Junction', 25.6129, 85.1415, 'PB-12', 2],
        ['Patna Airport', 25.5913, 85.0880, 'PB-12', 3],
        ['Gandhi Maidan', 25.6129, 85.1415, 'PB-15', 1],
        ['Danapur', 25.5711, 85.0475, 'PB-20', 2]
    ];
    
    foreach ($stops as $stop) {
        try {
            $sql = "INSERT IGNORE INTO stops (stop_name, latitude, longitude, route_id, stop_order) VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($stop);
            echo "<p>✅ Stop '{$stop[0]}' added/verified</p>";
        } catch (Exception $e) {
            echo "<p style='color: orange;'>⚠️ Stop '{$stop[0]}' already exists</p>";
        }
    }
    
    // Create admin user
    try {
        $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $sql = "INSERT IGNORE INTO users (username, password, email, role) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['admin', $adminPassword, 'admin@bustracking.com', 'admin']);
        echo "<p>✅ Admin user created/verified (username: admin, password: admin123)</p>";
    } catch (Exception $e) {
        echo "<p style='color: orange;'>⚠️ Admin user already exists</p>";
    }
    
    echo "<h2 style='color: green;'>🎉 Database setup completed successfully!</h2>";
    
    // Show database info
    echo "<h3>Database Information:</h3>";
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM buses WHERE status = 'active'");
    $busCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM routes WHERE status = 'active'");
    $routeCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM stops");
    $stopCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    echo "<p><strong>Active Buses:</strong> $busCount</p>";
    echo "<p><strong>Active Routes:</strong> $routeCount</p>";
    echo "<p><strong>Total Stops:</strong> $stopCount</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Setup failed: " . $e->getMessage() . "</p>";
    echo "<p><strong>Please make sure:</strong></p>";
    echo "<ul>";
    echo "<li>XAMPP is running</li>";
    echo "<li>MySQL service is started</li>";
    echo "<li>MySQL credentials are correct (default: root, no password)</li>";
    echo "</ul>";
}

echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li><a href='database/test_connection.php'>Test Database Connection</a></li>";
echo "<li><a href='admin/dashboard.php'>Access Admin Dashboard</a></li>";
echo "<li><a href='Docs/index.html'>View Main Application</a></li>";
echo "<li><a href='api/buses.php'>Test Buses API</a></li>";
echo "</ol>";

echo "<p><strong>Admin Login:</strong> username: admin, password: admin123</p>";
?>
