<?php
declare(strict_types=1);
send($uid, "👤 <b>User Profile</b>\n\nID: <code>{$uid}</code>\nName: {$user['name']}\nBalance: {$user['bal']} {$cur}\nReferrals: {$user['refs']} users", $main_kb);
