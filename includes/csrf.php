<?php
// Simple CSRF utilities
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function get_csrf_from_headers(): ?string
{
    $headerKey = 'HTTP_X_CSRF_TOKEN';
    if (!empty($_SERVER[$headerKey])) {
        return $_SERVER[$headerKey];
    }

    // Fallback for environments with getallheaders
    if (function_exists('getallheaders')) {
        $headers = getallheaders();
        foreach ($headers as $k => $v) {
            if (strtolower($k) === 'x-csrf-token') {
                return $v;
            }
        }
    }
    return null;
}

function verify_csrf(?string $provided = null): void
{
    $token = $provided ?? ($_POST['csrf_token'] ?? $_GET['csrf_token'] ?? get_csrf_from_headers());
    if (!isset($_SESSION['csrf_token']) || !$token || !hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(419);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid or missing CSRF token']);
        exit;
    }
}
