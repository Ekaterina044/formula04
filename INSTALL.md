# Установка и настройка системы регистрации и заказов

## Требования
- PHP 7.4 или выше
- MySQL 5.7 или выше / MariaDB 10.3+
- Веб-сервер (Apache/Nginx)

## Шаг 1: Настройка базы данных

1. Создайте базу данных MySQL:
```sql
CREATE DATABASE formula_health CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. Импортируйте структуру таблиц:
```bash
mysql -u root -p formula_health < database.sql
```

Или выполните SQL из файла `database.sql` через phpMyAdmin.

## Шаг 2: Настройка подключения к БД

Откройте файл `database.php` и укажите ваши данные для подключения:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'formula_health');
define('DB_USER', 'root');      // Ваше имя пользователя
define('DB_PASS', '');          // Ваш пароль
```

## Шаг 3: Проверка прав доступа

Убедитесь, что веб-сервер имеет права на чтение файлов в папке `api/`.

Для Linux:
```bash
chmod 755 api/
chmod 644 api/*.php
```

## Шаг 4: Тестирование

### Регистрация нового пользователя
Откройте `login.html`, перейдите на вкладку "Регистрация" и заполните форму.

### Вход
Используйте зарегистрированные данные для входа.

### Оформление заказа
1. Добавьте товары в корзину
2. Перейдите на страницу оформления (`checkout.html`)
3. Заполните данные доставки
4. Нажмите "Оформить заказ"

## Проверка данных в БД

### Просмотр пользователей:
```sql
SELECT * FROM users;
```

### Просмотр заказов:
```sql
SELECT o.*, u.name, u.email 
FROM orders o 
JOIN users u ON o.user_id = u.id 
ORDER BY o.created_at DESC;
```

### Просмотр товаров в заказах:
```sql
SELECT oi.*, o.order_number 
FROM order_items oi 
JOIN orders o ON oi.order_id = o.id;
```

## API Endpoints

| Метод | URL | Описание |
|-------|-----|----------|
| POST | `/api/register.php` | Регистрация пользователя |
| POST | `/api/login.php` | Авторизация |
| POST | `/api/logout.php` | Выход из системы |
| GET | `/api/user.php` | Получение данных текущего пользователя |
| POST | `/api/orders.php` | Создание заказа |

### Пример запроса регистрации:
```json
POST /api/register.php
{
  "name": "Иван Иванов",
  "email": "ivan@example.com",
  "password": "123456",
  "phone": "+7 (999) 123-45-67"
}
```

### Пример запроса создания заказа:
```json
POST /api/orders.php
{
  "items": [
    {"id": "effector", "name": "EFFECTOR", "price": 3910, "quantity": 2}
  ],
  "total": 7820,
  "shipping_name": "Иван Иванов",
  "shipping_email": "ivan@example.com",
  "shipping_phone": "+7 (999) 123-45-67",
  "shipping_address": "г. Москва, ул. Примерная, д. 1",
  "comment": "Доставить после 18:00",
  "payment_method": "cash_on_delivery"
}
```

## Безопасность

- Пароли хешируются алгоритмом `password_hash()` (bcrypt)
- Используются подготовленные выражения PDO для защиты от SQL-инъекций
- Сессии используют httponly cookies
- Реализована проверка CSRF через сессии

## Структура файлов

```
project/
├── api/
│   ├── register.php      # Регистрация
│   ├── login.php         # Авторизация
│   ├── logout.php        # Выход
│   ├── user.php          # Данные пользователя
│   └── orders.php        # Создание заказа
├── database.php          # Подключение к БД
├── database.sql          # Дамп структуры БД
├── config.php            # Конфигурация сайта
├── login.html            # Страница входа/регистрации
├── checkout.html         # Оформление заказа
└── script.js             # Основной JS файл
```

## Возможные ошибки и решения

### "Ошибка подключения к базе данных"
- Проверьте правильность учётных данных в `database.php`
- Убедитесь, что MySQL сервер запущен
- Проверьте, что база данных существует

### "Требуется авторизация" при оформлении заказа
- Убедитесь, что вы вошли в систему
- Проверьте, что сессия активна

### "Метод не поддерживается"
- API endpoints принимают только POST/GET запросы соответственно
- Проверьте метод запроса в вашем клиенте

## Тестовые данные

После импорта `database.sql` доступен тестовый пользователь:
- Email: `test@formula-health.ru`
- Пароль: `123456`
