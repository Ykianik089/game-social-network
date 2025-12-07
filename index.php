<?php
require_once 'db.php';
checkAuth();
$stmt = $pdo->query("
    SELECT p.*, u.username, u.avatar_url, 
    (SELECT COUNT(*) FROM likes WHERE post_id = p.id AND user_id = {$_SESSION['user_id']}) as liked_by_me
    FROM posts p 
    JOIN users u ON p.user_id = u.id 
    ORDER BY p.created_at DESC
");
$posts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Лента</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <nav class="nav">
                <div class="logo">GameNetwork</div>
                <ul class="nav-links">
                    <li><a href="index.php" class="active">Лента</a></li>
                    <li><a href="profile.php">Мой профиль</a></li>
                    <li><a href="users.php">Игроки</a></li>
                    <li><a href="friends.php">Друзья</a></li>
                    <li>
                        <a href="login.php" style="color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.5); padding: 8px 15px; border-radius: 8px;">
                            Выйти
                        </a>
                    </li>
                </ul>
            </nav>
        </header>

        <div class="main-content">
            <div class="feed">
                <h1>Новостная лента</h1>
                
                <?php foreach($posts as $post): ?>
                <div class="post" data-post-id="<?php echo $post['id']; ?>">
                    <div class="post-header">
                        <img src="protected_image.php?file=<?php echo basename($post['avatar_url']); ?>" class="avatar">
                        <div class="post-author">
                            <h3><a href="profile.php?id=<?php echo $post['user_id']; ?>"><?php echo htmlspecialchars($post['username']); ?></a></h3>
                            <span class="post-time"><?php echo date('d.m.Y H:i', strtotime($post['created_at'])); ?></span>
                        </div>
                    </div>
                    
                    <div class="post-content">
                        <p><?php echo htmlspecialchars($post['content']); ?></p>
                    </div>
                    
                    <div class="post-actions">
                        <button class="like-btn" onclick="toggleLike(<?php echo $post['id']; ?>)">
                            <span class="like-icon" style="<?php echo $post['liked_by_me'] ? 'color:#ef4444' : ''; ?>">
                                <?php echo $post['liked_by_me'] ? '❤️' : '🤍'; ?>
                            </span>
                            Нравится
                        </button>
                        <button class="comment-btn">💬 Комментировать</button>
                    </div>
                    
                    <div class="comments-section">
                        <?php
                        $cStmt = $pdo->prepare("SELECT c.*, u.username, u.avatar_url FROM comments c JOIN users u ON c.user_id = u.id WHERE c.post_id = ? ORDER BY c.created_at ASC");
                        $cStmt->execute([$post['id']]);
                        $comments = $cStmt->fetchAll();
                        foreach($comments as $comment):
                        ?>
                        <div class="comment">
                            <img src="protected_image.php?file=<?php echo basename($comment['avatar_url']); ?>" class="avatar-small">
                            <div class="comment-content">
                                <strong><?php echo htmlspecialchars($comment['username']); ?></strong>
                                <p><?php echo htmlspecialchars($comment['content']); ?></p>
                            </div>
                            <?php if($comment['user_id'] == $_SESSION['user_id']): ?>
                                <button onclick="deleteComment(<?php echo $comment['id']; ?>, this)" style="color: #ef4444; font-size: 12px; opacity: 0.7;">✕</button>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                        
                        <div class="comment-form">
                            <input type="text" class="comment-input" placeholder="Напишите комментарий...">
                            <button class="send-comment" onclick="addComment(<?php echo $post['id']; ?>)">Send</button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <aside class="sidebar">
                <div class="online-users">
                    <h3>Онлайн сейчас</h3>
                    <ul>
                        <li><span class="online-dot"></span> Player_1</li>
                        <li><span class="online-dot"></span> CyberPunk_Fan</li>
                    </ul>
                </div>
            </aside>
        </div>
    </div>
    <script src="/js/app.js"></script>
</body>
</html>