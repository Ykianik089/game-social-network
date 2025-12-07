<?php
require_once 'db.php';
checkAuth(); 
$file = basename($_GET['file']);
$path = __DIR__ . '/images/' . $file;

if (file_exists($path) && getimagesize($path)) {
    header('Content-Type: image/jpeg');
    header('Content-Length: ' . filesize($path));
    readfile($path);
} else {
    header("HTTP/1.0 404 Not Found");
    exit;
}