<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: backend/login.php");
    exit();
}

// Include database configuration
require '/var/www/php/if_config.php';

// Generate a CSRF token if one does not exist
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Fetch existing stories for the logged-in user
$userID = $_SESSION['user_id'];
$stories = $pdo->prepare("
    SELECT s.ID, s.Story_Name 
    FROM Stories s
    JOIN StoriesPlayed sp ON s.ID = sp.StoryID
    WHERE sp.UserID = :userID
");
$stories->bindParam(':userID', $userID, PDO::PARAM_INT);
$stories->execute();
$userStories = $stories->fetchAll(PDO::FETCH_ASSOC);

// Function to create a new story
function createStory($pdo, $story_name, $user_id)
{
    $sql = "INSERT INTO Stories (Story_Name, UserID) VALUES (:story_name, :user_id)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':story_name' => $story_name,
        ':user_id' => $user_id
    ]);
}

// Function to edit an existing story's name
function editStory($pdo, $story_id, $story_name)
{
    $sql = "UPDATE Stories SET Story_Name = :story_name WHERE ID = :story_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':story_name' => $story_name,
        ':story_id' => $story_id
    ]);
}

// Function to retrieve a story by its ID
function getStoryById($pdo, $story_id)
{
    $sql = "SELECT ID, Story_Name FROM Stories WHERE ID = :story_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':story_id' => $story_id
    ]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Function to get passages for a story
function getPassagesByStoryId($pdo, $story_id)
{
    $sql = "SELECT PassageID, Text FROM Passages WHERE StoryID = :story_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':story_id' => $story_id
    ]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to add a passage to a story
function addPassage($pdo, $story_id, $passage_text)
{
    $sql = "INSERT INTO Passages (StoryID, Text) VALUES (:story_id, :passage_text)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':story_id' => $story_id,
        ':passage_text' => $passage_text
    ]);
}

// Function to add choices linked to a passage
function addChoice($pdo, $passage_id, $choice_text, $next_passage_id = null)
{
    $sql = "INSERT INTO Choices (PassageID, ChoiceText, NextPassageID) VALUES (:passage_id, :choice_text, :next_passage_id)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':passage_id' => $passage_id,
        ':choice_text' => $choice_text,
        ':next_passage_id' => $next_passage_id
    ]);
}

// Function to delete a choice
function deleteChoice($pdo, $choice_id)
{
    $sql = "DELETE FROM Choices WHERE ChoiceID = :choice_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':choice_id' => $choice_id
    ]);
}

// Function to retrieve user's stories
function getUserStories($pdo, $user_id)
{
    $sql = "SELECT ID, Story_Name FROM Stories WHERE UserID = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':user_id' => $user_id
    ]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to get outgoing choices from a passage
function getOutgoingChoices($pdo, $passage_id)
{
    $sql = "SELECT ChoiceID, ChoiceText, NextPassageID FROM Choices WHERE PassageID = :passage_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':passage_id' => $passage_id
    ]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to get incoming choices to a passage
function getIncomingChoices($pdo, $passage_id)
{
    $sql = "SELECT c.ChoiceID, c.ChoiceText, c.PassageID as FromPassageID
            FROM Choices c
            WHERE c.NextPassageID = :passage_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':passage_id' => $passage_id
    ]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to get a passage by its ID
function getPassageById($pdo, $passage_id)
{
    $sql = "SELECT PassageID, StoryID, Text FROM Passages WHERE PassageID = :passage_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':passage_id' => $passage_id
    ]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Function to update passage text
function updatePassage($pdo, $passage_id, $passage_text)
{
    $sql = "UPDATE Passages SET Text = :passage_text WHERE PassageID = :passage_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':passage_text' => $passage_text,
        ':passage_id' => $passage_id
    ]);
}

// Function to delete a story and all related passages and choices
function deleteStory($pdo, $story_id)
{
    // Begin a transaction
    $pdo->beginTransaction();
    try {
        // Delete choices linked to passages of the story
        $sql = "DELETE c FROM Choices c
                JOIN Passages p ON c.PassageID = p.PassageID
                WHERE p.StoryID = :story_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':story_id' => $story_id]);

        // Delete passages of the story
        $sql = "DELETE FROM Passages WHERE StoryID = :story_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':story_id' => $story_id]);

        // Delete the story itself
        $sql = "DELETE FROM Stories WHERE ID = :story_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':story_id' => $story_id]);

        // Delete entries from StoriesPlayed (if any)
        $sql = "DELETE FROM StoriesPlayed WHERE StoryID = :story_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':story_id' => $story_id]);

        // Commit the transaction
        $pdo->commit();
    } catch (PDOException $e) {
        // Roll back the transaction if something failed
        $pdo->rollBack();
        throw $e; // Re-throw the exception for handling in calling code
    }
}
