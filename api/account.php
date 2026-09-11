<?php
/**
 * api/account.php - Account Profile Retrieval & Update
 */

require_once __DIR__ . '/../db.php';

header('Content-Type: application/json; charset=utf-8');

ensure_session_started();

if (empty($_SESSION['user_id'])) {
    send_json(['success' => false, 'message' => 'Please sign in to access your account.', 'redirect' => 'user.html'], 401);
}

$userId = (int)$_SESSION['user_id'];
$pdo = getDBConnection();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->prepare("SELECT id, username, email, full_name, phone, address, city, zip, card_name, card_number, exp_month, exp_year, created_at FROM users WHERE id = ? LIMIT 1");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    if (!$user) {
        send_json(['success' => false, 'message' => 'User not found.'], 404);
    }

    send_json([
        'success' => true,
        'user'    => $user
    ]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = get_request_data();

    $fullName   = trim($data['firstname'] ?? $data['full_name'] ?? '');
    $phone      = trim($data['phone'] ?? '');
    $address    = trim($data['address'] ?? '');
    $city       = trim($data['city'] ?? '');
    $zip        = trim($data['zip'] ?? '');
    $cardName   = trim($data['cardname'] ?? $data['card_name'] ?? '');
    $cardNumber = trim($data['cardnumber'] ?? $data['card_number'] ?? '');
    $expMonth   = trim($data['expmonth'] ?? $data['exp_month'] ?? '');
    $expYear    = trim($data['expyear'] ?? $data['exp_year'] ?? '');

    $stmt = $pdo->prepare("UPDATE users SET 
        full_name   = ?,
        phone       = ?,
        address     = ?,
        city        = ?,
        zip         = ?,
        card_name   = ?,
        card_number = ?,
        exp_month   = ?,
        exp_year    = ?
        WHERE id = ?");

    $stmt->execute([
        $fullName,
        $phone,
        $address,
        $city,
        $zip,
        $cardName,
        $cardNumber,
        $expMonth,
        $expYear,
        $userId
    ]);

    send_json([
        'success' => true,
        'message' => 'Your account information has been updated successfully!'
    ]);
}

send_json(['success' => false, 'message' => 'Method not allowed.'], 405);

