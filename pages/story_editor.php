<?php
include 'backend/editor.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if session is active and user is logged in
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    echo "User not logged in.";
    exit;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRF Protection
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('Invalid CSRF token');
    }

    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        if ($action == 'edit_existing_story') {
            $selected_story_id = intval($_POST['existing_story']);
            header("Location: story_editor.php?story_id=$selected_story_id");
            exit();
        } elseif ($action == 'create_new_story') {
            $new_story_name = $_POST['new_story_name'];
            if (empty($new_story_name)) {
                echo "Please enter a story name.";
                exit();
            }
            try {
                createStory($pdo, $new_story_name, $user_id);
                $new_story_id = $pdo->lastInsertId();
                header("Location: story_editor.php?story_id=$new_story_id");
                exit();
            } catch (PDOException $e) {
                echo "Error creating story: " . htmlspecialchars($e->getMessage());
                exit();
            }
        } elseif ($action == 'save_story') {
            // Handle updating the story name
            $story_id = intval($_POST['story_id']);
            $story_name = $_POST['story_name'];
            try {
                editStory($pdo, $story_id, $story_name);
                header("Location: story_editor.php?edit_success=true&story_id=$story_id");
                exit();
            } catch (PDOException $e) {
                echo "Error updating story: " . htmlspecialchars($e->getMessage());
                exit();
            }
        } elseif ($action == 'save_passage') {
            // Handle adding or updating a passage
            $story_id = intval($_POST['story_id']);
            $passage_id = isset($_POST['passage_id']) && !empty($_POST['passage_id']) ? intval($_POST['passage_id']) : null;
            $passage_text = $_POST['passage_text'];
            try {
                if ($passage_id) {
                    // Update existing passage
                    updatePassage($pdo, $passage_id, $passage_text);
                    header("Location: story_editor.php?story_id=$story_id&passage_id=$passage_id&passage_save_success=true");
                    exit();
                } else {
                    // Add new passage
                    addPassage($pdo, $story_id, $passage_text);
                    $new_passage_id = $pdo->lastInsertId();
                    header("Location: story_editor.php?story_id=$story_id&passage_id=$new_passage_id&passage_add_success=true");
                    exit();
                }
            } catch (PDOException $e) {
                echo "Error saving passage: " . htmlspecialchars($e->getMessage());
                exit();
            }
        } elseif ($action == 'add_choices') {
            // Handle adding choices
            $story_id = intval($_POST['story_id']);
            $passage_id = intval($_POST['passage_id']);
            $choices = $_POST['choices']; // This will be an array of choice texts
            try {
                foreach ($choices as $choice_text) {
                    // Create a new empty Passage
                    addPassage($pdo, $story_id, '');
                    $new_passage_id = $pdo->lastInsertId();
                    // Add the Choice linking to the new Passage
                    addChoice($pdo, $passage_id, $choice_text, $new_passage_id);
                }
                header("Location: story_editor.php?story_id=$story_id&passage_id=$passage_id&choice_success=true");
                exit();
            } catch (PDOException $e) {
                echo "Error adding choice: " . htmlspecialchars($e->getMessage());
                exit();
            }
        } elseif ($action == 'delete_story') {
            // Handle deleting the story and its related data
            $story_id = intval($_POST['story_id']);
            try {
                deleteStory($pdo, $story_id);
                header("Location: story_editor.php?delete_success=true");
                exit();
            } catch (PDOException $e) {
                echo "Error deleting story: " . htmlspecialchars($e->getMessage());
                exit();
            }
        }
    }
}

// Fetch user's stories
$story_id = isset($_GET['story_id']) ? intval($_GET['story_id']) : null;
$passage_id = isset($_GET['passage_id']) ? intval($_GET['passage_id']) : null;

