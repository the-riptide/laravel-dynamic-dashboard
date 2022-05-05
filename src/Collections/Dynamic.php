<?php 

namespace TheRiptide\LaravelDynamicDashboard\Collections;

use TheRiptide\LaravelDynamicDashboard\Models\DynHead;

class Dynamic 
{
    public $links;

    public function __construct(string $type) 
    {
        $this->links = DynHead::where('type', $type)->get()->map(
            fn ($head) => $head->links()
        );
    }
}