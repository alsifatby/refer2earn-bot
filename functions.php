<?php
/**
 * Project: Enterprise Telegram Refer & Earn Bot
 * Core Functions (`functions.php`)
 */

declare(strict_types=1);

// কনফিগ থেকে টোকেন নিয়ে টেলিগ্রাম এপিআইতে রিকোয়েস্ট পাঠানোর ফাংশন
function sendTelegramRequest(string $method, array $data): mixed {
    $url = "https://api.telegram.org/bot" . BOT_TOKEN . "/" . $method;
    
    $options = [
        'http' => [
            'header'  => "Content-Type: application/json\r\n",
            'method'  => 'POST',
            'content' => json_encode($data),
            'timeout' => 30
        ]
    ];
    
    $context = stream_context_create($options);
    $result = @file_get_contents($url, false, $context);
    
    if ($result === false) {
        return false;
    }
    
    return json_decode($result, true);
}

// সাধারণ টেক্সট বা মেসেজ পাঠানোর ফাংশন
function sendMessage(int|string $chat_id, string $text, array $keyboard = []): mixed {
    $data = [
        'chat_id' => $chat_id,
        'text' => $text,
        'parse_mode' => 'HTML'
    ];
    
    if (!empty($keyboard)) {
        if (isset($keyboard['inline_keyboard']) || isset($keyboard['keyboard'])) {
            $data['reply_markup'] = json_encode($keyboard);
        }
    }
    
    return sendTelegramRequest('sendMessage', $data);
}

// ইনলাইন বাটন ক্লিকের নোটিফিকেশন বন্ধ করার ফাংশন
function answerCallbackQuery(string $callback_id, string $text = '', bool $alert = false): mixed {
    $data = [
        'callback_query_id' => $callback_id,
        'text' => $text,
        'show_alert' => $alert
    ];
    
    return sendTelegramRequest('answerCallbackQuery', $data);
}

// ইউজার অ্যাডমিন কি না তা চেক করার ফাংশন
function isAdmin(int|string $user_id): bool {
    $admins = [123456789]; // এখানে আপনার টেলিগ্রাম আইডি বসাতে পারেন
    return in_array((int)$user_id, $admins, true);
}

// সেটিংস ফেচ করার ডামি ফাংশন
function getSetting(string $key): string {
    return '0'; // ডিফল্ট মেইনটেনেন্স অফ রাখা হলো
}
