<?php
require_once 'db.php';
checkAuth();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
    $file = $_FILES['avatar'];
    if ($file['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
            $newName = uniqid('avatar_') . '.' . $ext;
            $uploadPath = __DIR__ . '/images/' . $newName;
            
            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                $stmt = $pdo->prepare("UPDATE users SET avatar_url = ? WHERE id = ?");
                $stmt->execute(['images/' . $newName, $_SESSION['user_id']]);
                header("Location: profile.php");
                exit;
            }
        }
    }
}

$id = $_GET['id'] ?? $_SESSION['user_id'];
$isOwn = ($id == $_SESSION['user_id']);

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) die('Пользователь не найден');

$status = null;
if (!$isOwn) {
    $fsStmt = $pdo->prepare("SELECT status FROM friendships WHERE (user_id1 = ? AND user_id2 = ?) OR (user_id1 = ? AND user_id2 = ?)");
    $fsStmt->execute([$_SESSION['user_id'], $id, $id, $_SESSION['user_id']]);
    $friendship = $fsStmt->fetch();
    $status = $friendship ? $friendship['status'] : null;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Профиль - GameNetwork</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <nav class="nav">
                <div class="logo">GameNetwork</div>
                <ul class="nav-links">
                    <li><a href="index.php">Лента</a></li>
                    <li><a href="profile.php" class="<?php echo $isOwn ? 'active' : ''; ?>">Мой профиль</a></li>
                    <li><a href="users.php">Игроки</a></li>
                    <li><a href="friends.php">Друзья</a></li>
                    <li><a href="login.php" style="color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.5); padding: 8px 15px; border-radius: 8px;">Выйти</a></li>
                </ul>
            </nav>
        </header>

        <div class="profile-container">
            <div class="profile-banner">
                <div class="profile-info">
                    <div style="position: relative;">
                        <img src="protected_image.php?file=<?php echo basename($user['avatar_url']); ?>" class="profile-avatar">
                        <?php if($isOwn): ?>
                        <form method="POST" enctype="multipart/form-data" style="margin-top: 10px; text-align: center;">
                            <label for="avatar-upload" class="btn" style="font-size: 12px; padding: 5px 10px; cursor: pointer;">
                                📷 Сменить
                            </label>
                            <input type="file" id="avatar-upload" name="avatar" style="display: none;" onchange="this.form.submit()">
                        </form>
                        <?php endif; ?>
                    </div>
                    
                    <div class="profile-details">
                        <h1><?php echo htmlspecialchars($user['username']); ?></h1>
                        <div class="profile-status">
                            <span class="online-dot"></span>
                            <span class="status-text">В сети</span>
                        </div>
                        <p class="registration-date">На сайте с: <?php echo date('d.m.Y', strtotime($user['created_at'])); ?></p>
                        
                        <div class="profile-actions">
                            <?php if (!$isOwn): ?>
                                <?php if (!$status): ?>
                                    <button class="btn add-friend-btn" onclick="addFriend('<?php echo $id; ?>')">+ Добавить в друзья</button>
                                <?php elseif ($status === 'pending'): ?>
                                    <button class="btn pending-btn" onclick="cancelRequest('<?php echo $id; ?>')" title="Нажмите, чтобы отменить">Запрос отправлен ✕</button>
                                <?php elseif ($status === 'accepted'): ?>
                                    <button class="btn" style="background: #10b981;">В друзьях ✓</button>
                                    <button class="btn remove-friend" onclick="removeFriend('<?php echo $id; ?>')">Удалить</button>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="profile-content">
                <div class="user-posts">
                    <h2>Посты пользователя</h2>
                    <p style="color: #94a3b8; text-align: center; padding: 20px;">Постов пока нет</p>
                </div>
                
                <div class="sidebar-info">
                    <div class="info-card">
                        <h3>Информация</h3>
                        <ul class="stats-list">
                            <li>ID: <?php echo $user['id']; ?></li>
                            <li>Статус: Игрок</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="/js/app.js"></script>
</body>
</html>