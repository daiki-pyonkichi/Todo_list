<?php
// Composerでインストールしたライブラリを使用
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

try {
    $mail = new PHPMailer(true);

    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;    // デバッグ出力を有効化
    $mail->isSMTP();                          // SMTPを使用
    $mail->Host       = 'smtp.gmail.com';     // GmailのSMTPサーバー
    $mail->SMTPAuth   = true;                 // SMTP認証を有効化
    $mail->Username   = 'your-email@gmail.com'; // Gmailのメールアドレス
    $mail->Password   = 'your-app-password';    // Gmailのアプリパスワード
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;                  // TCPポート

    //Recipients
    $mail->setFrom('your-email@gmail.com', 'Sender Name');
    $mail->addAddress('recipient@example.com', 'Recipient Name');

    //Content
    $mail->isHTML(true);
    $mail->Subject = 'テストメール from Gmail SMTP';
    $mail->Body    = 'これはGmail SMTPを使用したテストメールです。<br>送信時刻: ' . date('Y-m-d H:i:s');
    $mail->AltBody = 'これはGmail SMTPを使用したテストメールです。\n送信時刻: ' . date('Y-m-d H:i:s');

    $mail->send();
    echo 'メールが送信されました';
} catch (Exception $e) {
    echo "メール送信に失敗しました。Mailer Error: {$mail->ErrorInfo}";
} 