<?php

namespace App\Dyndash;

use Illuminate\Support\Collection;
use TheRiptide\LaravelDynamicDashboard\Objects\DynamicBase;

class Event extends DynamicBase
{
    public function index(): Collection
    {
        return collect(['name']);
    }

    public function fields(): Collection
    {
        return collect([
            'name' => ['type' => 'string'],
            'featured' => ['type' => 'boolean'],
            'link' => ['type' => 'string'],
            'logo' => ['type' => 'image'],
        ]);
    }
}
