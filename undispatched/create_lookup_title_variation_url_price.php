<?php

/*
http://localhost/LVL_FESP/undispatched/create_lookup_title_variation_url_price.php
*/

$source_lookup = [
    'amazon'       => 'am',
    'ebay'         => 'ee',
    // 'ebay_prosalt' => 'ep',
    // 'floorworld'   => 'ef',
    'onbuy'        => 'on',
    'website'      => 'we',
    'manual'       => 'ma',
];

$json_undispatched = file('data/json_undispatched.php', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$squs = [];
$sql = [];
$sql[] = 'TRUNCATE TABLE `lookup_title_variation_url_price`;';
$sql[] = 'INSERT INTO `lookup_title_variation_url_price` VALUES';
foreach ($json_undispatched as $i => $json_rec) {
    $json_decode = json_decode(str_replace('\u', '_UNICODE_', $json_rec), true);
    
    // convert date to timestamp
    $date = $json_decode['date'];
    list($y,$m,$d) = explode('-', substr($date, 0,10));
    list($h,$i,$s) = explode(':', substr($date, 11,8));
    $ts = mktime($h,$i,$s,$m,$d,$y);
    
    foreach ($json_decode['items'] as $product) {
        $pl_form = $source_lookup[$json_decode['source']];
        // $pl_chan = $json_decode['channel'];
        $sku = $product['SKU'];
        $squs[] = $sku;
        $itemID = $product['itemID'];
        $title = str_replace('_UNICODE_', '\u', $product['name']);
        $title = str_replace('"', '\"', $title);
        
        $vars = '';
        if ('' != $product['variations']){
            $vars = json_encode($product['variations']);
            $vars = str_replace('"', '\"', $vars);
            $vars = str_replace('\\\\"', '\"', $vars);
        }
        
        /*
        Full URLs:
        https://www.amazon.co.uk/dp/B071JPLQCM
        https://www.ebay.co.uk/itm/175455821180
        https://www.onbuy.com/gb/P8ZFF7S
        https://elixirgardensupplies.co.uk/product/black-round-plant-pots
        */
        if ('we' != $pl_form) {
            $url = $itemID;
            $price = $product['price'] / $product['quantity'];
            
            if ('prosalt' == $json_decode['channel']) {$pl_form = 'ep';}
            elseif ('floorworld' == $json_decode['channel']) {$pl_form = 'ef';}
        }
        else {
            $url = $product['url'];
            $url = str_replace(['https://elixirgardensupplies.co.uk/product/', '/'], '', $url);
            
            $price = $product['price'];
        }
        
        $sql[] = "(NULL, \"$pl_form\", \"$ts\", \"$sku\" ,\"$title\", \"$vars\", \"$url\", \"$price\"),";
    }
}

$output = [];
foreach ($squs as $sku) {
    $output[] = "    '$sku',";
}
$output_str = implode("\n", $output);


echo '<pre style="background:#111; color:#b5ce28; font-size:11px;">'; print_r($output_str); echo '</pre>'; die();
echo '<div style="background:#111; color:#b5ce28; font-size:11px;">'; print_r( implode("','", $squs) ); echo '</div>'; die();

$sql_str = implode("\n", $sql);
$sql_str = substr($sql_str, 0,-1).';';
echo '<pre style="background:#111; color:#b5ce28; font-size:11px;">'; print_r($sql_str); echo '</pre>'; die();