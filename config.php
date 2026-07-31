<?php
/**
 * Project: Enterprise Telegram Refer & Earn Bot
 * Configuration File (`config.php`)
 */

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/error.log');

// ১. টেলিগ্রাম বট কনফিগারেশন
define('BOT_TOKEN', '8805535247:AAFZBrTqW0mDlT2IPQeDKPrMNnYEzflNZto');
define('BOT_USERNAME', 'TaskRefer_bot');

// ২. একাধিক অ্যাডমিন আইডি অ্যারে (এখানে আপনার দেওয়া দুটি আইডি যুক্ত করা হয়েছে)
define('ADMIN_IDS', [7460864063, 6254562905]);

// ৩. ডাটাবেজ কনফিগারেশন (ফায়ারবেস রিয়েলটাইম ডাটাবেজ লিংক)
define('FIREBASE_DB_URL', 'https://refer2earn5-default-rtdb.asia-southeast1.firebasedatabase.app/');

// লোকাল বা ব্যাকআপ মাইএসকিউএল কনফিগারেশন
define('DB_HOST', 'localhost');
define('DB_NAME', 'refer2earn_db');
define('DB_USER', 'root');
define('DB_PASS', '');
