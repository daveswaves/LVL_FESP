<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DbQueryService; // Import service class
use App\Services\ElixirVars; // Import service class
use Picqer\Barcode\BarcodeGeneratorPNG;
use App\Services\CreateDataService; // Import service class

use PDF;
/*
https://laravel-news.com/snappy-laravel
https://github.com/KnpLabs/snappy


https://github.com/picqer/php-barcode-generator
https://www.nicesnippets.com/blog/laravel-9-barcode-generator-tutorial-with-example
https://github.com/siddharth018/laravel-8-barcode-generator/blob/master/resources/views/barcode.blade.php

https://m.media-amazon.com/images/I/51E-8dfE1BL._SL75_.jpg
https://i.ebayimg.com/thumbs/images/g/YAEAAOSwK7RgDob3/s-l96.jpg
https://elixirgardensupplies.co.uk/wp-content/uploads/2021/07/Black-7cm-Sqaure-Pots-150x150.jpg
*/

class InvController extends Controller
{
    private $invoice_data;
    private $view_name;
    
    public function __construct(
        Request $request,
        DbQueryService $dbQueryService,
        ElixirVars $elixirVars,
        BarcodeGeneratorPNG $generator,
        CreateDataService $createDataService,
    )
    {
        $orderIDs = [];
        if (! $orderIDs = request()->orderIDs) {
            $orderIDs[] = $_GET['id'];
        }
        
        $this->view_name = 'invoice';
        
        $data = [];
        foreach ($orderIDs as $pl_min_orderID) {
            list($pl_min, $orderID) = explode('_', $pl_min_orderID);
            $data['platform_orderIDs'][$pl_min][] = $orderID;
            $data['orderIDs'][] = $orderID;
        }
        
        $data['pl_lkup'] = $elixirVars->platformLookup()['pl_lkup'];
        
        $args = [
            'op' => $this->view_name, // 'undispatched' OR 'invoice'
            'generator' => $generator,
            'data' => $dbQueryService->getOrders($data),
            'map_lookup' => $elixirVars->platformLookup(),
        ];
        
        $this->invoice_data = $createDataService->createDataArray($args);
    }
    
    
    public function invoice()
    {
        if ('preview' == request()->inv_type || isset($_GET['id'])) {
            $html = view('invoice', [
                'orders' => $this->invoice_data,
                'css' => '/css/invoice.css',
                'css_preview' => '/css/invoice_preview.css',
            ]);
            
            return str_replace(public_path(), '', $html);
        }
        elseif ('print' == request()->inv_type) {
            $html = view('invoice', [
                'orders' => $this->invoice_data,
                'css' => public_path('/css/invoice.css'),
            ]);
            
            // Remove full path so images display in HTML output
            // $html = str_replace(public_path(), '', $html); echo $html; die();
            
            // wkhtmltopdf -T 0 -B 0 -L 0 -R 0 --enable-local-file-access invoice.html invoice.pdf

            if (file_exists('files/invoice.pdf')) {
                unlink('files/invoice.pdf');
            }
            
            $pdf = PDF::loadHtml($html)
                ->setOption('enable-local-file-access', true)
                ->setOption('margin-top', 0)
                ->setOption('margin-bottom', 0)
                ->setOption('margin-left', 0)
                ->setOption('margin-right', 0)
                ->save('files/invoice.pdf');
            
            // shell_exec('lpr files/invoice.pdf > Kyocera-TASKalfa-6002i');
            // shell_exec('lpr files/invoice.pdf > Kyocera_Kyocera_TASKalfa_6002i_');
            
            return $pdf->stream();
        }
    }
}
