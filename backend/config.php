<?php
// config.php

// database connection parameters
$host = 'localhost'; // database server hostname
$db = 'story_db'; // name of the database
$user = 'root'; // database username DO NOT USE IN PRODUCTION
$pass = 'Pass123!'; // database password DO NOT USE IN PRODUCTION

try {
    // create a new PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);

    // set PDO error mode to exception for robust error handling
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // terminate the script and display an error message if the connection fails
    die("Connection failed: " . $e->getMessage());
}
