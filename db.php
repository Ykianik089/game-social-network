<?php
session_start();

// Временный файл для отладки - покажем больше информации
error_log("=== Начало db.php ===");

$host = 'db';  // Имя сервиса из docker-compose.yml
$db   = 'gamenetwork';
$user = 'root';
$pass = 'rootpassword';

error_log("Подключаемся к: host=$host, db=$db, user=$user");

// Пробуем несколько раз подключиться
$maxTries = 10;
$tries = 0;
$pdo = null;
$connected = false;

while (!$connected && $tries < $maxTries) {
    try {
        $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
        error_log("Попытка #" . ($tries + 1) . ": $dsn");
        
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_TIMEOUT => 5,  // Таймаут 5 секунд
        ]);
        
        $connected = true;
        error_log("✓ Подключение успешно!");
        
    } catch (\PDOException $e) {
        $tries++;
        error_log("✗ Попытка #$tries не удалась: " . $e->getMessage());
        
        if ($tries >= $maxTries) {
            // Показываем подробную ошибку
            if (!headers_sent()) {
                header('Content-Type: text/html; charset=utf-8');
            }
            ?>
            <!DOCTYPE html>
            <html>
            <head>
                <title>Ошибка подключения к БД</title>
                <style>
                    body { font-family: Arial; padding: 20px; background: #f0f0f0; }
                    .error { background: white; padding: 20px; border-radius: 5px; border-left: 5px solid red; }
                    code { background: #eee; padding: 2px 5px; }
                </style>
            </head>
            <body>
                <div class="error">
                    <h2>Ошибка подключения к базе данных</h2>
                    <p><strong>Сообщение:</strong> <?php echo htmlspecialchars($e->getMessage()); ?></p>
                    <p><strong>Параметры подключения:</strong></p>
                    <ul>
                        <li>Хост: <code><?php echo htmlspecialchars($host); ?></code></li>
                        <li>База данных: <code><?php echo htmlspecialchars($db); ?></code></li>
                        <li>Пользователь: <code><?php echo htmlspecialchars($user); ?></code></li>
                        <li>Попыток: <?php echo $tries; ?></li>
                    </ul>
                    <p>Проверьте:</p>
                    <ol>
                        <li>Запущен ли контейнер с MySQL: <code>docker ps | grep mysql</code></li>
                        <li>Логи MySQL: <code>docker logs social-db</code></li>
                        <li>Сеть между контейнерами: <code>docker network ls</code></li>
                    </ol>
                    <p><a href="javascript:location.reload()">Обновить страницу</a></p>
                </div>
            </body>
            </html>
            <?php
            exit;
        }
        
        sleep(2); // Ждем 2 секунды перед следующей попыткой
    }
}

// Функция проверки авторизации
function checkAuth() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}