<?php
// Error reporting चालू রাখা হলো
error_reporting(E_ALL);
ini_set('display_errors', 1);

// আপনার টেলিগ্রাম বট টোকেন
define('BOT_TOKEN', '8805535247:AAFZBrTqW0mDlT2IPQeDKPrMNnYEzflNZto'); 

// Telegram থেকে আসা ডাটা রিসিভ করা
$content = file_get_contents("php://input");
$update = json_decode($content, true);

// যদি সরাসরি ব্রাউজার থেকে ভিজিট করা হয়
if (!$update) {
    echo "Refer2Earn Bot Server is Running on Render!";
    exit;
}

// মেসেজ চেক করা
if (isset($update['message'])) {
    $chat_id = $update['message']['chat']['id'];
    $text = isset($update['message']['text']) ? trim($update['message']['text']) : '';
    $first_name = $update['message']['from']['first_name'] ?? 'User';

    // যদি কেউ /start লেখে
    if ($text === '/start') {
        $reply = "স্বাগতম {$first_name}!\n\nRefer2Earn বটে আপনাকে স্বাগতম। আমাদের মাধ্যমে আপনি খুব সহজে রেফার করে এবং টাস্ক পূরণ করে আয় করতে পারবেন।\n\nনিচের মেনু থেকে কাজ শুরু করুন:";
        sendTelegramMessage($chat_id, $reply);
    }
}

// টেলিগ্রামে মেসেজ পাঠানোর ফাংশন
function sendTelegramMessage($chat_id, $text) {
    $url = "https://api.telegram.org/bot" . BOT_TOKEN . "/sendMessage";
    $data = [
        'chat_id' => $chat_id,
        'text' => $text,
        'parse_mode' => 'HTML'
    ];

    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        ]
    ];

    $context  = stream_context_create($options);
    @file_get_contents($url, false, $context);
}
?>
