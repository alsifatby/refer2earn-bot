<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/functions.php';

$rawInput = file_get_contents('php://input');
$update = json_decode($rawInput, true);
if (!is_array($update)) { http_response_code(200); exit('OK'); }

$chat = $update['message']['chat'] ?? $update['callback_query']['message']['chat'] ?? null;
$from = $update['message']['from'] ?? $update['callback_query']['from'] ?? null;
$text = $update['message']['text'] ?? '';
$cb_data = $update['callback_query']['data'] ?? '';
$cb_id = $update['callback_query']['id'] ?? '';

if (!$chat || $chat['type'] !== 'private' || !$from) { http_response_code(200); exit('OK'); }

$uid = (string)$from['id'];
$isAdmin = ($uid == ADMIN_ID);

$users = db('users');
$bans = db('bans');
if (in_array($uid, $bans)) { exit; }

$now = time();
if (!isset($users[$uid])) {
    $ref = null;
    if (str_starts_with($text, '/start ')) {
        $p = explode(' ', $text)[1];
        if (isset($users[$p]) && $p != $uid) $ref = $p;
    }
    $users[$uid] = ['id'=>$uid, 'name'=>$from['first_name'], 'bal'=>0, 'earned'=>0, 'refs'=>0, 'ref_by'=>$ref, 'join_date'=>$now, 'last_msg'=>$now, 'state'=>'', 'state_data'=>[], 'verified'=>false];
} else {
    $users[$uid]['name'] = $from['first_name'];
}

$user = &$users[$uid];
$settings = db('settings');
$cur = $settings['currency'] ?? 'টাকা';

function saveUser() { 
    global $users; 
    db('users', $users); 
}

// মূল মেনু (ইংরেজি ভাষা)
$main_kb = [
    'keyboard' => [
        [['text'=>'🏠 Home'], ['text'=>'👤 Profile']],
        [['text'=>'💰 Balance'], ['text'=>'👥 Referral']],
        [['text'=>'📋 Tasks'], ['text'=>'💸 Withdraw']]
    ],
    'resize_keyboard' => true
];
if ($isAdmin) $main_kb['keyboard'][] = [['text'=>'🛠 Admin Panel']];

// যদি ইউজার ভেরিফাই বাটনে ক্লিক করে
if ($cb_data === 'verify_join') {
    $needJoinCheck = false;
    foreach ($settings['channels'] as $channel) {
        if (!isJoined($uid, $channel)) {
            $needJoinCheck = true;
            break;
        }
    }

    if ($needJoinCheck) {
        ans($cb_id, "❌ Please join all channels first!", true);
        exit;
    }

    $user['verified'] = true;
    ans($cb_id, "✅ Verified Successfully!", true);
    tg('deleteMessage', ['chat_id' => $uid, 'message_id' => $update['callback_query']['message']['message_id']]);
    
    if (!empty($user['ref_by']) && isset($users[$user['ref_by']]) && empty($user['ref_rewarded'])) {
        $refId = $user['ref_by'];
        $users[$refId]['refs']++;
        $users[$refId]['bal'] += $settings['reward'] ?? 10;
        $users[$refId]['earned'] += $settings['reward'] ?? 10;
        $user['ref_rewarded'] = true;
        send($refId, "🎉 New referral joined!\n👤 Name: {$user['name']}");
    }
    send($uid, "🎉 Welcome! You are verified.", $main_kb);
    saveUser();
    exit;
}

// ফোর্স জয়েন চেক
if (!$isAdmin && empty($user['verified']) && !empty($settings['channels'])) {
    $needJoin = false;
    $buttons = [];
    foreach ($settings['channels'] as $channel) {
        $buttons[] = [['text' => '📢 Join ' . ltrim($channel, '@'), 'url' => 'https://t.me/' . ltrim($channel, '@')]];
        if (!isJoined($uid, $channel)) { $needJoin = true; }
    }
    if ($needJoin) {
        $buttons[] = [['text' => '✅ Verify Join', 'callback_data' => 'verify_join']];
        send($uid, "🚫 <b>Access Denied!</b>\nPlease join our channels and click Verify.", ['inline_keyboard' => $buttons]);
        saveUser();
        exit;
    } else {
        $user['verified'] = true;
    }
}

// ==========================================
// মডিউলার রাউটিং (প্রতিটি ফিচারের জন্য আলাদা ফাইল কল হবে)
// ==========================================
if ($text === '/start' || str_starts_with($text, '/start ')) {
    if (file_exists(__DIR__ . '/modules/start.php')) require_once __DIR__ . '/modules/start.php';
} elseif ($text === '🏠 Home') {
    if (file_exists(__DIR__ . '/modules/home.php')) require_once __DIR__ . '/modules/home.php';
} elseif ($text === '👤 Profile') {
    if (file_exists(__DIR__ . '/modules/profile.php')) require_once __DIR__ . '/modules/profile.php';
} elseif ($text === '💰 Balance') {
    send($uid, "💰 Your Current Balance: <b>{$user['bal']} {$cur}</b>", $main_kb);
} elseif ($text === '👥 Referral') {
    $link = "https://t.me/".BOT_USERNAME."?start=".$uid;
    send($uid, "👥 <b>Referral System</b>\n\nYour Referrals: {$user['refs']}\n🔗 Link:\n<code>{$link}</code>", $main_kb);
} elseif ($text === '📋 Tasks') {
    if (file_exists(__DIR__ . '/modules/tasks.php')) require_once __DIR__ . '/modules/tasks.php';
} elseif ($text === '💸 Withdraw') {
    if (file_exists(__DIR__ . '/modules/withdraw.php')) require_once __DIR__ . '/modules/withdraw.php';
} elseif ($isAdmin && $text === '🛠 Admin Panel') {
    if (file_exists(__DIR__ . '/modules/admin.php')) require_once __DIR__ . '/modules/admin.php';
}

saveUser();
http_response_code(200); exit('OK');
