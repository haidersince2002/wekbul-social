<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: home.php');  // Changed from profile.php to home.php
} else {
    header('Location: auth/login.php');
}
exit;
