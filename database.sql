-- Happy Seoul Database Schema
-- Run this in phpMyAdmin or let db.php initialize it automatically.

CREATE DATABASE IF NOT EXISTS `happy_seoul_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `happy_seoul_db`;

CREATE TABLE IF NOT EXISTS `users` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

