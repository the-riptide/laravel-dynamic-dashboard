<?php 

namespace TheRiptide\LaravelDynamicDashboard\Traits;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use TheRiptide\LaravelDynamicDashboard\Models\DynHead;

trait Types {

    public function getType($type, DynHead|string $head = null) { 

        $model = 'App\\' . config('dyndash.folder') . '\\' . Str::of($type)->ucfirst()->singular(); 

        if (class_exists($model)) {

            if(is_string($head)) $head = DynHead::Find($head);

            $model = new $model($head);
            return $model;
        }

        throw new Exception("Not an existing type", 1);
    }

    public function getAllTypes() 
    {
        $files = File::allFiles(app_path(config('dyndash.folder')));

        return collect($files)
        ->map(
            fn ($file) => Str::before($file->getBasename(), '.')  
        );

    }
}