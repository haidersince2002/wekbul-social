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
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'ConnectHub'; ?></title>
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
            <div class="container">
                <div class="header-content">
                    <a class="brand" href="<?php echo (isset($_SESSION['user_id']) ? ((strpos($_SERVER['SCRIPT_NAME'], '/auth/') !== false) ? '../home.php' : 'home.php') : ((strpos($_SERVER['SCRIPT_NAME'], '/auth/') !== false) ? 'login.php' : 'auth/login.php')); ?>">
                        <span>ConnectHub</span>
                    </a>
                    <nav class="nav">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a class="nav-link <?php echo basename($_SERVER['SCRIPT_NAME']) == 'home.php' ? 'active' : ''; ?>" href="<?php echo (strpos($_SERVER['SCRIPT_NAME'], '/auth/') !== false) ? '../home.php' : 'home.php'; ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                                </svg>
                                Home
                            </a>
                            <a class="nav-link <?php echo basename($_SERVER['SCRIPT_NAME']) == 'profile.php' ? 'active' : ''; ?>" href="<?php echo (strpos($_SERVER['SCRIPT_NAME'], '/auth/') !== false) ? '../profile.php' : 'profile.php'; ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                Profile
                            </a>
                            <a class="nav-link" href="<?php echo (strpos($_SERVER['SCRIPT_NAME'], '/auth/') !== false) ? 'logout.php' : 'auth/logout.php'; ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                    <polyline points="16 17 21 12 16 7"></polyline>
                                    <line x1="21" y1="12" x2="9" y2="12"></line>
                                </svg>
                                Logout
                            </a>
                        <?php else: ?>
                            <a class="nav-link <?php echo basename($_SERVER['SCRIPT_NAME']) == 'login.php' ? 'active' : ''; ?>" href="<?php echo (strpos($_SERVER['SCRIPT_NAME'], '/auth/') !== false) ? 'login.php' : 'auth/login.php'; ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                                    <polyline points="10 17 15 12 10 7"></polyline>
                                    <line x1="15" y1="12" x2="3" y2="12"></line>
                                </svg>
                                Login
                            </a>
                            <a class="nav-link <?php echo basename($_SERVER['SCRIPT_NAME']) == 'signup.php' ? 'active' : ''; ?>" href="<?php echo (strpos($_SERVER['SCRIPT_NAME'], '/auth/') !== false) ? 'signup.php' : 'auth/signup.php'; ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="8.5" cy="7" r="4"></circle>
                                    <line x1="20" y1="8" x2="20" y2="14"></line>
                                    <line x1="23" y1="11" x2="17" y2="11"></line>
                                </svg>
                                Sign Up
                            </a>
                        <?php endif; ?>
                    </nav>
                </div>
            </div>
        </header>
        <main class="container">
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script src="<?php echo (strpos($_SERVER['SCRIPT_NAME'], '/auth/') !== false) ? '../' : ''; ?>assets/js/common.js"></script>