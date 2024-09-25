<?php
// Start the session
session_start();

// Include the database configuration
require 'config.php';  // Include the database connection

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
        echo "Username already exists. Please choose another one.";
    } else {
        // Hash the password for security
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert the new user into the database
        $stmt = $pdo->prepare("INSERT INTO Users (Name, Password, EULA_Agreement, Data_Collection_Agreement) VALUES (:username, :password, :eula_agreement, :data_collection_agreement)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':eula_agreement', $eula_agreement);
        $stmt->bindParam(':data_collection_agreement', $data_collection_agreement);

        if ($stmt->execute()) {
            // Log the user in by setting the session
            $_SESSION['user_id'] = $pdo->lastInsertId();
            header("Location: ../index.php");
            exit();
        } else {
            echo "Error creating account.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
    <script>
        function validateForm() {
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const eula = document.getElementById('eula_agreement').checked;

            // Enable submit button only if name, password, and EULA are provided
            document.getElementById('submit_button').disabled = !(username && password && eula);
        }
    </script>
</head>

<body>

    <h2>Create Account</h2>

    <form action="create_account.php" method="POST">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required oninput="validateForm()"><br><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required oninput="validateForm()"><br><br>

        <!-- EULA Agreement (Mandatory) -->
        <input type="checkbox" id="eula_agreement" name="eula_agreement" required onchange="validateForm()">
        <label for="eula_agreement">I agree to the EULA (mandatory)</label><br><br>

        <!-- Data Collection Agreement (Optional) -->
        <input type="checkbox" id="data_collection_agreement" name="data_collection_agreement">
        <label for="data_collection_agreement">I agree to data collection (optional)</label><br><br>

        <button type="submit" id="submit_button" disabled>Create Account</button>
    </form>

</body>

</html>