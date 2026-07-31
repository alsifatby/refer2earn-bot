<?php
declare(strict_types=1);

class LeaderboardSystem {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function renderLeaderboard(int|string $chat_id): void {
        $text = "🏆 <b>Top Earners Leaderboard</b>\n\n1. User A - $50.00\n2. User B - $35.00";
        $keyboard = [
            'inline_keyboard' => [
                [['text' => '🔙 Back to Menu', 'callback_data' => 'menu_home']]
            ]
        ];
        sendMessage($chat_id, $text, $keyboard);
    }
}
