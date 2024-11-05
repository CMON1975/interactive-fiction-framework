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

// Handle editing a choice
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_choice') {
    // CSRF Protection
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('Invalid CSRF token');
    }

    $choice_id = intval($_POST['choice_id']);
    $story_id = intval($_POST['story_id']);
    $passage_id = intval($_POST['passage_id']);

    try {
        // Fetch the existing choice
        $stmt = $pdo->prepare("SELECT ChoiceText, NextPassageID FROM Choices WHERE ChoiceID = :choice_id");
        $stmt->execute([':choice_id' => $choice_id]);
        $choice = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$choice) {
            throw new Exception("Choice not found.");
        }

        // Fetch existing passages for the story
        $passages = getPassagesByStoryId($pdo, $story_id);

        // Display the edit form
?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <title>Edit Choice</title>
            <script>
                function toggleLinkTypeEdit(radio) {
                    const choiceDiv = radio.closest('.choice');
                    const linkExistingDiv = choiceDiv.querySelector('.link-existing');
                    const linkNewDiv = choiceDiv.querySelector('.link-new');

                    if (radio.value === 'existing') {
                        linkExistingDiv.style.display = 'block';
                        linkNewDiv.style.display = 'none';
                        choiceDiv.querySelector('select[name="existing_passage_id"]').required = true;
                        choiceDiv.querySelector('textarea[name="new_passage_text"]').required = false;
                    } else {
                        linkExistingDiv.style.display = 'none';
                        linkNewDiv.style.display = 'block';
                        choiceDiv.querySelector('textarea[name="new_passage_text"]').required = true;
                        choiceDiv.querySelector('select[name="existing_passage_id"]').required = false;
                    }
                }

                document.addEventListener('DOMContentLoaded', function() {
                    const radioButtons = document.querySelectorAll('input[name="link_type"]');
                    radioButtons.forEach(radio => {
                        radio.addEventListener('change', function() {
                            toggleLinkTypeEdit(this);
                        });
                    });
                });
            </script>
        </head>

        <body>
            <h2>Edit Choice</h2>
            <form method="POST">
                <label for="choice_text">Choice Text:</label>
                <input type="text" id="choice_text" name="choice_text" value="<?php echo htmlspecialchars($choice['ChoiceText']); ?>" required>

                <br><br>

                <label>
                    <input type="radio" name="link_type" value="existing" <?php echo $choice['NextPassageID'] ? 'checked' : ''; ?> onchange="toggleLinkTypeEdit(this)"> Link to Existing Passage
                </label>
                <label>
                    <input type="radio" name="link_type" value="new" <?php echo !$choice['NextPassageID'] ? 'checked' : ''; ?> onchange="toggleLinkTypeEdit(this)"> Create New Passage
                </label>

                <br><br>

                <!-- Existing Passages Dropdown -->
                <div class="link-existing" <?php echo $choice['NextPassageID'] ? 'style="display: block;"' : 'style="display: none;"'; ?>>
                    <label for="existing_passage_id">Select Passage:</label>
                    <select name="existing_passage_id" id="existing_passage_id">
                        <option value="">-- Select a Passage --</option>
                        <?php foreach ($passages as $p): ?>
                            <option value="<?php echo $p['PassageID']; ?>" <?php echo ($p['PassageID'] == $choice['NextPassageID']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars(substr($p['Text'], 0, 50)); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- New Passage Textarea -->
                <div class="link-new" <?php echo !$choice['NextPassageID'] ? 'style="display: block;"' : 'style="display: none;"'; ?>>
                    <label for="new_passage_text">New Passage Text:</label>
                    <textarea name="new_passage_text" id="new_passage_text" placeholder="Enter new passage text"></textarea>
                </div>

                <br>

                <input type="hidden" name="choice_id" value="<?php echo $choice_id; ?>">
                <input type="hidden" name="story_id" value="<?php echo $story_id; ?>">
                <input type="hidden" name="passage_id" value="<?php echo $passage_id; ?>">
                <input type="hidden" name="action" value="update_choice">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <button type="submit">Update Choice</button>
            </form>
            <p><a href="story_editor.php?story_id=<?php echo $story_id; ?>&passage_id=<?php echo $passage_id; ?>">Back to Story Editor</a></p>
        </body>

        </html>
<?php
        exit();
    } catch (Exception $e) {
        echo "Error editing choice: " . htmlspecialchars($e->getMessage());
        exit();
    }
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
            $choices_text = $_POST['choices_text']; // Array of choice texts
            $link_types = $_POST['link_type']; // Array of link types: 'existing' or 'new'
            $existing_passage_ids = isset($_POST['existing_passage_id']) ? $_POST['existing_passage_id'] : [];
            $new_passage_texts = isset($_POST['new_passage_text']) ? $_POST['new_passage_text'] : [];

            try {
                foreach ($choices_text as $index => $choice_text) {
                    $link_type = $link_types[$index];
                    if ($link_type === 'existing') {
                        // Link to an existing passage
                        $existing_passage_id = intval($existing_passage_ids[$index]);
                        if ($existing_passage_id <= 0) {
                            throw new Exception("Invalid existing passage selected for choice: " . htmlspecialchars($choice_text));
                        }
                        // Add the choice linking to the existing passage
                        addChoice($pdo, $passage_id, $choice_text, $existing_passage_id);
                    } elseif ($link_type === 'new') {
                        // Create a new passage and link to it
                        $new_passage_text = trim($new_passage_texts[$index]);
                        if (empty($new_passage_text)) {
                            throw new Exception("New passage text cannot be empty for choice: " . htmlspecialchars($choice_text));
                        }
                        // Add the new passage
                        addPassage($pdo, $story_id, $new_passage_text);
                        $new_passage_id = $pdo->lastInsertId();
                        // Link the choice to the new passage
                        addChoice($pdo, $passage_id, $choice_text, $new_passage_id);
                    } else {
                        throw new Exception("Invalid link type for choice: " . htmlspecialchars($choice_text));
                    }
                }
                header("Location: story_editor.php?story_id=$story_id&passage_id=$passage_id&choice_success=true");
                exit();
            } catch (Exception $e) {
                echo "Error adding choice: " . htmlspecialchars($e->getMessage());
                exit();
            }
        } elseif ($action == 'update_choice') {
            // Handle updating a choice
            $choice_id = intval($_POST['choice_id']);
            $story_id = intval($_POST['story_id']);
            $passage_id = intval($_POST['passage_id']);
            $choice_text = trim($_POST['choice_text']);
            $link_type = $_POST['link_type'];
            $existing_passage_id = isset($_POST['existing_passage_id']) ? intval($_POST['existing_passage_id']) : null;
            $new_passage_text = isset($_POST['new_passage_text']) ? trim($_POST['new_passage_text']) : null;

            try {
                if (empty($choice_text)) {
                    throw new Exception("Choice text cannot be empty.");
                }

                if ($link_type === 'existing') {
                    if (!$existing_passage_id || $existing_passage_id <= 0) {
                        throw new Exception("Invalid existing passage selected.");
                    }
                    // Update the choice to link to the existing passage
                    $sql = "UPDATE Choices SET ChoiceText = :choice_text, NextPassageID = :next_passage_id WHERE ChoiceID = :choice_id";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        ':choice_text' => $choice_text,
                        ':next_passage_id' => $existing_passage_id,
                        ':choice_id' => $choice_id
                    ]);
                } elseif ($link_type === 'new') {
                    if (empty($new_passage_text)) {
                        throw new Exception("New passage text cannot be empty.");
                    }
                    // Create a new passage
                    addPassage($pdo, $story_id, $new_passage_text);
                    $new_passage_id = $pdo->lastInsertId();
                    // Update the choice to link to the new passage
                    $sql = "UPDATE Choices SET ChoiceText = :choice_text, NextPassageID = :next_passage_id WHERE ChoiceID = :choice_id";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        ':choice_text' => $choice_text,
                        ':next_passage_id' => $new_passage_id,
                        ':choice_id' => $choice_id
                    ]);
                } else {
                    throw new Exception("Invalid link type.");
                }

                header("Location: story_editor.php?story_id=$story_id&passage_id=$passage_id&choice_update_success=true");
                exit();
            } catch (Exception $e) {
                echo "Error updating choice: " . htmlspecialchars($e->getMessage());
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
        const passagesData = <?php echo $passages_json; ?>;
    </script>
    <script src="backend/story_editor.js"></script>
</head>

<body>

    <h1>Story Editor</h1>

    <!-- Success messages -->
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
    <?php elseif (isset($_GET['choice_update_success'])): ?>
        <p style="color: green;">Choice successfully updated!</p>
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
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="edit_choice">
                            <input type="hidden" name="choice_id" value="<?php echo $choice['ChoiceID']; ?>">
                            <input type="hidden" name="story_id" value="<?php echo $story_id; ?>">
                            <input type="hidden" name="passage_id" value="<?php echo $passage_id; ?>">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <button type="submit">Edit Choice</button>
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
                        <input type="text" name="choices_text[]" placeholder="Choice text" required>

                        <label>
                            <input type="radio" name="link_type[]" value="existing" checked onchange="toggleLinkType(this)"> Link to Existing Passage
                        </label>
                        <label>
                            <input type="radio" name="link_type[]" value="new" onchange="toggleLinkType(this)"> Create New Passage
                        </label>

                        <!-- Existing Passages Dropdown -->
                        <div class="link-existing">
                            <select name="existing_passage_id[]" required>
                                <option value="">-- Select a Passage --</option>
                                <?php foreach ($passages as $passage): ?>
                                    <option value="<?php echo $passage['PassageID']; ?>">
                                        <?php echo htmlspecialchars(substr($passage['Text'], 0, 50)); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- New Passage Textarea -->
                        <div class="link-new" style="display: none;">
                            <textarea name="new_passage_text[]" placeholder="New passage text"></textarea>
                        </div>

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