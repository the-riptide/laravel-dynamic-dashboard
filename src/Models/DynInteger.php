<?php

namespace TheRiptide\LaravelDynamicDashboard\Models;

use TheRiptide\LaravelDynamicDashboard\Models\DynModel;

class DynInteger extends DynModel
{
    public $rules = 'required|integer';
    public $component = 'simple';
    public $type = 'number';

}
