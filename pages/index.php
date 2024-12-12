<?php
/*
* Passages: Hypertext Fiction Platform
* This script generates the main interface for viewing available stories, 
* allows users to log in or out, and provides options to create or edit stories. 
* It fetches story data from the database and displays it for interaction.
*/
// includes database configuration file
include '/var/www/php/if_config.php';
// includes authentication functions
include 'backend/auth.php';

// starts the session to manage user login state
session_start();

// checks if the user is logged in
$loggedIn = isset($_SESSION['user_id']);
// retrieves the username if logged in
$username = $loggedIn ? $_SESSION['username'] : null;

try {
    // prepares and executes a query to fetch all stories from the database
    $sql = "SELECT ID, Story_Name FROM Stories";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $stories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // outputs an error message and exits if the query fails
    echo "Error fetching stories: " . htmlspecialchars($e->getMessage());
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passages: Create and Explore <s>Interactive</s> Hypertext Fiction</title>
</head>

<body>

    <h1>Passages: Create and Explore <s>Interactive</s> Hypertext Fiction</h1>

    <?php if ($loggedIn): ?>
        <!-- displays the logged-in username -->
        <p>Logged in as: <?php echo htmlspecialchars($username); ?></p>
        <!-- link to the story creation/editor page -->
        <p><a href="story_editor.php">Create/Edit Story</a></p>
        <!-- link to log out -->
        <p><a href="backend/logout.php">Logout</a></p>
    <?php else: ?>
        <!-- link to log in or create an account -->
        <p><a href="login.php">Login or Join</a></p>
    <?php endif; ?>

    <!-- displays a list of available stories -->
    <h2>Available Stories</h2>
    <?php if (!empty($stories)): ?>
        <ul>
            <?php foreach ($stories as $story): ?>
                <li>
                    <!-- displays the story name with a link to play the story -->
                    <?php echo htmlspecialchars($story['Story_Name']); ?>
                    - <a href="story_player.php?story_id=<?php echo $story['ID']; ?>">Play Story</a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <!-- message when no stories are available -->
        <p>No stories available at the moment.</p>
    <?php endif; ?>

</body>

</html>