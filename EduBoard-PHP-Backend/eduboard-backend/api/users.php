<?php
// =====================================================
// /api/users.php — Admin-only user management
//
// GET    /api/users.php           → list all users
// POST   /api/users.php           → create user
// PUT    /api/users.php           → update user
// DELETE /api/users.php?id=5      → delete user
// =====================================================

require_once __DIR__ . '/../includes/auth.php';
setCORS();

$user   = requireRole('admin');
$method = $_SERVER['REQUEST_METHOD'];
$db     = getDB();

if ($method === 'GET') {
    $stmt = $db->query("SELECT id, name, email, role, department, created_at FROM users ORDER BY role, name");
    respond(true, 'OK', ['users' => $stmt->fetchAll()]);
}

if ($method === 'POST') {
    $body  = getBody();
    $name  = clean($body['name'] ?? '');
    $email = clean($body['email'] ?? '');
    $pass  = $body['password'] ?? '';
    $role  = clean($body['role'] ?? 'faculty');
    $dept  = clean($body['department'] ?? 'General');

    if (!$name || !$email || !$pass) respond(false, 'Name, email, and password are required.');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) respond(false, 'Invalid email.');
    if (!in_array($role, ['admin','faculty','student'])) respond(false, 'Invalid role.');

    $hashed = password_hash($pass, PASSWORD_BCRYPT);

    try {
        $stmt = $db->prepare("INSERT INTO users (name, email, password, role, department) VALUES (?,?,?,?,?)");
        $stmt->execute([$name, $email, $hashed, $role, $dept]);
        respond(true, 'User created.', ['id' => $db->lastInsertId()]);
    } catch (PDOException $e) {
        respond(false, 'Email already exists.');
    }
}

if ($method === 'PUT') {
    $body = getBody();
    $id   = (int)($body['id'] ?? 0);
    if (!$id) respond(false, 'User ID required.');

    $name = clean($body['name'] ?? '');
    $dept = clean($body['department'] ?? 'General');
    $role = clean($body['role'] ?? 'faculty');

    $stmt = $db->prepare("UPDATE users SET name=?, department=?, role=? WHERE id=?");
    $stmt->execute([$name, $dept, $role, $id]);
    respond(true, 'User updated.');
}

if ($method === 'DELETE') {
    $id = (int)($_GET['id'] ?? 0);
    if (!$id) respond(false, 'User ID required.');
    if ($id === $user['id']) respond(false, 'Cannot delete your own account.');
    $stmt = $db->prepare("DELETE FROM users WHERE id=?");
    $stmt->execute([$id]);
    respond(true, 'User deleted.');
}

respond(false, 'Method not allowed.');
