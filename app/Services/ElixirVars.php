<?php

namespace App\Services;

class ElixirVars{
    
    public function platformLookup(): array
    {
        return [
            'pl' => [
                'am' => [
                    'source'=>'amazon',
                    'platform'=>'Amazon',
                    'channel'=>'Elixir'
                ],
                'ee' => [
                    'source'=>'ebay_elixir',
                    'platform'=>'eBay',
                    'channel'=>'Elixir'
                ],
                'ep' => [
                    'source'=>'ebay_prosalt',
                    'platform'=>'eBay',
                    'channel'=>'Prosalt'
                ],
                'ef' => [
                    'source'=>'ebay_floorworld',
                    'platform'=>'eBay',
                    'channel'=>'Floorworld'
                ],
                'on' => [
                    'source'=>'onbuy',
                    'platform'=>'Onbuy',
                    'channel'=>'Elixir'
                ],
                'we' => [
                    'source'=>'website',
                    'platform'=>'website',
                    'channel'=>'Elixir'
                ],
            ],
            
            'pl_lkup' => [
                'am' => 'amazon',
                'ee' => 'ebay_elixir',
                'ep' => 'ebay_prosalt',
                'ef' => 'ebay_floorworld',
                'on' => 'onbuy',
                'we' => 'website',
            ],
            
            'st' => [
                'M' => 'MARKED',
                'G' => 'GENERATED',
                'H' => 'HOLD',
                'W' => 'UNBARCODED',
            ],
            
            'vat' => [
                [
                    'rate' => 20,
                    'date' => 1294099200, // 04 Jan 2011
                ],
            ],
        ];
    }
}