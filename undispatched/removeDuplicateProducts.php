<?php

// http://localhost/LVL_FESP/undispatched/removeDuplicateProducts.php

$names = [
    'Dave',
    'Bob',
    'Vova',
    'Pete',
    'Mark',
];

$db_host = 'localhost';
$db_name = 'FESP';
$db_user = 'root';
$db_pass = '';

$db_mysql = new PDO(
    "mysql:host=$db_host;dbname=$db_name",
    $db_user,
    $db_pass,
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        // required to store / display unicode characters correctly - eg. 3m²-700m² (3m\u00b2-700m\u00b2)
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
    ]
);

$sql = "SELECT * FROM `products_orig`";
$results = $db_mysql->query($sql);
$products = $results->fetchAll(PDO::FETCH_ASSOC); // FETCH_ASSOC FETCH_COLUMN FETCH_KEY_PAIR FETCH_NUM

$tbl_name = 'products';

$create = "DROP TABLE IF EXISTS `$tbl_name`;
CREATE TABLE `$tbl_name` (
  `autoInc` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `platform` char(2) NOT NULL,
  `ts` int(11) NOT NULL,
  `sku` varchar(60) NOT NULL,
  `title` varchar(255) NOT NULL,
  `elix_title` varchar(150) NOT NULL,
  `variation` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `weight` double NOT NULL,
  `length` double NOT NULL,
  `price` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

TRUNCATE TABLE `$tbl_name`;";

// TRUNCATE TABLE `products`
// root 2022-12-08 08:44:28 {"Console\/Mode":"collapse","NavigationWidth":259}

$tmp = [];
$insert = "INSERT INTO `$tbl_name` (`autoInc`, `platform`, `ts`, `sku`, `title`, `elix_title`, `variation`, `url`, `weight`, `length`, `price`) VALUES";
$unique_products = [];
$unique_products[] = $create;
$unique_products[] = $insert;
$i = 0;
$j = 1;
foreach ($products as $rec) {
    if (!isset($tmp[$rec['platform'].$rec['sku']])) {
        $title = $rec['title'];
        $title_length = strlen($title);
        
        $autoInc    = $j++;
        $platform   = $rec['platform'];
        $ts         = $rec['ts'];
        $sku        = $rec['sku'];
        $title      = str_replace('"', '\"', $title);
        $title      = str_replace("'", "''", $title);
        $elix_title = $title_length > 80 ? substr($title, 0,80).'***' : $title;
        $variation  = str_replace('"', '\"', $rec['variation']);
        $url        = $rec['url'];
        $weight     = $rec['weight'];
        $length     = $rec['length'];
        $price      = $rec['price'];
        
        if (500 == $i++) {
            $unique_products[] = "($autoInc, '$platform', '$ts', '$sku', '$title', '$elix_title', '$variation', '$url', '$weight', '$length', '$price');";
            $unique_products[] = $insert;
            $i = 0;
        }
        else {
            $unique_products[] = "($autoInc, '$platform', '$ts', '$sku', '$title', '$elix_title', '$variation', '$url', '$weight', '$length', '$price'),";
        }
    }
    
    $tmp[$rec['platform'].$rec['sku']] = '';
}

$unique_products[count($unique_products)-1] = substr($unique_products[count($unique_products)-1], 0,-1).';';

$unique_products_str = implode("\n", $unique_products);

echo '<pre style="background:#111; color:#b5ce28; font-size:11px;">'; print_r($unique_products_str); echo '</pre>';