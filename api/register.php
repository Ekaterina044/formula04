<?php
/**
 * Регистрация пользователя
 * Method: POST
 * Body: name, email, password, phone
 */
require_once '../database.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Метод не поддерживается'], 405);
}

// Получаем данные
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    $data = $_POST;
}

$name = trim($data['name'] ?? '');
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';
$phone = trim($data['phone'] ?? '');

// Валидация
if (empty($name) || empty($email) || empty($password)) {
    jsonResponse(['success' => false, 'message' => 'Заполните обязательные поля'], 400);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(['success' => false, 'message' => 'Некорректный email'], 400);
}

if (strlen($password) < 6) {
    jsonResponse(['success' => false, 'message' => 'Пароль должен быть не менее 6 символов'], 400);
}

$pdo = getDbConnection();
if (!$pdo) {
    jsonResponse(['success' => false, 'message' => 'Ошибка подключения к базе данных'], 500);
}

try {
    // Проверяем, существует ли пользователь
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->fetch()) {
        jsonResponse(['success' => false, 'message' => 'Пользователь с таким email уже существует'], 409);
    }
    
    // Хешируем пароль
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    
    // Создаем пользователя
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, phone, role, is_active) VALUES (?, ?, ?, ?, 'user', 1)");
    $stmt->execute([$name, $email, $passwordHash, $phone]);
    
    $userId = $pdo->lastInsertId();
    
    // Стартуем сессию
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['user_id'] = $userId;
    
    jsonResponse([
        'success' => true,
        'message' => 'Регистрация успешна',
        'user' => [
            'id' => $userId,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'role' => 'user'
        ]
    ]);
    
} catch (PDOException $e) {
    error_log("Ошибка регистрации: " . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'Ошибка при регистрации'], 500);
}
