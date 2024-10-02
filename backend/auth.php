<?php
session_start();

function check_logged_in()
{
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}

function get_logged_in_user()
{
    return $_SESSION['username'] ?? null;
}
