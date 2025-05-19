<?php
$botToken = '8093851911:AAEyrP0mC_P-G3OrO7yV8CG5jlfUp7Hp9qA';
$chatId = '5666631780';

// Get server-side info
function getUserIP() {
    foreach (['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'] as $key) {
        if (!empty($_SERVER[$key])) return $_SERVER[$key];
    }
    return 'UNKNOWN';
}

function isBot($ua) {
    $bots = ['bot', 'crawl', 'slurp', 'spider', 'mediapartners', 'google', 'bing', 'yandex', 'duckduckgo'];
    $ua = strtolower($ua);
    foreach ($bots as $bot) {
        if (strpos($ua, $bot) !== false) return true;
    }
    return false;
}

// Extract headers
$ip         = getUserIP();
$ua         = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
$referrer   = $_SERVER['HTTP_REFERER'] ?? 'Direct / None';
$acceptLang = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'Unknown';
$botFlag    = isBot($ua) ? "🤖 Known Bot (UA Match)" : "🧍 Likely Human";

// Geo IP lookup
$geo = json_decode(file_get_contents("http://ip-api.com/json/{$ip}"), true);
$country = $geo['country'] ?? 'Unknown';
$region  = $geo['regionName'] ?? '';
$city    = $geo['city'] ?? '';
$location = "{$city}, {$region}, {$country}";

// Get browser-side info from JS
$data = json_decode(file_get_contents("php://input"), true);
$timezone     = $data['timezone'] ?? 'Unknown';
$screen       = $data['screen'] ?? 'Unknown';
$platform     = $data['platform'] ?? 'Unknown';
$browserLang  = $data['browserLang'] ?? 'Unknown';
$clientBot    = $data['browserBotFlag'] ?? 'Unknown';
$cores        = $data['cores'] ?? 'Unknown';
$ram          = $data['ram'] ?? 'Unknown';

// Generate visitor fingerprint
$fingerprint = md5($ip . $ua . $platform . $screen);

// Build final message
$message  = "🛬 *New Landing Page Visit*\n";
$message .= "-----------------------------\n";
$message .= "🌍 *IP:* `{$ip}`\n";
$message .= "📍 *Location:* {$location}\n";
$message .= "🔗 *Referrer:* {$referrer}\n";
$message .= "🗺 *Timezone:* {$timezone}\n";
$message .= "🧭 *Accept-Lang:* {$acceptLang}\n";
$message .= "🗣 *Browser Lang:* {$browserLang}\n";
$message .= "🖥 *Platform:* {$platform}\n";
$message .= "🖼 *Screen:* {$screen}\n";
$message .= "⚙️ *Cores:* {$cores} | *RAM:* {$ram} GB\n";
$message .= "📱 *User-Agent:* `" . substr($ua, 0, 200) . "`\n";
$message .= "🔍 *Bot Detection:* {$botFlag} + {$clientBot}\n";
$message .= "🆔 *Visitor Hash:* `{$fingerprint}`\n";
$message .= "📅 *Time:* " . date("Y-m-d H:i:s");

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
