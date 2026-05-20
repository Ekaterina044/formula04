<?php
/**
 * База данных MySQL - Пустышка (заготовка)
 * 
 * Этот файл содержит структуру базы данных для проекта "Формула здоровья"
 * Когда будет готова страница регистрации, входа и оплаты,
 * нужно будет заменить этот файл на реальное подключение к БД
 */

// ========== НАСТРОЙКИ ПОДКЛЮЧЕНИЯ (ДЛЯ БУДУЩЕГО ИСПОЛЬЗОВАНИЯ) ==========
// Раскомментировать и настроить при подключении реальной БД

/*
define('DB_HOST', 'localhost');        // Хост (обычно localhost)
define('DB_NAME', 'formula_health');   // Имя базы данных
define('DB_USER', 'root');             // Имя пользователя
define('DB_PASS', '');                 // Пароль
define('DB_CHARSET', 'utf8mb4');       // Кодировка

// Подключение к БД (будет использоваться позже)
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch(PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}
*/

// ========== СТРУКТУРА ТАБЛИЦ ДЛЯ БУДУЩЕГО ИСПОЛЬЗОВАНИЯ ==========

/**
 * Таблица: users (пользователи)
 * 
 * CREATE TABLE IF NOT EXISTS `users` (
 *   `id` INT(11) NOT NULL AUTO_INCREMENT,
 *   `name` VARCHAR(100) NOT NULL,
 *   `email` VARCHAR(100) NOT NULL UNIQUE,
 *   `password` VARCHAR(255) NOT NULL,
 *   `phone` VARCHAR(20) DEFAULT NULL,
 *   `avatar` VARCHAR(255) DEFAULT NULL,
 *   `role` ENUM('user', 'admin', 'manager') DEFAULT 'user',
 *   `is_active` TINYINT(1) DEFAULT 1,
 *   `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 *   `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 *   PRIMARY KEY (`id`),
 *   UNIQUE KEY `email` (`email`)
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
 */

/**
 * Таблица: products (товары)
 * 
 * CREATE TABLE IF NOT EXISTS `products` (
 *   `id` INT(11) NOT NULL AUTO_INCREMENT,
 *   `name` VARCHAR(200) NOT NULL,
 *   `category` VARCHAR(50) NOT NULL,
 *   `subcategory` VARCHAR(50) DEFAULT NULL,
 *   `price` DECIMAL(10,2) NOT NULL,
 *   `old_price` DECIMAL(10,2) DEFAULT NULL,
 *   `description` TEXT,
 *   `image` VARCHAR(255) DEFAULT NULL,
 *   `in_stock` INT(11) DEFAULT 0,
 *   `is_active` TINYINT(1) DEFAULT 1,
 *   `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 *   PRIMARY KEY (`id`),
 *   KEY `category` (`category`)
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
 */

/**
 * Таблица: cart (корзина)
 * 
 * CREATE TABLE IF NOT EXISTS `cart` (
 *   `id` INT(11) NOT NULL AUTO_INCREMENT,
 *   `user_id` INT(11) NOT NULL,
 *   `product_id` INT(11) NOT NULL,
 *   `quantity` INT(11) DEFAULT 1,
 *   `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 *   PRIMARY KEY (`id`),
 *   KEY `user_id` (`user_id`),
 *   KEY `product_id` (`product_id`),
 *   FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
 *   FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
 */

/**
 * Таблица: favorites (избранное)
 * 
 * CREATE TABLE IF NOT EXISTS `favorites` (
 *   `id` INT(11) NOT NULL AUTO_INCREMENT,
 *   `user_id` INT(11) NOT NULL,
 *   `product_id` INT(11) NOT NULL,
 *   `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 *   PRIMARY KEY (`id`),
 *   UNIQUE KEY `user_product` (`user_id`, `product_id`),
 *   FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
 *   FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
 */

/**
 * Таблица: orders (заказы)
 * 
 * CREATE TABLE IF NOT EXISTS `orders` (
 *   `id` INT(11) NOT NULL AUTO_INCREMENT,
 *   `user_id` INT(11) NOT NULL,
 *   `order_number` VARCHAR(50) NOT NULL UNIQUE,
 *   `total_amount` DECIMAL(10,2) NOT NULL,
 *   `status` ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
 *   `payment_status` ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
 *   `payment_method` VARCHAR(50) DEFAULT NULL,
 *   `shipping_address` TEXT,
 *   `shipping_phone` VARCHAR(20) DEFAULT NULL,
 *   `comment` TEXT,
 *   `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 *   `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 *   PRIMARY KEY (`id`),
 *   UNIQUE KEY `order_number` (`order_number`),
 *   KEY `user_id` (`user_id`),
 *   FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
 */

