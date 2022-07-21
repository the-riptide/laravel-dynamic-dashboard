<?php 

namespace TheRiptide\LaravelDynamicDashboard\Collections;

use Illuminate\Support\Str;
use TheRiptide\LaravelDynamicDashboard\Models\DynHead;
use TheRiptide\LaravelDynamicDashboard\Traits\GetType;

class DynamicCollection 
{
    use GetType;

    private $items;

    public function __construct(string $type, $getCache = true) 
    {
        $type = Str::of($type)->camel()->ucfirst();
        $this->items = DynHead::where('dyn_type', $type)->get()->map(
            fn ($head) => $this->getType($type, $head, $getCache)
        );

        if (count($this->items) > 0 ) $this->sort();
    }

    public function get() 
    {
        return $this->items;
    }

    private function sort()
    {        
        $this->items = $this->items->sortBy($this->items->first()->setOrderBy());


    }
}