<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../includes/csrf.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Social Network'; ?></title>
    <link rel="stylesheet" href="<?php echo (strpos($_SERVER['SCRIPT_NAME'], '/auth/') !== false) ? '../' : ''; ?>assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <script>
        window.CSRF_TOKEN = '<?php echo csrf_token(); ?>';
    </script>
</head>

<body>
    <div class="site-wrap">
        <header class="topbar">
            <div class="container row space-between center">
                <a class="brand" href="<?php echo (isset($_SESSION['user_id']) ? ((strpos($_SERVER['SCRIPT_NAME'], '/auth/') !== false) ? '../home.php' : 'home.php') : ((strpos($_SERVER['SCRIPT_NAME'], '/auth/') !== false) ? 'login.php' : 'auth/login.php')); ?>">📱 Social Network</a>
                <nav class="nav">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a class="nav-link" href="<?php echo (strpos($_SERVER['SCRIPT_NAME'], '/auth/') !== false) ? '../home.php' : 'home.php'; ?>">Home</a>
                        <a class="nav-link" href="<?php echo (strpos($_SERVER['SCRIPT_NAME'], '/auth/') !== false) ? '../profile.php' : 'profile.php'; ?>">Profile</a>
                        <a class="nav-link" href="<?php echo (strpos($_SERVER['SCRIPT_NAME'], '/auth/') !== false) ? 'logout.php' : 'auth/logout.php'; ?>">Logout</a>
                        <button id="themeToggle" class="theme-toggle" type="button" aria-label="Toggle theme">Toggle Theme</button>
                    <?php else: ?>
                        <a class="nav-link" href="<?php echo (strpos($_SERVER['SCRIPT_NAME'], '/auth/') !== false) ? 'login.php' : 'auth/login.php'; ?>">Login</a>
                        <a class="nav-link" href="<?php echo (strpos($_SERVER['SCRIPT_NAME'], '/auth/') !== false) ? 'signup.php' : 'auth/signup.php'; ?>">Sign Up</a>
                        <button id="themeToggle" class="theme-toggle" type="button" aria-label="Toggle theme">Toggle Theme</button>
                    <?php endif; ?>
                </nav>
            </div>
        </header>
        <main class="container">
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script src="<?php echo (strpos($_SERVER['SCRIPT_NAME'], '/auth/') !== false) ? '../' : ''; ?>assets/js/common.js"></script>