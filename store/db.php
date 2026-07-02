<?php
// db.php - database connection
// Edit these values to match your MySQL setup

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';        // <-- set your MySQL password
$db_name = 'medease'; // <-- database name we'll create below

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($mysqli->connect_errno) {
    // On production, log this and show user-friendly message.
    die("Database connection failed: " . $mysqli->connect_error);
}
$mysqli->set_charset("utf8mb4");
