<?php 

if (! function_exists('srcset')) {

    function srcset($model, $image = 'url') {

        return app('ManageImage')->prepSrcset($model, $image);    
    }
}
