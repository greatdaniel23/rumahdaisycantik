<?php
/**
 * API Test Script
 * Test this by navigating to: https://yourdomain.com/test-api.php
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>API Test Results</h1>";
echo "<hr>";

// Test 1: Check if API index.php exists
echo "<h2>1. API Files Check</h2>";
if (file_exists('api/index.php')) {
    echo "✅ api/index.php exists<br>";
} else {
    echo "❌ api/index.php NOT FOUND<br>";
}

if (file_exists('api/config/database.php')) {
    echo "✅ api/config/database.php exists<br>";
} else {
    echo "❌ api/config/database.php NOT FOUND<br>";
}

if (file_exists('.htaccess')) {
    echo "✅ Root .htaccess exists<br>";
} else {
    echo "❌ Root .htaccess NOT FOUND<br>";
}

if (file_exists('api/.htaccess')) {
    echo "✅ api/.htaccess exists<br>";
} else {
    echo "❌ api/.htaccess NOT FOUND<br>";
}

echo "<hr>";

// Test 2: Test database connection
echo "<h2>2. Database Connection Test</h2>";
try {
    require_once 'api/config/database.php';
    $db = DatabaseConfig::getInstance();
    $conn = $db->getConnection();
    
    if ($conn) {
        echo "✅ Database connection successful<br>";
        
        // Test if tables exist
        $tables = ['images', 'accommodations', 'room_types', 'rooms'];
        foreach ($tables as $table) {
            $stmt = $conn->prepare("SELECT COUNT(*) FROM $table");
            $stmt->execute();
            $count = $stmt->fetchColumn();
            echo "✅ Table '$table' exists with $count records<br>";
        }
    } else {
        echo "❌ Database connection failed<br>";
    }
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

echo "<hr>";

// Test 3: Test API endpoints
echo "<h2>3. API Endpoints Test</h2>";

function testApiEndpoint($endpoint) {
    $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . "/api/" . $endpoint;
    
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => "Content-Type: application/json\r\n",
            'timeout' => 10
        ]
    ]);
    
    $result = @file_get_contents($url, false, $context);
    
    if ($result !== false) {
        $data = json_decode($result, true);
        if ($data) {
            echo "✅ /api/$endpoint - Response: " . substr($result, 0, 100) . "...<br>";
        } else {
            echo "⚠️ /api/$endpoint - Got response but not valid JSON: " . substr($result, 0, 100) . "...<br>";
        }
    } else {
        echo "❌ /api/$endpoint - No response or error<br>";
        if (function_exists('curl_version')) {
            // Try with CURL as backup
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $result = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($result) {
                echo "   📡 CURL attempt: HTTP $httpCode - " . substr($result, 0, 100) . "...<br>";
            }
        }
    }
}

// Test each endpoint
$endpoints = ['health', 'images', 'accommodations', 'room-types', 'rooms'];
foreach ($endpoints as $endpoint) {
    testApiEndpoint($endpoint);
}

echo "<hr>";

// Test 4: PHP Configuration
echo "<h2>4. PHP Configuration</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "PDO MySQL: " . (extension_loaded('pdo_mysql') ? "✅ Available" : "❌ Not available") . "<br>";
echo "JSON: " . (extension_loaded('json') ? "✅ Available" : "❌ Not available") . "<br>";
echo "cURL: " . (extension_loaded('curl') ? "✅ Available" : "❌ Not available") . "<br>";

echo "<hr>";

// Test 5: File Permissions
echo "<h2>5. File Permissions</h2>";
$files = ['api/index.php', 'api/config/database.php', '.htaccess', 'api/.htaccess'];
foreach ($files as $file) {
    if (file_exists($file)) {
        $perms = fileperms($file);
        $octal = substr(sprintf('%o', $perms), -4);
        echo "$file: $octal<br>";
    }
}

echo "<hr>";
echo "<p><strong>Note:</strong> If you see ❌ errors above, check the deployment guide for troubleshooting steps.</p>";
echo "<p><strong>Delete this file</strong> after testing for security.</p>";
?>