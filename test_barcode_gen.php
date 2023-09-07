<?php
// http://localhost/LVL_FESP/test_barcode_gen.php

/*
https://github.com/picqer/php-barcode-generator

# install:
$ composer require picqer/php-barcode-generator
*/

// phpinfo(); die();

require 'vendor/autoload.php';

$barcode = '42245707';


// This will output the barcode as HTML output to display in the browser
$generator = new Picqer\Barcode\BarcodeGeneratorHTML();
$generatorPNG = new Picqer\Barcode\BarcodeGeneratorPNG();

echo "$barcode<br>";
// echo $generator->getBarcode($barcode, $generator::TYPE_INTERLEAVED_2_5);

?>
<img src="data:image/png;base64,<?= base64_encode($generatorPNG->getBarcode($barcode, $generatorPNG::TYPE_INTERLEAVED_2_5)) ?>">
