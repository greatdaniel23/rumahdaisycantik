<?php
require_once __DIR__ . '/../config/database.php';

/**
 * Room Types Model
 */
class RoomTypesModel extends BaseModel {
    protected $tableName = 'room_types';
    
    public function create($data) {
        $errors = RequestValidator::validateRequired($data, ['name', 'base_price', 'max_guests']);
        
        if (!empty($errors)) {
            ApiResponse::validationError($errors);
        }
        
        try {
            $stmt = $this->db->prepare("
                INSERT INTO room_types (name, description, base_price, max_guests, size_sqm, is_active, sort_order) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            $result = $stmt->execute([
                $data['name'],
                $data['description'] ?? null,
                $data['base_price'],
                $data['max_guests'],
                $data['size_sqm'] ?? null,
                isset($data['is_active']) ? (bool)$data['is_active'] : true,
                $data['sort_order'] ?? 0
            ]);
            
            $newId = $this->db->lastInsertId();
            
            // Log activity
            $this->logger->log('room_types', $newId, 'CREATE', null, $data);
            
            return $this->findById($newId);
        } catch (PDOException $e) {
            ApiResponse::error('Failed to create room type: ' . $e->getMessage(), 500);
        }
    }
    
    public function update($id, $data) {
        $existing = $this->findById($id);
        if (!$existing) {
            ApiResponse::notFound('Room Type');
        }
        
        try {
            $fields = [];
            $values = [];
            
            $allowedFields = ['name', 'description', 'base_price', 'max_guests', 'size_sqm', 'is_active', 'sort_order'];
            
            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $fields[] = "$field = ?";
                    if ($field === 'is_active') {
                        $values[] = (bool)$data[$field];
                    } else {
                        $values[] = $data[$field];
                    }
                }
            }
            
            if (empty($fields)) {
                ApiResponse::error('No valid fields to update');
            }
            
            $fields[] = "updated_at = NOW()";
            $values[] = $id;
            
            $sql = "UPDATE room_types SET " . implode(', ', $fields) . " WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($values);
            
            // Log activity
            $this->logger->log('room_types', $id, 'UPDATE', $existing, $data);
            
            return $this->findById($id);
        } catch (PDOException $e) {
            ApiResponse::error('Failed to update room type: ' . $e->getMessage(), 500);
        }
    }
    
    public function delete($id) {
        $existing = $this->findById($id);
        if (!$existing) {
            ApiResponse::notFound('Room Type');
        }
        
        // Check if any rooms are using this room type
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM rooms WHERE room_type_id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        
        if ($result['count'] > 0) {
            ApiResponse::error('Cannot delete room type that is being used by rooms', 400);
        }
        
        try {
            $stmt = $this->db->prepare("DELETE FROM room_types WHERE id = ?");
            $stmt->execute([$id]);
            
            // Log activity
            $this->logger->log('room_types', $id, 'DELETE', $existing, null);
            
            return true;
        } catch (PDOException $e) {
            ApiResponse::error('Failed to delete room type: ' . $e->getMessage(), 500);
        }
    }
}

/**
 * Rooms Model
 */
class RoomsModel extends BaseModel {
    protected $tableName = 'rooms';
    
