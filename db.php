<?php
/**
 * db.php - Database connection & auto-bootstrapping for Happy Seoul
 * Designed for XAMPP (Apache + MariaDB/MySQL)
 */

define('DB_HOST', 'localhost');
define('DB_PORT', 3306);
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'happy_seoul_db');

function getDBConnection(): PDO {
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $charset = 'utf8mb4';
    
    try {
        // First connect to MySQL server without database specified to ensure DB exists
        $serverDsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=" . $charset;
        $tempPdo = new PDO($serverDsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);

        // Auto-create database if it does not exist
        $tempPdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

        // Now connect to the specific database
        $dbDsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . $charset;
        $pdo = new PDO($dbDsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);

        // Auto-create users table if not exists
        $tableSql = "CREATE TABLE IF NOT EXISTS `users` (
            `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `username` VARCHAR(50) NOT NULL UNIQUE,
            `email` VARCHAR(100) NOT NULL UNIQUE,
            `password` VARCHAR(255) NOT NULL,
            `full_name` VARCHAR(100) DEFAULT NULL,
            `phone` VARCHAR(30) DEFAULT NULL,
            `address` VARCHAR(255) DEFAULT NULL,
            `city` VARCHAR(100) DEFAULT NULL,
            `zip` VARCHAR(20) DEFAULT NULL,
            `card_name` VARCHAR(100) DEFAULT NULL,
            `card_number` VARCHAR(30) DEFAULT NULL,
            `exp_month` VARCHAR(20) DEFAULT NULL,
            `exp_year` VARCHAR(10) DEFAULT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        $pdo->exec($tableSql);

        return $pdo;

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Database connection error. Please ensure MySQL is running in XAMPP. Detail: ' . $e->getMessage()
        ]);
        exit;
    }
}

/**
 * Start session safely if not already started
 */
function ensure_session_started(): void {
    if (session_status() === PHP_SESSION_NONE) {
        // 30 days lifetime if remember me
        ini_set('session.cookie_httponly', 1);
        session_start();
    }
}

/**
 * Helper to output JSON and exit
 */
function send_json(array $data, int $statusCode = 200): void {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

/**
 * Helper to get JSON or POST request body
 */
function get_request_data(): array {
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    if (stripos($contentType, 'application/json') !== false) {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);
        return is_array($data) ? $data : [];
    }
    return $_POST;
}

