<?php
session_start();
require_once 'db_config.php';

$token = $_GET['token'] ?? '';
$message = '';
$type = 'error';

if (empty($token)) {
    $message = '無効なリクエストです。';
} else {
    try {
        // トークンの検証
        $stmt = $pdo->prepare('SELECT id, token_expires FROM users WHERE verification_token = ? AND is_verified = 0');
        $stmt->execute([$token]);
        $user = $stmt->fetch();

        if (!$user) {
            $message = 'このリンクは無効か、既に使用されています。';
        } else if (strtotime($user['token_expires']) < time()) {
            $message = 'このリンクの有効期限が切れています。';
        } else {
            // メールアドレスを認証済みに更新
            $stmt = $pdo->prepare('UPDATE users SET is_verified = 1, verification_token = NULL WHERE id = ?');
            $stmt->execute([$user['id']]);
            
            $type = 'success';
            $message = 'メールアドレスの認証が完了しました。';
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
        $message = 'エラーが発生しました。';
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>メール認証 - ToDoリスト</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="login-container">
        <h1 class="login-title">メール認証</h1>
        <div class="verification-status <?php echo $type === 'success' ? 'verified' : 'pending'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
        <div class="register-link" style="margin-top: 2rem;">
            <a href="login.php">ログインページへ戻る</a>
        </div>
    </div>
</body>
</html> 