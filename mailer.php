<?php
/**
 * =============================================================================
 * MAILER - ОТПРАВКА EMAIL ЧЕРЕЗ PHPMAILER
 * =============================================================================
 * 
 * Файл: mailer.php
 * Проект: Формула здоровья
 * 
 * Использование:
 * require_once 'mailer.php';
 * sendEmail('user@example.com', 'Тема', 'Текст письма');
 * =============================================================================
 */

require_once __DIR__ . '/smtp_config.php';

// Подключаем PHPMailer (установить через Composer или скачать вручную)
// composer require phpmailer/phpmailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Проверяем, подключён ли PHPMailer
if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    // Пытаемся подключить вручную если файл существует
    $phpmailerPath = __DIR__ . '/vendor/autoload.php';
    if (file_exists($phpmailerPath)) {
        require_once $phpmailerPath;
    } else {
        // Fallback: используем стандартную mail() функцию
        function sendEmailFallback($to, $subject, $body, $altBody = '') {
            $headers = "From: " . SMTP_FROM_NAME . " <" . SMTP_FROM_EMAIL . ">\r\n";
            $headers .= "Reply-To: " . SMTP_FROM_EMAIL . "\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            
            return mail($to, $subject, $body, $headers);
        }
    }
}

/**
 * Отправить email через PHPMailer
 * 
 * @param string $to Email получателя
 * @param string $subject Тема письма
 * @param string $body Тело письма (HTML)
 * @param string $altBody Текстовая версия (опционально)
 * @param array $attachments Вложения (опционально)
 * @return array ['success' => bool, 'message' => string]
 */
