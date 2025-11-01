<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../models/ContentModel.php';
require_once __DIR__ . '/../models/RoomModel.php';

// Enable CORS
CorsHandler::handle();

// Set JSON content type
header('Content-Type: application/json');

// Parse request
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$pathParts = explode('/', trim($path, '/'));

// Remove 'api' from path if present
if ($pathParts[0] === 'api') {
    array_shift($pathParts);
}

$resource = $pathParts[0] ?? '';
$id = $pathParts[1] ?? null;

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true) ?? [];

// Authentication (skip for OPTIONS requests)
if ($method !== 'OPTIONS') {
    $auth = new AuthMiddleware();
    $currentUser = $auth->authenticate();
}

try {
    switch ($resource) {
        case 'images':
            handleImagesApi($method, $id, $input);
            break;
            
        case 'accommodations':
            handleAccommodationsApi($method, $id, $input);
            break;
            
        case 'room-types':
            handleRoomTypesApi($method, $id, $input);
            break;
            
        case 'rooms':
            handleRoomsApi($method, $id, $input);
            break;
            
        case 'room-amenities':
            handleRoomAmenitiesApi($method, $id, $input);
            break;
            
        case 'room-images':
            handleRoomImagesApi($method, $id, $input);
            break;
            
        case 'popup':
            handlePopupApi($method, $id, $input);
            break;
            
        case 'parallax':
            handleParallaxApi($method, $id, $input);
            break;
            
        case 'buttons':
            handleButtonsApi($method, $id, $input);
            break;
            
        case 'pages':
            handlePagesApi($method, $id, $input);
            break;
            
        case 'health':
            handleHealthCheck();
            break;
            
        default:
            ApiResponse::notFound('API endpoint');
    }
} catch (Exception $e) {
    error_log("API Error: " . $e->getMessage());
    ApiResponse::error('Internal server error', 500);
}

/**
 * Images API Handler
 */
function handleImagesApi($method, $id, $input) {
    $model = new ImagesModel();
    
    switch ($method) {
        case 'GET':
            if ($id) {
                $image = $model->findById($id);
                if (!$image) {
                    ApiResponse::notFound('Image');
                }
                ApiResponse::success($image);
            } else {
                // Check for filters
                $type = $_GET['type'] ?? null;
                $category = $_GET['category'] ?? null;
                
                if ($type) {
                    $images = $model->findByType($type);
                } elseif ($category) {
                    $images = $model->findByCategory($category);
                } else {
                    $images = $model->findAll();
                }
                
                ApiResponse::success($images);
            }
            break;
            
        case 'POST':
            $image = $model->create($input);
            ApiResponse::success($image, 'Image created successfully', 201);
            break;
            
        case 'PUT':
            if (!$id) {
                ApiResponse::error('Image ID is required');
            }
            $image = $model->update($id, $input);
            ApiResponse::success($image, 'Image updated successfully');
            break;
            
        case 'DELETE':
            if (!$id) {
                ApiResponse::error('Image ID is required');
            }
            $model->delete($id);
            ApiResponse::success(null, 'Image deleted successfully');
            break;
            
        default:
            ApiResponse::error('Method not allowed', 405);
    }
}

/**
 * Accommodations API Handler
 */
