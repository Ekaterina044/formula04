<?php
/**
 * =============================================================================
 * SMTP НАСТРОЙКИ - КОНФИГУРАЦИЯ ДЛЯ ОТПРАВКИ ПИСЕМ
 * =============================================================================
 * 
 * Файл: smtp_config.php
 * Проект: Формула здоровья
 * 
 * -----------------------------------------------------------------------------
 * ВАРИАНТЫ НАСТРОЙКИ SMTP:
 * -----------------------------------------------------------------------------
 * 
 * 1. XAMPP (локальный sendmail) - для тестирования
 *    - SMTP_HOST: smtp.mailtrap.io (или другой тестовый SMTP)
 *    - SMTP_PORT: 587
 *    - Требует настройки sendmail в XAMPP
 * 
 * 2. Gmail
 *    - SMTP_HOST: smtp.gmail.com
 *    - SMTP_PORT: 587 (TLS) или 465 (SSL)
 *    - Требует App Password (не обычный пароль)
 * 
 * 3. Yandex
 *    - SMTP_HOST: smtp.yandex.ru
 *    - SMTP_PORT: 465 (SSL)
 *    - Требует пароль приложения
 * 
 * 4. Mail.ru
 *    - SMTP_HOST: smtp.mail.ru
 *    - SMTP_PORT: 465 (SSL) или 587 (TLS)
 *    - Требует пароль приложения
 * 
 * 5. Ваш хостинг
 *    - Узнайте настройки у хостинг-провайдера
 * =============================================================================
 */

// ========== НАСТРОЙКИ SMTP - ИЗМЕНИТЬ ПОД ВАШ ПРОВАЙДЕР ==========

define('SMTP_ENABLED', true);
define('SMTP_HOST', 'sandbox.smtp.mailtrap.io');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');
define('SMTP_USER', '3df771ebc8fdf8');
define('SMTP_PASS', 'fabarm.1a212');
define('SMTP_FROM_EMAIL', 'no-reply@formula-health.ru');
define('SMTP_FROM_NAME', 'Формула здоровья');
define('ADMIN_EMAIL', 'dylykov.100@mail.ru');

// ========== АДРЕСА ДЛЯ УВЕДОМЛЕНИЙ ==========

define('ADMIN_EMAIL', 'admin@formula-health.ru');  // Email администратора
define('SUPPORT_EMAIL', 'support@formula-health.ru'); // Email поддержки

// ===========================================================================

/**
 * Получить конфигурацию PHPMailer
 * @return array Конфигурация для PHPMailer
 */
function getSmtpConfig() {
    return [
        'host' => SMTP_HOST,
        'port' => SMTP_PORT,
        'secure' => SMTP_SECURE,
        'username' => SMTP_USER,
        'password' => SMTP_PASS,
        'from_email' => SMTP_FROM_EMAIL,
        'from_name' => SMTP_FROM_NAME,
        'admin_email' => ADMIN_EMAIL,
        'support_email' => SUPPORT_EMAIL
    ];
}
