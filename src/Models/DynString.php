<?php

namespace TheRiptide\LaravelDynamicDashboard\Models;

use TheRiptide\LaravelDynamicDashboard\Models\DynModel;
use TheRiptide\LaravelDynamicDashboard\Factories\DynStringFactory;

class DynString extends DynModel
{

    public $rules = 'required|string';
    public $component = 'simple';
    
    protected static function newFactory()
    {
        return DynStringFactory::new();
    }

}
