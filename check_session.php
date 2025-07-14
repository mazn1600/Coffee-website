<?php
// Start session
session_start();

// Debug information
echo "<pre>";
echo "Session Status:\n";
echo "Session ID: " . session_id() . "\n";
echo "Session Data:\n";
print_r($_SESSION);
echo "</pre>";

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    echo "User is logged in as: " . $_SESSION['name'] . "\n";
    echo "Role: " . ($_SESSION['role'] ?? 'not set') . "\n";
} else {
    echo "No user is logged in\n";
}
?> 