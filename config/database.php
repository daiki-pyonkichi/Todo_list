<?php
// 環境変数からデータベース接続情報を取得
$db_url = getenv('DATABASE_URL');
$db_connection = getenv('DB_CONNECTION') ?: 'mysql';

if ($db_url) {
    // PostgreSQLの場合
    $db_parts = parse_url($db_url);
    $db_host = $db_parts['host'];
    $db_port = $db_parts['port'];
    $db_name = ltrim($db_parts['path'], '/');
    $db_user = $db_parts['user'];
    $db_pass = $db_parts['pass'];
} else {
    // デフォルト値（ローカル開発用）
    $db_host = 'localhost';
    $db_port = '3306';
    $db_name = 'todo_app';
    $db_user = 'root';
    $db_pass = 'root';
}

try {
    if ($db_connection === 'pgsql') {
        $dsn = "pgsql:host={$db_host};port={$db_port};dbname={$db_name}";
    } else {
        $dsn = "mysql:host={$db_host};port={$db_port};dbname={$db_name}";
    }

    $pdo = new PDO(
        $dsn,
        $db_user,
        $db_pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ]
    );
} catch (PDOException $e) {
    exit('データベース接続に失敗しました。' . $e->getMessage());
} 