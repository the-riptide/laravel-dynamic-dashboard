<?php

namespace ExampleNameSpace;

use TheRiptide\LaravelDynamicDashboard\Objects\DynamicBase;

class ExampleName extends DynamicBase {

    /** The 'index' method decides on fields that will show up on the dashboard index. Simply include a name if it's an attribute that should be included as is.
     * If you're accessing a method, create a sub-array where you set 'function' to true. In this way, the function will be accessed and whatever is returned
     * will be displayed in that column. 
     */
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

    /** The field method determines the fields that will be set in the model. Two fields are mandatory, the 'key' and the 'type'. 
     * The Key determines the name of the attribute you're setting and can be used anywhere to call that attribute.
     * The Type, which must be set in a sub array, decides what kind of data will be stored in that field. You can see a full list of available types in the readme
     * It is possible to set other values as well including the validation rules (rules) and the placeholder text (placeholder).
     * See a full list of the options in the readme.
    */

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