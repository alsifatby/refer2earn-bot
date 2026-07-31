<?php
/**
 * Project: Enterprise Telegram Refer & Earn Bot
 * Core Dispatcher / Webhook Entry (`index.php`)
 */

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

// গ্লোবাল মেইনটেনেন্স মোড চেক
if (getSetting('maintenance_mode') === '1') {
    if (isset($update['message'])) {
        $chat_id = $update['message']['chat']['id'];
        $user_id = $update['message']['from']['id'];
        if (!isAdmin($user_id)) {
            sendMessage($chat_id, "⚠️ Bot is currently under maintenance. Please try again later.");
            exit;
        }
    }
}

// মেসেজ বা কমান্ড রাউটিং
if (isset($update['message'])) {
    $message = $update['message'];
    $chat_id = $message['chat']['id'];
    $user_id = $message['from']['id'];
    $text = trim($message['text'] ?? '');

    $db = Database::getConnection();

    if (str_starts_with($text, '/start')) {
        require_once __DIR__ . '/modules/start.php';
        // start মডিউল হ্যান্ডেল হবে
    } elseif ($text === '🏠 Main Menu' || $text === '/menu') {
        require_once __DIR__ . '/modules/home.php';
        $home = new HomeDashboard($db);
        $home->renderDashboard($chat_id, $user_id);
    } elseif ($text === '👤 Profile') {
        require_once __DIR__ . '/modules/profile.php';
        $profile = new UserProfile($db);
        $profile->renderProfile($chat_id, $user_id);
    } elseif ($text === '🔗 Referrals') {
        require_once __DIR__ . '/modules/refer.php';
        $ref = new ReferralSystem($db);
        $ref->renderReferralStats($chat_id, $user_id);
    } elseif ($text === '📋 Tasks') {
        require_once __DIR__ . '/modules/tasks.php';
        $tasks = new TaskManager($db);
        $tasks->renderTaskList($chat_id, $user_id);
    } elseif ($text === '💳 Withdraw') {
        require_once __DIR__ . '/modules/withdraw.php';
        $wd = new WithdrawSystem($db);
        $wd->renderMethods($chat_id, $user_id);
    } elseif (isAdmin($user_id) && str_starts_with($text, '/admin')) {
        require_once __DIR__ . '/modules/admin.php';
        $admin = new AdminPanel($db);
        $admin->handleAdminCommand($chat_id, $text);
    } else {
        // গিফট কোড বা অন্য টেক্সট ইনপুট
        require_once __DIR__ . '/modules/giftcode.php';
        $gift = new GiftCodeSystem($db);
        if ($gift->redeemCode($chat_id, $user_id, $text)) {
            exit;
        }
        
        // ডিফল্ট হোম মেনু
        require_once __DIR__ . '/modules/home.php';
        $home = new HomeDashboard($db);
        $home->renderDashboard($chat_id, $user_id);
    }
}

// ইনলাইন বাটন ক্লিক (Callback Query) রাউটিং
if (isset($update['callback_query'])) {
    $callback = $update['callback_query'];
    $callback_id = $callback['id'];
    $chat_id = $callback['message']['chat']['id'];
    $message_id = $callback['message']['message_id'];
    $user_id = $callback['from']['id'];
    $data = $callback['data'];

    $db = Database::getConnection();

    if ($data === 'menu_home') {
        answerCallbackQuery($callback_id);
        require_once __DIR__ . '/modules/home.php';
        $home = new HomeDashboard($db);
        $home->renderDashboard($chat_id, $user_id);
    } elseif ($data === 'profile') {
        answerCallbackQuery($callback_id);
        require_once __DIR__ . '/modules/profile.php';
        $profile = new UserProfile($db);
        $profile->renderProfile($chat_id, $user_id);
    } elseif ($data === 'referral') {
        answerCallbackQuery($callback_id);
        require_once __DIR__ . '/modules/refer.php';
        $ref = new ReferralSystem($db);
        $ref->renderReferralStats($chat_id, $user_id);
    } elseif ($data === 'tasks') {
        answerCallbackQuery($callback_id);
        require_once __DIR__ . '/modules/tasks.php';
        $tasks = new TaskManager($db);
        $tasks->renderTaskList($chat_id, $user_id);
    } elseif (str_starts_with($data, 'view_task_')) {
        $task_id = (int)str_replace('view_task_', '', $data);
        require_once __DIR__ . '/modules/tasks.php';
        $tasks = new TaskManager($db);
        $tasks->renderTaskDetails($callback_id, $chat_id, $message_id, $user_id, $task_id);
    } elseif (str_starts_with($data, 'claim_task_')) {
        $task_id = (int)str_replace('claim_task_', '', $data);
        require_once __DIR__ . '/modules/tasks.php';
        $tasks = new TaskManager($db);
        $tasks->claimTaskReward($callback_id, $chat_id, $message_id, $user_id, $task_id);
    } elseif ($data === 'withdraw') {
        answerCallbackQuery($callback_id);
        require_once __DIR__ . '/modules/withdraw.php';
        $wd = new WithdrawSystem($db);
        $wd->renderMethods($chat_id, $user_id);
    } elseif ($data === 'leaderboard') {
        answerCallbackQuery($callback_id);
        require_once __DIR__ . '/modules/leaderboard.php';
        $lb = new LeaderboardSystem($db);
        $lb->renderLeaderboard($chat_id);
    }
}
