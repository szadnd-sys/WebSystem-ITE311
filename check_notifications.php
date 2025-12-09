<?php
$hostname = 'localhost';
$username = 'root';
$password = '';
$database = 'lms_emben';

$mysqli = new mysqli($hostname, $username, $password, $database);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error . "\n");
}

$result = $mysqli->query('SELECT COUNT(*) as count FROM notifications');
$row = $result->fetch_assoc();
echo "Total notifications: " . $row['count'] . "\n\n";

$result2 = $mysqli->query('SELECT id, user_id, title, message, is_read, created_at FROM notifications ORDER BY created_at DESC LIMIT 10');
echo "Recent notifications:\n";
echo str_repeat("-", 80) . "\n";
while($row2 = $result2->fetch_assoc()) {
    echo "ID: {$row2['id']}, User: {$row2['user_id']}, Title: {$row2['title']}, Read: {$row2['is_read']}, Created: {$row2['created_at']}\n";
    if (!empty($row2['message'])) {
        echo "  Message: {$row2['message']}\n";
    }
    echo "\n";
}

$mysqli->close();

