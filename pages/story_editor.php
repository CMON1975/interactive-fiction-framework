<?php
include 'backend/editor.php'
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create or Select Story</title>
</head>

<body>
    <h1>Create or Select a Story</h1>

    <!-- Form to create a new story -->
    <form method="POST" action="create_story.php">
        <label for="story_name">New Story Title:</label>
        <input type="text" id="story_name" name="story_name" required>
        <button type="submit">Create Story</button>
    </form>

    <h2>Your Existing Stories</h2>
    <ul>
        <?php if ($userStories): ?>
            <?php foreach ($userStories as $story): ?>
                <li>
                    <a href="edit_story.php?story_id=<?php echo $story['ID']; ?>">
                        <?php echo htmlspecialchars($story['Story_Name']); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li>No stories created yet.</li>
        <?php endif; ?>
    </ul>

    <p><a href="index.php">Return to Front Page</a></p>
</body>

</html>