<?php
/**
 * Database Configuration for Rumah Daisy Cantik
 */

class DatabaseConfig {
    const DB_HOST = 'localhost';
    const DB_NAME = 'u289291769_websiterdc';
    const DB_USER = 'u289291769_websiterdc';
    const DB_PASS = 'Kanibal123!!!';
    const DB_CHARSET = 'utf8mb4';
    
    private static $instance = null;
    private $pdo;
    
    private function __construct() {
        try {
            $dsn = "mysql:host=" . self::DB_HOST . ";dbname=" . self::DB_NAME . ";charset=" . self::DB_CHARSET;
            $this->pdo = new PDO($dsn, self::DB_USER, self::DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . self::DB_CHARSET . " COLLATE utf8mb4_unicode_ci"
            ]);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            throw new Exception("Database connection failed");
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->pdo;
    }
    
    public function testConnection() {
        try {
            $stmt = $this->pdo->query("SELECT 1");
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}

/**
 * Activity Logger for tracking admin changes
 */
class ActivityLogger {
    private $db;
    
    public function __construct() {
        $this->db = DatabaseConfig::getInstance()->getConnection();
    }
    
    public function log($tableName, $recordId, $action, $oldValues = null, $newValues = null, $adminUserId = null) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO activity_log 
                (table_name, record_id, action, old_values, new_values, admin_user_id, ip_address, user_agent) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $tableName,
                $recordId,
                $action,
                $oldValues ? json_encode($oldValues) : null,
                $newValues ? json_encode($newValues) : null,
                $adminUserId,
                $_SERVER['REMOTE_ADDR'] ?? null,
                $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);
        } catch (Exception $e) {
            error_log("Activity logging failed: " . $e->getMessage());
        }
    }
}

/**
 * Base Model Class
 */
abstract class BaseModel {
    protected $db;
    protected $logger;
    protected $tableName;
    
    public function __construct() {
        $this->db = DatabaseConfig::getInstance()->getConnection();
        $this->logger = new ActivityLogger();
    }
    
    public function findAll($active = true) {
        $sql = "SELECT * FROM {$this->tableName}";
        if ($active && $this->hasActiveColumn()) {
            $sql .= " WHERE is_active = 1";
        }
        $sql .= " ORDER BY " . $this->getDefaultOrderBy();
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->tableName} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    protected function hasActiveColumn() {
        // Override in child classes if table doesn't have is_active column
        return in_array($this->tableName, ['accommodations', 'buttons', 'pages', 'admin_users']);
    }
    
    protected function getDefaultOrderBy() {
        // Override in child classes for custom ordering
        switch ($this->tableName) {
            case 'accommodations':
                return 'sort_order ASC, created_at ASC';
            case 'activity_log':
                return 'created_at DESC';
            default:
                return 'created_at ASC';
        }
    }
}
?>