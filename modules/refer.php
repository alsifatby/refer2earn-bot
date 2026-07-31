<?php
declare(strict_types=1);

class ReferralSystem {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function renderReferralStats(int|string $chat_id, int|string $user_id): void {
        $ref_link = "https://t.me/" . BOT_USERNAME . "?start=ref_" . $user_id;
        $text = "🔗 <b>Referral Program</b>\n\nInvite your friends and earn bonus!\n\nYour Link:\n<code>{$ref_link}</code>";

        $keyboard = [
            'inline_keyboard' => [
                [['text' => '🔙 Back to Menu', 'callback_data' => 'menu_home']]
            ]
        ];

        sendMessage($chat_id, $text, $keyboard);
    }
}
