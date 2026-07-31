<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/functions.php';

$content = file_get_contents("php://input");
$update = json_decode($content, true);

if (!$update) {
    echo "Enterprise Telegram Bot Webhook Active & Running Successfully.";
    exit;
}

$chat_id = '';
$user_id = '';
$text = '';
$callback_query_id = '';

if (isset($update["message"])) {
    $chat_id = $update["message"]["chat"]["id"];
    $user_id = $update["message"]["from"]["id"];
    $text = $update["message"]["text"] ?? '';
} elseif (isset($update["callback_query"])) {
    $chat_id = $update["callback_query"]["message"]["chat"]["id"];
    $user_id = $update["callback_query"]["from"]["id"];
    $text = $update["callback_query"]["data"];
    $callback_query_id = $update["callback_query"]["id"];
}

if (!empty($text)) {
    $db = Database::getConnection();

    if ($text === '/start' || str_starts_with($text, '/start ref_')) {
        if (file_exists(__DIR__ . '/modules/start.php')) {
            require_once __DIR__ . '/modules/start.php';
        }
    } elseif ($text === '🏠 Main Menu' || $text === 'menu_home') {
        if (file_exists(__DIR__ . '/modules/home.php')) {
            require_once __DIR__ . '/modules/home.php';
            $home = new HomeDashboard($db);
            $home->renderDashboard($chat_id, $user_id);
        }
    } elseif ($text === '👤 Profile' || $text === 'profile') {
        if (file_exists(__DIR__ . '/modules/profile.php')) {
            require_once __DIR__ . '/modules/profile.php';
            $profile = new UserProfile($db);
            $profile->renderProfile($chat_id, $user_id);
        }
    } elseif ($text === '🔗 Referrals' || $text === 'referral') {
        if (file_exists(__DIR__ . '/modules/refer.php')) {
            require_once __DIR__ . '/modules/refer.php';
            $refer = new ReferralSystem($db);
            $refer->renderReferralStats($chat_id, $user_id);
        }
    } elseif ($text === '📋 Tasks' || $text === 'tasks') {
        if (file_exists(__DIR__ . '/modules/tasks.php')) {
            require_once __DIR__ . '/modules/tasks.php';
            $tasks = new TaskManager($db);
            $tasks->renderTaskList($chat_id, $user_id);
        }
    } elseif ($text === '💳 Withdraw' || $text === 'withdraw') {
        if (file_exists(__DIR__ . '/modules/withdraw.php')) {
            require_once __DIR__ . '/modules/withdraw.php';
            $withdraw = new WithdrawSystem($db);
            $withdraw->renderMethods($chat_id, $user_id);
        }
    } else {
        sendMessage($chat_id, "Welcome! Please use the menu below to navigate.");
    }
}

if (!empty($callback_query_id)) {
    answerCallbackQuery($callback_query_id);
}
