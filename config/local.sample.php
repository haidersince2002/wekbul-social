<?php
// File: config/local.sample.php
// Copy this file to local.php and update with your local settings
// Do NOT commit local.php to version control

// Database credentials
putenv('DB_HOST=localhost');
putenv('DB_NAME=social_network');
putenv('DB_USER=root');
putenv('DB_PASS=your_password_here');

// Base URL (for asset paths, etc.)
putenv('BASE_URL=http://localhost/Webkul-Project');

// Debug mode (set to false in production)
putenv('DEBUG=true');

// Upload directories (update if necessary)
putenv('UPLOAD_DIR_PROFILES=uploads/profiles');
putenv('UPLOAD_DIR_POSTS=uploads/posts');
