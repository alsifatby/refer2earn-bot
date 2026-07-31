<?php
declare(strict_types=1);
send($uid, "🏠 <b>Home Dashboard</b>\n\nName: {$user['name']}\nBalance: <b>{$user['bal']} {$cur}</b>\nTotal Earned: {$user['earned']} {$cur}", $main_kb);
