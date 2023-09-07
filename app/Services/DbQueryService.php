<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Services\ElixirVars; // Import service class

class DbQueryService
{
    private $map_lookup = [];
    
    public function __construct(ElixirVars $platformLookup)
    {
        $this->map_lookup = $platformLookup->platformLookup()['pl'];
    }
    
    public function recordsCount(string $tbl)
    {
        return DB::table($tbl)->count();
    }
    
    public function tblRecords(string $tbl, int $offset, int $limit): Collection
    {
        return DB::table($tbl)
                    ->offset($offset)
                    ->limit($limit)
                    ->get();
    }
    
    public function tblRecord(string $tbl, array $where): Collection
    {
        return DB::table($tbl)
                    ->where($where['fld'], $where['val'])
                    ->get();
    }
    
    public function insertRecords(string $tbl, array $insert_data)
    {
        DB::table($tbl)->insert($insert_data);
    }
    
    public function updateRecord(string $tbl, array $where, array $update)
    {
        DB::table($tbl)
            ->where($where['fld'], $where['val'])
            ->update($update);
    }
    
    /**
     * app/Services/DbQueryService.php
     * Returns an numeric array of the orderIds that exist in the `undispatched_orderIDs` table,
     * but not in the `barcode` table.
     * Nb. These are the orders that return an error when you select 'Viw Invoice' from 'order ID' menu.
     */ 
    public function missingBarcodeOrders(): array
    {
        $barcode_orderIDs = DB::table('barcode')->pluck('orderId')->toArray();
        $undispatched_orderIDs = DB::table('undispatched_orderIDs')->pluck('orderId')->toArray();

        return array_diff($undispatched_orderIDs, $barcode_orderIDs);
    } 
    
    public function getOrders(array $data): array
    {
        // echo '<pre style="background:#111; color:#b5ce28; font-size:11px;">'; print_r($data); echo '</pre>'; die();
        
        if (!isset($data['platform_orderIDs'])) {
            foreach (DB::table('undispatched_orderIDs')->get()->toArray() as $rec) {
                $data['platform_orderIDs'][$rec->platform][] = $rec->orderId;
                $data['orderIDs'][] = $rec->orderId;
            }
        }
        
        $skus = [];
        $db_data = [];
        foreach ($data['platform_orderIDs'] as $pl_min => $orderIDs) {
            $db_data['obj_orders'][$pl_min] = DB::table($data['pl_lkup'][$pl_min].'_orders')
                    ->whereIn('orderId', $orderIDs)
                    ->get();
            
            $db_data['obj_items'][$pl_min] = DB::table($data['pl_lkup'][$pl_min].'_items')
                    ->whereIn('orderId', $orderIDs)
                    ->get();
            
            $skus = array_merge($skus, $db_data['obj_items'][$pl_min]->pluck('sku')->all() );
        }
        
        $db_data['obj_sku_img'] = DB::table('sku_img')
                ->whereIn('sku', $skus)
                ->get();
        
        // Remember that item titles, variations and prices are no longer stored with
        // individual orders, so they also need to be retrieved.
        $in = implode("','", $skus);
        $db_data['obj_items_lkp'] = DB::select("SELECT * FROM `products` WHERE `sku` IN('$in')");
        // $db_data['obj_items_lkp'] = DB::table('products')
        //         ->whereIn('sku', $skus)
        //         ->get();
        
        $db_data['obj_barcode_lkp'] = DB::table('barcode')
                ->whereIn('orderId', $data['orderIDs'])
                ->get();
        
        $db_data['obj_platform_url_domain_lkp'] = DB::table('platform_url_domain')->get();
        
        // echo '<pre style="background:#111; color:#b5ce28; font-size:11px;">'; print_r($db_data); echo '</pre>'; die();
        
        return $db_data;
    }
    
    public function getOrder(array $data): array
    {
        $platform = $data['pl_lkup'][$data['platform']];
        
        $order = DB::table($platform.'_orders')
            ->where('orderId', $data['orderID'])
            ->get();
        
        $items = DB::table($platform.'_items')
            ->where('orderId', $data['orderID'])
            ->get();
        
        $order = $order->toArray()[0];
        $items = $items->toArray();
        
        $skus = array_column($items, 'sku');
        
        $products = DB::table('products')
            ->whereIn('sku', $skus)
            ->get();
            
        $products = $products->toArray();
        
        $data = json_decode(json_encode([
            'order' => $order,
            'items' => $items,
            'products' => $products,
        ]), true);
        
        // echo '<pre style="background:#111; color:#b5ce28; font-size:11px;">'; print_r($data); echo '</pre>'; die();
        
        return $data;
    }
    
    public function getCouriers(): array
    {
        return DB::table('courier')->get()->toArray();
    }
    
    public function getWebsiteSkuLkup()
    {
        return DB::select("SELECT `sku`, `title`, `price` FROM `website_sku_lkup`");
    }
}
