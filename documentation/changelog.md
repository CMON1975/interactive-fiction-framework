# Changelog
### 241023 
- Added a script to `story_editor.php` to detect changes in the Passage field (currently completely broken on the test build).
- Moved the database credentials to a "safe" directory for security purposes
### 241030
- Modified HTML form to include options for selecting existing Passage or creating new Passage (radio buttons)
### 241101
- Added dropdown of existing Passages for new passage selection option to link to existing.
### 241102
- Added small text area for new passage text rather than a jump to a new editor frame. Scared this will break a lot of logic but so far so good.
### 241103
- JavaScript for existing Passage/new Passage editing wired up and working. PHP implementation in /backend/editor.php seems to be working. I now have way too much working PHP/JavaScript in the story-editor.php code, but it's all functional.
### 241104
- Added success message for Choice update. I need to deal with code duplication.