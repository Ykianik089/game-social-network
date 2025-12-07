<?php
require_once 'db.php';
header('Content-Type: application/json');
$input = file_get_contents('php://input');
$data = json_decode($input, true);
$action = $data['action'] ?? '';
$currentUserId = $_SESSION['user_id'] ?? 0;

if (!$currentUserId) {
    echo json_encode(['success' => false, 'error' => 'Auth required']);
    exit;
}

try {
    if ($action === 'toggle_like') {
        $postId = $data['postId'];
        
        $stmt = $pdo->prepare("SELECT post_id FROM likes WHERE post_id = ? AND user_id = ?");
        $stmt->execute([$postId, $currentUserId]);
        
        if ($stmt->fetch()) {
            $pdo->prepare("DELETE FROM likes WHERE post_id = ? AND user_id = ?")->execute([$postId, $currentUserId]);
            $liked = false;
        } else {
            $pdo->prepare("INSERT IGNORE INTO likes (post_id, user_id) VALUES (?, ?)")->execute([$postId, $currentUserId]);
            $liked = true;
        }

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE post_id = ?");
        $stmt->execute([$postId]);
        $count = $stmt->fetchColumn();

        echo json_encode(['success' => true, 'liked' => $liked, 'count' => $count]);

    } elseif ($action === 'add_comment') {
        $postId = $data['postId'];
        $content = trim($data['content']);

        if (!empty($content)) {
            $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)");
            $stmt->execute([$postId, $currentUserId, $content]);
            
            $id = $pdo->lastInsertId();
            $stmt = $pdo->prepare("SELECT c.*, u.username, u.avatar_url FROM comments c JOIN users u ON c.user_id = u.id WHERE c.id = ?");
            $stmt->execute([$id]);
            $newComment = $stmt->fetch();
            
            echo json_encode(['success' => true, 'comment' => $newComment]);
        }

    } elseif ($action === 'delete_comment') {
        $commentId = $data['commentId'];
        $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ? AND user_id = ?");
        $stmt->execute([$commentId, $currentUserId]);
        
        echo json_encode(['success' => true]);

    } elseif ($action === 'add_friend') {
        $targetId = $data['userId'];
        $stmt = $pdo->prepare("INSERT IGNORE INTO friendships (user_id1, user_id2, status) VALUES (?, ?, 'pending')");
        $stmt->execute([$currentUserId, $targetId]);
        echo json_encode(['success' => true]);

    } elseif ($action === 'cancel_request') {
        $targetId = $data['userId'];
        $stmt = $pdo->prepare("DELETE FROM friendships WHERE user_id1 = ? AND user_id2 = ? AND status = 'pending'");
        $stmt->execute([$currentUserId, $targetId]);
        echo json_encode(['success' => true]);

    } elseif ($action === 'accept_friend') {
        $targetId = $data['userId'];
        $stmt = $pdo->prepare("UPDATE friendships SET status = 'accepted' WHERE user_id1 = ? AND user_id2 = ?");
        $stmt->execute([$targetId, $currentUserId]);
        echo json_encode(['success' => true]);

    } elseif ($action === 'remove_friend') {
        $targetId = $data['userId'];
        $stmt = $pdo->prepare("DELETE FROM friendships WHERE (user_id1 = ? AND user_id2 = ?) OR (user_id1 = ? AND user_id2 = ?)");
        $stmt->execute([$currentUserId, $targetId, $targetId, $currentUserId]);
        echo json_encode(['success' => true]);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}