<?php
// File: api/reactions.php
session_start();
require_once '../config/database.php';
require_once '../classes/Post.php';
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
        $postId = intval($_POST['post_id'] ?? 0);
        $reactionType = $_POST['reaction_type'] ?? '';

        if (!in_array($reactionType, ['like', 'dislike'])) {
            throw new Exception('Invalid reaction type');
        }

        if ($post->addReaction($postId, $_SESSION['user_id'], $reactionType)) {
            $reactions = $post->getReactionCounts($postId);
            $response['success'] = true;
            $response['reactions'] = $reactions;
        } else {
            throw new Exception('Failed to add reaction');
        }
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
