<?>
<?php
/**
 * Project: Enterprise Telegram Refer & Earn Bot
 * Core Functions Handler (`functions.php`)
 */

declare(strict_types=1);

require_once __DIR__ . '/database.php';

function apiRequest(string $method, array $parameters): mixed {
    $url = "https://api.telegram.org/bot" . BOT_TOKEN . "/" . $method;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        error_log("Curl error in $method: " . curl_error($ch));
        curl_close($ch);
        return false;
    }
    curl_close($ch);
    return json_decode($response, true);
}

function sendMessage(int|string $chat_id, string $text, ?array $reply_markup = null, string $parse_mode = 'HTML'): mixed {
    $data = [
        'chat_id' => $chat_id,
        'text' => $text,
        'parse_mode' => $parse_mode
    ];
    if ($reply_markup) {
        $data['reply_markup'] = $reply_markup;
    }
    return apiRequest('sendMessage', $data);
}

function editMessageText(int|string $chat_id, int $message_id, string $text, ?array $reply_markup = null): mixed {
    $data = [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => $text,
        'parse_mode' => 'HTML'
    ];
    if ($reply_markup) {
        $data['reply_markup'] = $reply_markup;
    }
    return apiRequest('editMessageText', $data);
}

function answerCallbackQuery(string $callback_query_id, string $text = '', bool $show_alert = false): mixed {
    return apiRequest('answerCallbackQuery', [
        'callback_query_id' => $callback_query_id,
        'text' => $text,
        'show_alert' => $show_alert
    ]);
}

function isAdmin(int|string $user_id): bool {
    return in_array((int)$user_id, ADMIN_IDS, true);
}

function getSetting(string $key): string {
    $db = Database::getConnection();
    $stmt = $db->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
    $stmt->execute([$key]);
    return $stmt->fetchColumn() ?: '';
}
