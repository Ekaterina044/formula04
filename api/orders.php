<?php
/**
 * Создание заказа
 * Method: POST
 * Body: items[], total, shipping_name, shipping_email, shipping_phone, shipping_address, comment
 */
require_once '../database.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Метод не поддерживается'], 405);
}

// Проверяем авторизацию
$user = getCurrentUser();
if (!$user) {
    jsonResponse(['success' => false, 'message' => 'Требуется авторизация'], 401);
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    $data = $_POST;
}

$items = $data['items'] ?? [];
$total = floatval($data['total'] ?? 0);
$shippingName = trim($data['shipping_name'] ?? '');
$shippingEmail = trim($data['shipping_email'] ?? '');
$shippingPhone = trim($data['shipping_phone'] ?? '');
$shippingAddress = trim($data['shipping_address'] ?? '');
$comment = trim($data['comment'] ?? '');
$paymentMethod = trim($data['payment_method'] ?? 'cash_on_delivery');

// Валидация
if (empty($items)) {
    jsonResponse(['success' => false, 'message' => 'Корзина пуста'], 400);
}

if (empty($shippingName) || empty($shippingEmail) || empty($shippingPhone) || empty($shippingAddress)) {
    jsonResponse(['success' => false, 'message' => 'Заполните все обязательные поля доставки'], 400);
}

if (!filter_var($shippingEmail, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(['success' => false, 'message' => 'Некорректный email'], 400);
}

$pdo = getDbConnection();
if (!$pdo) {
    jsonResponse(['success' => false, 'message' => 'Ошибка подключения к базе данных'], 500);
}

try {
    $pdo->beginTransaction();
    
    // Генерируем номер заказа
    $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    $createdAt = date('Y-m-d H:i:s');
    
    // Создаем заказ
    $stmt = $pdo->prepare("INSERT INTO orders 
        (user_id, order_number, total_amount, status, payment_status, payment_method, shipping_address, shipping_phone, shipping_name, shipping_email, comment, created_at) 
        VALUES (?, ?, ?, 'pending', 'pending', ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->execute([
        $user['id'],
        $orderNumber,
        $total,
        $paymentMethod,
        $shippingAddress,
        $shippingPhone,
        $shippingName,
        $shippingEmail,
        $comment,
        $createdAt
    ]);
    
    $orderId = $pdo->lastInsertId();
    
    // Добавляем товары заказа
    $stmtItem = $pdo->prepare("INSERT INTO order_items 
        (order_id, product_id, product_name, product_price, quantity) 
        VALUES (?, ?, ?, ?, ?)");
    
    foreach ($items as $item) {
        $productId = intval($item['id'] ?? 0);
        $productName = trim($item['name'] ?? 'Неизвестный товар');
        $productPrice = floatval($item['price'] ?? 0);
        $quantity = intval($item['quantity'] ?? 1);
        
        // Если id строковый (как в текущем JS), используем 0
        if (!is_numeric($item['id'])) {
            $productId = 0;
        }
        
        $stmtItem->execute([$orderId, $productId, $productName, $productPrice, $quantity]);
    }
    
    $pdo->commit();
    
    // Отправляем уведомления (асинхронно, чтобы не блокировать ответ)
    try {
        require_once __DIR__ . '/../mailer.php';
        
        $orderData = [
            'order_number' => $orderNumber,
            'created_at' => $createdAt,
            'status' => 'pending',
            'shipping_name' => $shippingName,
            'shipping_email' => $shippingEmail,
            'shipping_phone' => $shippingPhone,
            'shipping_address' => $shippingAddress,
            'comment' => $comment,
            'total' => $total,
            'items' => $items
        ];
        
        // Уведомление администратору
        sendOrderNotification($orderData);
        
        // Подтверждение клиенту
        sendOrderConfirmation($orderData);
        
    } catch (Exception $e) {
        // Ошибки отправки писем не должны ломать заказ
        error_log("Ошибка отправки уведомлений: " . $e->getMessage());
    }
    
    jsonResponse([
        'success' => true,
        'message' => 'Заказ успешно оформлен',
        'order' => [
            'id' => $orderId,
            'order_number' => $orderNumber,
            'total' => $total,
            'status' => 'pending'
        ]
    ]);
    
} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Ошибка создания заказа: " . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'Ошибка при оформлении заказа'], 500);
}
