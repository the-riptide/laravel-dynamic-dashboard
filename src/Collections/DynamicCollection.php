<?php 

namespace TheRiptide\LaravelDynamicDashboard\Collections;

use Illuminate\Support\Str;
use TheRiptide\LaravelDynamicDashboard\Models\DynHead;
use TheRiptide\LaravelDynamicDashboard\Traits\GetType;

class DynamicCollection 
{
    use GetType;

    private $items;

    public function __construct(string $type) 
    {
        $type = Str::of($type)->camel()->ucfirst();
        $this->items = DynHead::where('type', $type)->get()->map(
            fn ($head) => $this->getType($type, $head)
        );
    }

    public function get() 
    {
        return $this->items;

    }
}