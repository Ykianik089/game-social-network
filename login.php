<?php
require_once 'db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $_SESSION['user_id'] = $_POST['user_id'];
    header('Location: index.php');
    exit;
}

$stmt = $pdo->query("SELECT * FROM users");
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход - GameNetwork</title>
    <link rel="stylesheet" href="/css/style.css">
    <style>
        .login-container { max-width: 600px; margin: 100px auto; text-align: center; }
        .login-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 20px; margin-top: 30px; }
        .login-card { cursor: pointer; border: 2px solid transparent; transition: 0.3s; }
        .login-card:hover { border-color: #3b82f6; transform: translateY(-5px); }
    </style>
</head>
<body>
    <div class="container login-container">
        <h1>Выберите персонажа</h1>
        <p style="color: #94a3b8;">Демонстрационный вход (без пароля)</p>
        
        <div class="login-grid">
            <?php foreach($users as $user): ?>
            <form method="POST" style="display: contents;">
                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                <button type="submit" class="user-card login-card" style="width: 100%; border: none; padding: 20px; color: white;">
                    <img src="protected_image.php?file=<?php echo basename($user['avatar_url']); ?>" class="user-avatar">
                    <h3><?php echo htmlspecialchars($user['username']); ?></h3>
                </button>
            </form>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>