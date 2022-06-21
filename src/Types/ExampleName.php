<?php

namespace ExampleNameSpace;

use TheRiptide\LaravelDynamicDashboard\Objects\DynamicBase;

class ExampleName extends DynamicBase {

    public function index() 
    {
        return collect([
            'title',
            // 'show' => [
            //     'function' => true,
            // ]
        ]);
    }

    public function show($item) {

        return $item->show
            ? 'yes'
            : 'no';
    }

    public function fields()
    {
        return collect([

            'title' => [
                'type' => 'string',

                'properties' => [
                    'placeholder' => 'Enter your title here...',
                ],
            ],


            'show' => [
                'type' => 'boolean',
            ],

            'date' => [
                'type' => 'date',
            ],

            'result' => [
                'type' => 'dropdown',

                'properties' => [
                    'items' => [
                        1 => 'one',
                        2 => 'two',
                        3 => 'three',
                        4 => 'four',
                        5 => 'five',
                    ],
                ],
            ],

            'image' => [
                'type' => 'image',
            ],

            'integer' => [
                'type' => 'integer',
            ],

            'text' => [
                'type' => 'text',
            ],
        ]);
    }
}