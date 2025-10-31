<?php
require_once __DIR__ . '/../config/database.php';

/**
 * Images Model
 */
class ImagesModel extends BaseModel {
    protected $tableName = 'images';
    
    public function create($data) {
        $errors = RequestValidator::validateRequired($data, ['src', 'alt', 'type']);
        $errors = array_merge($errors, RequestValidator::validateImage($data));
        
        if (!empty($errors)) {
            ApiResponse::validationError($errors);
        }
        
        try {
            $stmt = $this->db->prepare("
                INSERT INTO images (src, alt, type, category, width, height, lazy, responsive, description) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $result = $stmt->execute([
                $data['src'],
                $data['alt'],
                $data['type'],
                $data['category'] ?? null,
                $data['width'] ?? null,
                $data['height'] ?? null,
                isset($data['lazy']) ? (bool)$data['lazy'] : false,
                isset($data['responsive']) ? (bool)$data['responsive'] : true,
                $data['description'] ?? null
            ]);
            
            $newId = $this->db->lastInsertId();
            
            // Log activity
            $this->logger->log('images', $newId, 'CREATE', null, $data);
            
            return $this->findById($newId);
        } catch (PDOException $e) {
            ApiResponse::error('Failed to create image: ' . $e->getMessage(), 500);
        }
    }
    
    public function update($id, $data) {
        $existing = $this->findById($id);
        if (!$existing) {
            ApiResponse::notFound('Image');
        }
        
        $errors = RequestValidator::validateImage($data);
        if (!empty($errors)) {
            ApiResponse::validationError($errors);
        }
        
        try {
            $fields = [];
            $values = [];
            
            $allowedFields = ['src', 'alt', 'type', 'category', 'width', 'height', 'lazy', 'responsive', 'description'];
            
            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $fields[] = "$field = ?";
                    $values[] = $field === 'lazy' || $field === 'responsive' ? (bool)$data[$field] : $data[$field];
                }
            }
            
            if (empty($fields)) {
                ApiResponse::error('No valid fields to update');
            }
            
            $fields[] = "updated_at = NOW()";
            $values[] = $id;
            
            $sql = "UPDATE images SET " . implode(', ', $fields) . " WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($values);
            
            // Log activity
            $this->logger->log('images', $id, 'UPDATE', $existing, $data);
            
            return $this->findById($id);
        } catch (PDOException $e) {
            ApiResponse::error('Failed to update image: ' . $e->getMessage(), 500);
        }
    }
    
    public function delete($id) {
        $existing = $this->findById($id);
        if (!$existing) {
            ApiResponse::notFound('Image');
        }
        
        try {
            $stmt = $this->db->prepare("DELETE FROM images WHERE id = ?");
            $stmt->execute([$id]);
            
            // Log activity
            $this->logger->log('images', $id, 'DELETE', $existing, null);
            
            return true;
        } catch (PDOException $e) {
            ApiResponse::error('Failed to delete image: ' . $e->getMessage(), 500);
        }
    }
    
    public function findByType($type) {
        $stmt = $this->db->prepare("SELECT * FROM images WHERE type = ? ORDER BY created_at ASC");
        $stmt->execute([$type]);
        return $stmt->fetchAll();
    }
    
    public function findByCategory($category) {
        $stmt = $this->db->prepare("SELECT * FROM images WHERE category = ? ORDER BY created_at ASC");
        $stmt->execute([$category]);
        return $stmt->fetchAll();
    }
}

/**
 * Accommodations Model
 */
class AccommodationsModel extends BaseModel {
    protected $tableName = 'accommodations';
    
    public function create($data) {
        $errors = RequestValidator::validateRequired($data, ['name', 'type']);
        $errors = array_merge($errors, RequestValidator::validateAccommodation($data));
        
        if (!empty($errors)) {
            ApiResponse::validationError($errors);
        }
        
        try {
            $stmt = $this->db->prepare("
                INSERT INTO accommodations (name, description, type, max_guests, bedrooms, bathrooms, 
                                          price_per_night, image_url, amenities, sort_order, is_active) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $result = $stmt->execute([
                $data['name'],
                $data['description'] ?? null,
                $data['type'],
                $data['max_guests'] ?? null,
                $data['bedrooms'] ?? null,
                $data['bathrooms'] ?? null,
                $data['price_per_night'] ?? null,
                $data['image_url'] ?? null,
                isset($data['amenities']) ? json_encode($data['amenities']) : null,
                $data['sort_order'] ?? 0,
                isset($data['is_active']) ? (bool)$data['is_active'] : true
            ]);
            
            $newId = $this->db->lastInsertId();
            
            // Log activity
            $this->logger->log('accommodations', $newId, 'CREATE', null, $data);
            
            return $this->findById($newId);
        } catch (PDOException $e) {
            ApiResponse::error('Failed to create accommodation: ' . $e->getMessage(), 500);
        }
    }
    
    public function update($id, $data) {
        $existing = $this->findById($id);
        if (!$existing) {
            ApiResponse::notFound('Accommodation');
        }
        
        $errors = RequestValidator::validateAccommodation($data);
        if (!empty($errors)) {
            ApiResponse::validationError($errors);
        }
        
        try {
            $fields = [];
            $values = [];
            
            $allowedFields = ['name', 'description', 'type', 'max_guests', 'bedrooms', 'bathrooms', 
                            'price_per_night', 'image_url', 'amenities', 'sort_order', 'is_active'];
            
            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $fields[] = "$field = ?";
                    if ($field === 'amenities') {
                        $values[] = json_encode($data[$field]);
                    } elseif ($field === 'is_active') {
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
            
            $sql = "UPDATE accommodations SET " . implode(', ', $fields) . " WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($values);
            
            // Log activity
            $this->logger->log('accommodations', $id, 'UPDATE', $existing, $data);
            
            return $this->findById($id);
        } catch (PDOException $e) {
            ApiResponse::error('Failed to update accommodation: ' . $e->getMessage(), 500);
        }
    }
    
    public function delete($id) {
        $existing = $this->findById($id);
        if (!$existing) {
            ApiResponse::notFound('Accommodation');
        }
        
        try {
            $stmt = $this->db->prepare("DELETE FROM accommodations WHERE id = ?");
            $stmt->execute([$id]);
            
            // Log activity
            $this->logger->log('accommodations', $id, 'DELETE', $existing, null);
            
            return true;
        } catch (PDOException $e) {
            ApiResponse::error('Failed to delete accommodation: ' . $e->getMessage(), 500);
        }
    }
    
    public function findByType($type) {
        $stmt = $this->db->prepare("
            SELECT * FROM accommodations 
            WHERE type = ? AND is_active = 1 
            ORDER BY sort_order ASC, created_at ASC
        ");
        $stmt->execute([$type]);
        return $stmt->fetchAll();
    }
}
?>