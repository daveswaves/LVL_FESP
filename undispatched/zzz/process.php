<?php

// http://localhost/LVL_FESP/undispatched_data/process.php

// Omit newline at the end of each array element | Skip empty lines
$file_arr = file('undispatched_data.json', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$undispatched_data = [];
$undispatched_order_ids = [];
foreach ($file_arr as $rec) {
    $order = json_decode($rec, true);
    
    $undispatched_data[] = $order;
    
    $undispatched_order_ids[] = $order['orderID'];
}



echo '<pre style="background:#111; color:#b5ce28; font-size:11px;">'; print_r($undispatched_order_ids); echo '</pre>'; die();