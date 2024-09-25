<?php
// config.php

$host = 'localhost';
$db = 'story_db';
$user = 'root';
$pass = 'Pass123!';

try {
    // Create a new PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    // Set PDO error mode to exception for better error handling
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Display an error message and stop the script if the connection fails
    die("Connection failed: " . $e->getMessage());
}
