<?php
declare(strict_types=1);
$methods = $settings['methods'] ?? ['bKash', 'Nagad'];
$btns = []; 
foreach($methods as $m) {
    $btns[] = [['text' => "💳 $m", 'callback_data' => "wdm_$m"]];
}
send($uid, "💸 <b>Select Payout Method:</b>\n(Minimum Withdraw: {$settings['min_wd']} {$cur})", ['inline_keyboard' => $btns]);
