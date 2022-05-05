<?php 

namespace TheRiptide\LaravelDynamicDashboard\Objects;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class Base {

    private $typePath = 'App\Dynamic\Types\\';

    protected function getType($type) { 

        $model = $this->typePath . Str::of($type)->ucfirst()->singular(); 

        if (class_exists($model)) {

            $model = new $model;
            return $model;
        }

        throw new Exception("Not an existing type", 1);
    }

    protected function getAllTypes() 
    {
        $files = File::allFiles(config('dyndash.path'));

        return collect($files)->map(
            fn ($file) => Str::before($file->getBasename(), '.')  
        );

    }
}