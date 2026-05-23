<?php
/**
 * Получение данных текущего пользователя
 * Method: GET
 */
require_once '../database.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(['success' => false, 'message' => 'Метод не поддерживается'], 405);
}

$user = getCurrentUser();

if (!$user) {
    jsonResponse(['success' => false, 'message' => 'Не авторизован'], 401);
}

jsonResponse([
    'success' => true,
    'user' => $user
]);
