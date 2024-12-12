/*
 * Story Editor JavaScript Functions
 * This script provides dynamic functionality for creating and managing choices in the story editor.
 * Features:
 * - Dynamically add or remove choice fields for linking story passages.
 * - Toggle between linking to an existing passage or creating a new passage.
 * - Confirm story deletion with a warning dialog.
 * - Initialize toggle states for link type fields on page load.
 * 
 * Functions:
 * 1. addChoice: Adds a new choice field with options for linking to an existing passage or creating a new one.
 * 2. removeChoice: Removes a specific choice field.
 * 3. confirmDelete: Displays a confirmation dialog before story deletion.
 * 4. toggleLinkType: Handles visibility and required attributes for existing and new passage fields.
 * 5. Event Listener: Initializes toggle states for link type radio buttons when the page loads.
 */

// function to dynamically add choice fields for creating/editing story passages
function addChoice() {
    const choicesDiv = document.getElementById('choices'); // container for all choices
    const choiceCount = choicesDiv.querySelectorAll('.choice').length; // current number of choices

    // generate options for existing passages from `passagesData`
    let passageOptions = '<option value="">-- Select a Passage --</option>';
    passagesData.forEach(passage => {
        passageOptions += `<option value="${passage.PassageID}">
            ${passage.Text.substring(0, 50)} <!-- show first 50 characters of passage text -->
        </option>`;
    });

    // HTML template for a new choice field
    const choiceHTML = `
        <div class="choice">
            <!-- input field for choice text -->
            <input type="text" name="choices_text[]" placeholder="Choice text" required>
            
            <!-- radio buttons to select link type -->
            <label>
                <input type="radio" name="link_type[${choiceCount}]" value="existing" checked onchange="toggleLinkType(this)"> Link to Existing Passage
            </label>
            <label>
                <input type="radio" name="link_type[${choiceCount}]" value="new" onchange="toggleLinkType(this)"> Create New Passage
            </label>
            
            <!-- dropdown for selecting an existing passage -->
            <div class="link-existing">
                <select name="existing_passage_id[]" required>
                    ${passageOptions}
                </select>
            </div>
            
            <!-- textarea for entering a new passage -->
            <div class="link-new" style="display: none;">
                <textarea name="new_passage_text[]" placeholder="New passage text"></textarea>
            </div>
            
            <!-- button to remove this choice -->
            <button type="button" onclick="removeChoice(this)">Remove Choice</button>
        </div>`;
    
    // append the new choice to the choices container
    choicesDiv.insertAdjacentHTML('beforeend', choiceHTML);
}

// function to remove a specific choice field
function removeChoice(button) {
    const choiceDiv = button.parentNode; // parent container of the button
    choiceDiv.remove(); // removes the choice field
}

// function to confirm story deletion with a warning
function confirmDelete() {
    return confirm('Are you sure you want to delete this story and all its passages and choices? This action cannot be undone.');
}

// function to toggle between existing passage selection and new passage input
function toggleLinkType(radio) {
    const choiceDiv = radio.closest('.choice'); // parent container of the radio button
    const linkExistingDiv = choiceDiv.querySelector('.link-existing'); // container for existing passage dropdown
    const linkNewDiv = choiceDiv.querySelector('.link-new'); // container for new passage textarea

    if (radio.value === 'existing') {
        // show the dropdown for existing passages
        linkExistingDiv.style.display = 'block';
        linkNewDiv.style.display = 'none';

        // make the existing passage dropdown required
        choiceDiv.querySelector('select[name="existing_passage_id[]"]').required = true;
        // remove the required attribute from the new passage textarea
        choiceDiv.querySelector('textarea[name="new_passage_text[]"]').required = false;
    } else {
        // show the textarea for creating a new passage
        linkExistingDiv.style.display = 'none';
        linkNewDiv.style.display = 'block';

        // make the new passage textarea required
        choiceDiv.querySelector('textarea[name="new_passage_text[]"]').required = true;
        // remove the required attribute from the existing passage dropdown
        choiceDiv.querySelector('select[name="existing_passage_id[]"]').required = false;
    }
}

// initialize toggle state for link type radio buttons on page load
document.addEventListener('DOMContentLoaded', function() {
    const radioButtons = document.querySelectorAll('input[name^="link_type"]'); // select all link type radio buttons
    radioButtons.forEach(radio => {
        // add event listener to toggle the link type fields dynamically
        radio.addEventListener('change', function() {
            toggleLinkType(this);
        });
    });
});
