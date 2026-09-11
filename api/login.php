<?php
/**
 * api/login.php - User Login Authentication Handler
 */

require_once __DIR__ . '/../db.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json(['success' => false, 'message' => 'Method not allowed. Use POST.'], 405);
}

$data = get_request_data();

$loginIdentifier = trim($data['username'] ?? $data['login-name'] ?? '');
$password        = $data['password'] ?? $data['login-password'] ?? '';
$remember        = !empty($data['remember']);

if (empty($loginIdentifier) || empty($password)) {
    send_json(['success' => false, 'message' => 'Please enter both username and password.'], 400);
}

$pdo = getDBConnection();

// Find user by username or email
$stmt = $pdo->prepare("SELECT id, username, email, password FROM users WHERE username = ? OR email = ? LIMIT 1");
$stmt->execute([$loginIdentifier, $loginIdentifier]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
    send_json(['success' => false, 'message' => 'Incorrect username or password. Please try again.'], 401);
}

// Rehash if algorithm cost changed
if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
    $newHash = password_hash($password, PASSWORD_DEFAULT);
    $updateStmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
    $updateStmt->execute([$newHash, $user['id']]);
}

// Establish session
ensure_session_started();
$_SESSION['user_id']  = (int)$user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['email']    = $user['email'];

if ($remember) {
    // Extend session cookie to 30 days
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        session_id(),
        time() + (86400 * 30),
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

send_json([
    'success'  => true,
    'message'  => 'Welcome back, ' . htmlspecialchars($user['username']) . '!',
    'redirect' => 'Home.html',
    'user'     => [
        'id'       => (int)$user['id'],
        'username' => $user['username'],
        'email'    => $user['email']
    ]
]);

