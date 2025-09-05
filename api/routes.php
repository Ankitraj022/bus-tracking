<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../database/config.php';

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        getRoutes();
        break;
    case 'POST':
        addRoute();
        break;
    case 'PUT':
        updateRoute();
        break;
    case 'DELETE':
        deleteRoute();
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}

function getRoutes() {
    try {
        $conn = getConnection();
        
        $sql = "SELECT r.*, 
                       COUNT(DISTINCT b.id) as active_buses,
                       COUNT(s.id) as total_stops
                FROM routes r 
                LEFT JOIN buses b ON r.id = b.route_id AND b.status = 'active'
                LEFT JOIN stops s ON r.id = s.route_id
                WHERE r.status = 'active'
                GROUP BY r.id";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $routes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode($routes);
    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

function addRoute() {
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['route_name']) || !isset($input['start_location']) || !isset($input['end_location'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            return;
        }
        
        $conn = getConnection();
        
        // Generate route ID
        $routeId = 'PB-' . rand(100, 999);
        
        $sql = "INSERT INTO routes (id, route_name, start_location, end_location, frequency_minutes, estimated_duration_minutes) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $routeId,
            $input['route_name'],
            $input['start_location'],
            $input['end_location'],
            $input['frequency_minutes'] ?? 15,
            $input['estimated_duration_minutes'] ?? 45
        ]);
        
        echo json_encode(['message' => 'Route added successfully', 'route_id' => $routeId]);
    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

function updateRoute() {
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Route ID is required']);
            return;
        }
        
        $conn = getConnection();
        
        $sql = "UPDATE routes SET ";
        $params = [];
        $updates = [];
        
        if (isset($input['route_name'])) {
            $updates[] = "route_name = ?";
            $params[] = $input['route_name'];
        }
        if (isset($input['start_location'])) {
            $updates[] = "start_location = ?";
            $params[] = $input['start_location'];
        }
        if (isset($input['end_location'])) {
            $updates[] = "end_location = ?";
            $params[] = $input['end_location'];
        }
        if (isset($input['frequency_minutes'])) {
            $updates[] = "frequency_minutes = ?";
            $params[] = $input['frequency_minutes'];
        }
        if (isset($input['estimated_duration_minutes'])) {
            $updates[] = "estimated_duration_minutes = ?";
            $params[] = $input['estimated_duration_minutes'];
        }
        if (isset($input['status'])) {
            $updates[] = "status = ?";
            $params[] = $input['status'];
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
            echo json_encode(['message' => 'Route updated successfully']);
        } else {
            echo json_encode(['message' => 'No changes made']);
        }
    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

function deleteRoute() {
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Route ID is required']);
            return;
        }
        
        $conn = getConnection();
        
        // Check if route has active buses
        $checkSql = "SELECT COUNT(*) as bus_count FROM buses WHERE route_id = ? AND status = 'active'";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->execute([$input['id']]);
        $busCount = $checkStmt->fetch(PDO::FETCH_ASSOC)['bus_count'];
        
        if ($busCount > 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Cannot delete route with active buses']);
            return;
        }
        
        $sql = "UPDATE routes SET status = 'inactive' WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$input['id']]);
        
        echo json_encode(['message' => 'Route deactivated successfully']);
    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}
?>
