<?php

// http://localhost/LVL_FESP/undispatched_data/api_orders_address_data.php

$db_sqlite = new PDO('sqlite:api_orders.db3');


$postcodes_unique = [];

foreach (['amazon','ebay'] as $platform) {
    $sql = "SELECT DISTINCT `postcode` FROM `{$platform}_orders`";
    $results = $db_sqlite->query($sql);
    $postcodes = $results->fetchAll(PDO::FETCH_COLUMN);

    $postcodes = array_map(function($postcode){
        $postcode = strtoupper($postcode);
        
        $postcode = str_replace('.', '', $postcode);
        $postcode = str_replace(',', '', $postcode);
        
        return $postcode;
    }, $postcodes);


    $postcodes = array_map(function($postcode){
        // Remove all non-numbers & non-letters
        $postcode = preg_replace('/[^0-9A-Z]/', '', $postcode);
        
        // $postcode_area = '';
        
        if (5 == strlen($postcode) || 6 == strlen($postcode)) {
            $postcode_area = substr($postcode, 0,3);
        }
        elseif (7 == strlen($postcode)) {
            $postcode_area = substr($postcode, 0,4);
        }
        else {
            $postcode_area = "****************************** $postcode";
        }
        
        return $postcode_area;
    }, $postcodes);
    
    foreach ($postcodes as $postcode_area) {
        $postcodes_unique[$postcode_area] = 1;
    }
}





echo '<pre style="background:#111; color:#b5ce28; font-size:11px;">'; print_r($postcodes_unique); echo '</pre>'; die();