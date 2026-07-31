<?php
declare(strict_types=1);

class WithdrawSystem {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function renderMethods(int|string $chat_id, int|string $user_id): void {
        $text = "💳 <b>Withdrawal System</b>\n\nSelect your payment method:";
        $keyboard = [
            'inline_keyboard' => [
                [['text' => 'Bkash / Nagad', 'callback_data' => 'wd_bkash'], ['text' => 'USDT / Crypto', 'callback_data' => 'wd_crypto']],
                [['text' => '🔙 Back to Menu', 'callback_data' => 'menu_home']]
            ]
        ];
        sendMessage($chat_id, $text, $keyboard);
    }
}
