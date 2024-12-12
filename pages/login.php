<?php
/*
 * Login Page
 * This script provides the user interface for logging into the application.
 * Features:
 * - A form for users to input their username and password.
 * - The form submits data to `backend/login_handler.php` for authentication.
 * - Includes a link to the account creation page for new users.
 * 
 * Note: Validation for input fields is handled via the `required` attribute in HTML.
 */
// includes the login handler script for processing login requests
include 'backend/login_handler.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>

<body>

    <h2>Login</h2>

    <!-- login form to authenticate the user -->
    <form action="backend/login_handler.php" method="POST">
        <!-- input field for username -->
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>

        <!-- input field for password -->
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>

        <!-- submit button to send login request -->
        <button type="submit">Login</button>
    </form>

    <!-- link to the account creation page -->
    <p>Don't have an account? <a href="create_account.php">Create one here</a></p>

</body>

</html>