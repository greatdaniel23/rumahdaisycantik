<?php
require_once __DIR__ . '/../config/database.php';

/**
 * Authentication Middleware
 */
class AuthMiddleware {
    private $db;
    
    public function __construct() {
        $this->db = DatabaseConfig::getInstance()->getConnection();
    }
    
    public function authenticate() {
        // Check session or API key authentication
        if (!$this->isAuthenticated()) {
            http_response_code(401);
            echo json_encode(['error' => 'Authentication required']);
            exit;
        }
        
        return $this->getCurrentUser();
    }
    
    private function isAuthenticated() {
        // Check for API key in headers
        $apiKey = $_SERVER['HTTP_X_API_KEY'] ?? null;
        if ($apiKey) {
            return $this->validateApiKey($apiKey);
        }
        
        // Check for session-based auth (for admin panel)
        $sessionData = $_SERVER['HTTP_X_SESSION_DATA'] ?? null;
        if ($sessionData) {
            return $this->validateSession(json_decode($sessionData, true));
        }
        
        return false;
    }
    
    private function validateApiKey($apiKey) {
        // For now, use a simple API key validation
        // In production, store hashed API keys in database
        return $apiKey === 'rdc_admin_2024_secure';
    }
    
    private function validateSession($sessionData) {
        if (!$sessionData || !isset($sessionData['authenticated'], $sessionData['timestamp'])) {
            return false;
        }
        
        // Check if session is still valid (30 minutes)
        $sessionTime = $sessionData['timestamp'];
        $currentTime = time() * 1000;
        $thirtyMinutes = 30 * 60 * 1000;
        
        if (($currentTime - $sessionTime) > $thirtyMinutes) {
            return false;
        }
        
        return $sessionData['authenticated'] === true;
    }
    
    private function getCurrentUser() {
        // Return basic user info for logging
        return [
            'id' => 1,
            'username' => 'admin',
            'role' => 'admin'
        ];
    }
}

/**
 * API Response Helper
 */
class ApiResponse {
    public static function success($data = null, $message = 'Success', $code = 200) {
        http_response_code($code);
        echo json_encode([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => date('c')
        ]);
        exit;
    }
    
    public static function error($message = 'An error occurred', $code = 400, $details = null) {
        http_response_code($code);
        echo json_encode([
            'success' => false,
            'message' => $message,
            'error' => $details,
            'timestamp' => date('c')
        ]);
        exit;
    }
    
    public static function notFound($resource = 'Resource') {
        self::error($resource . ' not found', 404);
    }
    
    public static function validationError($errors) {
        self::error('Validation failed', 422, $errors);
    }
}

/**
 * Request Validator
 */
class RequestValidator {
    public static function validateRequired($data, $requiredFields) {
        $errors = [];
        
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                $errors[$field] = "The {$field} field is required";
            }
        }
        
        return $errors;
    }
    
    public static function validateImage($data) {
        $errors = [];
        
        if (isset($data['src']) && !filter_var($data['src'], FILTER_VALIDATE_URL) && !self::isValidPath($data['src'])) {
            $errors['src'] = 'Invalid image source URL or path';
        }
        
        if (isset($data['type']) && !in_array($data['type'], ['hero', 'gallery', 'thumbnail', 'parallax', 'popup'])) {
            $errors['type'] = 'Invalid image type';
        }
        
        return $errors;
    }
    
    public static function validateAccommodation($data) {
        $errors = [];
        
        if (isset($data['type']) && !in_array($data['type'], ['villa', 'room', 'suite'])) {
            $errors['type'] = 'Invalid accommodation type';
        }
        
        if (isset($data['max_guests']) && (!is_numeric($data['max_guests']) || $data['max_guests'] < 1)) {
            $errors['max_guests'] = 'Max guests must be a positive number';
        }
        
        if (isset($data['price_per_night']) && (!is_numeric($data['price_per_night']) || $data['price_per_night'] < 0)) {
            $errors['price_per_night'] = 'Price must be a valid number';
        }
        
        return $errors;
    }
    
    private static function isValidPath($path) {
        // Basic path validation
        return preg_match('/^(\/|\.\/|\.\.\/)?[\w\-\/\.]+\.(jpg|jpeg|png|gif|webp|avif)$/i', $path);
    }
}

/**
 * CORS Handler
 */
class CorsHandler {
    public static function handle() {
        // Allow from same origin
        $origin = $_SERVER['HTTP_ORIGIN'] ?? $_SERVER['HTTP_HOST'] ?? '*';
        
        header("Access-Control-Allow-Origin: $origin");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, X-API-Key, X-Session-Data");
        header("Access-Control-Allow-Credentials: true");
        
        // Handle preflight OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
    }
}
?>