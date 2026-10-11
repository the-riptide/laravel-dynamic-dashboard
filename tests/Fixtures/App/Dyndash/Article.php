<?php

namespace App\Dyndash;

use Illuminate\Support\Collection;
use TheRiptide\LaravelDynamicDashboard\Objects\DynamicBase;

/** The type TestType relates to. */
class Article extends DynamicBase
{
    public function fields(): Collection
    {
        return collect([
            'head' => ['type' => 'string'],
        ]);
    }
}
