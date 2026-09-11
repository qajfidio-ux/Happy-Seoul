<?php
/**
 * api/logout.php - Log out user, destroy session
 */

require_once __DIR__ . '/../db.php';

ensure_session_started();

$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

session_destroy();

$isJson = (
    (isset($_SERVER['HTTP_ACCEPT']) && stripos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) ||
    (isset($_GET['format']) && $_GET['format'] === 'json')
);

if ($isJson) {
    send_json(['success' => true, 'message' => 'You have been logged out successfully.', 'redirect' => 'user.html']);
} else {
    header('Location: ../user.html');
    exit;
}