function handleAccommodationsApi($method, $id, $input) {
    $model = new AccommodationsModel();
    
    switch ($method) {
        case 'GET':
            if ($id) {
                $accommodation = $model->findById($id);
                if (!$accommodation) {
                    ApiResponse::notFound('Accommodation');
                }
                
                // Parse amenities JSON
                if ($accommodation['amenities']) {
                    $accommodation['amenities'] = json_decode($accommodation['amenities'], true);
                }
                
                ApiResponse::success($accommodation);
            } else {
                // Check for filters
                $type = $_GET['type'] ?? null;
                
                if ($type) {
                    $accommodations = $model->findByType($type);
                } else {
                    $accommodations = $model->findAll();
                }
                
                // Parse amenities JSON for all items
                foreach ($accommodations as &$accommodation) {
                    if ($accommodation['amenities']) {
                        $accommodation['amenities'] = json_decode($accommodation['amenities'], true);
                    }
                }
                
                ApiResponse::success($accommodations);
            }
            break;
            
        case 'POST':
            $accommodation = $model->create($input);
            if ($accommodation['amenities']) {
                $accommodation['amenities'] = json_decode($accommodation['amenities'], true);
            }
            ApiResponse::success($accommodation, 'Accommodation created successfully', 201);
            break;
            
        case 'PUT':
            if (!$id) {
                ApiResponse::error('Accommodation ID is required');
            }
            $accommodation = $model->update($id, $input);
            if ($accommodation['amenities']) {
                $accommodation['amenities'] = json_decode($accommodation['amenities'], true);
            }
            ApiResponse::success($accommodation, 'Accommodation updated successfully');
            break;
            
        case 'DELETE':
            if (!$id) {
                ApiResponse::error('Accommodation ID is required');
            }
            $model->delete($id);
            ApiResponse::success(null, 'Accommodation deleted successfully');
            break;
            
        default:
            ApiResponse::error('Method not allowed', 405);
    }
}

/**
 * Generic API Handler for simple tables
 */
function handleGenericApi($method, $id, $input, $tableName, $requiredFields = []) {
    $db = DatabaseConfig::getInstance()->getConnection();
    $logger = new ActivityLogger();
    
    switch ($method) {
        case 'GET':
            if ($id) {
                $stmt = $db->prepare("SELECT * FROM $tableName WHERE id = ?");
                $stmt->execute([$id]);
                $item = $stmt->fetch();
                
                if (!$item) {
                    ApiResponse::notFound(ucfirst($tableName));
                }
                
                ApiResponse::success($item);
            } else {
                $stmt = $db->prepare("SELECT * FROM $tableName ORDER BY created_at ASC");
                $stmt->execute();
                $items = $stmt->fetchAll();
                
                ApiResponse::success($items);
            }
            break;
            
        case 'POST':
            $errors = RequestValidator::validateRequired($input, $requiredFields);
            if (!empty($errors)) {
                ApiResponse::validationError($errors);
            }
            
            // Build insert query dynamically
            $fields = array_keys($input);
            $placeholders = str_repeat('?,', count($fields) - 1) . '?';
            $sql = "INSERT INTO $tableName (" . implode(',', $fields) . ") VALUES ($placeholders)";
            
            $stmt = $db->prepare($sql);
            $stmt->execute(array_values($input));
            
            $newId = $db->lastInsertId();
            $logger->log($tableName, $newId, 'CREATE', null, $input);
            
            // Return the created item
            $stmt = $db->prepare("SELECT * FROM $tableName WHERE id = ?");
            $stmt->execute([$newId]);
            $item = $stmt->fetch();
            
            ApiResponse::success($item, ucfirst($tableName) . ' created successfully', 201);
            break;
            
        case 'PUT':
            if (!$id) {
                ApiResponse::error(ucfirst($tableName) . ' ID is required');
            }
            
            // Check if item exists
            $stmt = $db->prepare("SELECT * FROM $tableName WHERE id = ?");
            $stmt->execute([$id]);
            $existing = $stmt->fetch();
            
            if (!$existing) {
                ApiResponse::notFound(ucfirst($tableName));
            }
            
            // Build update query dynamically
            $fields = [];
            $values = [];
            
            foreach ($input as $field => $value) {
                $fields[] = "$field = ?";
                $values[] = $value;
            }
            
            if (empty($fields)) {
                ApiResponse::error('No valid fields to update');
            }
            
            $fields[] = "updated_at = NOW()";
            $values[] = $id;
            
            $sql = "UPDATE $tableName SET " . implode(', ', $fields) . " WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute($values);
            
            $logger->log($tableName, $id, 'UPDATE', $existing, $input);
            
            // Return updated item
            $stmt = $db->prepare("SELECT * FROM $tableName WHERE id = ?");
            $stmt->execute([$id]);
            $item = $stmt->fetch();
            
            ApiResponse::success($item, ucfirst($tableName) . ' updated successfully');
            break;
            
        case 'DELETE':
            if (!$id) {
                ApiResponse::error(ucfirst($tableName) . ' ID is required');
            }
            
            // Check if item exists
            $stmt = $db->prepare("SELECT * FROM $tableName WHERE id = ?");
            $stmt->execute([$id]);
            $existing = $stmt->fetch();
            
            if (!$existing) {
                ApiResponse::notFound(ucfirst($tableName));
            }
            
            $stmt = $db->prepare("DELETE FROM $tableName WHERE id = ?");
            $stmt->execute([$id]);
            
            $logger->log($tableName, $id, 'DELETE', $existing, null);
            
            ApiResponse::success(null, ucfirst($tableName) . ' deleted successfully');
            break;
            
        default:
            ApiResponse::error('Method not allowed', 405);
    }
}

