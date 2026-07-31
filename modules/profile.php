<?php
declare(strict_types=1);

class UserProfile {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function renderProfile(int|string $chat_id, int|string $user_id): void {
        $text = "👤 <b>User Profile</b>\n\n🆔 ID: <code>{$user_id}</code>\n💰 Balance: <b>$0.00</b>\n👥 Total Referrals: <b>0</b>";
        
        $keyboard = [
            'inline_keyboard' => [
                [['text' => '🔙 Back to Menu', 'callback_data' => 'menu_home']]
            ]
        ];

        sendMessage($chat_id, $text, $keyboard);
    }
}
