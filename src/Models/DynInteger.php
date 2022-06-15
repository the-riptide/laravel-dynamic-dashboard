<?php

namespace TheRiptide\LaravelDynamicDashboard\Models;

use TheRiptide\LaravelDynamicDashboard\Models\DynModel;
use TheRiptide\LaravelDynamicDashboard\Factories\DynIntegerFactory;

class DynInteger extends DynModel
{
    public $rules = 'required|integer';
    public $component = 'simple';
    public $type = 'number';


    protected static function newFactory()
    {
        return DynIntegerFactory::new();
    }

}
