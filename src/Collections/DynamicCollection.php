<?php 

namespace TheRiptide\LaravelDynamicDashboard\Collections;

use TheRiptide\LaravelDynamicDashboard\Models\DynHead;
use TheRiptide\LaravelDynamicDashboard\Traits\GetType;

class DynamicCollection 
{
    use GetType;

    private $items;

    public function __construct(string $type) 
    {
        $this->items = DynHead::where('type', $type)->get()->map(
            fn ($head) => $this->getType($type, $head)
        );
    }

    public function get() 
    {
        return $this->items;

    }
}