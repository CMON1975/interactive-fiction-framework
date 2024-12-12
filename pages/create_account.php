<?php
/*
 * Create Account Page
 * This script provides the user interface for creating a new account.
 * Features:
 * - Displays error or success messages from the session after registration attempts.
 * - Includes a form for inputting username, password, and optional agreements.
 * - Requires users to agree to the EULA before submitting the form.
 * - Sends form data to `backend/create_account_handler.php` for processing.
 * - Provides a link to the login page for existing users.
 * 
 * Note: Session variables for error and success messages are cleared after being displayed.
 */
session_start();  // start session to check for error messages

// check for any registration error or success messages in the session
$register_error = isset($_SESSION['register_error']) ? $_SESSION['register_error'] : '';
$register_success = isset($_SESSION['register_success']) ? $_SESSION['register_success'] : '';
unset($_SESSION['register_error']);  // clear the error message after displaying it
unset($_SESSION['register_success']);  // clear the success message after displaying it
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
</head>

<body>

    <h2>Create Account</h2>

    <!-- display registration error if it exists -->
    <?php if ($register_error): ?>
        <p style="color: red;"><?php echo htmlspecialchars($register_error); ?></p>
    <?php endif; ?>

    <!-- display success message if it exists -->
    <?php if ($register_success): ?>
        <p style="color: green;"><?php echo htmlspecialchars($register_success); ?></p>
    <?php endif; ?>

    <!-- create account form for user input -->
    <form action="backend/create_account_handler.php" method="POST">
        <!-- input field for username -->
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>

        <!-- input field for password -->
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>

        <!-- checkbox for agreeing to the EULA (required) -->
        <label for="eula_agreement">
            <input type="checkbox" id="eula_agreement" name="eula_agreement" required>
            I agree to the EULA
        </label><br><br>

        <!-- optional checkbox for data collection agreement -->
        <label for="data_collection_agreement">
            <input type="checkbox" id="data_collection_agreement" name="data_collection_agreement">
            I agree to data collection
        </label><br><br>

        <!-- submit button for the form -->
        <button type="submit">Create Account</button>
    </form>

    <!-- link to login page for users who already have an account -->
    <p>Already have an account? <a href="login.php">Log in here</a></p>

</body>

</html>