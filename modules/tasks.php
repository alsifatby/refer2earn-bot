<?php
declare(strict_types=1);
$ts = db('tasks'); 
$btns = [];
foreach($ts as $t) {
    if($t['status'] == 'active') {
        $btns[] = [['text' => "📋 {$t['title']} [{$t['reward']} {$cur}]", 'callback_data' => "task_{$t['id']}"]];
    }
}
if (empty($btns)) {
    send($uid, "🤷‍♂️ No tasks available right now.", $main_kb);
} else {
    send($uid, "📋 <b>Available Tasks:</b>", ['inline_keyboard' => $btns]);
}
