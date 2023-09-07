<?php
/*
http://localhost/LVL_FESP/dbs/get_website_skus_titles_vars_prices.php

http://192.168.0.24/FESP-REFACTOR/ 
*/

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
// set_time_limit(30);
// ini_set("memory_limit", "-1");

$db = new PDO('sqlite:api_orders.db3');


/*
$sql = "SELECT sku, title, variations, price FROM `website_items` WHERE `sku` IN (
    'p_gallup_1ltrx2',
    'p_360-glyphosate_1ltr',
    '5C6-663-L6S',
    'p_Gallup-1-Litre-01',
    'clover_mosspeat_100L_Bale',
    'p_00516',
    'p_growmore_tub_10kg'
)";
*/

$sql = "SELECT sku, title, variations, price FROM `website_items` ORDER BY `rowid` DESC";
$results = $db->query($sql);
$results = $results->fetchAll(PDO::FETCH_ASSOC); // FETCH_ASSOC FETCH_COLUMN FETCH_KEY_PAIR FETCH_NUM

// echo '<pre style="background:#111; color:#b5ce28; font-size:11px;">'; print_r($results); echo '</pre>'; die();

$tmp = [];
foreach ($results as $rec) {
    if (!isset($tmp[$rec['sku']])) {
        $price = '';
        if ('' != $rec['price']) {
            $price = number_format($rec['price'], 2, '.', '');
        }
        else {
            // echo '<pre style="background:#111; color:#b5ce28; font-size:11px;">'; print_r($rec['sku']); echo '</pre>';
        }
        
        $tmp[$rec['sku']] = [
            'sku' => $rec['sku'],
            'title' => $rec['title'],
            'variations' => $rec['variations'],
            'price' => $price,
        ];
    }
}
$results = $tmp;

// echo '<pre style="background:#111; color:#b5ce28; font-size:11px;">'; print_r($results); echo '</pre>'; die();


$insert = 'INSERT INTO `website_sku_lkup` (`sku`, `title`, `vars`, `price`) VALUES';

$inc = 0;
$sql = [];
$sql[] = $insert;
foreach ($results as $rec) {
    $title = str_replace("'", "\'", $rec['title']);
    
    if ($inc++ < 1000) {
        $sql[] = "('{$rec['sku']}','$title','{$rec['variations']}','{$rec['price']}')";
    }
    else {
        $sql[] = "('{$rec['sku']}','$title','{$rec['variations']}','{$rec['price']}')".$insert;
        $inc = 0;
    }
}
$sql_str = implode(",[__NL__]", $sql);

$sql_str = str_replace(') VALUES,', ') VALUES', $sql_str);
$sql_str = str_replace(')INSERT INTO', ");\nINSERT INTO", $sql_str);
$sql_str = str_replace('[__NL__]', "\n", $sql_str).';';

echo '<pre style="background:#111; color:#b5ce28; font-size:11px;">'; print_r($sql_str); echo '</pre>';