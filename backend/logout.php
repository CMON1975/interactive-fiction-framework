<?php
// start the session to access session data
session_start();

/**
 * Destroy the session to log out the user.
 * - Removes all session data, effectively logging out the user.
 */
session_destroy();

/**
 * Redirect the user to the landing page.
 * - Sends a header to redirect the browser to the homepage.
 * - Ensures no further script execution after redirection.
 */
header("Location: ../index.php");
exit(); // terminate script execution after the redirect
