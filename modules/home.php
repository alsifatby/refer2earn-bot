<?php
declare(strict_types=1);

class HomeDashboard {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function renderDashboard(int|string $chat_id, int|string $user_id): void {
        $text = "🏠 <b>Main Menu Dashboard</b>\n\nChoose an option below to continue:";
        
        $keyboard = [
            'inline_keyboard' => [
                [['text' => '👤 My Profile', 'callback_data' => 'profile'], ['text' => '🔗 Referrals', 'callback_data' => 'referral']],
                [['text' => '📋 Earn Tasks', 'callback_data' => 'tasks'], ['text' => '💳 Withdraw', 'callback_data' => 'withdraw']],
                [['text' => '🏆 Leaderboard', 'callback_data' => 'leaderboard']]
            ]
        ];

        sendMessage($chat_id, $text, $keyboard);
    }
}
