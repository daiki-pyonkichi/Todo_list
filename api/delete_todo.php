<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => '認証エラー']);
    exit;
}

// POSTデータを取得
$data = json_decode(file_get_contents('php://input'), true);
$todo_id = $data['id'] ?? null;

if (!$todo_id) {
    echo json_encode(['success' => false, 'error' => 'タスクIDが指定されていません']);
    exit;
}

try {
    require_once '../config/database.php';
    
    // タスクの所有者を確認
    $stmt = $pdo->prepare('SELECT user_id FROM todos WHERE id = ?');
    $stmt->execute([$todo_id]);
    $todo = $stmt->fetch();
    
    if (!$todo || $todo['user_id'] != $_SESSION['user_id']) {
        echo json_encode(['success' => false, 'error' => '権限がありません']);
        exit;
    }
    
    // タスクを削除
    $stmt = $pdo->prepare('DELETE FROM todos WHERE id = ? AND user_id = ?');
    $result = $stmt->execute([$todo_id, $_SESSION['user_id']]);
    
    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => '削除に失敗しました']);
    }
} catch (PDOException $e) {
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'error' => 'データベースエラー']);
}
?> 