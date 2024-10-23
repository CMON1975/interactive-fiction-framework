<?php
include '/var/www/php/if_config.php'; // Include your database configuration
include 'backend/auth.php';   // Include your authentication functions

// Check if user is logged in
session_start();
$loggedIn = isset($_SESSION['user_id']);
$username = $loggedIn ? $_SESSION['username'] : null;

// Fetch all stories from the database
try {
    $sql = "SELECT ID, Story_Name FROM Stories";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $stories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching stories: " . htmlspecialchars($e->getMessage());
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passages: Create and Explore Interactive Fiction</title>
</head>

<body>

    <h1>Passages: Create and Explore Interactive Fiction</h1>

    <?php if ($loggedIn): ?>
        <!-- Display the logged-in username -->
        <p>Logged in as: <?php echo htmlspecialchars($username); ?></p>
        <!-- Display Story Creator link if the user is logged in -->
        <p><a href="story_editor.php">Create/Edit Story</a></p>
        <!-- Display the Logout link if the user is logged in -->
        <p><a href="backend/logout.php">Logout</a></p>
    <?php else: ?>
        <!-- Display the Login/Join link if the user is not logged in -->
        <p><a href="login.php">Login or Join</a></p>
    <?php endif; ?>

    <!-- Display list of available stories -->
    <h2>Available Stories</h2>
    <?php if (!empty($stories)): ?>
        <ul>
            <?php foreach ($stories as $story): ?>
                <li>
                    <?php echo htmlspecialchars($story['Story_Name']); ?>
                    - <a href="story_player.php?story_id=<?php echo $story['ID']; ?>">Play Story</a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No stories available at the moment.</p>
    <?php endif; ?>

</body>

</html>