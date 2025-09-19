<?php
// File: index.php (Updated to redirect to home)
session_start();

// Redirect to appropriate page
if (isset($_SESSION['user_id'])) {
    header('Location: home.php');  // Changed from profile.php to home.php
} else {
    header('Location: auth/login.php');
}
exit;
