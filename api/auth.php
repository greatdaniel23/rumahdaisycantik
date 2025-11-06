<?php
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/middleware/auth.php';

CorsHandler::handle();
header('Content-Type: application/json');

$auth = new Auth();
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

switch ($method) {
    case 'POST':
        // Login
        if (isset($input['username']) && isset($input['password'])) {
            if ($auth->login($input['username'], $input['password'])) {
                ApiResponse::success(['loggedIn' => true, 'username' => $input['username']], 'Login successful');
            } else {
                ApiResponse::error('Invalid credentials', 401);
            }
        } else {
            ApiResponse::error('Username and password are required');
        }
        break;

    case 'GET':
        // Check session status
        $status = $auth->getSessionStatus();
        ApiResponse::success($status);
        break;

    case 'DELETE':
        // Logout
        $auth->logout();
        ApiResponse::success(null, 'Logged out successfully');
        break;

    case 'OPTIONS':
        // Handle preflight requests for CORS
        http_response_code(204); // No Content
        break;

    default:
        ApiResponse::error('Method not allowed', 405);
}
