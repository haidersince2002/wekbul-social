<?php
// File: db-init.php
// This script initializes the database for deployment

require_once 'config/database.php';

echo "==== Database Initialization Script ====\n";
echo "Starting database initialization...\n";

try {
    // Connect to the database
    $database = new Database();
    $conn = $database->getConnection();

    // Display connection info (without credentials)
    echo "Connected to database: " . getenv('DB_NAME') . " on host: " . getenv('DB_HOST') . "\n";

    // Read the SQL from database.sql file
    echo "Reading SQL schema from database.sql...\n";
    $sql = file_get_contents('database.sql');

    // Remove USE statement as Render will set the database
    $sql = preg_replace('/USE.*?;/s', '', $sql);

    // Remove CREATE DATABASE statement as database is already created on Render
    $sql = preg_replace('/CREATE DATABASE.*?;/s', '', $sql);

    // Split into separate statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    echo "Found " . count($statements) . " SQL statements to execute\n";

    // Execute each statement
    $executed = 0;
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            try {
                echo "Executing: " . substr($statement, 0, 40) . "...\n";
                $conn->exec($statement);
                $executed++;
                echo "✓ Success\n";
            } catch (PDOException $e) {
                // If table already exists, this is ok to continue
                if (strpos($e->getMessage(), 'already exists') !== false) {
                    echo "ℹ Table already exists, continuing...\n";
                    $executed++;
                } else {
                    echo "✗ Error: " . $e->getMessage() . "\n";
                    // Don't throw, try to continue with other statements
                }
            }
        }
    }

    echo "\n==== Summary ====\n";
    echo "Successfully executed $executed out of " . count($statements) . " SQL statements\n";
    echo "Database initialization completed\n";

    // Create default profile image if not exists
    $defaultProfilePath = 'uploads/profiles/default.jpg';
    if (!file_exists($defaultProfilePath)) {
        echo "Creating default profile image...\n";
        if (copy('assets/images/default.jpg', $defaultProfilePath)) {
            echo "✓ Default profile image created successfully\n";
        } else {
            echo "✗ Failed to create default profile image\n";
        }
    }
} catch (PDOException $e) {
    echo "✗ Database initialization failed: " . $e->getMessage() . "\n";
    exit(1);
}

echo "==== Database initialization completed successfully ====\n";
