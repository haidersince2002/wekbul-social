<?php
// File: api/profile.php
session_start();
require_once '../config/database.php';
require_once '../classes/User.php';
require_once '../classes/FileUpload.php';
require_once '../includes/csrf.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$database = new Database();
$user = new User($database);
$response = ['success' => false, 'message' => ''];

header('Content-Type: application/json');
header('Cache-Control: no-store');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        verify_csrf();
        if (isset($_FILES['profile_picture'])) {
            // Handle profile picture upload
            $fileUpload = new FileUpload();
            $uploadResult = $fileUpload->uploadFile($_FILES['profile_picture'], 'profiles');

            if (!$uploadResult['success']) {
                throw new Exception($uploadResult['message']);
            }

            $updateData = ['profile_picture' => $uploadResult['filename']];

            if ($user->updateProfile($_SESSION['user_id'], $updateData)) {
                $response['success'] = true;
                $response['filename'] = $uploadResult['filename'];
                $response['message'] = 'Profile picture updated successfully!';
            } else {
                throw new Exception('Failed to update profile picture');
            }
        } else {
            // Handle profile field update
            $field = $_POST['field'] ?? '';
            $value = trim($_POST['value'] ?? '');

            if (empty($field) || empty($value)) {
                throw new Exception('Field and value are required');
            }

            if (!in_array($field, ['full_name', 'age'])) {
                throw new Exception('Invalid field');
            }

            if ($field === 'age') {
                $value = intval($value);
                if ($value < 13 || $value > 120) {
                    throw new Exception('Age must be between 13 and 120');
                }
            }

            $updateData = [];
            if ($field === 'full_name') {
                $updateData['full_name'] = $value;
            } elseif ($field === 'age') {
                $updateData['age'] = $value;
            }

            if ($user->updateProfile($_SESSION['user_id'], $updateData)) {
                $response['success'] = true;
                $response['message'] = 'Profile updated successfully!';
            } else {
                throw new Exception('Failed to update profile');
            }
        }
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
