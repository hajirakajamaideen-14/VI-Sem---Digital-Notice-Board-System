<?php
// =====================================================
// POST /api/logout.php
// Destroys the session and logs the user out
// =====================================================

require_once __DIR__ . '/../includes/auth.php';
setCORS();

startSession();
session_unset();
session_destroy();

respond(true, 'Logged out successfully.');