    public function findAll($active = true) {
        $sql = "
            SELECT r.*, rt.name as room_type_name, rt.description as room_type_description,
                   i.src as main_image_src, i.alt as main_image_alt
            FROM rooms r 
            LEFT JOIN room_types rt ON r.room_type_id = rt.id
            LEFT JOIN images i ON r.main_image_id = i.id
        ";
        
        if ($active) {
            $sql .= " WHERE r.is_active = 1";
        }
        
        $sql .= " ORDER BY r.room_number ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function findById($id) {
        $stmt = $this->db->prepare("
            SELECT r.*, rt.name as room_type_name, rt.description as room_type_description,
                   i.src as main_image_src, i.alt as main_image_alt
            FROM rooms r 
            LEFT JOIN room_types rt ON r.room_type_id = rt.id
            LEFT JOIN images i ON r.main_image_id = i.id
            WHERE r.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function create($data) {
        $errors = RequestValidator::validateRequired($data, ['room_number', 'room_type_id', 'name', 'price_per_night']);
        
        // Validate room type exists
        $stmt = $this->db->prepare("SELECT id FROM room_types WHERE id = ?");
        $stmt->execute([$data['room_type_id']]);
        if (!$stmt->fetch()) {
            $errors['room_type_id'] = 'Invalid room type';
        }
        
        // Check room number uniqueness
        $stmt = $this->db->prepare("SELECT id FROM rooms WHERE room_number = ?");
        $stmt->execute([$data['room_number']]);
        if ($stmt->fetch()) {
            $errors['room_number'] = 'Room number already exists';
        }
        
        if (!empty($errors)) {
            ApiResponse::validationError($errors);
        }
        
        try {
            $stmt = $this->db->prepare("
                INSERT INTO rooms (room_number, room_type_id, name, description, floor, view_type, 
                                 price_per_night, max_guests, bedrooms, bathrooms, size_sqm, bed_type, 
                                 main_image_id, status, is_active, check_in_time, check_out_time) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $result = $stmt->execute([
                $data['room_number'],
                $data['room_type_id'],
                $data['name'],
                $data['description'] ?? null,
                $data['floor'] ?? null,
                $data['view_type'] ?? null,
                $data['price_per_night'],
                $data['max_guests'] ?? 2,
                $data['bedrooms'] ?? 1,
                $data['bathrooms'] ?? 1,
                $data['size_sqm'] ?? null,
                $data['bed_type'] ?? null,
                $data['main_image_id'] ?? null,
                $data['status'] ?? 'available',
                isset($data['is_active']) ? (bool)$data['is_active'] : true,
                $data['check_in_time'] ?? '14:00:00',
                $data['check_out_time'] ?? '12:00:00'
            ]);
            
            $newId = $this->db->lastInsertId();
            
            // Log activity
            $this->logger->log('rooms', $newId, 'CREATE', null, $data);
            
            return $this->findById($newId);
        } catch (PDOException $e) {
            ApiResponse::error('Failed to create room: ' . $e->getMessage(), 500);
        }
    }
    
    public function update($id, $data) {
        $existing = $this->findById($id);
        if (!$existing) {
            ApiResponse::notFound('Room');
        }
        
        $errors = [];
        
        // Validate room type if provided
        if (isset($data['room_type_id'])) {
            $stmt = $this->db->prepare("SELECT id FROM room_types WHERE id = ?");
            $stmt->execute([$data['room_type_id']]);
            if (!$stmt->fetch()) {
                $errors['room_type_id'] = 'Invalid room type';
            }
        }
        
        // Check room number uniqueness if changed
        if (isset($data['room_number']) && $data['room_number'] !== $existing['room_number']) {
            $stmt = $this->db->prepare("SELECT id FROM rooms WHERE room_number = ? AND id != ?");
            $stmt->execute([$data['room_number'], $id]);
            if ($stmt->fetch()) {
                $errors['room_number'] = 'Room number already exists';
            }
        }
        
        if (!empty($errors)) {
            ApiResponse::validationError($errors);
        }
        
        try {
            $fields = [];
            $values = [];
            
            $allowedFields = ['room_number', 'room_type_id', 'name', 'description', 'floor', 'view_type',
                            'price_per_night', 'max_guests', 'bedrooms', 'bathrooms', 'size_sqm', 'bed_type',
                            'main_image_id', 'status', 'is_active', 'check_in_time', 'check_out_time'];
            
            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $fields[] = "$field = ?";
                    if ($field === 'is_active') {
                        $values[] = (bool)$data[$field];
                    } else {
                        $values[] = $data[$field];
                    }
                }
            }
            
            if (empty($fields)) {
                ApiResponse::error('No valid fields to update');
            }
            
            $fields[] = "updated_at = NOW()";
            $values[] = $id;
            
            $sql = "UPDATE rooms SET " . implode(', ', $fields) . " WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($values);
            
            // Log activity
            $this->logger->log('rooms', $id, 'UPDATE', $existing, $data);
            
            return $this->findById($id);
        } catch (PDOException $e) {
            ApiResponse::error('Failed to update room: ' . $e->getMessage(), 500);
        }
    }
    
    public function delete($id) {
        $existing = $this->findById($id);
        if (!$existing) {
            ApiResponse::notFound('Room');
        }
        
        try {
            // Start transaction to delete room and related data
            $this->db->beginTransaction();
            
            // Delete room amenities
            $stmt = $this->db->prepare("DELETE FROM room_amenities WHERE room_id = ?");
            $stmt->execute([$id]);
            
            // Delete room images relationships
            $stmt = $this->db->prepare("DELETE FROM room_images WHERE room_id = ?");
            $stmt->execute([$id]);
            
            // Delete room
            $stmt = $this->db->prepare("DELETE FROM rooms WHERE id = ?");
            $stmt->execute([$id]);
            
            $this->db->commit();
            
            // Log activity
            $this->logger->log('rooms', $id, 'DELETE', $existing, null);
            
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            ApiResponse::error('Failed to delete room: ' . $e->getMessage(), 500);
        }
    }
    
    public function findByRoomType($roomTypeId) {
        $stmt = $this->db->prepare("
            SELECT r.*, rt.name as room_type_name, i.src as main_image_src, i.alt as main_image_alt
            FROM rooms r 
            LEFT JOIN room_types rt ON r.room_type_id = rt.id
            LEFT JOIN images i ON r.main_image_id = i.id
            WHERE r.room_type_id = ? AND r.is_active = 1
            ORDER BY r.room_number ASC
        ");
        $stmt->execute([$roomTypeId]);
        return $stmt->fetchAll();
    }
    
    public function findByStatus($status) {
        $stmt = $this->db->prepare("
            SELECT r.*, rt.name as room_type_name, i.src as main_image_src, i.alt as main_image_alt
            FROM rooms r 
            LEFT JOIN room_types rt ON r.room_type_id = rt.id
            LEFT JOIN images i ON r.main_image_id = i.id
            WHERE r.status = ? AND r.is_active = 1
            ORDER BY r.room_number ASC
        ");
        $stmt->execute([$status]);
        return $stmt->fetchAll();
    }
}

/**
 * Room Amenities Model
 */
class RoomAmenitiesModel extends BaseModel {
    protected $tableName = 'room_amenities';
    
    public function findByRoomId($roomId) {
        $stmt = $this->db->prepare("
            SELECT * FROM room_amenities 
            WHERE room_id = ? 
            ORDER BY is_highlighted DESC, amenity_type ASC, amenity_name ASC
        ");
        $stmt->execute([$roomId]);
        return $stmt->fetchAll();
    }
    
    public function create($data) {
        $errors = RequestValidator::validateRequired($data, ['room_id', 'amenity_name']);
        
        // Validate room exists
        $stmt = $this->db->prepare("SELECT id FROM rooms WHERE id = ?");
        $stmt->execute([$data['room_id']]);
        if (!$stmt->fetch()) {
            $errors['room_id'] = 'Invalid room ID';
        }
        
        if (!empty($errors)) {
            ApiResponse::validationError($errors);
        }
        
        try {
            $stmt = $this->db->prepare("
                INSERT INTO room_amenities (room_id, amenity_name, amenity_type, description, icon, is_highlighted) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            $result = $stmt->execute([
                $data['room_id'],
                $data['amenity_name'],
                $data['amenity_type'] ?? 'comfort',
                $data['description'] ?? null,
                $data['icon'] ?? null,
                isset($data['is_highlighted']) ? (bool)$data['is_highlighted'] : false
            ]);
            
            $newId = $this->db->lastInsertId();
            
            // Log activity
            $this->logger->log('room_amenities', $newId, 'CREATE', null, $data);
            
            return $this->findById($newId);
        } catch (PDOException $e) {
            ApiResponse::error('Failed to create room amenity: ' . $e->getMessage(), 500);
        }
    }
    
    public function bulkCreate($roomId, $amenities) {
        try {
            $this->db->beginTransaction();
            
            // Delete existing amenities for this room
            $stmt = $this->db->prepare("DELETE FROM room_amenities WHERE room_id = ?");
            $stmt->execute([$roomId]);
            
            // Insert new amenities
            $stmt = $this->db->prepare("
                INSERT INTO room_amenities (room_id, amenity_name, amenity_type, description, icon, is_highlighted) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            foreach ($amenities as $amenity) {
                $stmt->execute([
                    $roomId,
                    $amenity['amenity_name'],
                    $amenity['amenity_type'] ?? 'comfort',
                    $amenity['description'] ?? null,
                    $amenity['icon'] ?? null,
                    isset($amenity['is_highlighted']) ? (bool)$amenity['is_highlighted'] : false
                ]);
            }
            
            $this->db->commit();
            
            // Log activity
            $this->logger->log('room_amenities', $roomId, 'BULK_UPDATE', null, $amenities);
            
            return $this->findByRoomId($roomId);
        } catch (PDOException $e) {
            $this->db->rollBack();
            ApiResponse::error('Failed to update room amenities: ' . $e->getMessage(), 500);
        }
    }
}

/**
 * Room Images Model
 */
class RoomImagesModel extends BaseModel {
    protected $tableName = 'room_images';
    
    public function findByRoomId($roomId) {
        $stmt = $this->db->prepare("
            SELECT ri.*, i.src, i.alt, i.type, i.description
            FROM room_images ri
            JOIN images i ON ri.image_id = i.id
            WHERE ri.room_id = ?
            ORDER BY ri.is_primary DESC, ri.sort_order ASC
        ");
        $stmt->execute([$roomId]);
        return $stmt->fetchAll();
    }
    
    public function addImageToRoom($roomId, $imageId, $isPrimary = false, $sortOrder = 0) {
        try {
            // If setting as primary, unset other primary images for this room
            if ($isPrimary) {
                $stmt = $this->db->prepare("UPDATE room_images SET is_primary = FALSE WHERE room_id = ?");
                $stmt->execute([$roomId]);
            }
            
            $stmt = $this->db->prepare("
                INSERT INTO room_images (room_id, image_id, sort_order, is_primary) 
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order), is_primary = VALUES(is_primary)
            ");
            
            $stmt->execute([$roomId, $imageId, $sortOrder, $isPrimary]);
            
            // Log activity
            $this->logger->log('room_images', $roomId . '_' . $imageId, 'CREATE', null, [
                'room_id' => $roomId,
                'image_id' => $imageId,
                'is_primary' => $isPrimary,
                'sort_order' => $sortOrder
            ]);
            
            return true;
        } catch (PDOException $e) {
            ApiResponse::error('Failed to add image to room: ' . $e->getMessage(), 500);
        }
    }
    
    public function removeImageFromRoom($roomId, $imageId) {
        try {
            $stmt = $this->db->prepare("DELETE FROM room_images WHERE room_id = ? AND image_id = ?");
            $stmt->execute([$roomId, $imageId]);
            
            // Log activity
            $this->logger->log('room_images', $roomId . '_' . $imageId, 'DELETE', null, null);
            
            return true;
        } catch (PDOException $e) {
            ApiResponse::error('Failed to remove image from room: ' . $e->getMessage(), 500);
        }
    }
}
?>