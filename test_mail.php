<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$to = "your-email@example.com";  // テスト用のメールアドレス
$subject = "テストメール from MAMP";
$message = "これはMAMPからのテストメールです。\n送信時刻: " . date('Y-m-d H:i:s');
$headers = "From: sender@example.com\r\n";
$headers .= "Reply-To: sender@example.com\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

echo "メール送信を試みます...<br>";

if(mail($to, $subject, $message, $headers)) {
    echo "メールが送信されました。<br>";
    echo "To: " . $to . "<br>";
    echo "Subject: " . $subject . "<br>";
} else {
    echo "メール送信に失敗しました。<br>";
    
    // エラー情報の表示
    if (error_get_last()) {
        print_r(error_get_last());
    }
}

// メール設定の確認
echo "<h2>現在のメール設定:</h2>";
echo "sendmail_path: " . ini_get('sendmail_path') . "<br>";
echo "SMTP: " . ini_get('SMTP') . "<br>";
echo "smtp_port: " . ini_get('smtp_port') . "<br>";
echo "sendmail_from: " . ini_get('sendmail_from') . "<br>";
?> 