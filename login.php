<?php
session_start();
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン - ToDoリスト</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="login-container">
        <h1 class="login-title">ToDoリスト ログイン</h1>
        <?php if ($error): ?>
            <div class="error-banner"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form action="auth/login.php" method="POST" class="login-form">
            <div class="form-group">
                <label for="email">メールアドレス</label>
                <input type="email" id="email" name="email" required placeholder="example@example.com">
                <div class="error-message"></div>
            </div>

            <div class="form-group">
                <label for="password">パスワード</label>
                <input type="password" id="password" name="password" required placeholder="パスワードを入力">
                <div class="error-message"></div>
            </div>

            <button type="submit" class="login-btn">ログイン</button>
        </form>
        <div class="register-link">
            アカウントをお持ちでない方は<a href="register.php">新規登録</a>
        </div>
    </div>
</body>
</html> 