<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start the session
session_start();

// Include the database configuration
require '/var/www/php/if_config.php';  // Ensure this file contains a valid PDO connection

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Retrieve the user from the database
    $stmt = $pdo->prepare("SELECT * FROM Users WHERE Name = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['Password'])) {
        // Login successful, set session
        $_SESSION['user_id'] = $user['ID'];
        $_SESSION['username'] = $user['Name'];  // Store username in session
        header("Location: ../index.php");
        exit();
    } else {
        echo "Invalid username or password.";
    }
}
