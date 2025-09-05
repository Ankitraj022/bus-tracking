<?php
// Test database connection
require_once 'config.php';

echo "<h2>Database Connection Test</h2>";

try {
    $conn = getConnection();
    echo "<p style='color: green;'>✅ Database connection successful!</p>";
    
    // Test if database exists
    $stmt = $conn->query("SELECT DATABASE() as db_name");
    $dbName = $stmt->fetch(PDO::FETCH_ASSOC)['db_name'];
    echo "<p>Connected to database: <strong>$dbName</strong></p>";
    
    // Test if tables exist
    $tables = ['buses', 'routes', 'bus_locations', 'users', 'stops'];
    echo "<h3>Checking Tables:</h3>";
    
    foreach ($tables as $table) {
        try {
            $stmt = $conn->query("SELECT COUNT(*) as count FROM $table");
            $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            echo "<p>✅ Table '$table' exists with $count records</p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Table '$table' does not exist or has error: " . $e->getMessage() . "</p>";
        }
    }
    
    // Show sample data
    echo "<h3>Sample Data:</h3>";
    
    // Sample buses
    $stmt = $conn->query("SELECT * FROM buses LIMIT 3");
    $buses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<h4>Buses:</h4>";
    echo "<pre>" . print_r($buses, true) . "</pre>";
    
    // Sample routes
    $stmt = $conn->query("SELECT * FROM routes LIMIT 3");
    $routes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<h4>Routes:</h4>";
    echo "<pre>" . print_r($routes, true) . "</pre>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database connection failed: " . $e->getMessage() . "</p>";
    echo "<p><strong>Please make sure:</strong></p>";
    echo "<ul>";
    echo "<li>XAMPP is running</li>";
    echo "<li>MySQL service is started</li>";
    echo "<li>Database 'bus_tracking_db' exists</li>";
    echo "<li>Tables are created using setup.sql</li>";
    echo "</ul>";
}
?>

<h3>Next Steps:</h3>
<ol>
    <li>If connection failed, check XAMPP and create database using setup.sql</li>
    <li>If connection successful, you can now use the admin dashboard</li>
    <li>Access admin dashboard at: <a href="../admin/dashboard.php">Admin Dashboard</a></li>
    <li>Test API endpoints at: <a href="../api/buses.php">Buses API</a></li>
</ol>
