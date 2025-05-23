<?php
session_start();

// セッションを破棄
$_SESSION = array();
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-42000, '/');
}
session_destroy();

// ログアウト成功メッセージをセット
session_start();
$_SESSION['notification'] = [
    'type' => 'success',
    'message' => '正常にログアウトされました'
];

// ログイン画面にリダイレクト
header('Location: login.php');
exit; 