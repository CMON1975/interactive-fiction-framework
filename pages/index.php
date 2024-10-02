<?php
include 'backend/auth.php';

// Check if user is logged in
$loggedIn = isset($_SESSION['user_id']);
$username = get_logged_in_user();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page</title>
</head>

<body>

    <h1>Passages: Create and Explore Interactive Fiction</h1>

    <?php if ($loggedIn): ?>
        <!-- Display the logged-in username -->
        <p>Logged in as: <?php echo htmlspecialchars($_SESSION['username']); ?></p>
        <!-- Display Story Creator link if the user is logged in -->
        <p><a href="story_editor.php">Create/Edit Story</a></p>
        <!-- Display the Logout link if the user is logged in -->
        <p><a href="backend/logout.php">Logout</a></p>
    <?php else: ?>
        <!-- Display the Login/Join link if the user is not logged in -->
        <p><a href="login.php">Login or Join</a></p>
    <?php endif; ?>

</body>

</html>