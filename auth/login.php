<?php
session_start();
require_once '../config/database.php';
require_once '../classes/User.php';
require_once __DIR__ . '/../includes/csrf.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: ../home.php');  // Changed from profile.php
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF check
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
        header('Content-Type: application/json');
        http_response_code(419);
        echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
        exit;
    }
    $response = ['success' => false, 'message' => ''];

    try {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            throw new Exception('Email and password are required');
        }

        $database = new Database();
        $user = new User($database);

        $userData = $user->login($email, $password);

        if ($userData) {
            // Regenerate session ID for security
            session_regenerate_id(true);

            $_SESSION['user_id'] = $userData['id'];
            $_SESSION['user_name'] = $userData['full_name'];
            $_SESSION['user_email'] = $userData['email'];

            $response['success'] = true;
            $response['message'] = 'Login successful!';
            $response['redirect'] = '../home.php';  // Changed redirect
        } else {
            throw new Exception('Invalid email or password');
        }
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>
<?php $pageTitle = 'Login - Social Network';
include __DIR__ . '/../includes/header.php'; ?>
<div class="container">
    <div class="auth-form">
        <h2>🔐 Welcome Back</h2>
        <p style="text-align: center; color: #666; margin-bottom: 25px;">Sign in to your social network</p>

        <form id="loginForm">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <div class="form-group">
                <label for="email">📧 Email Address</label>
                <input type="email" id="email" name="email" required>
                <span class="error-message"></span>
            </div>

            <div class="form-group">
                <label for="password">🔒 Password</label>
                <input type="password" id="password" name="password" required>
                <span class="error-message"></span>
            </div>

            <button type="submit" class="btn-primary">🚀 Sign In</button>

            <div class="auth-link">
                New here? <a href="signup.php">Create your account</a>
            </div>
        </form>
    </div>
</div>

<script src="../assets/js/login.js"></script>
<?php include __DIR__ . '/../includes/footer.php'; ?>