<?php
/**
 * Выход из системы
 * Method: POST
 */
require_once '../database.php';

header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Уничтожаем сессию
$_SESSION = [];

if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', [
        'expires' => time() - 3600,
        'path' => '/',
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
}

session_destroy();

jsonResponse([
    'success' => true,
    'message' => 'Выход выполнен успешно'
]);
