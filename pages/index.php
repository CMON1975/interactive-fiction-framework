<?php
$servername = "localhost";
$username = "cmon1975";
$password = "Password1!";
$dbname = "interactive_fiction_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";
