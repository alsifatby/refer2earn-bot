<?php
declare(strict_types=1);

class TaskManager {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function renderTaskList(int|string $chat_id, int|string $user_id): void {
        $text = "📋 <b>Available Tasks</b>\n\nComplete tasks below to earn rewards:";
        $keyboard = [
            'inline_keyboard' => [
                [['text' => '📢 Join Channel ($0.10)', 'callback_data' => 'view_task_1']],
                [['text' => '🔙 Back to Menu', 'callback_data' => 'menu_home']]
            ]
        ];
        sendMessage($chat_id, $text, $keyboard);
    }
}
