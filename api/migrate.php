<?php
/**
 * Database Migration Script
 * Run this script to create database tables and import existing data
 */

require_once __DIR__ . '/config/database.php';

class DatabaseMigration {
    private $db;
    
    public function __construct() {
        $this->db = DatabaseConfig::getInstance()->getConnection();
    }
    
    public function runMigration() {
        echo "Starting database migration...\n";
        
            try {
                // Check if enhanced schema exists, use it if available
                $enhancedSqlFile = __DIR__ . '/database/enhanced-setup.sql';
                $sqlFile = __DIR__ . '/database/setup.sql';
                
                if (file_exists($enhancedSqlFile)) {
                    $sqlFile = $enhancedSqlFile;
                    echo "Using enhanced database schema...\n";
                } elseif (file_exists($sqlFile)) {
                    echo "Using standard database schema...\n";
                } else {
                    throw new Exception("Setup SQL file not found");
                }
                
                $sql = file_get_contents($sqlFile);
                $statements = explode(';', $sql);
                
                foreach ($statements as $statement) {
                    $statement = trim($statement);
                    if (!empty($statement)) {
                        $this->db->exec($statement);
                    }
                }
                
                echo "✓ Database tables created successfully\n";            // Import existing data from content.json
            $this->importExistingData();
            
            echo "✓ Database migration completed successfully\n";
            
        } catch (Exception $e) {
            echo "✗ Migration failed: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
    
    private function importExistingData() {
        echo "Importing existing data from content.json...\n";
        
        $contentFile = __DIR__ . '/../content.json';
        if (!file_exists($contentFile)) {
            echo "! No content.json file found, skipping data import\n";
            return;
        }
        
        $content = json_decode(file_get_contents($contentFile), true);
        if (!$content) {
            echo "! Invalid content.json format, skipping data import\n";
            return;
        }
        
        // Import images
        if (isset($content['images'])) {
            $this->importImages($content['images']);
        }
        
        // Import accommodations
        if (isset($content['accommodations'])) {
            $this->importAccommodations($content['accommodations']);
        }
        
        // Import popup
        if (isset($content['popup'])) {
            $this->importPopup($content['popup']);
        }
        
        // Import parallax
        if (isset($content['parallax'])) {
            $this->importParallax($content['parallax']);
        }
        
        // Import buttons
        if (isset($content['buttons'])) {
            $this->importButtons($content['buttons']);
        }
        
        // Import pages
        if (isset($content['pages'])) {
            $this->importPages($content['pages']);
        }
        
        echo "✓ Data import completed\n";
    }
    
    private function importImages($images) {
        $stmt = $this->db->prepare("
            INSERT INTO images (src, alt, type, category, width, height, lazy, responsive, description) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $count = 0;
        foreach ($images as $category => $categoryImages) {
            foreach ($categoryImages as $image) {
                $stmt->execute([
                    $image['src'] ?? '',
                    $image['alt'] ?? '',
                    $image['type'] ?? 'gallery',
                    $category,
                    $image['width'] ?? null,
                    $image['height'] ?? null,
                    isset($image['lazy']) ? (bool)$image['lazy'] : false,
                    isset($image['responsive']) ? (bool)$image['responsive'] : true,
                    $image['description'] ?? null
                ]);
                $count++;
            }
        }
        
        echo "  ✓ Imported $count images\n";
    }
    
    private function importAccommodations($accommodations) {
        $stmt = $this->db->prepare("
            INSERT INTO accommodations (name, description, type, max_guests, bedrooms, bathrooms, 
                                      price_per_night, image_url, amenities, sort_order, is_active) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $count = 0;
        $sortOrder = 0;
        
        foreach ($accommodations as $accommodation) {
            $stmt->execute([
                $accommodation['name'] ?? '',
                $accommodation['description'] ?? null,
                $accommodation['type'] ?? 'villa',
                $accommodation['max_guests'] ?? null,
                $accommodation['bedrooms'] ?? null,
                $accommodation['bathrooms'] ?? null,
                $accommodation['price_per_night'] ?? null,
                $accommodation['image_url'] ?? null,
                isset($accommodation['amenities']) ? json_encode($accommodation['amenities']) : null,
                $sortOrder++,
                true
            ]);
            $count++;
        }
        
        echo "  ✓ Imported $count accommodations\n";
    }
    
    private function importPopup($popupItems) {
        $stmt = $this->db->prepare("
            INSERT INTO popup (title, description, image_url, button_text, button_url) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        $count = 0;
        foreach ($popupItems as $item) {
            $stmt->execute([
                $item['title'] ?? '',
                $item['description'] ?? null,
                $item['image_url'] ?? '',
                $item['button_text'] ?? null,
                $item['button_url'] ?? null
            ]);
            $count++;
        }
        
        echo "  ✓ Imported $count popup items\n";
    }
    
    private function importParallax($parallaxItems) {
        $stmt = $this->db->prepare("
            INSERT INTO parallax (title, description, image_url, overlay_opacity) 
            VALUES (?, ?, ?, ?)
        ");
        
        $count = 0;
        foreach ($parallaxItems as $item) {
            $stmt->execute([
                $item['title'] ?? '',
                $item['description'] ?? null,
                $item['image_url'] ?? '',
                $item['overlay_opacity'] ?? 0.5
            ]);
            $count++;
        }
        
        echo "  ✓ Imported $count parallax items\n";
    }
    
    private function importButtons($buttons) {
        $stmt = $this->db->prepare("
            INSERT INTO buttons (text, url, style, icon, target, is_active) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $count = 0;
        foreach ($buttons as $button) {
            $stmt->execute([
                $button['text'] ?? '',
                $button['url'] ?? '',
                $button['style'] ?? 'primary',
                $button['icon'] ?? null,
                $button['target'] ?? '_self',
                true
            ]);
            $count++;
        }
        
        echo "  ✓ Imported $count buttons\n";
    }
    
    private function importPages($pages) {
        $stmt = $this->db->prepare("
            INSERT INTO pages (page_name, title, description, meta_description, keywords, is_active) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $count = 0;
        foreach ($pages as $pageName => $page) {
            $stmt->execute([
                $pageName,
                $page['title'] ?? '',
                $page['description'] ?? null,
                $page['meta_description'] ?? null,
                isset($page['keywords']) ? json_encode($page['keywords']) : null,
                true
            ]);
            $count++;
        }
        
        echo "  ✓ Imported $count pages\n";
    }
    
    public function createBackup() {
        $backupFile = __DIR__ . '/../content.json.backup.' . date('Y-m-d_H-i-s');
        $originalFile = __DIR__ . '/../content.json';
        
        if (file_exists($originalFile)) {
            copy($originalFile, $backupFile);
            echo "✓ Created backup: " . basename($backupFile) . "\n";
        }
    }
}

// Run migration if called directly
if (php_sapi_name() === 'cli') {
    try {
        $migration = new DatabaseMigration();
        $migration->createBackup();
        $migration->runMigration();
        echo "\n🎉 Migration completed successfully! Your website is now using the database.\n";
    } catch (Exception $e) {
        echo "\n❌ Migration failed: " . $e->getMessage() . "\n";
        exit(1);
    }
}
?>