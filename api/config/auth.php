<?php
require_once __DIR__ . '/database.php';

class Auth {
    private $db;
    private $sessionTimeout = 1800; // 30 minutes

    public function __construct() {
        $this->db = DatabaseConfig::getInstance()->getConnection();
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function login($username, $password) {
        $stmt = $this->db->prepare("SELECT * FROM admin_users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $this->setSession($user);
            $this->updateLastLogin($user['id']);
            return true;
        }
        return false;
    }

    public function logout() {
        session_unset();
        session_destroy();
    }

    public function checkSession() {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }

        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $this->sessionTimeout) {
            $this->logout();
            return false;
        }

        $_SESSION['last_activity'] = time();
        return true;
    }

    public function getSessionStatus() {
        if ($this->checkSession()) {
            return [
                'loggedIn' => true,
                'username' => $_SESSION['username']
            ];
        }
        return ['loggedIn' => false];
    }

    private function setSession($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['last_activity'] = time();
    }

    private function updateLastLogin($userId) {
        $stmt = $this->db->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
        $stmt->execute([$userId]);
    }
}
