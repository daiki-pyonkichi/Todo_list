<?php
session_start();
require_once('../config/database.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    // バリデーション
    if (empty($name) || empty($email) || empty($password) || empty($password_confirm)) {
        $_SESSION['error'] = '全ての項目を入力してください。';
        header('Location: ../register.php');
        exit;
    }

    if ($password !== $password_confirm) {
        $_SESSION['error'] = 'パスワードが一致しません。';
        header('Location: ../register.php');
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = '有効なメールアドレスを入力してください。';
        header('Location: ../register.php');
        exit;
    }

    try {
        // メールアドレスの重複チェック
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $_SESSION['error'] = 'このメールアドレスは既に登録されています。';
            header('Location: ../register.php');
            exit;
        }

        // 認証トークンを生成
        $verification_token = bin2hex(random_bytes(32));
        $token_expires = date('Y-m-d H:i:s', strtotime('+24 hours'));

        // ユーザー登録
        $stmt = $pdo->prepare('INSERT INTO users (name, email, password, verification_token, token_expires, is_verified) VALUES (?, ?, ?, ?, ?, 0)');
        $stmt->execute([
            $name,
            $email,
            password_hash($password, PASSWORD_DEFAULT),
            $verification_token,
            $token_expires
        ]);

        // 認証メールを送信
        $verification_link = "http://{$_SERVER['HTTP_HOST']}/verify_email.php?token=" . $verification_token;
        $to = $email;
        $subject = 'メールアドレスの認証';
        $message = "
            {$name}様\n\n
            ToDoリストへのご登録ありがとうございます。\n
            以下のリンクをクリックして、メールアドレスの認証を完了してください：\n\n
            {$verification_link}\n\n
            このリンクは24時間有効です。\n
            心当たりのない場合は、このメールを無視してください。\n\n
            ToDoリストチーム
        ";
        $headers = 'From: noreply@todolist.com' . "\r\n" .
                  'Reply-To: noreply@todolist.com' . "\r\n" .
                  'X-Mailer: PHP/' . phpversion();

        mail($to, $subject, $message, $headers);

        // 成功メッセージをセット
        $_SESSION['notification'] = [
            'type' => 'success',
            'message' => 'アカウントを作成しました。メールアドレスの認証を行ってください。'
        ];
        
        header('Location: ../login.php');
        exit;

    } catch (PDOException $e) {
        error_log($e->getMessage());
        $_SESSION['error'] = 'アカウントの作成に失敗しました。';
        header('Location: ../register.php');
        exit;
    }
}
?> 