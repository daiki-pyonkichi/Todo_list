<?php
$host = 'localhost:8889';
$dbname = 'todo_app';
$username = 'root';
$password = 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "接続エラー: " . $e->getMessage();
    exit;
}
?> 