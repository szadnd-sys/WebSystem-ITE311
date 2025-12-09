<?php
/**
 * Script to create notifications table
 * Run: php create_notifications_table.php
 */

// Database connection settings
$hostname = 'localhost';
$username = 'root';
$password = '';
$database = 'lms_emben';

try {
    $mysqli = new mysqli($hostname, $username, $password, $database);
    
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error . "\n");
    }
    
    echo "Connected to database: $database\n";
    
    // Check if table already exists
    $result = $mysqli->query("SHOW TABLES LIKE 'notifications'");
    if ($result && $result->num_rows > 0) {
        echo "Table 'notifications' already exists.\n";
        $mysqli->close();
        exit(0);
    }
    
    // Create the notifications table
    $sql = "CREATE TABLE `notifications` (
        `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `user_id` INT(11) UNSIGNED NOT NULL,
        `title` VARCHAR(255) NOT NULL,
        `message` TEXT NULL,
        `link_url` VARCHAR(255) NULL,
        `is_read` TINYINT(1) DEFAULT 0,
        `created_at` DATETIME NULL,
        `updated_at` DATETIME NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `user_id` (`user_id`),
        KEY `is_read` (`is_read`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    
    if ($mysqli->query($sql)) {
        echo "Successfully created 'notifications' table!\n";
    } else {
        die("Error creating table: " . $mysqli->error . "\n");
    }
    
    $mysqli->close();
    echo "\nDone! Notifications table is ready.\n";
    
} catch (\Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}

