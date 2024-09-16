# Project Structure and Local Development Environment
- **Front end:** Basic HTML interface that gives creators access to creative tools and players access to published interactive fiction.
- **Local development environment:** VS Code, MySQL database.
- **Database initialization:** interactive_fiction_db
---
# Project Goals
- Deliver a working prototype of website that allows both creation and playing of interactive fiction no later than December 6, 2024.
---
# Thoughts
## landing page:
- create account/login
- play an existing story
    - create account
    - login
- create a story
    - create account
    -login
## create page:
- visualization of story structure (stretch goal)
- create new
- load exisiting
    - populate (primary) text edit frame from dropdown of "passages" (units of story)
    - secondary frame contains interactive list of branches from, branches to, and variables (variables are a stretch goal)
## backend
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