<?php
// File: render-setup.php
// Helper script to parse a Render MySQL connection URL into environment variables
// Run this script after getting your database URL from Render
// Example usage: php render-setup.php mysql://user:pass@host:port/dbname

// Check if a URL was provided
if ($argc < 2) {
    echo "Usage: php render-setup.php <mysql-connection-url>\n";
    echo "Example: php render-setup.php mysql://username:password@host:port/database\n";
    exit(1);
}

// Get the URL from command line
$url = $argv[1];
echo "Parsing connection URL: $url\n\n";

// Parse the URL
if (!preg_match('/^mysql:\/\/([^:]+):([^@]+)@([^:\/]+)(?::(\d+))?\/(.+)$/', $url, $matches)) {
    echo "Error: Invalid MySQL connection URL format.\n";
    echo "Expected format: mysql://username:password@host:port/database\n";
    exit(1);
}

// Extract components
$username = $matches[1];
$password = $matches[2];
$host = $matches[3];
$port = isset($matches[4]) ? $matches[4] : '3306';
$database = $matches[5];

// Display the parsed values
echo "==== Parsed Database Connection Details ====\n";
echo "Host: $host\n";
echo "Port: $port\n";
echo "Database: $database\n";
echo "Username: $username\n";
echo "Password: " . str_repeat('*', strlen($password)) . "\n\n";

// Generate environment variable settings
echo "==== Environment Variables for Render ====\n";
echo "Add these environment variables to your Render web service:\n\n";

echo "DB_HOST=$host\n";
echo "DB_NAME=$database\n";
echo "DB_USER=$username\n";
echo "DB_PASS=$password\n";
echo "BASE_URL=https://your-app-name.onrender.com\n";
echo "DEBUG=false\n";
echo "UPLOAD_DIR_PROFILES=uploads/profiles\n";
echo "UPLOAD_DIR_POSTS=uploads/posts\n\n";

// Generate .env file
echo "==== Creating .env.render file ====\n";
$envContent = <<<EOT
# Render Environment Variables
# Generated from connection URL: $url
# Date: " . date('Y-m-d H:i:s') . "

DB_HOST=$host
DB_NAME=$database
DB_USER=$username
DB_PASS=$password
BASE_URL=https://your-app-name.onrender.com
DEBUG=false
UPLOAD_DIR_PROFILES=uploads/profiles
UPLOAD_DIR_POSTS=uploads/posts
EOT;

file_put_contents('.env.render', $envContent);
echo "Created .env.render file with environment variables.\n";
echo "IMPORTANT: Do NOT commit this file to your repository.\n";
echo "Use it only to copy values to the Render dashboard.\n";

echo "\n==== Next Steps ====\n";
echo "1. Go to your Render web service dashboard\n";
echo "2. Navigate to the Environment tab\n";
echo "3. Add each variable above as a key-value pair\n";
echo "4. Update BASE_URL with your actual Render URL\n";
echo "5. Click 'Save Changes'\n";
echo "6. Redeploy your application\n";
