<?php
/**
 * Script to add is_active column to users table
 * Run: php add_is_active_column.php
 */

// Database connection settings (from app/Config/Database.php)
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
    
    // Check if column already exists
    $result = $mysqli->query("SHOW COLUMNS FROM `users` LIKE 'is_active'");
    if ($result && $result->num_rows > 0) {
        echo "Column 'is_active' already exists in users table.\n";
        $mysqli->close();
        exit(0);
    }
    
    // Add the column
    $sql = "ALTER TABLE `users` 
            ADD COLUMN `is_active` TINYINT(1) NOT NULL DEFAULT 1 
            COMMENT '1 = active, 0 = deactivated' 
            AFTER `role`";
    
    if ($mysqli->query($sql)) {
        // Update existing users to be active
        $mysqli->query("UPDATE `users` SET `is_active` = 1");
        
        echo "Successfully added 'is_active' column to users table!\n";
        echo "All existing users have been set to active (is_active = 1).\n";
    } else {
        die("Error adding column: " . $mysqli->error . "\n");
    }
    
    $mysqli->close();
    echo "\nDone! You can now use the activate/deactivate feature.\n";
    
} catch (\Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}
