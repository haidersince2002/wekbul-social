<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once '../config/database.php';
require_once '../classes/User.php';
require_once '../classes/FileUpload.php';
require_once __DIR__ . '/../includes/csrf.php';

// Set page title
$pageTitle = 'Sign Up - ConnectHub';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
        header('Content-Type: application/json');
        http_response_code(419);
        echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
        exit;
    }
    $response = ['success' => false, 'message' => ''];

    try {
        // Validate input
        $fullName = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $age = intval($_POST['age'] ?? 0);

        // Server-side validation
        if (empty($fullName) || strlen($fullName) < 2) {
            throw new Exception('Full name must be at least 2 characters');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email format');
        }

        if (strlen($password) < 8 || !preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
            throw new Exception('Password must be at least 8 characters with 1 special character');
        }

        if ($age < 13 || $age > 120) {
            throw new Exception('Age must be between 13 and 120');
        }

        // Initialize database and user
        $database = new Database();
        $user = new User($database);

        // Check if email exists
        if ($user->emailExists($email)) {
            throw new Exception('Email already exists');
        }

        // Handle profile picture upload
        $profilePicture = 'default.jpg';
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
            $fileUpload = new FileUpload();
            $uploadResult = $fileUpload->uploadFile($_FILES['profile_picture'], 'profiles');

            if (!$uploadResult['success']) {
                throw new Exception($uploadResult['message']);
            }

            $profilePicture = $uploadResult['filename'];
        }

        // Create user
        $userData = [
            'full_name' => $fullName,
            'email' => $email,
            'password' => $password,
            'age' => $age,
            'profile_picture' => $profilePicture
        ];

        if ($user->create($userData)) {
            $response['success'] = true;
            $response['message'] = 'Account created successfully!';
        } else {
            throw new Exception('Failed to create account');
        }
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>
<?php $pageTitle = 'Sign Up - Social Network';
include __DIR__ . '/../includes/header.php'; ?>
<div class="container">
    <div class="auth-form">
        <h2>Create Your Account</h2>
        <p style="text-align: center; color: var(--text-light); margin-bottom: 28px; font-size: 1.05rem;">Join our social network community</p>

        <form id="signupForm" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" placeholder="Your full name" required>
                <span class="error-message"></span>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="your@email.com" required>
                <span class="error-message"></span>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Create a strong password" required>
                <small>Minimum 8 characters with at least 1 special character</small>
                <span class="error-message"></span>
            </div>

            <div class="form-group">
                <label for="age">Age</label>
                <input type="number" id="age" name="age" min="13" max="120" placeholder="Your age" required>
                <small>You must be at least 13 years old</small>
                <span class="error-message"></span>
            </div>

            <div class="form-group">
                <label for="profile_picture">Profile Picture</label>
                <div class="file-input-wrapper">
                    <input type="file" id="profile_picture" name="profile_picture" accept="image/jpeg,image/jpg,image/png">
                    <small>Optional. JPG or PNG files up to 5MB</small>
                </div>
                <span class="error-message"></span>
            </div>

            <button type="submit" class="btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="8.5" cy="7" r="4"></circle>
                    <line x1="20" y1="8" x2="20" y2="14"></line>
                    <line x1="23" y1="11" x2="17" y2="11"></line>
                </svg>
                Create Account
            </button>

            <div class="auth-link">
                Already have an account? <a href="login.php">Login here</a>
            </div>
        </form>
    </div>
</div>

<script src="../assets/js/signup.js"></script>
<?php include __DIR__ . '/../includes/footer.php'; ?>