<?php
session_start();
require_once '../api/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];

    if (!$email) {
        $_SESSION['error'] = 'メールアドレスの形式が正しくありません。';
        header('Location: ../login.php');
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            header('Location: ../index.php');
        } else {
            $_SESSION['error'] = 'メールアドレスまたはパスワードが正しくありません。';
            header('Location: ../login.php');
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = 'エラーが発生しました。';
        header('Location: ../login.php');
    }
    exit;
}
?> 