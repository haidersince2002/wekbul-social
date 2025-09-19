<?php
// File: api/posts.php
session_start();
require_once '../config/database.php';
require_once '../classes/Post.php';
require_once '../classes/FileUpload.php';
require_once '../includes/csrf.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$database = new Database();
$post = new Post($database);
$response = ['success' => false, 'message' => ''];

header('Content-Type: application/json');
header('Cache-Control: no-store');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        verify_csrf();
        // Add new post
        $description = trim($_POST['description'] ?? '');

        if (empty($description)) {
            throw new Exception('Post description is required');
        }

        $imageName = null;
        if (isset($_FILES['post_image']) && $_FILES['post_image']['error'] === 0) {
            $fileUpload = new FileUpload();
            $uploadResult = $fileUpload->uploadFile($_FILES['post_image'], 'posts');

            if (!$uploadResult['success']) {
                throw new Exception($uploadResult['message']);
            }

            $imageName = $uploadResult['filename'];
        }

        if ($post->create($_SESSION['user_id'], $description, $imageName)) {
            $response['success'] = true;
            $response['message'] = 'Post added successfully!';
        } else {
            throw new Exception('Failed to add post');
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        verify_csrf();
        // Delete post
        parse_str(file_get_contents("php://input"), $data);
        $postId = intval($data['post_id'] ?? 0);

        if ($post->delete($postId, $_SESSION['user_id'])) {
            $response['success'] = true;
            $response['message'] = 'Post deleted successfully!';
        } else {
            throw new Exception('Failed to delete post');
        }
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
