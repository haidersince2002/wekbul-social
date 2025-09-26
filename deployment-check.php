<?php
// File: deployment-check.php
// Use this file to verify your deployment environment
// Access it at: https://your-app-url.com/deployment-check.php
// Remove this file after successful deployment verification

// Don't display in production
if (getenv('DEBUG') !== 'true') {
    die('This file is only accessible in debug mode. Set DEBUG=true to use it.');
}

// Set content type to plain text
header('Content-Type: text/plain');

echo "==== ConnectHub Deployment Verification ====\n\n";

// Check PHP Version
echo "PHP Version: " . phpversion() . "\n";
echo "Required: 7.0 or higher\n";
echo "Status: " . (version_compare(PHP_VERSION, '7.0.0') >= 0 ? "✓ OK" : "✗ Upgrade needed") . "\n\n";

// Check PHP Extensions
$required_extensions = ['pdo', 'pdo_mysql', 'gd', 'json', 'session'];
echo "PHP Extensions:\n";
foreach ($required_extensions as $ext) {
    echo "- $ext: " . (extension_loaded($ext) ? "✓ Loaded" : "✗ Missing") . "\n";
}
echo "\n";

// Check MySQL Connection
echo "Database Connection:\n";
try {
    require_once 'config/database.php';
    $database = new Database();
    $conn = $database->getConnection();
    echo "- Connection: ✓ Successful\n";

    // Check if tables exist
    $tables = ['users', 'posts', 'post_reactions'];
    foreach ($tables as $table) {
        $stmt = $conn->query("SHOW TABLES LIKE '$table'");
        echo "- Table '$table': " . ($stmt->rowCount() > 0 ? "✓ Exists" : "✗ Missing") . "\n";
    }
} catch (Exception $e) {
    echo "- Connection: ✗ Failed - " . $e->getMessage() . "\n";
}
echo "\n";

// Check File Permissions
echo "File Permissions:\n";
$directories = [
    'uploads/profiles',
    'uploads/posts'
];
foreach ($directories as $dir) {
    echo "- $dir: ";
    if (file_exists($dir)) {
        echo "✓ Exists, ";
        echo (is_writable($dir) ? "✓ Writable" : "✗ Not writable");
    } else {
        echo "✗ Missing";
    }
    echo "\n";
}
echo "\n";

// Environment Variables
echo "Environment Variables:\n";
$env_vars = [
    'DB_HOST',
    'DB_NAME',
    'DB_USER',
    'BASE_URL',
    'UPLOAD_DIR_PROFILES',
    'UPLOAD_DIR_POSTS'
];
foreach ($env_vars as $var) {
    $value = getenv($var);
    // Hide password
    if ($var === 'DB_PASS' && !empty($value)) {
        $value = '********';
    }
    echo "- $var: " . (!empty($value) ? "✓ Set to '$value'" : "✗ Not set") . "\n";
}
echo "\n";

// Server Information
echo "Server Information:\n";
echo "- Server software: " . $_SERVER['SERVER_SOFTWARE'] . "\n";
echo "- Document root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "- Request URI: " . $_SERVER['REQUEST_URI'] . "\n\n";

echo "==== Verification Complete ====\n";
echo "You may delete this file after reviewing the results.\n";
