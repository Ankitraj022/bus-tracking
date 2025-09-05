<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../database/config.php';

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        getBuses();
        break;
    case 'POST':
        addOrUpdateBus();
        break;
    case 'PUT':
        updateBus();
        break;
    case 'DELETE':
        deleteBus();
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}

function getBuses() {
    try {
        $conn = getConnection();
        
        $sql = "SELECT b.*, r.route_name, r.start_location, r.end_location 
                FROM buses b 
                LEFT JOIN routes r ON b.route_id = r.id 
                WHERE b.status = 'active'";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $buses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode($buses);
    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

function addOrUpdateBus() {
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['id']) || !isset($input['lat']) || !isset($input['lng'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            return;
        }
        
        $conn = getConnection();
        
        // Check if bus exists
        $checkSql = "SELECT id FROM buses WHERE id = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->execute([$input['id']]);
        
        if ($checkStmt->rowCount() > 0) {
            // Update existing bus
            $sql = "UPDATE buses SET 
                    current_lat = ?, 
                    current_lng = ?, 
                    speed = ?, 
                    occupancy = ?, 
                    last_updated = CURRENT_TIMESTAMP 
                    WHERE id = ?";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                $input['lat'],
                $input['lng'],
                $input['speed'] ?? 0,
                $input['occupancy'] ?? 0,
                $input['id']
            ]);
            
            // Log location
            logBusLocation($conn, $input['id'], $input['lat'], $input['lng'], $input['speed'] ?? 0, $input['occupancy'] ?? 0);
            
            echo json_encode(['message' => 'Bus location updated successfully']);
        } else {
            // Add new bus
            $sql = "INSERT INTO buses (id, bus_number, current_lat, current_lng, speed, occupancy, status) 
                    VALUES (?, ?, ?, ?, ?, ?, 'active')";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                $input['id'],
                $input['bus_number'] ?? $input['id'],
                $input['lat'],
                $input['lng'],
                $input['speed'] ?? 0,
                $input['occupancy'] ?? 0
            ]);
            
            // Log location
            logBusLocation($conn, $input['id'], $input['lat'], $input['lng'], $input['speed'] ?? 0, $input['occupancy'] ?? 0);
            
            echo json_encode(['message' => 'New bus added successfully']);
        }
    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

function updateBus() {
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Bus ID is required']);
            return;
        }
        
        $conn = getConnection();
        
        $sql = "UPDATE buses SET ";
        $params = [];
        $updates = [];
        
        if (isset($input['route_id'])) {
            $updates[] = "route_id = ?";
            $params[] = $input['route_id'];
        }
        if (isset($input['status'])) {
            $updates[] = "status = ?";
            $params[] = $input['status'];
        }
        if (isset($input['capacity'])) {
            $updates[] = "capacity = ?";
            $params[] = $input['capacity'];
        }
        
        if (empty($updates)) {
            http_response_code(400);
            echo json_encode(['error' => 'No fields to update']);
            return;
        }
        
        $sql .= implode(', ', $updates) . " WHERE id = ?";
        $params[] = $input['id'];
        
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['message' => 'Bus updated successfully']);
        } else {
            echo json_encode(['message' => 'No changes made']);
        }
    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

function deleteBus() {
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Bus ID is required']);
            return;
        }
        
        $conn = getConnection();
        
        $sql = "UPDATE buses SET status = 'inactive' WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$input['id']]);
        
        echo json_encode(['message' => 'Bus deactivated successfully']);
    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

function logBusLocation($conn, $busId, $lat, $lng, $speed, $occupancy) {
    try {
        $sql = "INSERT INTO bus_locations (bus_id, latitude, longitude, speed, occupancy) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$busId, $lat, $lng, $speed, $occupancy]);
    } catch(Exception $e) {
        // Log error but don't fail the main operation
        error_log("Failed to log bus location: " . $e->getMessage());
    }
}
?>
