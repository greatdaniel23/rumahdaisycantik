<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup - Rumah Daisy Cantik</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-8 max-w-4xl">
        <h1 class="text-3xl font-bold mb-6">Database Setup</h1>
        
        <?php
        // Only run if form is submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['setup_database'])) {
            echo '<div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-md">';
            echo '<h2 class="text-xl font-semibold text-blue-800 mb-2">Setup Progress</h2>';
            echo '<div id="progress-log">';
            
            try {
                // Include the migration script
                require_once __DIR__ . '/api/migrate.php';
                
                echo '<p class="text-green-600">✓ Migration script loaded successfully</p>';
                
                // Create migration instance and run
                $migration = new DatabaseMigration();
                
                echo '<p class="text-blue-600">📄 Creating backup of content.json...</p>';
                $migration->createBackup();
                echo '<p class="text-green-600">✓ Backup created successfully</p>';
                
                echo '<p class="text-blue-600">🗄️ Setting up database tables...</p>';
                $migration->runMigration();
                echo '<p class="text-green-600">✓ Database setup completed successfully!</p>';
                
                echo '<div class="mt-4 p-3 bg-green-100 border border-green-300 rounded">';
                echo '<h3 class="font-semibold text-green-800">🎉 Setup Complete!</h3>';
                echo '<p class="text-green-700 mt-1">Your website is now using the MySQL database for content management.</p>';
                echo '<div class="mt-3">';
                echo '<a href="admin.html" class="inline-block bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Go to Admin Panel</a>';
                echo '<a href="index.html" class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 ml-2">View Website</a>';
                echo '</div>';
                echo '</div>';
                
            } catch (Exception $e) {
                echo '<p class="text-red-600">❌ Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
                echo '<div class="mt-4 p-3 bg-red-100 border border-red-300 rounded">';
                echo '<h3 class="font-semibold text-red-800">Setup Failed</h3>';
                echo '<p class="text-red-700 mt-1">Please check your database credentials and try again.</p>';
                echo '</div>';
            }
            
            echo '</div>';
            echo '</div>';
            
        } else {
            // Show setup form
            ?>
            
            <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                <h2 class="text-xl font-semibold mb-4">Database Migration</h2>
                <p class="text-gray-600 mb-4">
                    This will transform your website from static JSON files to a dynamic MySQL database system.
                </p>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-6">
                    <h3 class="text-lg font-semibold text-yellow-800 mb-2">What this enhanced setup will do:</h3>
                    <ul class="list-disc list-inside text-yellow-700 space-y-1">
                        <li>Create enhanced database tables including dedicated room management system</li>
                        <li>Set up room types, individual rooms, and room amenities management</li>
                        <li>Create tables for images, accommodations, buttons, popup, parallax, and pages</li>
                        <li>Import all existing data from content.json into the database</li>
                        <li>Create sample room types and rooms with amenities</li>
                        <li>Create a backup of your current content.json file</li>
                        <li>Enable real-time content management through the admin panel</li>
                        <li>Support for room image galleries and detailed room information</li>
                    </ul>
                </div>
                
                <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-6">
                    <h3 class="text-lg font-semibold text-blue-800 mb-2">Database Credentials:</h3>
                    <ul class="list-none text-blue-700 space-y-1">
                        <li><strong>Host:</strong> localhost</li>
                        <li><strong>Database:</strong> u289291769_websiterdc</li>
                        <li><strong>Username:</strong> u289291769_websiterdc</li>
                        <li><strong>Password:</strong> Kanibal123!!!</li>
                    </ul>
                </div>
                
                <form method="POST">
                    <div class="flex items-start space-x-3 mb-6">
                        <input type="checkbox" id="confirm-setup" name="confirm_setup" required class="mt-1">
                        <label for="confirm-setup" class="text-sm text-gray-700">
                            I understand that this will modify my database and I have reviewed the setup process above.
                        </label>
                    </div>
                    
                    <button type="submit" name="setup_database" value="1" 
                            class="bg-purple-600 text-white px-6 py-3 rounded-md hover:bg-purple-700 font-semibold">
                        🚀 Start Database Setup
                    </button>
                </form>
            </div>
            
            <div class="bg-gray-50 border border-gray-200 rounded-md p-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Need Help?</h3>
                <p class="text-gray-600 text-sm">
                    If you encounter any issues during setup, check that:
                </p>
                <ul class="list-disc list-inside text-gray-600 text-sm mt-2 space-y-1">
                    <li>Your database credentials are correct</li>
                    <li>The database server is running</li>
                    <li>PHP has the necessary permissions</li>
                    <li>The content.json file exists in the root directory</li>
                </ul>
            </div>
            
            <?php
        }
        ?>
    </div>
</body>
</html>