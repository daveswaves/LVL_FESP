<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DbQueryService; // Import service class
use App\Services\ElixirVars; // Import service class

class CreateOrderController extends Controller
{
    public function __construct()
    {
        /* CODE_HERE */
    }
    
    public function index(
        Request $request,
        DbQueryService $dbQueryService,
        ElixirVars $elixirVars,
    )
    {
        $page_title = 'Create an Order';
        
        $order = null;
        if (isset($_GET['id'])) {
            $data = [
                'pl_lkup' => $elixirVars->platformLookup()['pl_lkup'],
                'platform' => $_GET['pf'],
                'orderID' => $_GET['id'],
                
            ];
            
            $order = $dbQueryService->getOrder($data);
            $page_title = 'Reorder';
        }
        
        
        $couriers = $dbQueryService->getCouriers();
        $websiteSkuLkup = $dbQueryService->getWebsiteSkuLkup();
        
        $courier_data = [];
        foreach ($couriers as $rec) {
            $courier_data[] = "<option value=\"{$rec->key}\">{$rec->val}</option>";
        }
        
        $websiteSkuLkup_data = [];
        foreach ($websiteSkuLkup as $rec) {
            $title = str_replace('"', '\"', $rec->title);
            $websiteSkuLkup_data[] = "{sku: \"{$rec->sku}\", title: \"$title\", price: \"{$rec->price}\"}";
            // {sku: "plum_slate_10kg", name: "name - plum_slate_10kg"},
        }
        
        if (!$order) {
            $order = [
                'items' => [
                    [
                        'sku' => '',
                        'qty' => '1',
                        'shipping' => '0.00',
                    ]
                ],
                'products' => [
                    [
                        'elix_title' => '',
                        'price' => '',
                    ]
                ],
            ];
        }
        else {
            foreach ($order['items'] as $i => $rec) {
                $order['items'][$i]['shipping'] = number_format($rec['shipping'], 2, '.', '');
                $order['products'][$i]['price'] = number_format($order['products'][$i]['price'], 2, '.', '');
            }
        }
        
        
        // echo '<pre style="background:#111; color:#b5ce28; font-size:11px;">'; print_r($order); echo '</pre>'; die();
        
        return view('createOrder', [
            'couriers' => $courier_data,
            'websiteSkuLkup' => "let skus = [\n" . implode(",\n", $websiteSkuLkup_data) . "\n];",
            'order' => $order,
            'page_title' => $page_title,
        ]);
    }
}