/**
 * Popup API Handler
 */
function handlePopupApi($method, $id, $input) {
    handleGenericApi($method, $id, $input, 'popup', ['title', 'image_url']);
}

/**
 * Parallax API Handler
 */
function handleParallaxApi($method, $id, $input) {
    handleGenericApi($method, $id, $input, 'parallax', ['title', 'image_url']);
}

/**
 * Buttons API Handler
 */
function handleButtonsApi($method, $id, $input) {
    handleGenericApi($method, $id, $input, 'buttons', ['text', 'url']);
}

/**
 * Pages API Handler
 */
function handlePagesApi($method, $id, $input) {
    handleGenericApi($method, $id, $input, 'pages', ['page_name', 'title']);
}

/**
 * Room Types API Handler
 */
function handleRoomTypesApi($method, $id, $input) {
    $model = new RoomTypesModel();
    
    switch ($method) {
        case 'GET':
            if ($id) {
                $roomType = $model->findById($id);
                if (!$roomType) {
                    ApiResponse::notFound('Room Type');
                }
                ApiResponse::success($roomType);
            } else {
                $roomTypes = $model->findAll();
                ApiResponse::success($roomTypes);
            }
            break;
            
        case 'POST':
            $roomType = $model->create($input);
            ApiResponse::success($roomType, 'Room type created successfully', 201);
            break;
            
        case 'PUT':
            if (!$id) {
                ApiResponse::error('Room Type ID is required');
            }
            $roomType = $model->update($id, $input);
            ApiResponse::success($roomType, 'Room type updated successfully');
            break;
            
        case 'DELETE':
            if (!$id) {
                ApiResponse::error('Room Type ID is required');
            }
            $model->delete($id);
            ApiResponse::success(null, 'Room type deleted successfully');
            break;
            
        default:
            ApiResponse::error('Method not allowed', 405);
    }
}

/**
 * Rooms API Handler
 */
function handleRoomsApi($method, $id, $input) {
    $model = new RoomsModel();
    
    switch ($method) {
        case 'GET':
            if ($id) {
                $room = $model->findById($id);
                if (!$room) {
                    ApiResponse::notFound('Room');
                }
                ApiResponse::success($room);
            } else {
                // Check for filters
                $roomTypeId = $_GET['room_type_id'] ?? null;
                $status = $_GET['status'] ?? null;
                
                if ($roomTypeId) {
                    $rooms = $model->findByRoomType($roomTypeId);
                } elseif ($status) {
                    $rooms = $model->findByStatus($status);
                } else {
                    $rooms = $model->findAll();
                }
                
                ApiResponse::success($rooms);
            }
            break;
            
        case 'POST':
            $room = $model->create($input);
            ApiResponse::success($room, 'Room created successfully', 201);
            break;
            
        case 'PUT':
            if (!$id) {
                ApiResponse::error('Room ID is required');
            }
            $room = $model->update($id, $input);
            ApiResponse::success($room, 'Room updated successfully');
            break;
            
        case 'DELETE':
            if (!$id) {
                ApiResponse::error('Room ID is required');
            }
            $model->delete($id);
            ApiResponse::success(null, 'Room deleted successfully');
            break;
            
        default:
            ApiResponse::error('Method not allowed', 405);
    }
}

