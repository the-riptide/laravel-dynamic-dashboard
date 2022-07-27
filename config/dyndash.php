<?php

return [

    /** This is the path to the 'types' folder that is to be included in the dashboard */
    'folder' => 'Dyndash',

    /** Include a registered user's email here to let them access the dashboard */

    'emails' => [env('DASH_EMAIL')],

    /** In menu items, you can add additional entries to the the menu that are outside of the dyndash. In this case, the key will be the name 
     * given to the file. In a sub array you can then specify the route name the link should lead to with the key 'route'. 
     * In menu names you can indicate what a Type should be called in your menu. Specify the Type as the key and the name you'd like to use as the value.
     * You do not need to specify a name. In that case, the Type name will be modified slightly and used.  
    */

    'menu' => [
        'items' => [
            'texts' => [
                'route' => 'dashboard_texts'
            ],
        ],
        'names' => [
        ],
    ],

    /** for srcset purposes a range of standard image sizes have been set. 
     * If you would like to specify your own sizes, create a 'sizes' sub arary in 'images'. In there, specify the dyndash 'type' of
     * where you'd like to set the sizes of your image. These sizes will then automatically be created when an image is saved
     * and used when you use the 'srcset' helper. */
    'images' => [
    ],

    /** set the url to the public folder in order to switch out the application mark in the top left */
    'application-mark' => '',


];
