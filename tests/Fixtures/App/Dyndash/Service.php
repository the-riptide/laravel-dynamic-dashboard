<?php

namespace App\Dyndash;

use Illuminate\Support\Collection;
use TheRiptide\LaravelDynamicDashboard\Objects\DynamicBase;

/** Shaped like the timit app's Service: every input the app uses. */
class Service extends DynamicBase
{
    public function index(): Collection
    {
        return collect(['head']);
    }

    public function fields(): Collection
    {
        return collect([
            'head' => ['type' => 'string', 'properties' => ['title' => 'Title']],
            'neck' => ['type' => 'text', 'properties' => ['title' => 'Summary']],
            'color' => ['type' => 'dropdown', 'properties' => ['items' => ['gold' => 'gold', 'navy' => 'navy']]],
            'body' => ['type' => 'editor', 'properties' => ['title' => 'Description']],
            'primary_image' => ['type' => 'image', 'properties' => ['title' => 'Primary Image']],
            'primary_image_copyright' => ['type' => 'string', 'properties' => ['rules' => 'string|nullable']],
            'secondary_image' => ['type' => 'image', 'properties' => ['rules' => 'nullable|image:allow_svg']],
        ]);
    }
}
