<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: backend/login.php");
    exit();
}

// Include database configuration
require 'backend/config.php';

// Fetch existing stories for the logged-in user
$userID = $_SESSION['user_id'];
$stories = $pdo->prepare("
    SELECT s.ID, s.Story_Name 
    FROM Stories s
    JOIN StoriesPlayed sp ON s.ID = sp.StoryID
    WHERE sp.UserID = :userID
");
$stories->bindParam(':userID', $userID);
$stories->execute();
$userStories = $stories->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission for new story creation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['story_name'])) {
    $newStoryName = $_POST['story_name'];

    // Insert new story into the database
    $stmt = $pdo->prepare("INSERT INTO Stories (Story_Name) VALUES (:storyName)");
    $stmt->bindParam(':storyName', $newStoryName);
    $stmt->execute();

    // Get the new story ID
    $storyID = $pdo->lastInsertId();

    // Insert entry into StoriesPlayed for the user to track their progress
    $stmt = $pdo->prepare("INSERT INTO StoriesPlayed (UserID, StoryID) VALUES (:userID, :storyID)");
    $stmt->bindParam(':userID', $userID);
    $stmt->bindParam(':storyID', $storyID);
    $stmt->execute();

    // Redirect to the same page to reflect the new story
    header("Location: create_story.php");
    exit();
}
