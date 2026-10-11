<?php

namespace App\Dyndash;

use Illuminate\Support\Collection;
use TheRiptide\LaravelDynamicDashboard\Objects\DynamicBase;

class Experience extends DynamicBase
{
    public function fields(): Collection
    {
        return collect([
            'head' => ['type' => 'string'],
        ]);
    }

    public function relationships(): Collection
    {
        return collect([
            'Event' => ['show' => 'name'],
        ]);
    }
}
