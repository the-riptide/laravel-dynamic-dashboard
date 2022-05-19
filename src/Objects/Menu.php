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
        ->mapWithKeys(
            fn ($item) => [
                $item => Str::of($item)->lower()
            ]
        );        
    }
}