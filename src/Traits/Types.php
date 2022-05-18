<?php 

namespace TheRiptide\LaravelDynamicDashboard\Traits;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use TheRiptide\LaravelDynamicDashboard\Models\DynHead;

trait Types {

    public function getType($type, $id = null) { 

        $model = 'App\\' . config('dyndash.folder') . '\\' . Str::of($type)->ucfirst()->singular(); 

        if (class_exists($model)) {

            $model = new $model(DynHead::find($id));
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