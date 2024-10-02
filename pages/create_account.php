<?php
session_start();  // Start session to check for error messages

// Check for any registration error or success messages
$register_error = isset($_SESSION['register_error']) ? $_SESSION['register_error'] : '';
$register_success = isset($_SESSION['register_success']) ? $_SESSION['register_success'] : '';
unset($_SESSION['register_error']);  // Clear the error after displaying
unset($_SESSION['register_success']);  // Clear success message after displaying
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

    <!-- Display registration error if it exists -->
    <?php if ($register_error): ?>
        <p style="color: red;"><?php echo htmlspecialchars($register_error); ?></p>
    <?php endif; ?>

    <!-- Display success message if it exists -->
    <?php if ($register_success): ?>
        <p style="color: green;"><?php echo htmlspecialchars($register_success); ?></p>
    <?php endif; ?>

    <!-- Create Account Form -->
    <form action="backend/create_account_handler.php" method="POST">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>

        <label for="eula_agreement">
            <input type="checkbox" id="eula_agreement" name="eula_agreement" required>
            I agree to the EULA
        </label><br><br>

        <label for="data_collection_agreement">
            <input type="checkbox" id="data_collection_agreement" name="data_collection_agreement">
            I agree to data collection
        </label><br><br>

        <button type="submit">Create Account</button>
    </form>

    <p>Already have an account? <a href="login.php">Log in here</a></p>

</body>

</html>