<?php
// =====================================================
// EduBoard — Database Configuration
// Edit these values to match your hosting environment
// =====================================================

define('DB_HOST', 'localhost');
define('DB_NAME', 'eduboard');
define('DB_USER', 'root');       // Change to your MySQL username
define('DB_PASS', '');           // Change to your MySQL password
define('DB_CHARSET', 'utf8mb4');

// JWT Secret Key (change this to a long random string)
define('JWT_SECRET', 'eduboard_secret_key_change_this_2025');

// App settings
define('APP_NAME', 'EduBoard');
define('SESSION_LIFETIME', 3600); // 1 hour in seconds

// =====================================================
// DO NOT EDIT BELOW THIS LINE
// =====================================================

function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
            exit;
        }
    }
    return $pdo;
}
