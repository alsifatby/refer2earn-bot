<?php
declare(strict_types=1);

class AdminPanel {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function handleAdminCommand(int|string $chat_id, string $text): void {
        $reply = "🛠 <b>Admin Control Panel</b>\n\nCommands:\n/stats - Bot Statistics\n/broadcast - Send message to all";
        sendMessage($chat_id, $reply);
    }
}
