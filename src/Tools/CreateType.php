<?php

namespace TheRiptide\LaravelDynamicDashboard\Tools;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class CreateType {

    public function run($type)
    {
        $origin = __DIR__ .'/../Types/ExampleName.php';

        File::put(
            'app/' . (config('dyndash.folder') ?? 'DynDash') . '/' . $type . '.php', 
            Str::of(File::get($origin))
                ->replace('ExampleNameSpace', 'App\\' . (config('dyndash.folder') ?? 'DynDash'))
                ->replace('ExampleName', $type)
                ->__toString()
        );
    }
}