try {
    $userStories = getUserStories($pdo, $user_id);
    $currentStory = $story_id ? getStoryById($pdo, $story_id) : null;
    $passages = $story_id ? getPassagesByStoryId($pdo, $story_id) : [];
    $passageCount = count($passages);

    $currentPassage = null;
    $outgoingChoices = [];
    $incomingChoices = [];
    if ($passage_id) {
        $currentPassage = getPassageById($pdo, $passage_id);
        $outgoingChoices = getOutgoingChoices($pdo, $passage_id);
        $incomingChoices = getIncomingChoices($pdo, $passage_id);
    }
} catch (PDOException $e) {
    echo "Error fetching data: " . htmlspecialchars($e->getMessage());
    exit();
}

// Generate CSRF token if not set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Story Editor</title>
    <script>
        // JavaScript to dynamically add/remove choice fields
        function addChoice() {
            const choicesDiv = document.getElementById('choices');
            const choiceHTML = `
                <div class="choice">
                    <input type="text" name="choices[]" placeholder="Choice text" required>
                    <button type="button" onclick="removeChoice(this)">Remove Choice</button>
                </div>`;
            choicesDiv.insertAdjacentHTML('beforeend', choiceHTML);
        }

        function removeChoice(button) {
            const choiceDiv = button.parentNode;
            choiceDiv.remove();
        }

        // JavaScript function for confirmation
        function confirmDelete() {
            return confirm('Are you sure you want to delete this story and all its passages and choices? This action cannot be undone.');
        }
    </script>
</head>

