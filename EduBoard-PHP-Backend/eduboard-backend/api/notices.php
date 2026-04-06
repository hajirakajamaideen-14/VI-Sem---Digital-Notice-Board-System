<?php
// =====================================================
// /api/notices.php — Full CRUD for notices
//
// GET    /api/notices.php              → list notices (public, with filters)
// GET    /api/notices.php?id=5         → single notice
// POST   /api/notices.php              → create notice (admin/faculty)
// PUT    /api/notices.php              → update notice (admin/faculty)
// DELETE /api/notices.php?id=5         → delete notice (admin/faculty)
//
// Query params for GET list:
//   ?dept=CSE&category=Exam&search=keyword&status=Active
// =====================================================

require_once __DIR__ . '/../includes/auth.php';
setCORS();

$method = $_SERVER['REQUEST_METHOD'];
$db     = getDB();

// --------------------------------------------------
// GET — list or single notice (public)
// --------------------------------------------------
if ($method === 'GET') {
    // Auto-expire notices past their expiry date
    $db->exec("UPDATE notices SET status = 'Expired' WHERE expiry_date < CURDATE() AND status = 'Active'");

    if (!empty($_GET['id'])) {
        $stmt = $db->prepare("
            SELECT n.*, u.name AS posted_by_name
            FROM notices n
            JOIN users u ON n.posted_by = u.id
            WHERE n.id = ?
        ");
        $stmt->execute([(int)$_GET['id']]);
        $notice = $stmt->fetch();
        if (!$notice) respond(false, 'Notice not found.');
        respond(true, 'OK', ['notice' => $notice]);
    }

    // Build filtered list query
    $where  = ['1=1'];
    $params = [];

    if (!empty($_GET['dept'])) {
        $where[] = 'n.department = ?';
        $params[] = clean($_GET['dept']);
    }
    if (!empty($_GET['category'])) {
        $where[] = 'n.category = ?';
        $params[] = clean($_GET['category']);
    }
    if (!empty($_GET['status'])) {
        $where[] = 'n.status = ?';
        $params[] = clean($_GET['status']);
    }
    if (!empty($_GET['search'])) {
        $where[] = '(n.title LIKE ? OR n.content LIKE ?)';
        $term     = '%' . clean($_GET['search']) . '%';
        $params[] = $term;
        $params[] = $term;
    }
    // Faculty: only see their own notices (if role param passed)
    if (!empty($_GET['mine']) && !empty($_GET['user_id'])) {
        $where[] = 'n.posted_by = ?';
        $params[] = (int)$_GET['user_id'];
    }

    $sql  = "SELECT n.id, n.title, n.content, n.department, n.category, n.priority, n.status, n.expiry_date, n.created_at, u.name AS posted_by_name
             FROM notices n
             JOIN users u ON n.posted_by = u.id
             WHERE " . implode(' AND ', $where) . "
             ORDER BY n.priority = 'High' DESC, n.created_at DESC";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $notices = $stmt->fetchAll();

    respond(true, 'OK', ['notices' => $notices, 'count' => count($notices)]);
}

// --------------------------------------------------
// POST — create a notice (admin or faculty)
// --------------------------------------------------
if ($method === 'POST') {
    $user = requireRole(['admin', 'faculty']);
    $body = getBody();

    $title    = clean($body['title'] ?? '');
    $content  = clean($body['content'] ?? '');
    $dept     = clean($body['department'] ?? 'General');
    $category = clean($body['category'] ?? 'Academic');
    $priority = clean($body['priority'] ?? 'Medium');
    $expiry   = clean($body['expiry_date'] ?? '');

    if (!$title || !$content || !$expiry) {
        respond(false, 'Title, content, and expiry date are required.');
    }

    // Faculty can only post to their own department
    if ($user['role'] === 'faculty') {
        $dept = $user['department'];
    }

    $stmt = $db->prepare("
        INSERT INTO notices (title, content, department, category, priority, expiry_date, posted_by)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$title, $content, $dept, $category, $priority, $expiry, $user['id']]);

    respond(true, 'Notice created successfully.', ['id' => $db->lastInsertId()]);
}

// --------------------------------------------------
// PUT — update a notice
// --------------------------------------------------
if ($method === 'PUT') {
    $user = requireRole(['admin', 'faculty']);
    $body = getBody();

    $id = (int)($body['id'] ?? 0);
    if (!$id) respond(false, 'Notice ID is required.');

    // Check ownership (faculty can only edit own notices)
    $stmt = $db->prepare("SELECT posted_by, department FROM notices WHERE id = ?");
    $stmt->execute([$id]);
    $notice = $stmt->fetch();

    if (!$notice) respond(false, 'Notice not found.');
    if ($user['role'] === 'faculty' && $notice['posted_by'] != $user['id']) {
        respond(false, 'You can only edit your own notices.');
    }

    $title    = clean($body['title']    ?? '');
    $content  = clean($body['content']  ?? '');
    $dept     = clean($body['department'] ?? $notice['department']);
    $category = clean($body['category'] ?? 'Academic');
    $priority = clean($body['priority'] ?? 'Medium');
    $expiry   = clean($body['expiry_date'] ?? '');

    if (!$title || !$content || !$expiry) {
        respond(false, 'Title, content, and expiry date are required.');
    }

    if ($user['role'] === 'faculty') $dept = $user['department'];

    $stmt = $db->prepare("
        UPDATE notices SET title=?, content=?, department=?, category=?, priority=?, expiry_date=?
        WHERE id=?
    ");
    $stmt->execute([$title, $content, $dept, $category, $priority, $expiry, $id]);

    respond(true, 'Notice updated successfully.');
}

// --------------------------------------------------
// DELETE — remove a notice
// --------------------------------------------------
if ($method === 'DELETE') {
    $user = requireRole(['admin', 'faculty']);
    $id   = (int)($_GET['id'] ?? 0);
    if (!$id) respond(false, 'Notice ID is required.');

    $stmt = $db->prepare("SELECT posted_by FROM notices WHERE id = ?");
    $stmt->execute([$id]);
    $notice = $stmt->fetch();

    if (!$notice) respond(false, 'Notice not found.');
    if ($user['role'] === 'faculty' && $notice['posted_by'] != $user['id']) {
        respond(false, 'You can only delete your own notices.');
    }

    $stmt = $db->prepare("DELETE FROM notices WHERE id = ?");
    $stmt->execute([$id]);

    respond(true, 'Notice deleted successfully.');
}

respond(false, 'Method not allowed.');
