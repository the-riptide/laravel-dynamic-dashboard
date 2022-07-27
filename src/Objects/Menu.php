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
            function ($item) 
            { 
                return [
                
                    'name' => config('dyndash.menu.names.' . $item) != null 
                        ? config('dyndash.menu.names.'. $item) 
                        : Str::of($item)->snake()->replace('_', ' ')->ucfirst()->plural(),
                    'route' => 'dyndash.index',
                    'parameter' =>Str::of($item)->snake(),
                    'active' => request()->routeIs('dyndash.*') && request()->route()->parameter('type') == Str::snake($item) ? true : false,           
                ];
            }
        )->concat(
            collect(config('dyndash.menu.items'))->map(function ($item, $key) 
            {
                if (isset($item['route'])) {
                    
                    return [ 
                        'name' => Str::of($key)->snake()->replace('_', ' ')->ucfirst(),
                        'route' => $item['route'],
                        'parameter' => $item['parameter'] ?? null,
                        'active' => request()->route()->getName() == $item['route'],
                    ];
                }
                else {
                    return [
                        
                        'route' => $item,
                        'name' => Str::of($key)->snake()->replace('_', ' ')->ucfirst(),
                        'active' => request()->route()->getName() == $item,
                    ];
                }
            })
        );
    }
}