function sendEmail($to, $subject, $body, $altBody = '', $attachments = []) {
    if (!SMTP_ENABLED) {
        // SMTP отключён - используем fallback
        if (function_exists('sendEmailFallback')) {
            $success = sendEmailFallback($to, $subject, $body);
            return [
                'success' => $success,
                'message' => $success ? 'Письмо отправлено' : 'Ошибка отправки'
            ];
        }
        return [
            'success' => false,
            'message' => 'SMTP отключён и mail() недоступен'
        ];
    }
    
    $mail = new PHPMailer(true);
    
    try {
        $config = getSmtpConfig();
        
        // Настройки сервера
        $mail->isSMTP();
        $mail->Host = $config['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $config['username'];
        $mail->Password = $config['password'];
        $mail->SMTPSecure = $config['secure'];
        $mail->Port = $config['port'];
        $mail->CharSet = 'UTF-8';
        
        // Отправитель и получатель
        $mail->setFrom($config['from_email'], $config['from_name']);
        $mail->addAddress($to);
        
        // Вложения
        foreach ($attachments as $attachment) {
            $mail->addAttachment($attachment);
        }
        
        // Контент
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = $altBody ?: strip_tags($body);
        
        $mail->send();
        
        return [
            'success' => true,
            'message' => 'Письмо успешно отправлено'
        ];
        
    } catch (Exception $e) {
        error_log("Ошибка отправки email: " . $mail->ErrorInfo);
        return [
            'success' => false,
            'message' => 'Ошибка: ' . $mail->ErrorInfo
        ];
    }
}

/**
 * Отправить уведомление о новом заказе администратору
 * 
 * @param array $orderData Данные заказа
 * @return array Результат отправки
 */
function sendOrderNotification($orderData) {
    $adminEmail = ADMIN_EMAIL;
    
    $subject = "📦 Новый заказ №" . $orderData['order_number'];
    
    $body = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; }
            .header { background: #64b61a; color: white; padding: 20px; }
            .content { padding: 20px; }
            .order-info { background: #f9f6f3; padding: 15px; border-radius: 8px; margin: 15px 0; }
            .items-table { width: 100%; border-collapse: collapse; margin: 15px 0; }
            .items-table th, .items-table td { padding: 10px; text-align: left; border-bottom: 1px solid #e0d6d0; }
            .total { font-size: 18px; font-weight: bold; color: #64b61a; }
        </style>
    </head>
    <body>
        <div class='header'>
            <h2>🎉 Новый заказ</h2>
        </div>
        <div class='content'>
            <p><strong>Заказ №:</strong> {$orderData['order_number']}</p>
            <p><strong>Дата:</strong> {$orderData['created_at']}</p>
            <p><strong>Статус:</strong> {$orderData['status']}</p>
            
            <div class='order-info'>
                <h3>📍 Данные клиента</h3>
                <p><strong>Имя:</strong> {$orderData['shipping_name']}</p>
                <p><strong>Email:</strong> {$orderData['shipping_email']}</p>
                <p><strong>Телефон:</strong> {$orderData['shipping_phone']}</p>
                <p><strong>Адрес:</strong> {$orderData['shipping_address']}</p>
                " . (!empty($orderData['comment']) ? "<p><strong>Комментарий:</strong> {$orderData['comment']}</p>" : "") . "
            </div>
            
            <h3>📦 Товары в заказе</h3>
            <table class='items-table'>
                <tr>
                    <th>Товар</th>
                    <th>Цена</th>
                    <th>Кол-во</th>
                    <th>Сумма</th>
                </tr>
    ";
    
    foreach ($orderData['items'] as $item) {
        $itemTotal = $item['price'] * $item['quantity'];
        $body .= "
            <tr>
                <td>{$item['name']}</td>
                <td>" . number_format($item['price'], 0, '.', ' ') . " ₽</td>
                <td>{$item['quantity']}</td>
                <td>" . number_format($itemTotal, 0, '.', ' ') . " ₽</td>
            </tr>
        ";
    }
    
    $body .= "
            </table>
            <p class='total'>💰 Итого: " . number_format($orderData['total'], 0, '.', ' ') . " ₽</p>
        </div>
    </body>
    </html>
    ";
    
    return sendEmail($adminEmail, $subject, $body);
}

/**
 * Отправить подтверждение заказа клиенту
 * 
 * @param array $orderData Данные заказа
 * @return array Результат отправки
 */
function sendOrderConfirmation($orderData) {
    $clientEmail = $orderData['shipping_email'];
    
    $subject = "✅ Заказ №" . $orderData['order_number'] . " подтверждён";
    
    $body = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; }
            .header { background: linear-gradient(135deg, #64b61a 0%, #e8be52 100%); color: white; padding: 30px; text-align: center; }
            .content { padding: 20px; }
            .order-info { background: #f9f6f3; padding: 20px; border-radius: 12px; margin: 20px 0; }
            .items-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
            .items-table th, .items-table td { padding: 12px; text-align: left; border-bottom: 1px solid #e0d6d0; }
            .total { font-size: 20px; font-weight: bold; color: #64b61a; }
            .footer { background: #f5f0ed; padding: 20px; text-align: center; color: #693D3D; }
            .btn { display: inline-block; padding: 12px 30px; background: #64b61a; color: white; text-decoration: none; border-radius: 25px; margin-top: 15px; }
        </style>
    </head>
    <body>
        <div class='header'>
            <h1>✅ Спасибо за заказ!</h1>
            <p>Ваш заказ успешно оформлен</p>
        </div>
        <div class='content'>
            <p>Здравствуйте, <strong>{$orderData['shipping_name']}</strong>!</p>
            <p>Ваш заказ №<strong>{$orderData['order_number']}</strong> принят в обработку.</p>
            
            <div class='order-info'>
                <h3>📍 Данные доставки</h3>
                <p><strong>Адрес:</strong> {$orderData['shipping_address']}</p>
                <p><strong>Телефон:</strong> {$orderData['shipping_phone']}</p>
                <p><strong>Способ оплаты:</strong> Наличными при получении</p>
            </div>
            
            <h3>📦 Ваш заказ</h3>
            <table class='items-table'>
                <tr>
                    <th>Товар</th>
                    <th>Кол-во</th>
                    <th>Сумма</th>
                </tr>
    ";
    
    foreach ($orderData['items'] as $item) {
        $itemTotal = $item['price'] * $item['quantity'];
        $body .= "
            <tr>
                <td>{$item['name']}</td>
                <td>{$item['quantity']}</td>
                <td>" . number_format($itemTotal, 0, '.', ' ') . " ₽</td>
            </tr>
        ";
    }
    
    $body .= "
            </table>
            <p class='total'>💰 Итого к оплате: " . number_format($orderData['total'], 0, '.', ' ') . " ₽</p>
            
            <p style='margin-top: 20px;'>Менеджер свяжется с вами в ближайшее время для подтверждения заказа.</p>
            
            <p style='color: #693D3D;'>
                Если у вас возникли вопросы, ответьте на это письмо или позвоните нам.
            </p>
        </div>
        <div class='footer'>
            <p><strong>Формула здоровья</strong></p>
            <p>📧 support@formula-health.ru | 📞 +7 (XXX) XXX-XX-XX</p>
            <p>© 2026 Все права защищены</p>
        </div>
    </body>
    </html>
    ";
    
    return sendEmail($clientEmail, $subject, $body);
}
