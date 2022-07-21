<?php 

namespace TheRiptide\LaravelDynamicDashboard\Traits;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use TheRiptide\LaravelDynamicDashboard\Models\DynHead;
use TheRiptide\LaravelDynamicDashboard\Objects\DynamicBase;

trait GetType {

    public function getType($type, DynHead|string $head = null, $getCache = true) : DynamicBase { 

        if ($type == 'TestType') return $this->setModel('TheRiptide\LaravelDynamicDashboard\Types\TestType', $head);

        $model = 'App\\' . config('dyndash.folder') . '\\' . Str::of($type)->camel()->ucfirst(); 
        if (class_exists($model)) return $this->setModel($model, $head, $getCache);

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

    private function setModel($model, $head = null, $getCache = true)
    {
        // if(is_string($head)) $head = DynHead::Find($head);

        return $head 
            ? (new $model)->find($head, $getCache)
            : (new $model)->new();
    }
}