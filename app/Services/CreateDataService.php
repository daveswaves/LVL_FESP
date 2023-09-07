<?php

namespace App\Services;

class CreateDataService{
    public function createDataArray(array $args): array
    {
        $data = $args['data'];
        $map_lookup = $args['map_lookup'];
        
        $order_items = [];
        foreach ($data['obj_items'] as $platform => $obj_items) {
            foreach ($obj_items as $rec) {
                $order_items[$rec->orderId][$platform][] = [
                    'sku'      => $rec->sku,
                    'qty'      => $rec->qty,
                    'shipping' => $rec->shipping,
                ];
            }
        }
        
        $sku_img_lkp = [];
        foreach ($data['obj_sku_img'] as $rec) {
            $sku_img_lkp[$rec->sku][$rec->platform] = $rec->img;
        }
        
        $items_lkp = [];
        foreach ($data['obj_items_lkp'] as $rec) {
            $items_lkp[$rec->sku][$rec->platform] = [
                'title'     => $rec->elix_title,
                // 'title'     => $rec->price,
                'variation' => $rec->variation,
                'url'       => $rec->url,
                'price'     => $rec->price,
                'weight'    => $rec->weight,
            ];
        }
        
        $barcode_lkp = [];
        foreach ($data['obj_barcode_lkp'] as $rec) {
            $barcode_lkp[$rec->orderId][$rec->platform] = [
                'barcode'       => $rec->barcode,
                'generatedTime' => $rec->generatedTime,
                'statusTime'    => $rec->statusTime,
                'status'        => $rec->status,
                'courier'       => $rec->courier,
            ];
        }
        
        $platform_url_domain_lkp = [];
        foreach ($data['obj_platform_url_domain_lkp'] as $rec) {
            $platform_url_domain_lkp[$rec->platform] = $rec->url;
        }
        
        // Only supplied for invoices
        $vat = $map_lookup['vat'][0]['rate'];
        
        $return_data = [];
        foreach ($data['obj_orders'] as $platform => $objs) {
            foreach ($objs as $obj) {
                $courier = '';
                $status = 'W';
                $print_scan = '';
                if (isset($barcode_lkp[$obj->orderId][$platform]['courier'])) {
                    $courier = $barcode_lkp[$obj->orderId][$platform]['courier'];
                    
                    $status = $barcode_lkp[$obj->orderId][$platform]['status'];
                    $generatedTime = date('d/m/y H:i', $barcode_lkp[$obj->orderId][$platform]['generatedTime']);
                    $statusTime = date('d/m/y H:i', $barcode_lkp[$obj->orderId][$platform]['statusTime']);
                    $print_scan = "$generatedTime-$statusTime";
                }
                
                $date = date('D dS M Y H:i', $obj->date);
                
                $total_price = 0;
                $total_weight = 0;
                $items = [];
                foreach ($order_items[$obj->orderId][$platform] as $i => $arr) {
                    $title = $items_lkp[$arr['sku']][$platform]['title'];
                    $variation = $items_lkp[$arr['sku']][$platform]['variation'];
                    $variation = str_replace(['{','}'], '', $variation);
                    $variation = str_replace('":"', ': ', $variation);
                    $variation = str_replace('"', '', $variation);
                    $url = $items_lkp[$arr['sku']][$platform]['url'];
                    $price = $items_lkp[$arr['sku']][$platform]['price'];
                    $weight = $items_lkp[$arr['sku']][$platform]['weight'];
                    $shipping = $arr['shipping'];
                    
                    $total_price += $price * $arr['qty'];
                    
                    $items[$i] = [
                        'url'       => $platform_url_domain_lkp[$platform].$url,
                        'qty'       => $arr['qty'],
                        'sku'       => $arr['sku'],
                        'img'       => isset($sku_img_lkp[$arr['sku']][$platform]) ? $sku_img_lkp[$arr['sku']][$platform] : '',
                        'title'     => $title,
                        'variation' => $variation,
                        'price'     => $price,
                        'shipping'  => number_format($shipping, 2, '.', ','),
                    ];
                    
                    if ($vat) {
                        $price_vat = $price * $vat/100;
                        $items[$i]['price_net'] = $price - $price_vat;
                        $items[$i]['price_vat'] = $price_vat;
                    }
                    
                    $total_weight += $weight * $arr['qty'];
                }
                
                $return_data[$obj->orderId] = [
                    'orderID'      => $obj->orderId,
                    'source'       => $map_lookup['pl'][$platform]['platform'],
                    'platform'     => $platform,
                    'price'        => $total_price,
                    'date'         => $date,
                    'ts'           => $obj->date,
                    'email'        => $obj->email,
                    'shippingName' => $obj->shippingName,
                    'postcode'     => $obj->postcode,
                    'items'        => $items,
                    'courier'      => $courier,
                ];
                
                if ('invoice' == $args['op']) {
                    $return_data[$obj->orderId]['phone']        = $obj->phone;
                    $return_data[$obj->orderId]['addressLine1'] = $obj->addressLine1;
                    $return_data[$obj->orderId]['addressLine2'] = $obj->addressLine2;
                    $return_data[$obj->orderId]['city']         = $obj->city;
                    $return_data[$obj->orderId]['county']       = $obj->county;
                    $return_data[$obj->orderId]['generator']    = $args['generator'];
                    $return_data[$obj->orderId]['barcode']      = $barcode_lkp[$obj->orderId][$platform]['barcode'];
                }
                elseif ('undispatched' == $args['op']) {
                    $return_data[$obj->orderId]['message'] = $obj->message;
                    $return_data[$obj->orderId]['source'] = $map_lookup['pl'][$platform]['platform'];
                    $return_data[$obj->orderId]['channel'] = $map_lookup['pl'][$platform]['channel'];
                    $return_data[$obj->orderId]['service'] = $obj->service;
                    $return_data[$obj->orderId]['status'] = $status;
                    $return_data[$obj->orderId]['weight'] = $total_weight;
                    $return_data[$obj->orderId]['print_scan'] = $print_scan;
                }
            }
        }
        
        if ('invoice' == $args['op']) {
            return $return_data;
        }
        elseif ('undispatched' == $args['op']) {
            return [
                'tbl_body' => $return_data,
                'lkp_status' => $map_lookup['st'],
            ];
        }
    }
}