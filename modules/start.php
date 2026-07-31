<?php
declare(strict_types=1);

class StartModule {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function handleStart(int|string $chat_id, int|string $user_id, string $text): void {
        $name = "User_" . $user_id; // চাইলে টেলিগ্রাম থেকে নাম ফেচ করা যায়
        
        // ফায়ারবেস বা লোকাল ডাটাবেজে ইউজার চেক ও সেভ করার লজিক
        $keyboard = [
            'keyboard' => [
                [['text' => '🏠 Main Menu'], ['text' => '👤 Profile']],
                [['text' => '🔗 Referrals'], ['text' => '📋 Tasks']],
                [['text' => '💳 Withdraw'], ['text' => '🎁 Gift Code']]
            ],
            'resize_keyboard' => true
        ];

        sendMessage($chat_id, "🎉 Welcome to <b>" . BOT_USERNAME . "</b>!\n\nEarn money by completing tasks and inviting friends.", $keyboard);
    }
}

// ইনডেক্স থেকে কল করার জন্য
$db = Database::getConnection();
$start = new StartModule($db);
$start->handleStart($chat_id, $user_id, $text);
