<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// আপনার টেলিগ্রাম বট টোকেন
define('BOT_TOKEN', '8805535247:AAFZBrTqW0mDlT2IPQeDKPrMNnYEzflNZto'); 

// আপনার Firebase Realtime Database URL
define('FIREBASE_URL', 'https://refer2earn5-default-rtdb.asia-southeast1.firebasedatabase.app/');

// Telegram থেকে আসা ডাটা রিসিভ করা
$content = file_get_contents("php://input");
$update = json_decode($content, true);

// যদি সরাসরি ব্রাউজার থেকে ভিজিট করা হয়
if (!$update) {
    echo "Refer2Earn Bot Server is Running & Connected to Firebase!";
    exit;
}

// মেসেজ চেক করা
if (isset($update['message'])) {
    $chat_id = $update['message']['chat']['id'];
    $text = isset($update['message']['text']) ? trim($update['message']['text']) : '';
    $user_id = $update['message']['from']['id'];
    $first_name = $update['message']['from']['first_name'] ?? 'User';
    $username = $update['message']['from']['username'] ?? 'None';

    // যদি কেউ /start লেখে
    if ($text === '/start') {
        // ফায়ারবেসে ইউজার ডাটা সেভ করার ডেটা প্রস্তুত করা
        $userData = [
            'user_id'       => $user_id,
            'name'          => $first_name,
            'username'      => $username,
            'balance'       => 0.00,
            'total_earned'  => 0.00,
            'referrals'     => 0,
            'joined_at'     => date('Y-m-d H:i:s')
        ];

        // Firebase-এ ডাটা সেভ করা (PUT মেথড ব্যবহার করে user_id দিয়ে ইউনিক রেকর্ড রাখা)
        saveToFirebase("users/{$user_id}.json", $userData);

        // ইউজারকে ওয়েলকাম মেসেজ পাঠানো
        $reply = "স্বাগতম {$first_name}!\n\nRefer2Earn বটে আপনাকে স্বাগতম। আপনার অ্যাকাউন্ট সফলভাবে তৈরি হয়েছে।\n\nনিচের মেনু থেকে কাজ শুরু করুন:";
        sendTelegramMessage($chat_id, $reply);
    }
}

/**
 * Firebase Realtime Database এ ডাটা পাঠানোর ফাংশন
 */
function saveToFirebase($path, $data) {
    $url = FIREBASE_URL . $path;
    $jsonData = json_encode($data);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return $response;
}

/**
 * টেলিগ্রামে মেসেজ পাঠানোর ফাংশন
 */
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
