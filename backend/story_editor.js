// JavaScript to dynamically add/remove choice fields
function addChoice() {
    const choicesDiv = document.getElementById('choices');
    const choiceCount = choicesDiv.querySelectorAll('.choice').length;
    
    // Generate options for existing passages
    let passageOptions = '<option value="">-- Select a Passage --</option>';
    passagesData.forEach(passage => {
        passageOptions += `<option value="${passage.PassageID}">
            ${passage.Text.substring(0, 50)}
        </option>`;
    });

    const choiceHTML = `
        <div class="choice">
            <input type="text" name="choices_text[]" placeholder="Choice text" required>
            
            <label>
                <input type="radio" name="link_type[${choiceCount}]" value="existing" checked onchange="toggleLinkType(this)"> Link to Existing Passage
            </label>
            <label>
                <input type="radio" name="link_type[${choiceCount}]" value="new" onchange="toggleLinkType(this)"> Create New Passage
            </label>
            
            <!-- Existing Passages Dropdown -->
            <div class="link-existing">
                <select name="existing_passage_id[]" required>
                    ${passageOptions}
                </select>
            </div>
            
            <!-- New Passage Textarea -->
            <div class="link-new" style="display: none;">
                <textarea name="new_passage_text[]" placeholder="New passage text"></textarea>
            </div>
            
            <button type="button" onclick="removeChoice(this)">Remove Choice</button>
        </div>`;
    choicesDiv.insertAdjacentHTML('beforeend', choiceHTML);
} //TODO? adjust name attributes with appropriate indexing if necessary to handle multiple choices correctly

// story_editor.js

// Function to remove a choice field
function removeChoice(button) {
    const choiceDiv = button.parentNode;
    choiceDiv.remove();
}

// Function to confirm story deletion
function confirmDelete() {
    return confirm('Are you sure you want to delete this story and all its passages and choices? This action cannot be undone.');
}

// Function to toggle link type fields
function toggleLinkType(radio) {
    const choiceDiv = radio.closest('.choice');
    const linkExistingDiv = choiceDiv.querySelector('.link-existing');
    const linkNewDiv = choiceDiv.querySelector('.link-new');

    if (radio.value === 'existing') {
        linkExistingDiv.style.display = 'block';
        linkNewDiv.style.display = 'none';
        // Make existing passage selection required
        choiceDiv.querySelector('select[name="existing_passage_id[]"]').required = true;
        // Remove required attribute from new passage text
        choiceDiv.querySelector('textarea[name="new_passage_text[]"]').required = false;
    } else {
        linkExistingDiv.style.display = 'none';
        linkNewDiv.style.display = 'block';
        // Make new passage text required
        choiceDiv.querySelector('textarea[name="new_passage_text[]"]').required = true;
        // Remove required attribute from existing passage selection
        choiceDiv.querySelector('select[name="existing_passage_id[]"]').required = false;
    }
}

// Update the initial toggle state
document.addEventListener('DOMContentLoaded', function() {
    const radioButtons = document.querySelectorAll('input[name^="link_type"]');
    radioButtons.forEach(radio => {
        radio.addEventListener('change', function() {
            toggleLinkType(this);
        });
    });
});

    