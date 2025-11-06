<?php
require_once __DIR__ . '/config/database.php';

function setupAdminUser() {
    $db = DatabaseConfig::getInstance()->getConnection();
    $username = 'admin';
    $password = 'password';
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Check if the user already exists
        $stmt = $db->prepare("SELECT * FROM admin_users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            echo "Admin user already exists.\n";
            return;
        }

        // Insert the new admin user
        $stmt = $db->prepare("INSERT INTO admin_users (username, password_hash) VALUES (?, ?)");
        $stmt->execute([$username, $passwordHash]);
        echo "Admin user created successfully.\n";
        echo "Username: $username\n";
        echo "Password: $password\n";
        echo "\nIMPORTANT: Please change this password immediately after your first login.\n";
    } catch (Exception $e) {
        echo "Error creating admin user: " . $e->getMessage() . "\n";
    }
}

setupAdminUser();
