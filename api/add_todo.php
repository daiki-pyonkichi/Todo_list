<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

// エラーログの設定
ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// セッションチェック
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => '認証されていません。']);
    exit;
}

// POSTデータの取得とバリデーション
$data = json_decode(file_get_contents('php://input'), true);
error_log('Received data: ' . print_r($data, true));

if (!isset($data['task']) || !isset($data['deadline'])) {
    http_response_code(400);
    echo json_encode([
        'error' => '必要なデータが不足しています。',
        'received' => $data
    ]);
    exit;
}

try {
    // データの準備
    $userId = $_SESSION['user_id'];
    $task = trim($data['task']);
    $deadline = trim($data['deadline']);
    $detail = isset($data['detail']) ? trim($data['detail']) : '';

    // 基本的なバリデーション
    if (empty($task)) {
        throw new Exception('タスク名を入力してください。');
    }
    if (empty($deadline)) {
        throw new Exception('締め切り日を設定してください。');
    }

    // SQLの準備と実行
    $stmt = $pdo->prepare("
        INSERT INTO todos (user_id, task, detail, deadline) 
        VALUES (:user_id, :task, :detail, :deadline)
    ");

    $params = [
        ':user_id' => $userId,
        ':task' => $task,
        ':detail' => $detail,
        ':deadline' => $deadline
    ];
    
    error_log('Executing SQL with params: ' . print_r($params, true));
    
    $stmt->execute($params);
    $newTodoId = $pdo->lastInsertId();

    // 追加したタスクの情報を取得
    $stmt = $pdo->prepare("SELECT * FROM todos WHERE id = :id");
    $stmt->execute([':id' => $newTodoId]);
    $todo = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'message' => 'タスクが追加されました。',
        'todo' => $todo
    ]);

} catch (PDOException $e) {
    error_log('Database Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'データベースエラーが発生しました。',
        'details' => $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log('Application Error: ' . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}
?> 