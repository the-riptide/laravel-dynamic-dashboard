<?php

namespace TheRiptide\LaravelDynamicDashboard\Models;

use TheRiptide\LaravelDynamicDashboard\Models\DynModel;

class DynString extends DynModel
{

    public $rules = 'required|string';
    public $component = 'simple';
    
}
