<?php
require_once 'db.php';
checkAuth();
$myId = $_SESSION['user_id'];
$stmt = $pdo->prepare("
    SELECT u.* FROM users u 
    JOIN friendships f ON (f.user_id1 = u.id OR f.user_id2 = u.id)
    WHERE (f.user_id1 = ? OR f.user_id2 = ?) 
    AND f.status = 'accepted' AND u.id != ?
");
$stmt->execute([$myId, $myId, $myId]);
$friends = $stmt->fetchAll();

$reqStmt = $pdo->prepare("
    SELECT u.* FROM users u 
    JOIN friendships f ON f.user_id1 = u.id
    WHERE f.user_id2 = ? AND f.status = 'pending'
");
$reqStmt->execute([$myId]);
$requests = $reqStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Мои друзья</title>
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
                    <li><a href="users.php">Игроки</a></li>
                    <li><a href="friends.php" class="active">Друзья</a></li>
                    <li>
                        <a href="login.php" style="color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.5); padding: 8px 15px; border-radius: 8px;">
                            Выйти
                        </a>
                    </li>
                </ul>
            </nav>
        </header>

        <div class="friends-container">
            <h1>Мои друзья</h1>
            
            <div class="friends-tabs">
                <button class="tab-btn active" data-target="tab-friends">Все друзья (<?php echo count($friends); ?>)</button>
                <button class="tab-btn" data-target="tab-requests">Заявки (<?php echo count($requests); ?>)</button>
            </div>
            
            <div class="friends-list-container">
                <div id="tab-friends" class="tab-content active">
                    <div class="friends-grid">
                        <?php foreach($friends as $friend): ?>
                        <div class="friend-card">
                            <div class="friend-info">
                                <a href="profile.php?id=<?php echo $friend['id']; ?>">
                                    <img src="protected_image.php?file=<?php echo basename($friend['avatar_url']); ?>" class="friend-avatar">
                                </a>
                                <div class="friend-details">
                                    <h3><a href="profile.php?id=<?php echo $friend['id']; ?>"><?php echo htmlspecialchars($friend['username']); ?></a></h3>
                                    <div class="friend-status">
                                        <span class="online-dot"></span> <span>В сети</span>
                                    </div>
                                </div>
                            </div>
                            <div class="friend-actions">
                                <button class="btn remove-friend" onclick="removeFriend(<?php echo $friend['id']; ?>)">Удалить</button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php if(empty($friends)) echo "<p style='color:#ccc'>Список друзей пуст</p>"; ?>
                    </div>
                </div>
                
                <div id="tab-requests" class="tab-content">
                    <div class="friends-grid">
                        <?php foreach($requests as $req): ?>
                        <div class="friend-card">
                            <div class="friend-info">
                                <img src="protected_image.php?file=<?php echo basename($req['avatar_url']); ?>" class="friend-avatar">
                                <div class="friend-details">
                                    <h3><?php echo htmlspecialchars($req['username']); ?></h3>
                                    <div class="friend-request">Входящая заявка</div>
                                </div>
                            </div>
                            <div class="friend-actions">
                                <button class="btn accept-friend" onclick="acceptFriend(<?php echo $req['id']; ?>)">Принять</button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php if(empty($requests)) echo "<p style='color:#ccc'>Нет новых заявок</p>"; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="/js/app.js"></script>
</body>
</html>