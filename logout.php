<?php
// Include database functions
require_once 'db_functions.php';

// Start session
session_start();

// Debug information
error_log('Logout attempt - Session before logout: ' . print_r($_SESSION, true));

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page
    header('Location: login.php');
    exit;
}

// Log the user out
$_SESSION = array();
session_destroy();

// Debug information
error_log('Logout completed - Session destroyed');

// Redirect to home page
header('Location: index.php');
exit;
?>
