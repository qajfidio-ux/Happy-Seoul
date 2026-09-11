<?php
/**
 * api/signup.php - User Registration Handler
 */

require_once __DIR__ . '/../db.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json(['success' => false, 'message' => 'Method not allowed. Use POST.'], 405);
}

$data = get_request_data();

$username = trim($data['username'] ?? '');
$email    = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

// Validation
if (empty($username) || empty($email) || empty($password)) {
    send_json(['success' => false, 'message' => 'Please fill in all required fields.'], 400);
}

if (strlen($username) < 3) {
    send_json(['success' => false, 'message' => 'Username must be at least 3 characters long.'], 400);
}

if (!preg_match('/^[a-zA-Z0-9_.-]+$/', $username)) {
    send_json(['success' => false, 'message' => 'Username can only contain letters, numbers, underscores, and hyphens.'], 400);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    send_json(['success' => false, 'message' => 'Please enter a valid email address.'], 400);
}

if (strlen($password) < 4) {
    send_json(['success' => false, 'message' => 'Password must be at least 4 characters long.'], 400);
}

$pdo = getDBConnection();

// Check if username already exists
$stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? LIMIT 1");
$stmt->execute([$username]);
if ($stmt->fetch()) {
    send_json(['success' => false, 'message' => 'This username is already taken. Please choose another.'], 409);
}

// Check if email already exists
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    send_json(['success' => false, 'message' => 'An account with this email address already exists.'], 409);
}

// Hash password securely
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Insert new user
$insertStmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
$insertStmt->execute([$username, $email, $hashedPassword]);

$newUserId = (int)$pdo->lastInsertId();

// Establish session
ensure_session_started();
$_SESSION['user_id']  = $newUserId;
$_SESSION['username'] = $username;
$_SESSION['email']    = $email;

send_json([
    'success'  => true,
    'message'  => 'Welcome to Happy Seoul, ' . htmlspecialchars($username) . '! Account created successfully.',
    'redirect' => 'Home.html',
    'user'     => [
        'id'       => $newUserId,
        'username' => $username,
        'email'    => $email
    ]
], 201);