<body>

    <h1>Story Editor</h1>

    <?php if (isset($_GET['edit_success'])): ?>
        <p style="color: green;">Story successfully updated!</p>
    <?php elseif (isset($_GET['passage_save_success'])): ?>
        <p style="color: green;">Passage successfully updated!</p>
    <?php elseif (isset($_GET['passage_add_success'])): ?>
        <p style="color: green;">Passage successfully added!</p>
    <?php elseif (isset($_GET['choice_success'])): ?>
        <p style="color: green;">Choices successfully added!</p>
    <?php elseif (isset($_GET['delete_success'])): ?>
        <p style="color: red;">Story successfully deleted.</p>
    <?php endif; ?>

    <?php if (!$story_id): ?>
        <h2>Select or Create a Story</h2>
        <form method="POST">
            <?php if (!empty($userStories)): ?>
                <label for="existing_story">Select Existing Story:</label>
                <select id="existing_story" name="existing_story">
                    <option value="">-- Select a Story --</option>
                    <?php foreach ($userStories as $story): ?>
                        <option value="<?php echo $story['ID']; ?>"><?php echo htmlspecialchars($story['Story_Name']); ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" name="action" value="edit_existing_story">Edit Selected Story</button>
                <br><br>
            <?php endif; ?>
            <label for="new_story_name">Or Enter New Story Name:</label>
            <input type="text" id="new_story_name" name="new_story_name">
            <button type="submit" name="action" value="create_new_story">Create New Story</button>
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        </form>
    <?php else: ?>
        <h2>Editing Story: <?php echo htmlspecialchars($currentStory['Story_Name']); ?></h2>
        <!-- Story Name Edit Form -->
        <form method="POST">
            <label for="story_name">Story Name:</label>
            <input type="text" id="story_name" name="story_name"
                value="<?php echo isset($currentStory['Story_Name']) ? htmlspecialchars($currentStory['Story_Name']) : ''; ?>"
                required>
            <input type="hidden" name="story_id" value="<?php echo $story_id; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <button type="submit" name="action" value="save_story" <?php echo $passageCount > 0 ? '' : 'disabled'; ?>>Save Story</button>
            <?php if ($passageCount == 0): ?>
                <p style="color: red;">You must add at least one passage before saving the story.</p>
            <?php endif; ?>
        </form>

        <!-- Delete Story Button -->
        <form method="POST" onsubmit="return confirmDelete();">
            <input type="hidden" name="story_id" value="<?php echo $story_id; ?>">
            <input type="hidden" name="action" value="delete_story">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <button type="submit" style="background-color: red; color: white;">Delete Story</button>
        </form>

        <!-- Navigation between Passages -->
        <?php if ($passageCount > 0): ?>
            <h3>Navigate to Passage</h3>
            <form method="GET">
                <select name="passage_id">
                    <?php foreach ($passages as $passage): ?>
                        <option value="<?php echo $passage['PassageID']; ?>" <?php echo ($passage['PassageID'] == $passage_id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars(substr($passage['Text'], 0, 50)); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="hidden" name="story_id" value="<?php echo $story_id; ?>">
                <button type="submit">Load Passage</button>
            </form>
        <?php endif; ?>

        <!-- If no passage is selected -->
        <?php if (!$currentPassage): ?>
            <h2>Add First Passage</h2>
            <!-- Passage Input Form -->
            <form method="POST">
                <label for="passage_text">Passage Text:</label>
                <textarea id="passage_text" name="passage_text" required></textarea>
                <input type="hidden" name="story_id" value="<?php echo $story_id; ?>">
                <input type="hidden" name="action" value="save_passage">
                <!-- <input type="hidden" name="passage_id" value=""> -->
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <button type="submit">Add Passage</button>
            </form>
        <?php else: ?>
            <!-- Incoming Choices -->
            <?php if (!empty($incomingChoices)): ?>
                <h3>Incoming Choices</h3>
                <?php foreach ($incomingChoices as $choice): ?>
                    <div>
                        <strong>From Passage ID:</strong> <?php echo $choice['FromPassageID']; ?>
                        <br>
                        <strong>Choice Text:</strong> <?php echo htmlspecialchars($choice['ChoiceText']); ?>
                        <br>
                        <form method="GET" style="display:inline;">
                            <input type="hidden" name="story_id" value="<?php echo $story_id; ?>">
                            <input type="hidden" name="passage_id" value="<?php echo $choice['FromPassageID']; ?>">
                            <button type="submit">Go to From Passage</button>
                        </form>
                    </div>
                    <hr>
                <?php endforeach; ?>
            <?php endif; ?>

            <!-- Passage Edit Form -->
            <h2>Edit Passage</h2>
            <form method="POST">
                <label for="passage_text">Passage Text:</label>
                <textarea id="passage_text" name="passage_text" required><?php echo htmlspecialchars($currentPassage['Text']); ?></textarea>
                <input type="hidden" name="story_id" value="<?php echo $story_id; ?>">
                <input type="hidden" name="passage_id" value="<?php echo $passage_id; ?>">
                <input type="hidden" name="action" value="save_passage">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <button type="submit">Save Passage</button>
            </form>

            <!-- Outgoing Choices -->
            <?php if (!empty($outgoingChoices)): ?>
                <h3>Outgoing Choices</h3>
                <?php foreach ($outgoingChoices as $choice): ?>
                    <div>
                        <strong>Choice Text:</strong> <?php echo htmlspecialchars($choice['ChoiceText']); ?>
                        <br>
                        <strong>To Passage ID:</strong> <?php echo $choice['NextPassageID']; ?>
                        <br>
                        <form method="GET" style="display:inline;">
                            <input type="hidden" name="story_id" value="<?php echo $story_id; ?>">
                            <input type="hidden" name="passage_id" value="<?php echo $choice['NextPassageID']; ?>">
                            <button type="submit">Go to Linked Passage</button>
                        </form>
                    </div>
                    <hr>
                <?php endforeach; ?>
            <?php endif; ?>

            <!-- Add Choices Form -->
            <h3>Add Choices</h3>
            <form method="POST">
                <div id="choices">
                    <div class="choice">
                        <input type="text" name="choices[]" placeholder="Choice text" required>
                        <button type="button" onclick="removeChoice(this)">Remove Choice</button>
                    </div>
                </div>
                <button type="button" onclick="addChoice()">Add Choice</button>
                <input type="hidden" name="story_id" value="<?php echo $story_id; ?>">
                <input type="hidden" name="passage_id" value="<?php echo $passage_id; ?>">
                <input type="hidden" name="action" value="add_choices">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <button type="submit">Save Choices</button>
            </form>
        <?php endif; ?>
    <?php endif; ?>

    <p><a href="index.php">Return to Front Page</a></p>
</body>

</html>