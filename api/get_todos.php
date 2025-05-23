<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => '認証されていません。']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT * FROM todos 
        WHERE user_id = :user_id 
        ORDER BY deadline ASC
    ");
    
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $todos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'todos' => $todos
    ]);

} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'タスクの取得に失敗しました。',
        'details' => $e->getMessage()
    ]);
}
?> 