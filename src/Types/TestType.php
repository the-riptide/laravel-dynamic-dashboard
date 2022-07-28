<?php

namespace TheRiptide\LaravelDynamicDashboard\Types; 

use Illuminate\Support\Collection;
use TheRiptide\LaravelDynamicDashboard\Objects\DynamicBase;

class TestType extends DynamicBase 
{
    public function index() : Collection
    {
        return collect([
            'head',
        ]);
    }

    public function fields() : Collection
    {
        return collect([

            'head' => [
                'type' => 'string',

                'properties' => [
                    'title' => 'Title',
                    'placeholder' => 'Bitte tragen Sie den Titel des Artikels ein',
                ],
            ],
            
            'neck' => [
                'type' => 'string',

                'properties' => [
                    'title' => 'Artikelauszug',
                    'placeholder' => 'Bitte Artikelauszug ausfüllen',
                ],

            ],

            'body' => [
                'type' => 'editor',

                'properties' => [
                    'title' => 'Artikelkörper',
                    'placeholder' => 'Bitte Artikelkörper ausfüllen',
                ],
            ],

            'image' => [
                'type' => 'image',
            ],
        ]);
    }

    public function relationships() : Collection
    {
        return collect([
            'Article' => [
                'name' => 'head',
            ],
        ]);
    }
}