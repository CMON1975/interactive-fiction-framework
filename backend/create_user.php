<?php
// Create connection to MySQL
$host = 'localhost'; // Change if your database is hosted elsewhere
$db = 'story_db'; // The name of your database
$user = 'root'; // Your MySQL username
$pass = 'Pass123!'; // Your MySQL password (if there is one)

// Connect to the database
$pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);

// Check if form data has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $password = $_POST['password'];

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare the SQL statement to insert the user
    $stmt = $pdo->prepare("INSERT INTO Users (Name, Password) VALUES (:name, :password)");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':password', $hashed_password);

    // Execute the statement
    if ($stmt->execute()) {
        echo "User created successfully!";
    } else {
        echo "Error creating user.";
    }
}
