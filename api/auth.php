<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../database/config.php';

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'POST':
        login();
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}

function login() {
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['username']) || !isset($input['password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Username and password are required']);
            return;
        }
        
        $conn = getConnection();
        
        $sql = "SELECT id, username, password, role, email FROM users WHERE username = ? AND status = 'active'";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$input['username']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($input['password'], $user['password'])) {
            // Remove password from response
            unset($user['password']);
            
            // Start session
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            echo json_encode([
                'message' => 'Login successful',
                'user' => $user,
                'session_id' => session_id()
            ]);
        } else {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid username or password']);
        }
    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

function logout() {
    session_start();
    session_destroy();
    echo json_encode(['message' => 'Logout successful']);
}

function checkAuth() {
    session_start();
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        return false;
    }
    return true;
}

function requireRole($requiredRole) {
    if (!checkAuth()) {
        return false;
    }
    
    if ($_SESSION['role'] !== $requiredRole && $_SESSION['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Insufficient permissions']);
        return false;
    }
    
    return true;
}
?>
