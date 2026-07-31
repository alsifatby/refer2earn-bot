<?php
declare(strict_types=1);

class GiftCodeSystem {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function redeemCode(int|string $chat_id, int|string $user_id, string $code): bool {
        // গিফট কোড চেক করার লজিক
        return false; 
    }
}
