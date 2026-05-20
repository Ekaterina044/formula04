<?php
/**
 * Конфигурационный файл - Пустышка
 * 
 * Содержит основные настройки сайта
 */

// Настройки сайта
define('SITE_NAME', 'Формула здоровья');
define('SITE_URL', 'https://formula-health.ru'); // Замените на свой URL
define('SITE_EMAIL', 'info@formula-health.ru');

// Настройки сессии
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Установите 1 при использовании HTTPS

// Временная зона
date_default_timezone_set('Europe/Moscow');

// Пути к директориям
define('ROOT_PATH', dirname(__FILE__));
define('UPLOAD_PATH', ROOT_PATH . '/uploads/');
define('IMAGES_PATH', ROOT_PATH . '/images/');

// Включение отображения ошибок (для разработки)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Для продакшена нужно отключить:
// error_reporting(0);
// ini_set('display_errors', 0);

// Подключение файла базы данных (пока закомментировано)
// require_once 'database.php';