/**
 * Room Amenities API Handler
 */
function handleRoomAmenitiesApi($method, $id, $input) {
    $model = new RoomAmenitiesModel();
    
    switch ($method) {
        case 'GET':
            if ($id) {
                $amenity = $model->findById($id);
                if (!$amenity) {
                    ApiResponse::notFound('Room Amenity');
                }
                ApiResponse::success($amenity);
            } else {
                $roomId = $_GET['room_id'] ?? null;
                if ($roomId) {
                    $amenities = $model->findByRoomId($roomId);
                    ApiResponse::success($amenities);
                } else {
                    ApiResponse::error('room_id parameter is required');
                }
            }
            break;
            
        case 'POST':
            // Check if this is a bulk update
            if (isset($input['room_id']) && isset($input['amenities']) && is_array($input['amenities'])) {
                $amenities = $model->bulkCreate($input['room_id'], $input['amenities']);
                ApiResponse::success($amenities, 'Room amenities updated successfully');
            } else {
                $amenity = $model->create($input);
                ApiResponse::success($amenity, 'Room amenity created successfully', 201);
            }
            break;
            
        case 'PUT':
            if (!$id) {
                ApiResponse::error('Room Amenity ID is required');
            }
            $amenity = $model->update($id, $input);
            ApiResponse::success($amenity, 'Room amenity updated successfully');
            break;
            
        case 'DELETE':
            if (!$id) {
                ApiResponse::error('Room Amenity ID is required');
            }
            $model->delete($id);
            ApiResponse::success(null, 'Room amenity deleted successfully');
            break;
            
        default:
            ApiResponse::error('Method not allowed', 405);
    }
}

/**
 * Room Images API Handler
 */
function handleRoomImagesApi($method, $id, $input) {
    $model = new RoomImagesModel();
    
    switch ($method) {
        case 'GET':
            $roomId = $_GET['room_id'] ?? null;
            if ($roomId) {
                $images = $model->findByRoomId($roomId);
                ApiResponse::success($images);
            } else {
                ApiResponse::error('room_id parameter is required');
            }
            break;
            
        case 'POST':
            if (!isset($input['room_id']) || !isset($input['image_id'])) {
                ApiResponse::error('room_id and image_id are required');
            }
            
            $result = $model->addImageToRoom(
                $input['room_id'],
                $input['image_id'],
                $input['is_primary'] ?? false,
                $input['sort_order'] ?? 0
            );
            
            ApiResponse::success($result, 'Image added to room successfully', 201);
            break;
            
        case 'DELETE':
            if (!isset($input['room_id']) || !isset($input['image_id'])) {
                ApiResponse::error('room_id and image_id are required');
            }
            
            $model->removeImageFromRoom($input['room_id'], $input['image_id']);
            ApiResponse::success(null, 'Image removed from room successfully');
            break;
            
        default:
            ApiResponse::error('Method not allowed', 405);
    }
}

/**
 * Health Check Handler
 */
function handleHealthCheck() {
    $db = DatabaseConfig::getInstance();
    $isHealthy = $db->testConnection();
    
    ApiResponse::success([
        'status' => $isHealthy ? 'healthy' : 'unhealthy',
        'database' => $isHealthy ? 'connected' : 'disconnected',
        'timestamp' => date('c'),
        'version' => '2.0.0'
    ], 'API is ' . ($isHealthy ? 'healthy' : 'unhealthy'));
}
?>