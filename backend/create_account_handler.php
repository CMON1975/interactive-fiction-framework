<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start the session
session_start();

// Include the database configuration
require '/var/www/php/if_config.php';  // Include the database connection

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $eula_agreement = isset($_POST['eula_agreement']) ? 1 : 0;
    $data_collection_agreement = isset($_POST['data_collection_agreement']) ? 1 : 0;

    // Check if the username already exists
    $stmt = $pdo->prepare("SELECT * FROM Users WHERE Name = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // If username exists, set error and redirect
        $_SESSION['register_error'] = "Username already exists. Please choose another one.";
        header("Location: ../create_account.php");
        exit();
    } else {
        // Hash the password for security
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert the new user into the database
        $stmt = $pdo->prepare("INSERT INTO Users (Name, Password, EULA_Agreement, Data_Collection_Agreement) VALUES (:username, :password, :eula_agreement, :data_collection_agreement)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':eula_agreement', $eula_agreement);
        $stmt->bindParam(':data_collection_agreement', $data_collection_agreement);
        $stmt->execute();

        // Set session and redirect to login
        $_SESSION['register_success'] = "Account created successfully. You can now log in.";
        header("Location: ../login.php");
        exit();
    }
}
