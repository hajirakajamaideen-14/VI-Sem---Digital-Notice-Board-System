<?php
// =====================================================
// GET /api/me.php — Check current session / logged-in user
// Returns the logged-in user's info, or 401 if not logged in
// =====================================================

require_once __DIR__ . '/../includes/auth.php';
setCORS();

startSession();

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    respond(false, 'Not logged in.', ['redirect' => true]);
}

respond(true, 'Authenticated.', [
    'user' => [
        'id'         => $_SESSION['user_id'],
        'name'       => $_SESSION['user_name'],
        'email'      => $_SESSION['user_email'],
        'role'       => $_SESSION['user_role'],
        'department' => $_SESSION['user_dept'],
    ]
]);
