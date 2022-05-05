<?php

namespace TheRiptide\LaravelDynamicDashboard\Objects;

use Illuminate\Support\Str;

class Menu extends Base
{

    public $items;

    public function __construct() {

        $this->items = $this->getAllTypes()->mapWithKeys(
            fn ($item) => [
                $item => Str::of($item)->lower()
            ]
        );        
    }
}