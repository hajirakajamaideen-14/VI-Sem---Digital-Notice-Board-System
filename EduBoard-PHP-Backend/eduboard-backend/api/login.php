<?php
// =====================================================
// POST /api/login.php
// Body: { "email": "...", "password": "..." }
// Returns: { success, message, user: { id, name, role, department } }
// =====================================================

require_once __DIR__ . '/../includes/auth.php';
setCORS();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(false, 'Method not allowed.');
}

$body    = getBody();
$email   = clean($body['email'] ?? '');
$password = $body['password'] ?? '';

if (!$email || !$password) {
    respond(false, 'Email and password are required.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond(false, 'Invalid email format.');
}

$db   = getDB();
$stmt = $db->prepare("SELECT id, name, email, password, role, department FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
    respond(false, 'Invalid email or password.');
}

// Start session and store user info
startSession();
session_regenerate_id(true);

$_SESSION['user_id']    = $user['id'];
$_SESSION['user_name']  = $user['name'];
$_SESSION['user_email'] = $user['email'];
$_SESSION['user_role']  = $user['role'];
$_SESSION['user_dept']  = $user['department'];

respond(true, 'Login successful.', [
    'user' => [
        'id'         => $user['id'],
        'name'       => $user['name'],
        'email'      => $user['email'],
        'role'       => $user['role'],
        'department' => $user['department'],
    ]
]);
