<?php 

namespace TheRiptide\LaravelDynamicDashboard\Objects;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class Base {

    protected function getType($type) { 

        $model = 'App\\' . config('dyndash.folder') . '\\' . Str::of($type)->ucfirst()->singular(); 

        if (class_exists($model)) {

            $model = new $model;
            return $model;
        }

        throw new Exception("Not an existing type", 1);
    }

    protected function getAllTypes() 
    {
        $files = File::allFiles(app_path(config('dyndash.folder')));

        return collect($files)
        ->map(
            fn ($file) => Str::before($file->getBasename(), '.')  
        );

    }
}