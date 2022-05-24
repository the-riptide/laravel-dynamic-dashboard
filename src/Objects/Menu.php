<?php

namespace TheRiptide\LaravelDynamicDashboard\Objects;

use Illuminate\Support\Str;
use TheRiptide\LaravelDynamicDashboard\Traits\GetType;

class Menu
{
    use GetType;

    public $items;

    public function __construct() {


        $this->items = $this->getAllTypes()
        ->filter(fn ($item) => $item !== 'Example' && $item !==  'example' )
        ->map(
            function ($item) { return [
                
                    'name' => $item,
                    'route' => 'dyndash.index',
                    'parameter' =>Str::of($item)->lower()
                
            ];
        }
        )->concat(
            collect(config('dyndash.menu_items'))->map(function ($item, $key) 
            {
                if (isset($item['route'])) {
                    
                    return [ 
                        'name' => $key,
                        'route' => $item['route'],
                        'parameter' => $item['parameter'] ?? null,
                    
                    ];
                }
                else {
                    return [
                        
                        'route' => $item,
                        'name' => $key,
                    ];
                }
            })
        );
    }
}