SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS gamenetwork CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gamenetwork;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    avatar_url VARCHAR(255) DEFAULT 'images/default-avatar.jpg',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS likes (
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    UNIQUE KEY (post_id, user_id),
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS friendships (
    user_id1 INT NOT NULL,
    user_id2 INT NOT NULL,
    status ENUM('pending', 'accepted') DEFAULT 'pending',
    PRIMARY KEY (user_id1, user_id2),
    FOREIGN KEY (user_id1) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id2) REFERENCES users(id) ON DELETE CASCADE
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Обновляем пароли на правильные
INSERT INTO users (username, email, password_hash, avatar_url) VALUES
('Player_1', 'admin@game.net', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'images/default-avatar.jpg'),
('CyberPunk_Fan', 'neo@game.net', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'images/default-avatar.jpg'),
('Witcher_Geralt', 'geralt@game.net', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'images/default-avatar.jpg');

INSERT INTO posts (user_id, content) VALUES
(2, 'Just finished the Phantom Liberty DLC. The ending is mind-blowing!'),
(3, 'Looking for a party for the raid tonight. Need a healer and a tank. PM me.');

INSERT INTO likes (post_id, user_id) VALUES (1, 1);

-- Создаем пользователя базы данных (если не создан автоматически)
CREATE USER IF NOT EXISTS 'user'@'%' IDENTIFIED BY 'userpassword';
GRANT ALL PRIVILEGES ON gamenetwork.* TO 'user'@'%';
FLUSH PRIVILEGES;