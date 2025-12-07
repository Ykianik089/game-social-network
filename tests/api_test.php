<?php
// game-social-network/tests/api_test.php
// Тестирование основных функций API

require_once __DIR__ . '/../db.php';

class ApiTest {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        session_start();
        $_SESSION['user_id'] = 1; // Тестовый пользователь
    }
    
    public function runAllTests() {
        echo "=== API ТЕСТИРОВАНИЕ ===\n\n";
        
        $this->testDatabaseConnection();
        $this->testApiEndpoints();
        $this->testBusinessLogic();
        
        echo "\n=== ТЕСТИРОВАНИЕ ЗАВЕРШЕНО ===\n";
    }
    
    private function testDatabaseConnection() {
        echo "1. Тест подключения к БД: ";
        try {
            $stmt = $this->pdo->query("SELECT 1");
            $result = $stmt->fetchColumn();
            echo "УСПЕХ\n";
        } catch (Exception $e) {
            echo "ОШИБКА: " . $e->getMessage() . "\n";
        }
    }
    
    private function testApiEndpoints() {
        echo "2. Тест API endpoints:\n";
        
        $tests = [
            'like' => ['action' => 'toggle_like', 'postId' => 1],
            'comment' => ['action' => 'add_comment', 'postId' => 1, 'content' => 'Тест'],
            'friend' => ['action' => 'add_friend', 'userId' => 2],
        ];
        
        foreach ($tests as $name => $data) {
            echo "  - $name: ";
            $result = $this->callApi($data);
            echo $result ? "УСПЕХ\n" : "ОШИБКА\n";
        }
    }
    
    private function testBusinessLogic() {
        echo "3. Тест бизнес-логики:\n";
        
        // Тест уникальности лайков
        echo "  - Уникальность лайков: ";
        $this->callApi(['action' => 'toggle_like', 'postId' => 1]);
        $this->callApi(['action' => 'toggle_like', 'postId' => 1]);
        
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM likes WHERE post_id = 1 AND user_id = 1");
        $stmt->execute();
        $count = $stmt->fetchColumn();
        
        echo ($count <= 1) ? "УСПЕХ\n" : "ОШИБКА\n";
        
        // Тест удаления только своих комментариев
        echo "  - Удаление комментариев: ";
        $stmt = $this->pdo->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (1, 1, 'Тест')");
        $stmt->execute();
        $commentId = $this->pdo->lastInsertId();
        
        $result = $this->callApi(['action' => 'delete_comment', 'commentId' => $commentId]);
        echo $result ? "УСПЕХ\n" : "ОШИБКА\n";
    }
    
    private function callApi($data) {
        $_POST = $data;
        ob_start();
        include __DIR__ . '/../api.php';
        $output = ob_get_clean();
        $result = json_decode($output, true);
        return $result && $result['success'];
    }
}

// Запуск тестов
try {
    $test = new ApiTest($pdo);
    $test->runAllTests();
} catch (Exception $e) {
    echo "Ошибка тестирования: " . $e->getMessage() . "\n";
}