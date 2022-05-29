<?php

namespace app\dyndash;

class Example {

    public function index() 
    {
        return collect([
            'title',
            // 'show' => [
            //     'function' => true,
            // ]
        ]);
    }

    public function getShow($item) {

        return $item->show
            ? 'yes'
            : 'no';
    }

    public function fields()
    {
        return collect([

            'title' => [
                'type' => 'string',

                'proporties' => [
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

                'proporties' => [
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