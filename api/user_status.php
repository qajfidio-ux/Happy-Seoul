<?php
/**
 * api/user_status.php - Returns current session authentication state
 */

require_once __DIR__ . '/../db.php';

header('Content-Type: application/json; charset=utf-8');

ensure_session_started();

if (empty($_SESSION['user_id'])) {
    send_json([
        'isLoggedIn' => false,
        'user'       => null
    ]);
}

$pdo = getDBConnection();
$stmt = $pdo->prepare("SELECT id, username, email, full_name, phone, address, city, zip FROM users WHERE id = ? LIMIT 1");
$stmt->execute([(int)$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
    // Session exists but user was deleted
    $_SESSION = [];
    session_destroy();
    send_json([
        'isLoggedIn' => false,
        'user'       => null
    ]);
}

send_json([
    'isLoggedIn' => true,
    'user'       => [
        'id'       => (int)$user['id'],
        'username' => $user['username'],
        'email'    => $user['email'],
        'fullName' => $user['full_name'] ?? $user['username']
    ]
]);

