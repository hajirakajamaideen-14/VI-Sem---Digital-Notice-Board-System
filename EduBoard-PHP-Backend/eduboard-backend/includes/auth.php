<?php
// =====================================================
// EduBoard — Auth & Response Helpers
// =====================================================

require_once __DIR__ . '/../config/db.php';

// Start session safely
function startSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

// Send JSON response and exit
function respond($success, $message, $data = []) {
    header('Content-Type: application/json');
    echo json_encode(array_merge(
        ['success' => $success, 'message' => $message],
        $data
    ));
    exit;
}

// Set CORS headers (adjust origin in production)
function setCORS() {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }
}

// Get logged-in user from session
function getAuthUser() {
    startSession();
    if (empty($_SESSION['user_id'])) {
        respond(false, 'Unauthorized. Please log in.', ['redirect' => true]);
    }
    return [
        'id'         => $_SESSION['user_id'],
        'name'       => $_SESSION['user_name'],
        'email'      => $_SESSION['user_email'],
        'role'       => $_SESSION['user_role'],
        'department' => $_SESSION['user_dept'],
    ];
}

// Require a specific role
function requireRole($roles) {
    $user = getAuthUser();
    $roles = (array) $roles;
    if (!in_array($user['role'], $roles)) {
        respond(false, 'Access denied. Insufficient permissions.');
    }
    return $user;
}

// Get JSON request body
function getBody() {
    $raw = file_get_contents('php://input');
    return json_decode($raw, true) ?? [];
}

// Sanitize string input
function clean($str) {
    return htmlspecialchars(trim((string)$str), ENT_QUOTES, 'UTF-8');
}
