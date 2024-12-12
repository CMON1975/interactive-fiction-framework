<?php
// start the session to manage user authentication and session data
session_start();

/**
 * Checks if the user is logged in.
 * - If the user is not logged in, redirects them to the login page.
 * - Terminates the script to prevent further execution for unauthorized users.
 */
function check_logged_in()
{
    if (!isset($_SESSION['user_id'])) { // check if user ID is not set in the session
        header("Location: login.php"); // redirect to the login page
        exit(); // terminate script execution after redirection
    }
}

/**
 * Retrieves the username of the currently logged-in user.
 * - Returns the username if it exists in the session, otherwise returns null.
 * 
 * @return string|null The username of the logged-in user or null if not set.
 */
function get_logged_in_user()
{
    return $_SESSION['username'] ?? null; // return username from session or null if not set
}
