<?php

namespace TheRiptide\LaravelDynamicDashboard\Models;

use TheRiptide\LaravelDynamicDashboard\Models\DynModel;
use TheRiptide\LaravelDynamicDashboard\Factories\DynTextFactory;

class DynText extends DynModel
{
    public $component = 'text';
    public $rules = 'required|string';


    protected static function newFactory()
    {
        return DynTextFactory::new();
    }

}
