# Interactive Fiction Framework Features

## Backend

### User Authentication and Session Management
- **User Login:** Users can log in to the system.
- **Session Handling:** Sessions are managed to ensure only authenticated users have access to the story editor.
- **CSRF Protection:** CSRF tokens are generated and validated to protect against cross-site request forgery attacks.

### Story Management
- **Create New Story:** Users can create new stories by providing a story name.
- **Edit Existing Story:** Users can edit the name of existing stories.
- **Delete Story:** Users can delete stories, which also removes all related passages and choices.
- **List User Stories:** Users can view a list of their stories to select and edit.

### Passage Management
- **Add New Passage:** Users can add passages to a story.
- **Edit Passage:** Users can edit the text of existing passages.
- **Navigate Between Passages:**
    - Users can select passages from a dropdown menu to edit or view.
    - Users can navigate to passages linked by choices.
- **View Incoming Choices:** Users can see which choices from other passages lead to the current passage.

### Choice Management
- **Add Choices to Passages:**
    - Users can add choices to a passage.
    - When adding a choice, users can choose to:
        - **Link to an Existing Passage:** Select from a list of existing passages.
        - **Create a New Passage:** Provide new passage text to create and link.
- **Edit Existing Choices:**
    - Users can edit the text of choices.
    - Users can change the linked passage of a choice:
        - Switch between linking to an existing passage or creating a new one.
- **Remove Choices:** Users can remove choices from a passage (functionality implied by the ability to edit and manage choices).

### User Interface and Interaction
- **Dynamic Forms with JavaScript:**
    - **Add/Remove Choice Fields:** Users can dynamically add or remove choice fields when editing a passage.
    - **Toggle Between Link Types:** Radio buttons allow users to toggle between linking to existing passages or creating new ones.
- **Separate JavaScript File:**
    - JavaScript code (story_editor.js) is moved to a separate file for better organization and maintainability.
- **Success and Error Messages:**
    - Feedback is provided to the user after actions (e.g., story saved, passage updated, choice added).

### Backend and Code Organization
- **Backend Logic Separation:**
    - Backend logic is moved to backend/editor.php to separate it from the presentation layer.
- **Function Definitions:**
    - Functions are defined for creating stories, passages, and choices.
    - Functions handle editing, updating, and deleting entities.
- **Database Interactions:**
    - Prepared statements and transactions are used to interact securely with the database.
    - CRUD operations are implemented for stories, passages, and choices.

### Security Measures
- **Input Validation and Sanitization:**
    - User inputs are validated and sanitized to prevent SQL injection and XSS attacks.
- **CSRF Tokens:**
    - CSRF tokens are used in forms to prevent unauthorized form submissions.
- **Error Reporting:**
    -Error reporting is enabled during development for debugging purposes (should be disabled in production).

### Data Structures and Relationships
- **Database Schema:**
    - **Users:** Table storing user information.
    - **Stories:** Each story has an ID, name, and is associated with a user.
    - **Passages:** Each passage has an ID, text, and is associated with a story.
    - **Choices:** Choices link passages together and have choice text and references to passages.
    - **StoriesPlayed:** Tracks which stories have been played by users (implied by initial code).
- **Passage and Choice Relationships:**
    - Passages can have multiple outgoing choices.
    - Choices can lead to existing passages or new ones.
    - Incoming and outgoing choices are tracked for each passage.

### Error Handling and Feedback
- **User Feedback:**
    - Success messages are displayed after actions like saving a story or adding a passage.
    - Error messages are displayed when actions fail, such as invalid inputs or database errors.
- **Exception Handling:**
    - Try-catch blocks are used to handle exceptions during database operations.

### Code Refactoring and Maintainability
- **Separation of Concerns:**
    - Backend logic, frontend presentation, and JavaScript functionalities are separated into different files.
- **Code Organization:**
    - Functions are grouped logically for better readability.
    - Redundant code is minimized to avoid duplication.

---

## Frontend

### Story Selection and Access
- **Landing Page Display:**
    - **List of Available Stories:** All stories from the database are displayed on the landing page, each with a "Play Story" link.
    - **Story Details:** Each story is listed with its name and an option to play it.
- **User Authentication Integration:**
    - **Login Status Display:** The landing page shows whether a user is logged in and displays their username if they are.
    - **Conditional Navigation Links:**
        - **Logged-In Users:** Access to "Create/Edit Story" and "Logout" links.
        - **Guest Users:** Access to "Login or Join" link.

### Story Playback Functionality
- **Passage Display:**
    - **Current Passage Text:** The player displays the text of the current passage to the user.
    - **Formatted Text:** Passage text is displayed with proper formatting (e.g., line breaks).
- **Choice Presentation:**
    - **Interactive Choices:** Available choices are presented as buttons within a form.
    - **Dynamic Navigation:** Selecting a choice directs the user to the next passage associated with that choice.
- **End of Story Handling:**
    - **Story Completion Message:** Displays "The End." when a passage has no outgoing choices, indicating the story has concluded.
    - **Restart Option:** Provides an option to restart the story from the beginning after completion.

### User Progress Tracking
- **Session-Based Progress:**
    - **Anonymous Users:** Can play stories without logging in but progress is not saved.
    - **Logged-In Users:**
        - **Progress Saving:** The user's current passage is saved in the StoriesPlayed table, allowing them to resume where they left off.
        - **Automatic Resume:** When revisiting a story, logged-in users are automatically taken to their last saved passage.
- **Restarting Stories:**
    - **Progress Reset:** Users can choose to restart the story, which resets their current passage to the first passage.
    - **Data Update:** The StoriesPlayed table is updated to reflect the new starting point upon restart.

### Navigation and User Experience
- **Return to Landing Page:** A link is provided to return to the main landing page (index.php) from the story player.
- **Story Title Display:** The name of the story is prominently displayed at the top of the story player page.
- **Consistent Layout:** The user interface is consistent between passages, providing a seamless reading experience.

### Technical and Security Features
- **Database Integration:**
    - **Dynamic Content:** Passages and choices are fetched from the database based on the current state of the story.
    - **Data Integrity:** Prepared statements are used to prevent SQL injection attacks.
- **Session Management:**
    - **User Authentication:** Sessions are managed to determine if a user is logged in.
    - **User-Specific Data:** Progress tracking is tied to the user's session and user ID.
- **Input Handling:**
    - **Form Submission Processing:** The application handles POST requests to process user choices and story restarts.
    - **Input Validation:** User inputs are sanitized and validated to ensure correct operation.
- **Error Handling:**
    - **Graceful Degradation:** If a passage or choice is not found, the application displays an error message.
    - **Redirection:** Users are redirected appropriately based on their actions (e.g., selecting a choice, restarting a story).

### Accessibility
- **Play Without Login:** Users are allowed to play stories without needing to create an account or log in.
- **Account Benefits:** Logging in provides the added benefit of progress saving but is not mandatory for story exploration.

---

## Additional Incidental Features
- **Deployment Scripts:** `bash` scripts for local/remote deployment for testing and production, and template `sql` database construction script.

---

## Not Implemented Yet (Discussed for Future Enhancements)
- **AJAX Data Loading:**
    - Implementing AJAX to load passages asynchronously for better performance, especially with large datasets.
- **Further Refactoring:**
    Modularizing code further to enhance scalability and maintainability.
- **Improved UI/UX:**
    - Potential enhancements to the user interface for a more intuitive experience.
- **Additional Security Measures:**
    - Enhancing security with more robust authentication and authorization checks.