<?php
/*
 * User Registration Handler
 * This script processes the account creation form, validating input and storing user data in the database.
 * 
 * Features:
 * - Validates if the provided username already exists in the database.
 * - Hashes passwords for secure storage.
 * - Stores user data, including agreements (EULA and optional data collection), in the database.
 * - Provides feedback via session messages and redirects to appropriate pages (e.g., login, account creation).
 * 
 * Workflow:
 * 1. Enable error reporting for debugging during development.
 * 2. Start the session to handle user feedback (error/success messages).
 * 3. Validate the submitted username to prevent duplicates.
 * 4. Hash the user’s password and insert the new account into the database.
 * 5. Redirect to the login page on success or back to the account creation page on failure.
 * 
 * Note: Requires `if_config.php` for database connection setup.
 */

// enable error reporting for debugging during development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// start the session to manage error/success messages
session_start();

// include the database configuration for database connection
require '/var/www/php/if_config.php';

// handle form submission for account creation
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // retrieve form inputs
    $username = $_POST['username']; // username entered by the user
    $password = $_POST['password']; // password entered by the user
    $eula_agreement = isset($_POST['eula_agreement']) ? 1 : 0; // checkbox for EULA agreement
    $data_collection_agreement = isset($_POST['data_collection_agreement']) ? 1 : 0; // optional data collection agreement

    // check if the username already exists in the database
    $stmt = $pdo->prepare("SELECT * FROM Users WHERE Name = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // if the username exists, set an error message and redirect back to the account creation page
        $_SESSION['register_error'] = "Username already exists. Please choose another one.";
        header("Location: ../create_account.php");
        exit();
    } else {
        // hash the password for secure storage
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // insert the new user into the database
        $stmt = $pdo->prepare("
            INSERT INTO Users (Name, Password, EULA_Agreement, Data_Collection_Agreement) 
            VALUES (:username, :password, :eula_agreement, :data_collection_agreement)
        ");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':eula_agreement', $eula_agreement);
        $stmt->bindParam(':data_collection_agreement', $data_collection_agreement);
        $stmt->execute();

        // set a success message and redirect to the login page
        $_SESSION['register_success'] = "Account created successfully. You can now log in.";
        header("Location: ../login.php");
        exit();
    }
}
