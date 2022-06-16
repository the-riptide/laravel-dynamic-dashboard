<?php

namespace TheRiptide\LaravelDynamicDashboard\Types; 

use TheRiptide\LaravelDynamicDashboard\Objects\DynamicBase;

class TestType extends DynamicBase 
{
    public function index() 
    {
        return collect([
            'head',
        ]);
    }

    public function fields()
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
}