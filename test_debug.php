<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== ПРОСТАЯ ПРОВЕРКА СИСТЕМЫ ===\n\n";

echo "1. PHP версия: " . PHP_VERSION . "\n";

echo "2. Проверка файлов:\n";
$files = ['db.php', 'api.php', 'index.php', 'profile.php'];
foreach ($files as $file) {
    echo "  - $file: ";
    if (file_exists($file)) {
        echo "✅ НАЙДЕН\n";
    } else {
        echo "❌ НЕ НАЙДЕН\n";
    }
}

echo "\n3. Проверка папок:\n";
$dirs = ['images', 'css', 'js', 'tests'];
foreach ($dirs as $dir) {
    echo "  - $dir: ";
    if (is_dir($dir)) {
        echo "✅ СУЩЕСТВУЕТ\n";
    } else {
        echo "❌ НЕТ\n";
    }
}

echo "\n=== ПРОВЕРКА ЗАВЕРШЕНА ===\n";
?>