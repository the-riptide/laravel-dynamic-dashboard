<?php 

namespace TheRiptide\LaravelDynamicDashboard\Collections;

use TheRiptide\LaravelDynamicDashboard\Models\DynHead;
use TheRiptide\LaravelDynamicDashboard\Traits\Types;

class Dynamic 
{
    use Types;

    public $links;

    public function __construct(string $type) 
    {
        $this->links = DynHead::where('type', $type)->get()->map(
            fn ($head) => $this->getType($type, $head)
        );
    }

    public function links() 
    {
        return $this->links;

    }
}