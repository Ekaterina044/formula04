# 📦 ПОЛНАЯ ИНСТРУКЦИЯ ПО УСТАНОВКЕ НА XAMPP

## Содержание
1. [Установка XAMPP](#1-установка-xampp)
2. [Настройка MySQL](#2-настройка-mysql)
3. [Настройка SMTP для отправки писем](#3-настройка-smtp)
4. [Установка PHPMailer](#4-установка-phpmailer)
5. [Запуск проекта](#5-запуск-проекта)
6. [Тестирование](#6-тестирование)
7. [Troubleshooting](#7-troubleshooting)

---

## 1. Установка XAMPP

### Шаг 1.1: Скачивание
1. Перейдите на официальный сайт: https://www.apachefriends.org/
2. Скачайте XAMPP для вашей ОС (Windows/Mac/Linux)
3. Для Windows рекомендуется версия с PHP 8.x

### Шаг 1.2: Установка (Windows)
1. Запустите установщик `xampp-installer.exe`
2. Выберите компоненты:
   - ✅ Apache
   - ✅ MySQL
   - ✅ PHP
   - ✅ phpMyAdmin
3. Установите в директорию: `C:\xampp` (рекомендуется)
4. Завершите установку

### Шаг 1.3: Запуск служб
1. Откройте **XAMPP Control Panel**
2. Нажмите **Start** рядом с **Apache**
3. Нажмите **Start** рядом с **MySQL**
4. Убедитесь, что оба сервиса горят **зелёным**

```
┌─────────────────────────────────────┐
│  XAMPP Control Panel                │
│  ─────────────────────────────────  │
│  Module   | Status  | Actions       │
│  ─────────────────────────────────  │
│  Apache   | Running | [Stop] [Admin]│
│  MySQL    | Running | [Stop] [Admin]│
└─────────────────────────────────────┘
```

---

## 2. Настройка MySQL

### Шаг 2.1: Открытие phpMyAdmin
1. В XAMPP Control Panel нажмите **Admin** рядом с MySQL
2. Или откройте в браузере: http://localhost/phpmyadmin

### Шаг 2.2: Создание базы данных
1. В phpMyAdmin нажмите **"Создать"** (New) в левой панели
2. Введите имя базы: `formula_health`
3. Кодировка: `utf8mb4_unicode_ci`
4. Нажмите **"Создать"**

### Шаг 2.3: Импорт структуры таблиц
1. Выберите базу `formula_health` в левой панели
2. Перейдите на вкладку **"Импорт"** (Import)
3. Нажмите **"Выберите файл"** и укажите `database.sql` из проекта
4. Нажмите **"Вперёд"** (Go)

### Шаг 2.4: Проверка таблиц
После импорта вы должны увидеть 6 таблиц:
- ✅ `users` — пользователи
- ✅ `orders` — заказы
- ✅ `order_items` — товары в заказе
- ✅ `cart` — корзина
- ✅ `favorites` — избранное
- ✅ `reviews` — отзывы

### Шаг 2.5: Настройка пароля MySQL (опционально)
По умолчанию в XAMPP пароль root пустой. Для установки пароля:

1. В phpMyAdmin перейдите: **Учётные записи пользователей** → **root** → **Изменить права**
2. Введите новый пароль
3. **Обновите файл `database.php`**:
   ```php
   define('DB_PASS', 'ваш_новый_пароль');
   ```

---

## 3. Настройка SMTP для отправки писем

### Вариант A: Mailtrap (рекомендуется для тестирования)

**Mailtrap** — бесплатный тестовый SMTP-сервис. Письма не отправляются реально, а попадают в виртуальный ящик.

#### Шаг 3.A.1: Регистрация
1. Перейдите: https://mailtrap.io/
2. Зарегистрируйтесь (бесплатно)
3. Подтвердите email

#### Шаг 3.A.2: Получение настроек SMTP
1. Войдите в аккаунт
2. Перейдите: **Inboxes** → выберите созданный ящик
3. Скопируйте **SMTP Settings**:
   ```
   Host: smtp.mailtrap.io
   Port: 587
   Username: ваш_username
   Password: ваш_password
   ```

#### Шаг 3.A.3: Настройка проекта
Откройте `smtp_config.php` и укажите:

```php
define('SMTP_ENABLED', true);
define('SMTP_HOST', 'smtp.mailtrap.io');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');
define('SMTP_USER', 'ваш_username_из_mailtrap');
define('SMTP_PASS', 'ваш_пароль_из_mailtrap');
define('SMTP_FROM_EMAIL', 'no-reply@formula-health.ru');
define('SMTP_FROM_NAME', 'Формула здоровья');
define('ADMIN_EMAIL', 'admin@formula-health.ru');  // Ваш email для уведомлений
```

#### Шаг 3.A.4: Проверка писем
1. Откройте Mailtrap → **Inbox**
2. После оформления заказа письмо появится там

---

### Вариант B: Gmail

#### Шаг 3.B.1: Включение 2FA
1. Войдите в Google Account: https://myaccount.google.com/
2. Перейдите: **Безопасность** → **Двухэтапная аутентификация**
3. Включите 2FA

#### Шаг 3.B.2: Создание пароля приложения
1. Перейдите: https://myaccount.google.com/apppasswords
2. Выберите: **Почта** → **Другое (custom name)**
3. Введите: `Formula Health Site`
4. Скопируйте 16-значный пароль

#### Шаг 3.B.3: Настройка проекта
```php
define('SMTP_ENABLED', true);
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');
define('SMTP_USER', 'ваш.email@gmail.com');
define('SMTP_PASS', 'пароль_приложения_16_символов');
define('SMTP_FROM_EMAIL', 'ваш.email@gmail.com');
define('SMTP_FROM_NAME', 'Формула здоровья');
define('ADMIN_EMAIL', 'ваш.email@gmail.com');
```

---

### Вариант C: Yandex Mail

#### Шаг 3.C.1: Создание пароля приложения
1. Войдите в Яндекс.Почту
2. Перейдите: **Настройки** → **Безопасность**
3. Включите **Двухфакторную аутентификацию**
4. Создайте **Пароль для внешних приложений**

#### Шаг 3.C.2: Настройка проекта
```php
define('SMTP_ENABLED', true);
define('SMTP_HOST', 'smtp.yandex.ru');
define('SMTP_PORT', 465);
define('SMTP_SECURE', 'ssl');
define('SMTP_USER', 'ваш.email@yandex.ru');
define('SMTP_PASS', 'пароль_приложения');
define('SMTP_FROM_EMAIL', 'ваш.email@yandex.ru');
define('SMTP_FROM_NAME', 'Формула здоровья');
define('ADMIN_EMAIL', 'ваш.email@yandex.ru');
```

---

### Вариант D: XAMPP Sendmail (локальная отправка)

#### Шаг 3.D.1: Настройка sendmail.ini
1. Откройте: `C:\xampp\sendmail\sendmail.ini`
2. Найдите и замените:

```ini
[sendmail]
smtp_server=smtp.mailtrap.io
smtp_port=587
auth_username=ваш_username_из_mailtrap
auth_password=ваш_пароль_из_mailtrap
force_sender=no-reply@formula-health.ru
hostname=localhost
```

#### Шаг 3.D.2: Настройка php.ini
1. Откройте: `C:\xampp\php\php.ini`
2. Найдите `[mail function]`
3. Замените:

```ini
[mail function]
SMTP=smtp.mailtrap.io
smtp_port=587
sendmail_from=no-reply@formula-health.ru
sendmail_path="C:\xampp\sendmail\sendmail.exe" -t
```

#### Шаг 3.D.3: Перезапуск Apache
В XAMPP Control Panel нажмите **Stop** → **Start** для Apache

---

## 4. Установка PHPMailer

### Способ A: Через Composer (рекомендуется)

#### Шаг 4.A.1: Установка Composer
1. Скачайте: https://getcomposer.org/download/
2. Запустите установщик
3. Укажите путь: `C:\xampp\php\php.exe`

#### Шаг 4.A.2: Установка PHPMailer
1. Откройте командную строку в папке проекта:
   ```cmd
   cd C:\xampp\htdocs\formula-health
   ```
2. Выполните:
   ```cmd
   composer require phpmailer/phpmailer
   ```
3. Появится папка `vendor/`

### Способ B: Вручную (без Composer)

#### Шаг 4.B.1: Скачивание
1. Перейдите: https://github.com/PHPMailer/PHPMailer/releases
2. Скачайте последнюю версию (ZIP)
3. Распакуйте в папку проекта

#### Шаг 4.B.2: Структура папок
```
project/
├── vendor/
│   └── phpmailer/
│       └── phpmailer/
│           └── src/
│               ├── PHPMailer.php
│               ├── SMTP.php
│               └── Exception.php
```

#### Шаг 4.B.3: Обновление mailer.php
В начале файла `mailer.php` замените:

```php
// Замените эту строку:
// require_once __DIR__ . '/vendor/autoload.php';

// На эту:
require_once __DIR__ . '/vendor/phpmailer/phpmailer/src/Exception.php';
require_once __DIR__ . '/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/vendor/phpmailer/phpmailer/src/SMTP.php';
```

---

## 5. Запуск проекта

### Шаг 5.1: Размещение файлов
1. Скопируйте все файлы проекта в:
   ```
   C:\xampp\htdocs\formula-health\
   ```

### Шаг 5.2: Проверка структуры
```
C:\xampp\htdocs\formula-health\
├── api/
│   ├── register.php
│   ├── login.php
│   ├── logout.php
│   ├── user.php
│   └── orders.php
├── vendor/                    (после установки PHPMailer)
│   └── phpmailer/
├── database.php              (настройки БД)
├── database.sql              (структура таблиц)
├── smtp_config.php           (настройки SMTP)
├── mailer.php                (отправка писем)
├── config.php
├── login.html
├── checkout.html
├── script.js
└── INSTALL_XAMPP.md          (этот файл)
```

### Шаг 5.3: Проверка подключения к БД
Откройте в браузере:
```
http://localhost/formula-health/api/user.php
```

Если видите `{"success":false,"message":"Не авторизован"}` — **БД подключена успешно!** ✅

Если ошибка подключения — проверьте `database.php`.

---

## 6. Тестирование

### Шаг 6.1: Регистрация пользователя
1. Откройте: http://localhost/formula-health/login.html
2. Перейдите на вкладку **"Регистрация"**
3. Заполните:
   - Имя: `Тестовый Пользователь`
   - Email: `test@example.com`
   - Пароль: `123456`
   - Телефон: `+7 (999) 000-00-00`
4. Нажмите **"Зарегистрироваться"**

### Шаг 6.2: Проверка в БД
1. Откройте phpMyAdmin
2. Выберите базу `formula_health`
3. Перейдите в таблицу `users`
4. Вы увидите нового пользователя ✅

### Шаг 6.3: Оформление заказа
1. Войдите в кабинет
2. Добавьте товары в корзину
3. Перейдите на: http://localhost/formula-health/checkout.html
4. Заполните данные доставки
5. Нажмите **"Оформить заказ"**

### Шаг 6.4: Проверка заказа в БД
```sql
-- Просмотр всех заказов
SELECT o.*, u.name, u.email 
FROM orders o 
JOIN users u ON o.user_id = u.id 
ORDER BY o.created_at DESC;

-- Просмотр товаров в заказах
SELECT oi.*, o.order_number 
FROM order_items oi 
JOIN orders o ON oi.order_id = o.id;
```

### Шаг 6.5: Проверка email
1. **Mailtrap**: Откройте https://mailtrap.io/inboxes/
2. **Gmail/Yandex**: Проверьте входящие
3. Вы должны получить 2 письма:
   - ✅ Подтверждение заказа (клиенту)
   - ✅ Уведомление о заказе (администратору)

---

## 7. Troubleshooting

### ❌ "Ошибка подключения к базе данных"

**Причина**: Неверные учётные данные или MySQL не запущен

**Решение**:
1. Проверьте, что MySQL запущен в XAMPP (зелёный индикатор)
2. Проверьте `database.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'formula_health');
   define('DB_USER', 'root');
   define('DB_PASS', '');  // Пустой по умолчанию в XAMPP
   ```
3. Убедитесь, что база `formula_health` существует

---

### ❌ "SMTP Error: Could not authenticate"

**Причина**: Неверный логин/пароль SMTP

**Решение**:
1. Проверьте `smtp_config.php`
2. Для Gmail используйте **пароль приложения**, не обычный пароль
3. Для Mailtrap проверьте настройки в личном кабинете

---

### ❌ "Class 'PHPMailer\PHPMailer\PHPMailer' not found"

**Причина**: PHPMailer не установлен

**Решение**:
```cmd
cd C:\xampp\htdocs\formula-health
composer require phpmailer/phpmailer
```

Или скачайте вручную с GitHub.

---

### ❌ Письма не отправляются

**Причина**: SMTP не настроен или заблокирован

**Решение**:
1. Проверьте `smtp_config.php` → `SMTP_ENABLED = true`
2. Проверьте логи PHP: `C:\xampp\apache\logs\error.log`
3. Для Gmail включите **"Ненадёжные приложения"** или используйте пароль приложения
4. Попробуйте Mailtrap для тестирования

---

### ❌ "Требуется авторизация" при оформлении заказа

**Причина**: Сессия не сохраняется

**Решение**:
1. Проверьте, что `session_start()` вызывается в API
2. Очистите куки браузера
3. Проверьте консоль браузера на ошибки CORS

---

### ❌ Apache не запускается (порт 80 занят)

**Причина**: Порт 80 занят Skype/IIS/другим сервисом

**Решение**:
1. В XAMPP Control Panel: **Apache** → **Config** → **httpd.conf**
2. Найдите: `Listen 80`
3. Замените на: `Listen 8080`
4. Перезапустите Apache
5. Открывайте проект: http://localhost:8080/formula-health/

---

### ❌ MySQL не запускается (порт 3306 занят)

**Причина**: Порт 3306 занят другой MySQL

**Решение**:
1. В XAMPP Control Panel: **MySQL** → **Config** → **my.ini**
2. Найдите: `port=3306`
3. Замените на: `port=3307`
4. Обновите `database.php`:
   ```php
   define('DB_PORT', 3307);
   ```
5. Перезапустите MySQL

---

## 📞 Контакты для поддержки

Если возникли проблемы:
1. Проверьте логи: `C:\xampp\apache\logs\error.log`
2. Проверьте консоль браузера (F12)
3. Убедитесь, что все файлы на месте

---

## ✅ Чек-лист успешной установки

- [ ] XAMPP установлен и запущен (Apache + MySQL)
- [ ] База данных `formula_health` создана
- [ ] Таблицы импортированы из `database.sql`
- [ ] `database.php` настроен (хост, имя БД, пользователь, пароль)
- [ ] PHPMailer установлен (папка `vendor/`)
- [ ] `smtp_config.php` настроен (SMTP-сервис, логин, пароль)
- [ ] Регистрация работает (пользователь сохраняется в БД)
- [ ] Вход работает (сессия создаётся)
- [ ] Заказ оформляется (запись в `orders` и `order_items`)
- [ ] Письма отправляются (проверка в Mailtrap/Gmail)

---

**Готово!** 🎉 Ваш проект полностью настроен и готов к работе.
