<?php
declare(strict_types=1);

class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            try {
                // লোকাল বা ডামি পিডিও কানেকশন (ফায়ারবেস বা লোকাল ডিবি হ্যান্ডেল করার জন্য)
                self::$instance = new PDO('sqlite::memory:');
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Database Connection Error: " . $e->getMessage());
            }
        }
        return self::$instance;
    }
}
