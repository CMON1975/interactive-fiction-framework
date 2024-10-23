<?php
include '/var/www/php/if_config.php';
include 'backend/auth.php';

// Start the session
session_start();
$loggedIn = isset($_SESSION['user_id']);
$user_id = $loggedIn ? $_SESSION['user_id'] : null;

// Get the story ID from the GET parameter
$story_id = isset($_GET['story_id']) ? intval($_GET['story_id']) : null;

// Redirect to index if no story ID is provided
if (!$story_id) {
    header("Location: index.php");
    exit();
}

// Handle POST request when a choice is selected
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['choice_id'])) {
        $selected_choice_id = intval($_POST['choice_id']);
        // Get the next passage ID from the selected choice
        $sql = "SELECT NextPassageID FROM Choices WHERE ChoiceID = :choice_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':choice_id' => $selected_choice_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $next_passage_id = $result['NextPassageID'];

        // Update the user's current passage in StoriesPlayed (if logged in)
        if ($loggedIn) {
            // Check if an entry exists in StoriesPlayed
            $sql = "SELECT * FROM StoriesPlayed WHERE UserID = :user_id AND StoryID = :story_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':user_id' => $user_id, ':story_id' => $story_id]);
            $storiesPlayed = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($storiesPlayed) {
                // Update existing entry
                $sql = "UPDATE StoriesPlayed SET CurrentPassageID = :current_passage_id WHERE UserID = :user_id AND StoryID = :story_id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':current_passage_id' => $next_passage_id,
                    ':user_id' => $user_id,
                    ':story_id' => $story_id
                ]);
            } else {
                // Insert new entry
                $sql = "INSERT INTO StoriesPlayed (UserID, StoryID, CurrentPassageID) VALUES (:user_id, :story_id, :current_passage_id)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':user_id' => $user_id,
                    ':story_id' => $story_id,
                    ':current_passage_id' => $next_passage_id
                ]);
            }
        }

        // Redirect to the same page with the updated passage ID
        header("Location: story_player.php?story_id=$story_id&passage_id=$next_passage_id");
        exit();
    } elseif (isset($_POST['restart'])) {
        // Restart the story
        $first_passage_id = getFirstPassageId($pdo, $story_id);

        // Update the user's current passage in StoriesPlayed (if logged in)
        if ($loggedIn) {
            // Update existing entry or insert new one
            $sql = "INSERT INTO StoriesPlayed (UserID, StoryID, CurrentPassageID)
                    VALUES (:user_id, :story_id, :current_passage_id)
                    ON DUPLICATE KEY UPDATE CurrentPassageID = :current_passage_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':user_id' => $user_id,
                ':story_id' => $story_id,
                ':current_passage_id' => $first_passage_id
            ]);
        }

        // Redirect to the first passage
        header("Location: story_player.php?story_id=$story_id&passage_id=$first_passage_id");
        exit();
    }
}

// Get the current passage ID
if (isset($_GET['passage_id'])) {
    $current_passage_id = intval($_GET['passage_id']);
} else {
    // If the user is logged in, check if they have a saved position
    if ($loggedIn) {
        $sql = "SELECT CurrentPassageID FROM StoriesPlayed WHERE UserID = :user_id AND StoryID = :story_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':user_id' => $user_id, ':story_id' => $story_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result && $result['CurrentPassageID']) {
            $current_passage_id = $result['CurrentPassageID'];
        } else {
            // Start at the first passage
            $current_passage_id = getFirstPassageId($pdo, $story_id);
        }
    } else {
        // Start at the first passage
        $current_passage_id = getFirstPassageId($pdo, $story_id);
    }
}

// Fetch the current passage
$sql = "SELECT PassageID, Text FROM Passages WHERE PassageID = :passage_id AND StoryID = :story_id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':passage_id' => $current_passage_id, ':story_id' => $story_id]);
$current_passage = $stmt->fetch(PDO::FETCH_ASSOC);

// If passage not found, show an error
if (!$current_passage) {
    echo "Passage not found.";
    exit();
}

// Fetch choices for the current passage
$sql = "SELECT ChoiceID, ChoiceText FROM Choices WHERE PassageID = :passage_id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':passage_id' => $current_passage_id]);
$choices = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Function to get the first passage ID of a story
function getFirstPassageId($pdo, $story_id)
{
    $sql = "SELECT PassageID FROM Passages WHERE StoryID = :story_id ORDER BY PassageID ASC LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':story_id' => $story_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['PassageID'] : null;
}

// Get story name
$sql = "SELECT Story_Name FROM Stories WHERE ID = :story_id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':story_id' => $story_id]);
$story = $stmt->fetch(PDO::FETCH_ASSOC);
$story_name = $story ? $story['Story_Name'] : "Unknown Story";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($story_name); ?> - Story Player</title>
</head>

<body>

    <h1><?php echo htmlspecialchars($story_name); ?></h1>

    <p><?php echo nl2br(htmlspecialchars($current_passage['Text'])); ?></p>

    <?php if (!empty($choices)): ?>
        <form method="POST">
            <?php foreach ($choices as $choice): ?>
                <div>
                    <button type="submit" name="choice_id" value="<?php echo $choice['ChoiceID']; ?>">
                        <?php echo htmlspecialchars($choice['ChoiceText']); ?>
                    </button>
                </div>
            <?php endforeach; ?>
        </form>
    <?php else: ?>
        <p>The End.</p>
        <?php if ($loggedIn): ?>
            <form method="POST" action="story_player.php?story_id=<?php echo $story_id; ?>">
                <button type="submit" name="restart" value="1">Restart Story</button>
            </form>
        <?php else: ?>
            <p><a href="story_player.php?story_id=<?php echo $story_id; ?>">Restart Story</a></p>
        <?php endif; ?>
    <?php endif; ?>

    <p><a href="index.php">Return to Landing Page</a></p>

</body>

</html>