/**
 * Таблица: order_items (товары в заказе)
 * 
 * CREATE TABLE IF NOT EXISTS `order_items` (
 *   `id` INT(11) NOT NULL AUTO_INCREMENT,
 *   `order_id` INT(11) NOT NULL,
 *   `product_id` INT(11) NOT NULL,
 *   `product_name` VARCHAR(200) NOT NULL,
 *   `product_price` DECIMAL(10,2) NOT NULL,
 *   `quantity` INT(11) NOT NULL,
 *   PRIMARY KEY (`id`),
 *   KEY `order_id` (`order_id`),
 *   FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
 */

/**
 * Таблица: reviews (отзывы)
 * 
 * CREATE TABLE IF NOT EXISTS `reviews` (
 *   `id` INT(11) NOT NULL AUTO_INCREMENT,
 *   `user_id` INT(11) NOT NULL,
 *   `product_id` INT(11) NOT NULL,
 *   `rating` TINYINT(1) DEFAULT 5,
 *   `comment` TEXT,
 *   `is_approved` TINYINT(1) DEFAULT 0,
 *   `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 *   PRIMARY KEY (`id`),
 *   KEY `user_id` (`user_id`),
 *   KEY `product_id` (`product_id`),
 *   FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
 *   FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
 */

/**
 * Таблица: settings (настройки сайта)
 * 
 * CREATE TABLE IF NOT EXISTS `settings` (
 *   `id` INT(11) NOT NULL AUTO_INCREMENT,
 *   `setting_key` VARCHAR(100) NOT NULL UNIQUE,
 *   `setting_value` TEXT,
 *   PRIMARY KEY (`id`),
 *   UNIQUE KEY `setting_key` (`setting_key`)
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
 */

// ========== ДЕМО-ДАННЫЕ ДЛЯ ТЕСТИРОВАНИЯ (ВРЕМЕННО) ==========

/**
 * Временные данные пользователей (для демо-режима)
 * Позже это будет заменено на реальную БД
 */
$demoUsers = [
    ['id' => 1, 'name' => 'Иван Петров', 'email' => 'ivan@test.ru', 'password' => '123456', 'role' => 'user'],
    ['id' => 2, 'name' => 'Мария Сидорова', 'email' => 'maria@test.ru', 'password' => '123456', 'role' => 'user'],
    ['id' => 3, 'name' => 'Администратор', 'email' => 'admin@test.ru', 'password' => 'admin123', 'role' => 'admin']
];

/**
 * Временные данные товаров (для демо-режима)
 */
$demoProducts = [
    ['id' => 1, 'name' => 'RNA комплекс', 'category' => 'health', 'subcategory' => 'supplements', 'price' => 4900, 'description' => 'ВП РНК для иммунитета'],
    ['id' => 2, 'name' => 'Витамин D3 + K2', 'category' => 'health', 'subcategory' => 'supplements', 'price' => 2100, 'description' => 'Для костей и иммунитета'],
    ['id' => 3, 'name' => 'Биомодулятор Effector', 'category' => 'health', 'subcategory' => 'devices', 'price' => 18900, 'description' => 'Омоложение клеток'],
    ['id' => 4, 'name' => 'Jenel Professional Cream', 'category' => 'beauty', 'subcategory' => 'cosmetics', 'price' => 3500, 'description' => 'Интенсивное увлажнение'],
    ['id' => 5, 'name' => 'Сыворотка с пептидами', 'category' => 'beauty', 'subcategory' => 'cosmetics', 'price' => 4200, 'description' => 'Лифтинг-эффект'],
    ['id' => 6, 'name' => 'VERUM ONE генератор H2', 'category' => 'devices', 'subcategory' => 'home', 'price' => 32900, 'description' => 'Водородная вода дома'],
    ['id' => 7, 'name' => 'Массажёр для лица', 'category' => 'devices', 'subcategory' => 'home', 'price' => 5900, 'description' => 'Уход за кожей']
];

/**
 * Функция для получения пользователя по email и паролю (демо-версия)
 */
function getUserByEmailAndPassword($email, $password) {
    global $demoUsers;
    foreach ($demoUsers as $user) {
        if ($user['email'] === $email && $user['password'] === $password) {
            return $user;
        }
    }
    return null;
}

/**
 * Функция для получения товара по ID (демо-версия)
 */
function getProductById($id) {
    global $demoProducts;
    foreach ($demoProducts as $product) {
        if ($product['id'] == $id) {
            return $product;
        }
    }
    return null;
}

/**
 * Функция для получения всех товаров (демо-версия)
 */
function getAllProducts() {
    global $demoProducts;
    return $demoProducts;
}

// ========== ИНФОРМАЦИЯ О СТАТУСЕ ==========
echo "<!-- 
===========================================
БАЗА ДАННЫХ (ПУСТЫШКА)
===========================================
Статус: Ожидает подключения реальной БД
Файл: database.php
Структура таблиц: готова (закомментирована)
Демо-данные: временные, для тестирования

Для подключения реальной БД:
1. Раскомментировать настройки подключения
2. Создать таблицы по структуре выше
3. Удалить демо-данные
===========================================
-->";