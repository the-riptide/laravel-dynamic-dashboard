<?php 

namespace TheRiptide\LaravelDynamicDashboard\Traits;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use TheRiptide\LaravelDynamicDashboard\Models\DynHead;
use TheRiptide\LaravelDynamicDashboard\Objects\DynamicBase;

trait GetType {

    public function getType($type, DynHead|string $head = null) : DynamicBase { 

        $model = 'App\\' . config('dyndash.folder') . '\\' . Str::of($type)->ucfirst(); 

        if (class_exists($model)) {

            if(is_string($head)) $head = DynHead::Find($head);

            return new $model($head);           
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