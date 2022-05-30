<?php

namespace TheRiptide\LaravelDynamicDashboard\Objects;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;
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
                    'parameter' =>Str::of($item)->lower(),
                    'active' => request()->routeIs('dyndash.*') && request()->route()->parameter('type') == Str::lower($item) ? true : false,
                
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
                        'active' => request()->route()->getName() == $item['route'],
                    
                    ];
                }
                else {
                    return [
                        
                        'route' => $item,
                        'name' => $key,
                        'active' => request()->route()->getName() == $item,

                    ];
                }
            })
        );
    }
}