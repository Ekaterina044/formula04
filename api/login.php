<?php
/**
 * Авторизация пользователя
 * Method: POST
 * Body: email, password
 */
require_once '../database.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Метод не поддерживается'], 405);
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    $data = $_POST;
}

$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if (empty($email) || empty($password)) {
    jsonResponse(['success' => false, 'message' => 'Укажите email и пароль'], 400);
}

$pdo = getDbConnection();
if (!$pdo) {
    jsonResponse(['success' => false, 'message' => 'Ошибка подключения к базе данных'], 500);
}

try {
    $stmt = $pdo->prepare("SELECT id, name, email, password, phone, avatar, role FROM users WHERE email = ? AND is_active = 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if (!$user) {
        jsonResponse(['success' => false, 'message' => 'Неверный email или пароль'], 401);
    }
    
    if (!password_verify($password, $user['password'])) {
        jsonResponse(['success' => false, 'message' => 'Неверный email или пароль'], 401);
    }
    
    // Стартуем сессию
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['user_id'] = $user['id'];
    
    // Убираем пароль из ответа
    unset($user['password']);
    
    jsonResponse([
        'success' => true,
        'message' => 'Вход выполнен успешно',
        'user' => $user
    ]);
    
} catch (PDOException $e) {
    error_log("Ошибка авторизации: " . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'Ошибка при авторизации'], 500);
}
