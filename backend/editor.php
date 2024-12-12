<?php
/*
 * Story Management Script
 * This script provides functionality for managing interactive stories and their components.
 * It includes user authentication, database interactions, and utility functions for CRUD operations.
 * 
 * Features:
 * - Validates user login and generates a CSRF token for secure form submissions.
 * - Fetches stories, passages, and choices associated with the logged-in user.
 * - Includes functions to create, edit, update, and delete stories, passages, and choices.
 * - Supports transactional deletion of a story and all related data for database consistency.
 * 
 * Functions:
 * - createStory: Adds a new story to the database.
 * - editStory: Updates the name of an existing story.
 * - getStoryById: Retrieves details of a specific story by ID.
 * - getPassagesByStoryId: Fetches all passages belonging to a story.
 * - addPassage: Adds a new passage to a story.
 * - addChoice: Links a choice to a passage with optional redirection to another passage.
 * - deleteChoice: Removes a choice by its ID.
 * - getUserStories: Retrieves all stories created by a specific user.
 * - getOutgoingChoices: Fetches choices originating from a passage.
 * - getIncomingChoices: Fetches choices leading to a specific passage.
 * - getPassageById: Retrieves details of a specific passage by ID.
 * - updatePassage: Updates the text of a passage.
 * - deleteStory: Deletes a story and all its associated passages and choices using transactions.
 * 
 * Note: Requires the `if_config.php` file for database connection and user session data for authentication.
 */

// starts a session to manage user authentication and other data
session_start();

// redirects to login page if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: backend/login.php");
    exit();
}

// includes database configuration for PDO connection
require '/var/www/php/if_config.php';

// generates a CSRF token for form submissions if not already set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// fetches existing stories for the logged-in user from the database
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

// creates a new story for a user
function createStory($pdo, $story_name, $user_id)
{
    $sql = "INSERT INTO Stories (Story_Name, UserID) VALUES (:story_name, :user_id)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':story_name' => $story_name,
        ':user_id' => $user_id
    ]);
}

// updates the name of an existing story
function editStory($pdo, $story_id, $story_name)
{
    $sql = "UPDATE Stories SET Story_Name = :story_name WHERE ID = :story_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':story_name' => $story_name,
        ':story_id' => $story_id
    ]);
}

// retrieves story details by its ID
function getStoryById($pdo, $story_id)
{
    $sql = "SELECT ID, Story_Name FROM Stories WHERE ID = :story_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':story_id' => $story_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// fetches all passages associated with a specific story
function getPassagesByStoryId($pdo, $story_id)
{
    $sql = "SELECT PassageID, Text FROM Passages WHERE StoryID = :story_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':story_id' => $story_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// adds a new passage to a story
function addPassage($pdo, $story_id, $passage_text)
{
    $sql = "INSERT INTO Passages (StoryID, Text) VALUES (:story_id, :passage_text)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':story_id' => $story_id,
        ':passage_text' => $passage_text
    ]);
}

// creates a choice linked to a passage
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

// deletes a specific choice by its ID
function deleteChoice($pdo, $choice_id)
{
    $sql = "DELETE FROM Choices WHERE ChoiceID = :choice_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':choice_id' => $choice_id]);
}

// retrieves all stories created by a specific user
function getUserStories($pdo, $user_id)
{
    $sql = "SELECT ID, Story_Name FROM Stories WHERE UserID = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':user_id' => $user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// fetches outgoing choices from a specific passage
function getOutgoingChoices($pdo, $passage_id)
{
    $sql = "SELECT ChoiceID, ChoiceText, NextPassageID FROM Choices WHERE PassageID = :passage_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':passage_id' => $passage_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// fetches incoming choices leading to a specific passage
function getIncomingChoices($pdo, $passage_id)
{
    $sql = "SELECT c.ChoiceID, c.ChoiceText, c.PassageID as FromPassageID
            FROM Choices c
            WHERE c.NextPassageID = :passage_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':passage_id' => $passage_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// retrieves a passage by its ID
function getPassageById($pdo, $passage_id)
{
    $sql = "SELECT PassageID, StoryID, Text FROM Passages WHERE PassageID = :passage_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':passage_id' => $passage_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// updates the text of a specific passage
function updatePassage($pdo, $passage_id, $passage_text)
{
    $sql = "UPDATE Passages SET Text = :passage_text WHERE PassageID = :passage_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':passage_text' => $passage_text,
        ':passage_id' => $passage_id
    ]);
}

// deletes a story and all associated passages and choices
function deleteStory($pdo, $story_id)
{
    // starts a transaction to ensure all deletions occur together
    $pdo->beginTransaction();
    try {
        // deletes choices linked to passages of the story
        $sql = "DELETE c FROM Choices c
                JOIN Passages p ON c.PassageID = p.PassageID
                WHERE p.StoryID = :story_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':story_id' => $story_id]);

        // deletes passages belonging to the story
        $sql = "DELETE FROM Passages WHERE StoryID = :story_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':story_id' => $story_id]);

        // deletes the story itself
        $sql = "DELETE FROM Stories WHERE ID = :story_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':story_id' => $story_id]);

        // deletes related entries from StoriesPlayed
        $sql = "DELETE FROM StoriesPlayed WHERE StoryID = :story_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':story_id' => $story_id]);

        // commits the transaction
        $pdo->commit();
    } catch (PDOException $e) {
        // rolls back the transaction on error
        $pdo->rollBack();
        throw $e;
    }
}
