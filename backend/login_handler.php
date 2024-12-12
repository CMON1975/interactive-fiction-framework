<?php
/*
 * User Login Handler
 * This script processes the login form, validates user credentials, and establishes a session for authenticated users.
 * 
 * Features:
 * - Retrieves user details from the database using the provided username.
 * - Verifies the entered password against the stored hashed password.
 * - Sets session variables for authenticated users (user ID and username).
 * - Redirects to the homepage upon successful login or displays an error message on failure.
 * 
 * Workflow:
 * 1. Enable error reporting for debugging during development.
 * 2. Start the session to manage authentication state.
 * 3. Validate the submitted username and password against the database.
 * 4. Establish a session for authenticated users and redirect to the homepage.
 * 5. Display an error message for invalid login attempts.
 * 
 * Note: Requires `if_config.php` for database connection setup and uses password hashing for security.
 */

// enable error reporting for debugging during development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// start a session to manage user authentication and state
session_start();

// include database configuration for establishing a PDO connection
require '/var/www/php/if_config.php';  // ensure this file contains a valid PDO connection

// handle the login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // retrieve form inputs for username and password
    $username = $_POST['username']; // entered username
    $password = $_POST['password']; // entered password

    // query the database to fetch user details based on the entered username
    $stmt = $pdo->prepare("SELECT * FROM Users WHERE Name = :username");
    $stmt->bindParam(':username', $username); // bind username parameter to prevent SQL injection
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC); // fetch the user's record as an associative array

    // verify the provided password with the hashed password stored in the database
    if ($user && password_verify($password, $user['Password'])) {
        // if login is successful, set session variables for user ID and username
        $_SESSION['user_id'] = $user['ID']; // store the user's ID in session
        $_SESSION['username'] = $user['Name']; // store the username in session

        // redirect the user to the homepage after successful login
        header("Location: ../index.php");
        exit();
    } else {
        // if login fails, display an error message
        echo "Invalid username or password.";
    }
}
