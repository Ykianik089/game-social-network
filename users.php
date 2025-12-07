<?php
require_once 'db.php';
checkAuth();
$stmt = $pdo->query("SELECT * FROM users WHERE id != {$_SESSION['user_id']}");
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Все игроки</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <nav class="nav">
                <div class="logo">GameNetwork</div>
                <ul class="nav-links">
                    <li><a href="index.php">Лента</a></li>
                    <li><a href="profile.php">Мой профиль</a></li>
                    <li><a href="users.php" class="active">Игроки</a></li>
                    <li><a href="friends.php">Друзья</a></li>
                    <li>
                        <a href="login.php" style="color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.5); padding: 8px 15px; border-radius: 8px;">
                            Выйти
                        </a>
                    </li>
                </ul>
            </nav>
        </header>

        <div class="users-container">
            <h1>Все игроки</h1>
            
            <div class="users-search">
                <input type="text" placeholder="Поиск игроков..." class="search-input">
                <button class="search-btn">Найти</button>
            </div>
            
            <div class="users-grid">
                <?php foreach($users as $user): ?>
                <div class="user-card">
                    <a href="profile.php?id=<?php echo $user['id']; ?>">
                        <img src="protected_image.php?file=<?php echo basename($user['avatar_url']); ?>" class="user-avatar">
                    </a>
                    <div class="user-info">
                        <h3><a href="profile.php?id=<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['username']); ?></a></h3>
                        <div class="user-status">
                            <span class="online-dot"></span>
                            <span>В сети</span>
                        </div>
                    </div>
                    <div class="user-actions">
                        <a href="profile.php?id=<?php echo $user['id']; ?>" class="btn view-profile">Профиль</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <script src="/js/app.js"></script>
</body>
</html>