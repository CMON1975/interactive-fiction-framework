# Project Structure and Local Development Environment
- **Front end:** Basic HTML interface that gives creators access to creative tools and players access to published interactive fiction.
- **Local development environment:** VS Code, MySQL database.
- **Database initialization:** interactive_fiction_db
---
## Project Goals
- Deliver a working prototype of website that allows both creation and playing of interactive fiction no later than December 6, 2024.
---
## Thoughts
### landing page:
- create account/login
- play an existing story
    - create account
    - login
- create a story
    - create account
    -login
### create page:
- visualization of story structure (stretch goal)
- create new
- load exisiting
    - populate (primary) text edit frame from dropdown of "passages" (units of story)
    - secondary frame contains interactive list of branches from, branches to, and variables (variables are a stretch goal)
### backend
- user account database
    - login name
    - password
    - (optional) email for future contact/notifications
    - stories authored
        - in progress
        - published
    - stories played
        - in progress
            - location in story
            - variables
        - finished
- stories database
    - "packaged" story:
        - passages
        - variables

### general formatting
- use a Markdown parser (Parsedown is a good choice, `wget https://raw.githubusercontent.com/erusev/parsedown/master/Parsedown.php
`) for ease of editing

## Database Design
### Question of structured format (JSON, CSV, XML) vs. MySQL database:
- This project has the potential, long-term need for user management, dynamic querying, and scalability. MySQL provides this flexibility, is an opportunity to learn relational databases, and would establish a solid foundation for future extension.
- Given the time constraint, it is a risk to learn structured formats rather than leverage existing MySQL knowledge.
- MySQL allows export to CSV, which would allow conversion to other structured formats. A possible extension could such an export/convert function.
- Decision: Use MySQL until it becomes untenable.
### Users Table:
- ID: `INT AUTO_INCREMENT PRIMARY KEY`
- Name: `VARCHAR(50) UNIQUE`
- Password: `VARCHAR(255)` (for hashed password storage)
- EULA Agreement: `BOOLEAN` (additional timestamp?)
- Data Collection Agreement: `BOOLEAN` (timestamp?)
- Preferences: (TBD, likely a `JSON` field to store user preferences and allow flexibility for extension.)

### Stories Table:
- ID: `INT AUTO_INCREMENT PRIMARY KEY`
- Story Name: `VARCHAR(100)`
- Tags `VARCHAR(255)` (a separate table to store tags for more flexible filtering?)
- Passages Table:
    - PassageID: `INT AUTO_INCREMENT PRIMARY KEY`
    - StoryID: `INT` (foreign key to link it to the story)
    - Text: `TEXT` (for the passage content)
    - Choices Table:
        - ChoiceID: `INT AUTO_INCREMENT PRIMARY KEY`
        - PassageID: `INT` (references which passage this choice belongs to)
        - ChoiceText: `VARCHAR(255)` (text shown to the user)
        - NextPassageID: `INT` (foreign key linking to the next passage)
        - IsFinal: `BOOLEAN` (marks if this leads to a story conclusion)

### Stories Played Table (Tracks Player Progress)
- UserID: `INT` (foreign key to the users table)
- StoryID: `INT` (foreign key to the stories table)
- CurrentPassageID: `INT` (to track where the player is in the story)

- Project goal version:
    - Passage(s): text of individual, pre- or post-choice text
    - Each story passage has a secondary unique number used to link to it from choices.
    - Each story passage has one ore more linked choice(s) that leads other passages. If the story passage is a special "final" passage, a terminator that would end the story, the link leads to a conclusion/evaluation passage.
    - A conclusion/evaluation passage summarizes the user's performance in the story then allows them to return to the main menu.
        - Stretch goal: related image and audio.

#### Visualization:
![database visualization](../assets/images/db_visualize.png)

#### Passage Example:
**Passage Number 1**
Text: "You are standing at a crossroads. The way forward leads east or west."
Choice 1: Go east (Link to Passage Number 2).
Choice 2: Go west (Link to Passage Number 3).